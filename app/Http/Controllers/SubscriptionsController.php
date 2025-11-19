<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use JavaScript;
use App\Invoice;
use App\Member;
use App\Plan;
use App\Setting;
use Carbon\Carbon;
use App\SmsTrigger;
use App\ChequeDetail;
use App\Subscription;
use App\InvoiceDetail;
use App\PaymentDetail;
use Illuminate\Http\Request;

class SubscriptionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Check if current user can access a subscription based on member timings
     * Admin users with 'manage-gymie' permission can access all subscriptions
     * Other users can only access subscriptions for members with matching timings
     * 
     * @param Subscription $subscription
     * @return bool
     */
    private function canAccessSubscription($subscription)
    {
        $user = Auth::user();
        
        // If user is not authenticated, deny access
        if (!$user) {
            return false;
        }
        
        // Admin users with manage-gymie permission can access all subscriptions
        if ($user->can('manage-gymie')) {
            return true;
        }
        
        // Load member relationship if not already loaded
        if (!$subscription->relationLoaded('member')) {
            $subscription->load('member');
        }
        
        $member = $subscription->member;
        
        if (!$member) {
            return false;
        }
        
        // If user has no timings set, they can't access any subscriptions (except admin)
        if (empty($user->timings)) {
            return false;
        }
        
        // If member has no timings set, only admin can access
        if (empty($member->timings)) {
            return false;
        }
        
        // User can only access subscriptions for members with matching timings
        return $user->timings === $member->timings;
    }

    /**
     * Apply timings filter to subscription query
     * Admin users with 'manage-gymie' permission see all subscriptions
     * Other users only see subscriptions for members with matching timings
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function applyTimingsFilter($query)
    {
        $user = Auth::user();
        
        // If user is not authenticated, return empty result
        if (!$user) {
            return $query->whereRaw('1=0');
        }
        
        // Admin users with manage-gymie permission see all subscriptions
        if ($user->can('manage-gymie')) {
            return $query;
        }
        
        // If user has no timings set, they see no subscriptions (except admin)
        if (empty($user->timings)) {
            return $query->whereRaw('1=0'); // Return empty result
        }
        
        // Filter subscriptions by joining with members and matching timings
        return $query->whereHas('member', function($q) use ($user) {
            $q->where('mst_members.timings', $user->timings);
        });
    }

    public function index(Request $request)
    {
        // Real-time check for expired cancelled subscriptions
        Subscription::checkAndInactivateExpiredCancelled();
        
        $query = Subscription::indexQuery($request->sort_field, $request->sort_direction, $request->drp_start, $request->drp_end, $request->plan_name);
        
        // Apply timings filter (users can only see subscriptions for members with their timings, admin sees all)
        $query = $this->applyTimingsFilter($query);
        
        $subscriptions = $query->search('"'.$request->input('search').'"')->paginate(10);
        $subscriptionTotal = Subscription::indexQuery($request->sort_field, $request->sort_direction, $request->drp_start, $request->drp_end, $request->plan_name);
        $subscriptionTotal = $this->applyTimingsFilter($subscriptionTotal);
        $subscriptionTotal = $subscriptionTotal->search('"'.$request->input('search').'"')->get();
        $count = $subscriptionTotal->count();

        if (! $request->has('drp_start') or ! $request->has('drp_end')) {
            $drp_placeholder = 'Select daterange filter';
        } else {
            $drp_placeholder = $request->drp_start.' - '.$request->drp_end;
        }

        $request->flash();

        return view('subscriptions.index', compact('subscriptions', 'count', 'drp_placeholder'));
    }

    public function expiring(Request $request)
    {
        $query = Subscription::expiring($request->sort_field, $request->sort_direction, $request->drp_start, $request->drp_end);
        
        // Apply timings filter (users can only see subscriptions for members with their timings, admin sees all)
        $query = $this->applyTimingsFilter($query);
        
        $expirings = $query->search('"'.$request->input('search').'"')->paginate(10);
        $count = $expirings->total();

        if (! $request->has('drp_start') or ! $request->has('drp_end')) {
            $drp_placeholder = 'Select daterange filter';
        } else {
            $drp_placeholder = $request->drp_start.' - '.$request->drp_end;
        }

        $request->flash();

        return view('subscriptions.expiring', compact('expirings', 'count', 'drp_placeholder'));
    }

    public function expired(Request $request)
    {
        $query = Subscription::expired($request->sort_field, $request->sort_direction, $request->drp_start, $request->drp_end);
        
        // Apply timings filter (users can only see subscriptions for members with their timings, admin sees all)
        $query = $this->applyTimingsFilter($query);
        
        $allExpired = $query->search('"'.$request->input('search').'"')->paginate(10);
        $count = $allExpired->total();

        if (! $request->has('drp_start') or ! $request->has('drp_end')) {
            $drp_placeholder = 'Select daterange filter';
        } else {
            $drp_placeholder = $request->drp_start.' - '.$request->drp_end;
        }

        $request->flash();

        return view('subscriptions.expired', compact('allExpired', 'count', 'drp_placeholder'));
    }

    public function create()
    {
        JavaScript::put([
          'gymieToday' => Carbon::today()->format('Y-m-d'),
          'servicesCount' => Plan::count(),
      ]);
        list($invoice_number_mode, $invoiceCounter, $invoice_number) = $this->generateInvoiceNumber();

        return view('subscriptions.create', compact('invoice_number', 'invoiceCounter', 'invoice_number_mode'));
    }

    public function store(Request $request)
    {
        \Log::info('=== SUBSCRIPTION STORE START ===');
        \Log::info('Request Data', [
            'member_id' => $request->member_id,
            'invoice_number' => $request->invoice_number,
            'payment_amount' => $request->payment_amount,
            'discount_amount' => $request->discount_amount,
            'mode' => $request->mode,
            'has_previous_subscriptions' => $request->has('previousSubscriptions'),
            'plan_count' => $request->has('plan') ? count($request->plan) : 0
        ]);
        
        DB::beginTransaction();

        try {
            // Get member
            $member = Member::find($request->member_id);
            if (!$member) {
                \Log::error('Member not found', ['member_id' => $request->member_id]);
                throw new \Exception('Member not found');
            }
            
            // Check if user can access this member (same logic as MembersController)
            $user = Auth::user();
            if (!$user) {
                flash()->error('You must be logged in to create a subscription');
                return redirect(action('SubscriptionsController@index'));
            }
            
            // Admin users with manage-gymie permission can create subscriptions for any member
            if (!$user->can('manage-gymie')) {
                // Non-admin users can only create subscriptions for members with matching timings
                if (empty($user->timings) || empty($member->timings) || $user->timings !== $member->timings) {
                    flash()->error('You do not have permission to create a subscription for this member');
                    return redirect(action('SubscriptionsController@index'));
                }
            }

            \Log::info('Member Found', [
                'member_id' => $member->id,
                'member_code' => $member->member_code,
                'member_name' => $member->name,
                'stored_credit_balance' => $member->credit_balance ?? 'NULL'
            ]);

            // Check if this is a renewal (has previousSubscriptions)
            $isRenewal = $request->has('previousSubscriptions') && count($request->previousSubscriptions) > 0;
            \Log::info('Renewal Check', [
                'is_renewal' => $isRenewal,
                'previous_subscriptions_count' => $isRenewal ? count($request->previousSubscriptions) : 0
            ]);

            // Calculate invoice total (subscription fee)
            $planTotal = 0;
            if ($request->has('plan') && is_array($request->plan)) {
                foreach ($request->plan as $p) {
                    $planTotal += isset($p['price']) ? (int)$p['price'] : 0;
                }
            }
            $subscriptionFee = (int)($planTotal + (int)($request->admission_amount ?: 0) + (int)($request->additional_fees ?: 0));
            $discountAmount = (int)($request->discount_amount ?: 0);
            $invoice_total = (int)($subscriptionFee - $discountAmount);
            
            \Log::info('Invoice Calculation', [
                'plan_total' => $planTotal,
                'admission_amount' => (int)($request->admission_amount ?: 0),
                'additional_fees' => (int)($request->additional_fees ?: 0),
                'subscription_fee' => $subscriptionFee,
                'discount_amount' => $discountAmount,
                'invoice_total' => $invoice_total
            ]);

            // Handle credit/due amount allocation for renewals
            $totalPaymentAllocated = 0;
            $appliedCredit = 0;
            $appliedDueAmount = 0;
            $remainingCredit = 0;
            $remainingPayment = 0;
            
            if ($isRenewal) {
                \Log::info('=== RENEWAL PROCESSING START ===');
                
                // Calculate credit balance on-the-fly (NO DATABASE SYNC)
                $calculatedCreditBalance = $member->calculateCreditBalance();
                \Log::info('Credit Balance Calculated (Runtime)', [
                    'calculated_credit_balance' => $calculatedCreditBalance
                ]);
                
                // Get all invoices before processing
                $allInvoicesBefore = $member->invoices()->get();
                \Log::info('All Invoices Before Processing', [
                    'count' => $allInvoicesBefore->count(),
                    'invoices' => $allInvoicesBefore->map(function($inv) {
                        return [
                            'invoice_id' => $inv->id,
                            'invoice_number' => $inv->invoice_number,
                            'total' => $inv->total,
                            'pending_amount' => $inv->pending_amount,
                            'status' => $inv->status
                        ];
                    })->toArray()
                ]);
                
                // Get available credit and due amount (calculated on-the-fly)
                $availableCredit = $member->getAvailableCredit();
                $dueAmount = $member->getDueAmount();
                
                \Log::info('Credit/Due Calculation', [
                    'available_credit' => $availableCredit,
                    'due_amount' => $dueAmount,
                    'credit_balance' => $calculatedCreditBalance
                ]);
                
                // Calculate payment amount according to formula:
                // Subscription fee - (Available Balance OR Due Amount) - Discount = Amount received
                if ($availableCredit > 0) {
                    // Member has credit: Subscription fee - Available Credit - Discount
                    $calculatedPaymentAmount = $subscriptionFee - $availableCredit - $discountAmount;
                    $appliedCredit = $availableCredit; // Total credit available
                    \Log::info('Payment Calculation (Has Credit)', [
                        'formula' => 'Subscription Fee - Available Credit - Discount',
                        'subscription_fee' => $subscriptionFee,
                        'available_credit' => $availableCredit,
                        'discount_amount' => $discountAmount,
                        'calculated_payment_amount' => $calculatedPaymentAmount
                    ]);
                } elseif ($dueAmount > 0) {
                    // Member has due: Subscription fee + Due Amount - Discount
                    $calculatedPaymentAmount = $subscriptionFee + $dueAmount - $discountAmount;
                    $appliedDueAmount = $dueAmount; // Due amount to be paid
                    \Log::info('Payment Calculation (Has Due)', [
                        'formula' => 'Subscription Fee + Due Amount - Discount',
                        'subscription_fee' => $subscriptionFee,
                        'due_amount' => $dueAmount,
                        'discount_amount' => $discountAmount,
                        'calculated_payment_amount' => $calculatedPaymentAmount
                    ]);
                } else {
                    // No credit or due: Normal calculation
                    $calculatedPaymentAmount = $invoice_total;
                    \Log::info('Payment Calculation (No Credit/Due)', [
                        'formula' => 'Invoice Total',
                        'calculated_payment_amount' => $calculatedPaymentAmount
                    ]);
                }
                
                // Use provided payment_amount or calculated amount (whichever is provided)
                // Track the actual payment amount received (before allocation)
                $actualPaymentReceived = ($request->has('payment_amount') && $request->payment_amount != '') 
                    ? (int)$request->payment_amount 
                    : max(0, (int)$calculatedPaymentAmount);
                
                $paymentAmount = $actualPaymentReceived;
                
                \Log::info('Payment Amount Determination', [
                    'request_payment_amount' => $request->payment_amount,
                    'calculated_payment_amount' => $calculatedPaymentAmount,
                    'actual_payment_received' => $actualPaymentReceived,
                    'final_payment_amount' => $paymentAmount
                ]);
                
                // Allocate payment: Old invoices first (oldest first), then new invoice
                $outstandingInvoices = $member->getOutstandingInvoices();
                // Get overpaid invoices (negative pending_amount) - these are the source of credit
                $overpaidInvoices = $member->getOverpaidInvoices();
                $remainingPayment = $paymentAmount; // Track remaining payment after allocating to old invoices
                $remainingCredit = $appliedCredit; // Track credit available for application
                
                // Check if user entered payment amount
                $userEnteredPayment = ($request->has('payment_amount') && $request->payment_amount != '' && (int)$request->payment_amount > 0);
                
                \Log::info('Initial Allocation State', [
                    'outstanding_invoices_count' => $outstandingInvoices->count(),
                    'overpaid_invoices_count' => $overpaidInvoices->count(),
                    'remaining_payment' => $remainingPayment,
                    'remaining_credit' => $remainingCredit,
                    'applied_credit' => $appliedCredit,
                    'applied_due_amount' => $appliedDueAmount,
                    'user_entered_payment' => $userEnteredPayment,
                    'note' => $userEnteredPayment ? 'User entered payment - credit will be used for new invoice, not deducted from old invoices' : 'No user payment - credit can be deducted from old invoices'
                ]);
                
                // Step 1: Apply credit to outstanding invoices first (priority: clear old debts)
                \Log::info('=== STEP 1: APPLY CREDIT TO OUTSTANDING INVOICES ===');
                if ($remainingCredit > 0 && count($outstandingInvoices) > 0) {
                    foreach ($outstandingInvoices as $oldInvoice) {
                        if ($remainingCredit <= 0) break;
                        
                        $outstandingAmount = $oldInvoice->pending_amount;
                        if ($outstandingAmount > 0) {
                            $creditToApply = min($remainingCredit, $outstandingAmount);
                            
                            \Log::info('Applying Credit to Outstanding Invoice', [
                                'invoice_id' => $oldInvoice->id,
                                'invoice_number' => $oldInvoice->invoice_number,
                                'invoice_total' => $oldInvoice->total,
                                'pending_amount_before' => $oldInvoice->pending_amount,
                                'credit_to_apply' => $creditToApply,
                                'remaining_credit_before' => $remainingCredit
                            ]);
                            
                            // Update old invoice
                            $oldInvoice->pending_amount -= $creditToApply;
                            $oldInvoice->status = \Utilities::setInvoiceStatus($oldInvoice->pending_amount, $oldInvoice->total);
                            $oldInvoice->updatedBy()->associate(Auth::user());
                            $oldInvoice->save();
                            
                            $remainingCredit -= $creditToApply;
                            $totalPaymentAllocated += $creditToApply;
                            
                            \Log::info('Credit Applied Successfully to Outstanding Invoice', [
                                'invoice_id' => $oldInvoice->id,
                                'pending_amount_after' => $oldInvoice->pending_amount,
                                'new_status' => $oldInvoice->status,
                                'remaining_credit_after' => $remainingCredit,
                                'total_payment_allocated' => $totalPaymentAllocated
                            ]);
                        }
                    }
                } else {
                    \Log::info('Step 1 Skipped', [
                        'reason' => $remainingCredit <= 0 ? 'No credit to apply' : 'No outstanding invoices',
                        'remaining_credit' => $remainingCredit,
                        'outstanding_invoices_count' => count($outstandingInvoices)
                    ]);
                }
                
                // Step 1.5: Apply credit to overpaid invoices (reduce credit balance)
                // IMPORTANT: Credit should ALWAYS be reserved for new invoice first
                // Only deduct credit from old overpaid invoices if there's leftover credit AFTER new invoice is covered
                // Calculate how much credit will be needed for new invoice
                $creditNeededForNewInvoice = min($remainingCredit, $invoice_total);
                $leftoverCreditAfterNewInvoice = $remainingCredit - $creditNeededForNewInvoice;
                
                \Log::info('=== STEP 1.5: APPLY CREDIT TO OVERPAID INVOICES (REDUCE CREDIT BALANCE) ===');
                \Log::info('Credit Reservation Check', [
                    'total_credit_available' => $remainingCredit,
                    'invoice_total' => $invoice_total,
                    'credit_needed_for_new_invoice' => $creditNeededForNewInvoice,
                    'leftover_credit_after_new_invoice' => $leftoverCreditAfterNewInvoice,
                    'note' => 'Only leftover credit (if any) can be used for old invoices'
                ]);
                
                // Only use leftover credit for old invoices (if there's any leftover after reserving for new invoice)
                if ($leftoverCreditAfterNewInvoice > 0 && count($overpaidInvoices) > 0) {
                    // There's leftover credit after reserving for new invoice - can use it for old invoices
                    foreach ($overpaidInvoices as $overpaidInvoice) {
                        if ($leftoverCreditAfterNewInvoice <= 0) break;
                        
                        $overpaidAmount = abs($overpaidInvoice->pending_amount); // Convert negative to positive for calculation
                        if ($overpaidAmount > 0) {
                            // Calculate how much credit to deduct from this overpaid invoice (only from leftover)
                            $creditToDeduct = min($leftoverCreditAfterNewInvoice, $overpaidAmount);
                            
                            \Log::info('Reducing Credit from Overpaid Invoice (Using Leftover Credit)', [
                                'invoice_id' => $overpaidInvoice->id,
                                'invoice_number' => $overpaidInvoice->invoice_number,
                                'invoice_total' => $overpaidInvoice->total,
                                'pending_amount_before' => $overpaidInvoice->pending_amount,
                                'overpaid_amount' => $overpaidAmount,
                                'credit_to_deduct' => $creditToDeduct,
                                'leftover_credit_before' => $leftoverCreditAfterNewInvoice
                            ]);
                            
                            // Reduce the negative pending_amount (add to it, since it's negative)
                            $newPendingAmount = $overpaidInvoice->pending_amount + $creditToDeduct;
                            
                            // Update invoice
                            $overpaidInvoice->pending_amount = $newPendingAmount;
                            
                            // Determine new status based on pending_amount
                            // If pending_amount becomes 0, status is Paid
                            // If pending_amount is still negative, status is Overpaid
                            $overpaidInvoice->status = \Utilities::setInvoiceStatus($newPendingAmount, $overpaidInvoice->total);
                            $overpaidInvoice->updatedBy()->associate(Auth::user());
                            $overpaidInvoice->save();
                            
                            $leftoverCreditAfterNewInvoice -= $creditToDeduct;
                            $remainingCredit -= $creditToDeduct; // Also reduce remaining credit
                            $totalPaymentAllocated += $creditToDeduct;
                            
                            \Log::info('Credit Deducted from Overpaid Invoice Successfully', [
                                'invoice_id' => $overpaidInvoice->id,
                                'pending_amount_after' => $overpaidInvoice->pending_amount,
                                'new_status' => $overpaidInvoice->status,
                                'status_name' => \Utilities::getInvoiceStatus($overpaidInvoice->status),
                                'remaining_credit_after' => $remainingCredit,
                                'total_payment_allocated' => $totalPaymentAllocated,
                                'note' => $newPendingAmount == 0 ? 'Invoice fully cleared - status changed to Paid' : 'Partial deduction - status remains Overpaid'
                            ]);
                        }
                    }
                } else {
                    \Log::info('Step 1.5 Skipped - Credit Reserved for New Invoice', [
                        'reason' => $leftoverCreditAfterNewInvoice <= 0 ? 'No leftover credit - all credit needed for new invoice' : 'No overpaid invoices',
                        'credit_needed_for_new_invoice' => $creditNeededForNewInvoice,
                        'leftover_credit' => $leftoverCreditAfterNewInvoice,
                        'overpaid_invoices_count' => count($overpaidInvoices),
                        'note' => 'Credit will be used for new invoice first'
                    ]);
                }
                
                // Step 2: Apply payment to remaining outstanding invoices
                \Log::info('=== STEP 2: APPLY PAYMENT TO OLD INVOICES ===');
                if ($remainingPayment > 0 && count($outstandingInvoices) > 0) {
                    foreach ($outstandingInvoices as $oldInvoice) {
                        if ($remainingPayment <= 0) break;
                        
                        $outstandingAmount = $oldInvoice->pending_amount;
                        if ($outstandingAmount > 0) {
                            $paymentToApply = min($remainingPayment, $outstandingAmount);
                            
                            \Log::info('Applying Payment to Invoice', [
                                'invoice_id' => $oldInvoice->id,
                                'invoice_number' => $oldInvoice->invoice_number,
                                'invoice_total' => $oldInvoice->total,
                                'pending_amount_before' => $oldInvoice->pending_amount,
                                'payment_to_apply' => $paymentToApply,
                                'remaining_payment_before' => $remainingPayment,
                                'payment_mode' => isset($request->mode) ? (int)$request->mode : 1
                            ]);
                            
                            // Create payment detail for old invoice
                            $oldPaymentData = [
                                'invoice_id' => $oldInvoice->id,
                                'payment_amount' => $paymentToApply,
                                'mode' => isset($request->mode) ? (int)$request->mode : 1,
                                'note' => 'Payment from renewal',
                            ];
                            $oldPaymentDetail = new PaymentDetail($oldPaymentData);
                            $oldPaymentDetail->createdBy()->associate(Auth::user());
                            $oldPaymentDetail->updatedBy()->associate(Auth::user());
                            $oldPaymentDetail->save();
                            
                            \Log::info('Payment Detail Created', [
                                'payment_detail_id' => $oldPaymentDetail->id,
                                'payment_amount' => $oldPaymentDetail->payment_amount,
                                'invoice_id' => $oldPaymentDetail->invoice_id
                            ]);
                            
                            // Update old invoice
                            $oldInvoice->pending_amount -= $paymentToApply;
                            $oldInvoice->status = \Utilities::setInvoiceStatus($oldInvoice->pending_amount, $oldInvoice->total);
                            $oldInvoice->updatedBy()->associate(Auth::user());
                            $oldInvoice->save();
                            
                            $remainingPayment -= $paymentToApply;
                            $totalPaymentAllocated += $paymentToApply;
                            
                            \Log::info('Payment Applied Successfully', [
                                'invoice_id' => $oldInvoice->id,
                                'pending_amount_after' => $oldInvoice->pending_amount,
                                'new_status' => $oldInvoice->status,
                                'remaining_payment_after' => $remainingPayment,
                                'total_payment_allocated' => $totalPaymentAllocated
                            ]);
                        }
                    }
                } else {
                    \Log::info('Step 2 Skipped', [
                        'reason' => $remainingPayment <= 0 ? 'No payment to apply' : 'No outstanding invoices',
                        'remaining_payment' => $remainingPayment,
                        'outstanding_invoices_count' => count($outstandingInvoices)
                    ]);
                }
                
                \Log::info('=== OLD INVOICES PROCESSING COMPLETE ===', [
                    'remaining_credit' => $remainingCredit,
                    'remaining_payment' => $remainingPayment,
                    'total_payment_allocated' => $totalPaymentAllocated
                ]);
            } else {
                // Not a renewal, normal flow
                $paymentAmount = (int)($request->payment_amount ?: 0);
                $remainingPayment = $paymentAmount;
                \Log::info('Not a Renewal - Normal Flow', [
                    'payment_amount' => $paymentAmount
                ]);
            }

            // Calculate new invoice pending amount
            // Formula: pending_amount = invoice_total - (payment applied to new invoice) - (credit applied to new invoice)
            // Negative pending_amount = overpaid, Positive = outstanding
            // IMPORTANT: Payment should be used FIRST, then credit only if needed
            $creditAppliedToNewInvoice = 0;
            if ($isRenewal) {
                \Log::info('=== NEW INVOICE CALCULATION ===');
                \Log::info('Before Calculation', [
                    'invoice_total' => $invoice_total,
                    'remaining_credit' => $remainingCredit,
                    'remaining_payment' => $remainingPayment
                ]);
                
                // IMPORTANT: Use credit FIRST, then payment (Option B: Use credit first from old invoices, then payment)
                // Apply credit first (up to invoice total)
                // Only use as much credit as needed to cover the invoice
                $creditAppliedToNewInvoice = 0;
                if ($remainingCredit > 0) {
                    $creditAppliedToNewInvoice = min($remainingCredit, $invoice_total);
                }
                
                // When credit is applied to new invoice, we need to deduct it from old overpaid invoices
                // This transfers the credit from old invoices to new invoice
                if ($creditAppliedToNewInvoice > 0 && count($overpaidInvoices) > 0) {
                    \Log::info('=== TRANSFERRING CREDIT FROM OLD INVOICES TO NEW INVOICE ===');
                    $creditToDeductFromOldInvoices = $creditAppliedToNewInvoice;
                    
                    foreach ($overpaidInvoices as $overpaidInvoice) {
                        if ($creditToDeductFromOldInvoices <= 0) break;
                        
                        $overpaidAmount = abs($overpaidInvoice->pending_amount);
                        if ($overpaidAmount > 0) {
                            // Calculate how much to deduct from this invoice
                            $deductAmount = min($creditToDeductFromOldInvoices, $overpaidAmount);
                            
                            \Log::info('Transferring Credit from Old Invoice to New Invoice', [
                                'old_invoice_id' => $overpaidInvoice->id,
                                'old_invoice_number' => $overpaidInvoice->invoice_number,
                                'pending_amount_before' => $overpaidInvoice->pending_amount,
                                'credit_to_transfer' => $deductAmount,
                                'remaining_to_transfer' => $creditToDeductFromOldInvoices
                            ]);
                            
                            // Reduce the negative pending_amount (add to it, since it's negative)
                            $newPendingAmount = $overpaidInvoice->pending_amount + $deductAmount;
                            
                            // Update old invoice
                            $overpaidInvoice->pending_amount = $newPendingAmount;
                            $overpaidInvoice->status = \Utilities::setInvoiceStatus($newPendingAmount, $overpaidInvoice->total);
                            $overpaidInvoice->updatedBy()->associate(Auth::user());
                            $overpaidInvoice->save();
                            
                            $creditToDeductFromOldInvoices -= $deductAmount;
                            
                            \Log::info('Credit Transferred Successfully', [
                                'old_invoice_id' => $overpaidInvoice->id,
                                'pending_amount_after' => $overpaidInvoice->pending_amount,
                                'new_status' => $overpaidInvoice->status,
                                'status_name' => \Utilities::getInvoiceStatus($overpaidInvoice->status),
                                'remaining_to_transfer' => $creditToDeductFromOldInvoices,
                                'note' => $newPendingAmount == 0 ? 'Old invoice fully cleared - status changed to Paid' : 'Partial transfer - status remains Overpaid'
                            ]);
                        }
                    }
                }
                
                // Calculate how much payment is needed after credit is applied
                $amountAfterCredit = $invoice_total - $creditAppliedToNewInvoice;
                
                // IMPORTANT: If user entered payment, ALWAYS apply it (even if credit already covers invoice)
                // This will make the invoice overpaid if payment + credit > invoice total
                $paymentAppliedToNewInvoice = 0;
                if ($remainingPayment > 0) {
                    // User entered payment - apply it regardless of whether credit already covers the invoice
                    // This allows overpayment scenario: credit + payment > invoice total
                    $paymentAppliedToNewInvoice = $remainingPayment;
                } elseif ($amountAfterCredit > 0) {
                    // No user payment entered, but credit didn't fully cover - this shouldn't happen in renewal flow
                    // But keeping for safety
                    $paymentAppliedToNewInvoice = 0;
                }
                
                // Calculate final pending amount
                // Formula: invoice_total - credit_applied - payment_applied
                // If negative, invoice is overpaid
                $newInvoicePending = (int)($invoice_total - $paymentAppliedToNewInvoice - $creditAppliedToNewInvoice);
                $newInvoicePaymentAmount = (int)$paymentAppliedToNewInvoice;
                
                \Log::info('New Invoice Calculation (Credit First, Then Payment)', [
                    'step_1_credit_available' => $remainingCredit,
                    'step_2_credit_applied' => $creditAppliedToNewInvoice,
                    'step_3_amount_after_credit' => $amountAfterCredit,
                    'step_4_payment_available' => $remainingPayment,
                    'step_5_payment_applied' => $paymentAppliedToNewInvoice,
                    'final_calculation' => [
                        'formula' => 'Invoice Total - Credit Applied - Payment Applied',
                        'invoice_total' => $invoice_total,
                        'credit_applied_to_new_invoice' => $creditAppliedToNewInvoice,
                        'payment_applied_to_new_invoice' => $newInvoicePaymentAmount,
                        'new_invoice_pending' => $newInvoicePending
                    ]
                ]);
            } else {
                // Not a renewal or normal flow
                $newInvoicePending = (int)($invoice_total - $paymentAmount);
                $newInvoicePaymentAmount = (int)$paymentAmount;
                \Log::info('New Invoice Calculation (Normal Flow)', [
                    'invoice_total' => $invoice_total,
                    'payment_amount' => $paymentAmount,
                    'new_invoice_pending' => $newInvoicePending
                ]);
            }

            // Determine new invoice payment status
            // CRITICAL: Check for overpaid (negative pending_amount) FIRST before checking Paid status
            // This handles cases where payment + credit > invoice_total
            $paymentStatus = \constPaymentStatus::Unpaid;
            if ($request->mode == 1 || $request->mode == 2) {
                // Check in order: Overpaid (negative) -> Paid (zero) -> Partial (positive but less than total) -> Unpaid
                if ($newInvoicePending < 0) {
                    // Negative pending_amount = overpaid (payment + credit exceeded invoice total)
                    $paymentStatus = \constPaymentStatus::Overpaid;
                } elseif ($newInvoicePending == 0) {
                    // Exactly paid (payment + credit = invoice total)
                    $paymentStatus = \constPaymentStatus::Paid;
                } elseif ($newInvoicePending > 0 && $newInvoicePending < $invoice_total) {
                    // Partially paid (payment + credit < invoice total but > 0)
                    $paymentStatus = \constPaymentStatus::Partial;
                } elseif ($newInvoicePending >= $invoice_total) {
                    // Not paid at all (payment + credit = 0 or less than 0 relative to total)
                    $paymentStatus = \constPaymentStatus::Unpaid;
                } else {
                    // Fallback to overpaid for safety
                    $paymentStatus = \constPaymentStatus::Overpaid;
                }
            }
            
            \Log::info('Invoice Payment Status Determined', [
                'payment_status' => $paymentStatus,
                'payment_status_name' => \Utilities::getInvoiceStatus($paymentStatus),
                'new_invoice_pending' => $newInvoicePending,
                'invoice_total' => $invoice_total,
                'mode' => $request->mode
            ]);

            // Storing Invoice
            $invoiceData = ['invoice_number'=> $request->invoice_number,
                                     'member_id'=> $request->member_id,
                                     'total'=> $invoice_total,
                                     'status'=> $paymentStatus,
                                     'pending_amount'=> $newInvoicePending,
                                     'discount_amount'=> $discountAmount,
                                     'discount_note'=> $request->discount_note ?: '',
                                     'additional_fees'=> ($request->additional_fees ?: 0),
                                     'note'=>' ', ];

            \Log::info('Creating New Invoice', ['invoice_data' => $invoiceData]);

            $invoice = new Invoice($invoiceData);
            $invoice->createdBy()->associate(Auth::user());
            $invoice->updatedBy()->associate(Auth::user());
            $invoice->save();
            
            \Log::info('Invoice Created', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'total' => $invoice->total,
                'pending_amount' => $invoice->pending_amount,
                'status' => $invoice->status
            ]);

            // Storing subscription
            foreach ($request->plan as $plan) {
                $subscriptionData = ['member_id'=> $request->member_id,
                                            'invoice_id'=> $invoice->id,
                                            'plan_id'=> $plan['id'],
                                            'start_date'=> $plan['start_date'],
                                            'end_date'=> $plan['end_date'],
                                            'status'=> \constSubscription::onGoing,
                                            'is_renewal'=> $isRenewal ? '1' : '0', ];

                $subscription = new Subscription($subscriptionData);
                $subscription->createdBy()->associate(Auth::user());
                $subscription->updatedBy()->associate(Auth::user());
                $subscription->save();

                //Adding subscription to invoice(Invoice Details)
                $detailsData = ['invoice_id'=> $invoice->id,
                                       'plan_id'=> $plan['id'],
                                       'item_amount'=> $plan['price'], ];

                $invoice_details = new InvoiceDetail($detailsData);
                $invoice_details->createdBy()->associate(Auth::user());
                $invoice_details->updatedBy()->associate(Auth::user());
                $invoice_details->save();
            }

            // Payment Details for new invoice (only if payment amount > 0)
            if ($newInvoicePaymentAmount > 0) {
                $paymentNote = '';
                if ($isRenewal) {
                    if ($creditAppliedToNewInvoice > 0) {
                        $paymentNote = 'Payment from renewal (credit of ' . $creditAppliedToNewInvoice . ' also applied)';
                    } else {
                        $paymentNote = 'Payment from renewal (after clearing old invoices)';
                    }
                }
                
                $paymentData = ['invoice_id'=> $invoice->id,
                                         'payment_amount'=> $newInvoicePaymentAmount,
                                         'mode'=> isset($request->mode) ? (int)$request->mode : 1,
                                       'note'=> $paymentNote, ];

                \Log::info('Creating Payment Detail for New Invoice', ['payment_data' => $paymentData]);

                $payment_details = new PaymentDetail($paymentData);
                $payment_details->createdBy()->associate(Auth::user());
                $payment_details->updatedBy()->associate(Auth::user());
                $payment_details->save();
                
                \Log::info('Payment Detail Created', [
                    'payment_detail_id' => $payment_details->id,
                    'invoice_id' => $payment_details->invoice_id,
                    'payment_amount' => $payment_details->payment_amount
                ]);
            } else {
                \Log::info('No Payment Detail Created', [
                    'reason' => 'Payment amount is 0',
                    'new_invoice_payment_amount' => $newInvoicePaymentAmount
                ]);
            }

            // Set the subscription status of the 'Renewed' subscription to Renew
            if ($isRenewal) {
                Subscription::where('invoice_id', $invoice->id)->update(['is_renewal' => '1']);

                foreach ($request->previousSubscriptions as $subscriptionId) {
                    $oldSubscription = Subscription::findOrFail($subscriptionId);
                    \Log::info('Marking Old Subscription as Renewed', [
                        'subscription_id' => $oldSubscription->id,
                        'old_status' => $oldSubscription->status
                    ]);
                    $oldSubscription->status = \constSubscription::renewed;
                    $oldSubscription->updatedBy()->associate(Auth::user());
                    $oldSubscription->save();
                }
                
                // Calculate final credit balance (NO DATABASE SYNC - just for logging)
                $finalCreditBalance = $member->calculateCreditBalance();
                \Log::info('=== FINAL CREDIT BALANCE CALCULATION (Runtime) ===', [
                    'final_credit_balance' => $finalCreditBalance,
                    'note' => 'Credit balance calculated on-the-fly, NOT saved to database'
                ]);
                
                // Get all invoices after processing for comparison
                $allInvoicesAfter = $member->invoices()->get();
                \Log::info('All Invoices After Processing', [
                    'count' => $allInvoicesAfter->count(),
                    'invoices' => $allInvoicesAfter->map(function($inv) {
                        return [
                            'invoice_id' => $inv->id,
                            'invoice_number' => $inv->invoice_number,
                            'total' => $inv->total,
                            'pending_amount' => $inv->pending_amount,
                            'status' => $inv->status
                        ];
                    })->toArray()
                ]);
                
                \Log::info('=== RENEWAL PROCESSING COMPLETE ===');
            }

            // Activate member if they were inactive
            // (When someone renews or gets a new subscription, they should be active)
            if ($member->status == \constStatus::InActive) {
                $member->status = \constStatus::Active;
                $member->save();
            }

            //Updating Numbering Counters
            Setting::where('key', '=', 'invoice_last_number')->update(['value' => $request->invoiceCounter]);
            $sender_id = \Utilities::getSetting('sms_sender_id');
            $gym_name = \Utilities::getSetting('gym_name');

            // Get subscription for SMS (use first subscription)
            $subscription = Subscription::where('invoice_id', $invoice->id)->first();
            
            //SMS Trigger
            if ($invoice->status == \constPaymentStatus::Paid && $subscription) {
                if ($request->mode == 0) {
                    $sms_trigger = SmsTrigger::where('alias', '=', 'payment_with_cheque')->first();
                    if ($sms_trigger) {
                        $message = $sms_trigger->message;
                        $cheque_details = ChequeDetail::where('payment_id', $payment_details->id ?? 0)->first();
                        $sms_text = sprintf($message, $member->name, ($payment_details->payment_amount ?? 0), ($cheque_details->number ?? ''), $invoice->invoice_number, $gym_name);
                        $sms_status = $sms_trigger->status;
                        \Utilities::Sms($sender_id, $member->contact, $sms_text, $sms_status);
                    }
                } else {
                    $sms_trigger = SmsTrigger::where('alias', '=', 'subscription_renewal_with_paid_invoice')->first();
                    if ($sms_trigger && isset($payment_details)) {
                        $message = $sms_trigger->message;
                        $sms_text = sprintf($message, $member->name, $payment_details->payment_amount, $invoice->invoice_number);
                        $sms_status = $sms_trigger->status;
                        \Utilities::Sms($sender_id, $member->contact, $sms_text, $sms_status);
                    }
                }
            } elseif ($invoice->status == \constPaymentStatus::Partial && $subscription) {
                if ($request->mode == 0) {
                    $sms_trigger = SmsTrigger::where('alias', '=', 'payment_with_cheque')->first();
                    if ($sms_trigger && isset($payment_details)) {
                        $message = $sms_trigger->message;
                        $cheque_details = ChequeDetail::where('payment_id', $payment_details->id)->first();
                        $sms_text = sprintf($message, $member->name, $payment_details->payment_amount, ($cheque_details->number ?? ''), $invoice->invoice_number, $gym_name);
                        $sms_status = $sms_trigger->status;
                        \Utilities::Sms($sender_id, $member->contact, $sms_text, $sms_status);
                    }
                } else {
                    $sms_trigger = SmsTrigger::where('alias', '=', 'subscription_renewal_with_partial_invoice')->first();
                    if ($sms_trigger && isset($payment_details)) {
                        $message = $sms_trigger->message;
                        $sms_text = sprintf($message, $member->name, $payment_details->payment_amount, $invoice->invoice_number, $invoice->pending_amount);
                        $sms_status = $sms_trigger->status;
                        \Utilities::Sms($sender_id, $member->contact, $sms_text, $sms_status);
                    }
                }
            } elseif ($invoice->status == \constPaymentStatus::Unpaid && $subscription) {
                if ($request->mode == 0 && isset($payment_details)) {
                    $sms_trigger = SmsTrigger::where('alias', '=', 'payment_with_cheque')->first();
                    if ($sms_trigger) {
                        $message = $sms_trigger->message;
                        $cheque_details = ChequeDetail::where('payment_id', $payment_details->id)->first();
                        $sms_text = sprintf($message, $member->name, $payment_details->payment_amount, ($cheque_details->number ?? ''), $invoice->invoice_number, $gym_name);
                        $sms_status = $sms_trigger->status;
                        \Utilities::Sms($sender_id, $member->contact, $sms_text, $sms_status);
                    }
                } else {
                    $sms_trigger = SmsTrigger::where('alias', '=', 'subscription_renewal_with_unpaid_invoice')->first();
                    if ($sms_trigger) {
                        $message = $sms_trigger->message;
                        $sms_text = sprintf($message, $member->name, $invoice->total, $invoice->invoice_number);
                        $sms_status = $sms_trigger->status;
                        \Utilities::Sms($sender_id, $member->contact, $sms_text, $sms_status);
                    }
                }
            }

            DB::commit();
            
            \Log::info('=== SUBSCRIPTION STORE SUCCESS ===', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'member_id' => $member->id,
                'is_renewal' => $isRenewal
            ]);
            
            flash()->success('Subscription was successfully created');

            return redirect(action('SubscriptionsController@index'));
        } catch (\Exception $e) {
            DB::rollback();
            
            \Log::error('=== SUBSCRIPTION STORE ERROR ===', [
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            flash()->error('Error while creating the Subscription');

            return redirect(action('SubscriptionsController@index'));
        }
    }

    //End of store method

    public function edit($id)
    {
        $subscription = Subscription::findOrFail($id);
        
        // Check if user can access this subscription
        if (!$this->canAccessSubscription($subscription)) {
            flash()->error('You do not have permission to edit this subscription');
            return redirect(action('SubscriptionsController@index'));
        }
        
        // $carbonToday = Carbon::today()->format('Y-m-d');
        // $subscriptionEndDate = $subscription->end_date->format('Y-m-d');
        $diff = Carbon::today()->diffInDays($subscription->end_date);
        //$gymieDiff = $diff->format('Y-m-d');
        $gymieDiff = $subscription->end_date->addDays($diff);

        JavaScript::put([
          'gymieToday' => Carbon::today()->format('Y-m-d'),
          'gymieEndDate' => $subscription->end_date->format('Y-m-d'),
          'gymieDiff' => $gymieDiff->format('Y-m-d'),
      ]);

        return view('subscriptions.edit', compact('subscription'));
    }

    public function update($id, Request $request)
    {
        $subscription = Subscription::findOrFail($id);
        
        // Check if user can access this subscription
        if (!$this->canAccessSubscription($subscription)) {
            flash()->error('You do not have permission to update this subscription');
            return redirect(action('SubscriptionsController@index'));
        }

        $subscription->update($request->all());
        $subscription->updatedBy()->associate(Auth::user());
        $subscription->save();
        flash()->success('Subscription details were successfully updated');

        return redirect('subscriptions/all');
    }

    public function renew($id, Request $request)
    {
        \Log::info('=== SUBSCRIPTION RENEW PAGE LOAD START ===');
        \Log::info('Invoice ID: ' . $id);
        
        //Get Numbering mode
        list($invoice_number_mode, $invoiceCounter, $invoice_number) = $this->generateInvoiceNumber();
        \Log::info('Invoice Number Generated', [
            'invoice_number' => $invoice_number,
            'invoice_number_mode' => $invoice_number_mode,
            'invoiceCounter' => $invoiceCounter
        ]);

        $subscriptions = Subscription::where('invoice_id', $id)->get();
        
        // Check if user can access any of these subscriptions
        $canAccess = false;
        foreach ($subscriptions as $subscription) {
            if ($this->canAccessSubscription($subscription)) {
                $canAccess = true;
                break;
            }
        }
        
        if (!$canAccess) {
            flash()->error('You do not have permission to renew this subscription');
            return redirect(action('SubscriptionsController@index'));
        }
        
        $member_id = $subscriptions->pluck('member_id')->first();
        \Log::info('Subscriptions Found', [
            'subscription_count' => $subscriptions->count(),
            'member_id' => $member_id
        ]);
        
        // Get member and calculate credit/due amounts
        $member = Member::find($member_id);
        if (!$member) {
            \Log::error('Member not found', ['member_id' => $member_id]);
            flash()->error('Member not found');
            return redirect('subscriptions/all');
        }
        
        \Log::info('Member Found', [
            'member_id' => $member->id,
            'member_code' => $member->member_code,
            'member_name' => $member->name,
            'stored_credit_balance' => $member->credit_balance ?? 'NULL'
        ]);
        
        // Calculate credit balance on-the-fly (NO DATABASE SYNC)
        $calculatedCreditBalance = $member->calculateCreditBalance();
        \Log::info('Credit Balance Calculated (Runtime)', [
            'calculated_credit_balance' => $calculatedCreditBalance
        ]);
        
        // Get all invoices for detailed logging
        $allInvoices = $member->invoices()->get();
        \Log::info('All Member Invoices', [
            'total_invoices' => $allInvoices->count(),
            'invoice_details' => $allInvoices->map(function($inv) {
                return [
                    'invoice_id' => $inv->id,
                    'invoice_number' => $inv->invoice_number,
                    'total' => $inv->total,
                    'pending_amount' => $inv->pending_amount,
                    'status' => $inv->status
                ];
            })->toArray()
        ]);
        
        // Calculate credit balance breakdown
        $creditFromInvoices = 0;
        $dueFromInvoices = 0;
        foreach ($allInvoices as $invoice) {
            if ($invoice->pending_amount < 0) {
                $creditFromInvoices += abs($invoice->pending_amount);
            } elseif ($invoice->pending_amount > 0) {
                $dueFromInvoices += $invoice->pending_amount;
            }
        }
        \Log::info('Credit Balance Breakdown', [
            'total_credit_from_invoices' => $creditFromInvoices,
            'total_due_from_invoices' => $dueFromInvoices,
            'net_credit_balance' => $calculatedCreditBalance
        ]);
        
        // Get available credit and due amount for display (calculated on-the-fly)
        $availableCredit = $member->getAvailableCredit();
        $dueAmount = $member->getDueAmount();
        
        \Log::info('Derived Values', [
            'available_credit' => $availableCredit,
            'due_amount' => $dueAmount,
            'credit_balance' => $calculatedCreditBalance
        ]);
        
        // Get outstanding invoices for reference
        $outstandingInvoices = $member->getOutstandingInvoices();
        \Log::info('Outstanding Invoices', [
            'count' => $outstandingInvoices->count(),
            'invoices' => $outstandingInvoices->map(function($inv) {
                return [
                    'invoice_id' => $inv->id,
                    'invoice_number' => $inv->invoice_number,
                    'pending_amount' => $inv->pending_amount,
                    'total' => $inv->total,
                    'status' => $inv->status
                ];
            })->toArray()
        ]);

        // Javascript Variables
        JavaScript::put([
            'gymieToday' => Carbon::today()->format('Y-m-d'),
            'servicesCount' => Plan::count(),
            'currentServices' => $subscriptions->count(),
            'availableCredit' => $availableCredit,
            'dueAmount' => $dueAmount,
        ]);

        \Log::info('=== SUBSCRIPTION RENEW PAGE LOAD END ===');
        \Log::info('Returning view with values', [
            'availableCredit' => $availableCredit,
            'dueAmount' => $dueAmount,
            'outstanding_invoices_count' => $outstandingInvoices->count()
        ]);

        return view('subscriptions.renew', compact(
            'subscriptions', 
            'member_id', 
            'invoice_number', 
            'invoiceCounter', 
            'invoice_number_mode',
            'availableCredit',
            'dueAmount',
            'outstandingInvoices',
            'member'
        ));
    }

    public function cancelSubscription($id)
    {
        DB::beginTransaction();
        try {
            $subscription = Subscription::findOrFail($id);
            
            // Check if user can access this subscription
            if (!$this->canAccessSubscription($subscription)) {
                DB::rollback();
                flash()->error('You do not have permission to cancel this subscription');
                return back();
            }

            $subscription->update(['status' => \constSubscription::cancelled]);

            // Keep member active until subscription period ends
            // The SetExpired scheduled command will automatically inactivate the member
            // after the subscription end_date if they have no other active subscriptions

            DB::commit();
            flash()->success('Subscription was successfully cancelled. Member will remain active until the subscription period ends.');

            return back();
        } catch (Exception $e) {
            DB::rollback();
            flash()->error('Error while cancelling the Subscription');

            return back();
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();

        try {
            $subscription = Subscription::findOrFail($id);
            
            // Check if user can access this subscription
            if (!$this->canAccessSubscription($subscription)) {
                DB::rollback();
                flash()->error('You do not have permission to delete this subscription');
                return back();
            }
            
            $invoice = Invoice::where('id', $subscription->invoice_id)->first();
            $invoice_details = InvoiceDetail::where('invoice_id', $invoice->id)->get();
            $payment_details = PaymentDetail::where('invoice_id', $invoice->id)->get();

            foreach ($invoice_details as $invoice_detail) {
                $invoice_detail->delete();
            }

            foreach ($payment_details as $payment_detail) {
                ChequeDetail::where('payment_id', $payment_detail->id)->delete();
                $payment_detail->delete();
            }

            $subscription->delete();
            $invoice->delete();

            DB::commit();

            return back();
        } catch (\Exception $e) {
            DB::rollback();

            return back();
        }
    }

    public function change($id)
    {
        $subscription = Subscription::findOrFail($id);
        
        // Check if user can access this subscription
        if (!$this->canAccessSubscription($subscription)) {
            flash()->error('You do not have permission to change this subscription');
            return redirect(action('SubscriptionsController@index'));
        }

        $already_paid = PaymentDetail::leftJoin('trn_cheque_details', 'trn_payment_details.id', '=', 'trn_cheque_details.payment_id')
                                     ->whereRaw("trn_payment_details.invoice_id = $subscription->invoice_id AND (trn_cheque_details.`status` = 2 or trn_cheque_details.`status` IS NULL)")
                                     ->sum('trn_payment_details.payment_amount');

        JavaScript::put([
          'gymieToday' => Carbon::today()->format('Y-m-d'),
          'servicesCount' => Plan::count(),
      ]);

        return view('subscriptions.change', compact('subscription', 'already_paid'));
    }

    public function modify($id, Request $request)
    {
        $subscription = Subscription::findOrFail($id);
        
        // Check if user can access this subscription
        if (!$this->canAccessSubscription($subscription)) {
            flash()->error('You do not have permission to modify this subscription');
            return redirect(action('SubscriptionsController@index'));
        }

        try {
            DB::beginTransaction();
            \Log::info('SUB_CHANGE: incoming request', ['id' => $id, 'payload' => $request->all()]);
            //Helper function to set Payment status
            $planTotal = 0;
            if ($request->has('plan') && is_array($request->plan)) {
                foreach ($request->plan as $p) {
                    $planTotal += isset($p['price']) ? (float) $p['price'] : 0;
                }
            }
            $invoice_total = ($request->admission_amount ?: 0)
                            + $planTotal
                            + ($request->additional_fees ?: 0)
                            - ($request->discount_amount ?: 0);
            $paymentStatus = \constPaymentStatus::Unpaid;
            $total_paid = $request->payment_amount + $request->previous_payment;
            $pending = $invoice_total - $total_paid;
            \Log::info('SUB_CHANGE: computed totals', [
                'plan_total' => $planTotal,
                'invoice_total' => $invoice_total,
                'discount_amount' => $request->discount_amount,
                'additional_fees' => $request->additional_fees,
                'total_paid' => $total_paid,
                'pending' => $pending,
                'mode' => $request->mode,
            ]);

            if ($request->mode == 1 || $request->mode == 2) {
                if ($total_paid == $invoice_total) {
                    $paymentStatus = \constPaymentStatus::Paid;
                } elseif ($total_paid > 0 && $total_paid < $invoice_total) {
                    $paymentStatus = \constPaymentStatus::Partial;
                } elseif ($total_paid == 0) {
                    $paymentStatus = \constPaymentStatus::Unpaid;
                } else {
                    $paymentStatus = \constPaymentStatus::Overpaid;
                }
            }

            Invoice::where('id', $subscription->invoice_id)->update(['invoice_number'=> $request->invoice_number,
                                                               'total'=> $invoice_total,
                                                               'status'=> $paymentStatus,
                                                               'pending_amount'=> $pending,
                                                               'discount_amount'=> $request->discount_amount ?: 0,
                                                               'discount_note'=> $request->discount_note,
                                                               'additional_fees'=> ($request->additional_fees ?: 0),
                                                               'note'=>' ', ]);
            \Log::info('SUB_CHANGE: invoice updated', ['invoice_id' => $subscription->invoice_id]);

            foreach ($request->plan as $plan) {
                $subscription->update(['plan_id'=> $plan['id'],
                                        'start_date'=> $plan['start_date'],
                                        'end_date'=> $plan['end_date'],
                                        'status'=> \constSubscription::onGoing,
                                        'is_renewal'=>'0', ]);

                //Adding subscription to invoice(Invoice Details)

                InvoiceDetail::where('invoice_id', $subscription->invoice_id)->update(['plan_id'=> $plan['id'],
                                                                                         'item_amount'=> $plan['price'], ]);
                \Log::info('SUB_CHANGE: invoice details updated', ['invoice_id' => $subscription->invoice_id, 'plan_id' => $plan['id'], 'price' => $plan['price']]);
            }

            //Payment Details
            $paymentData = ['invoice_id'=> $subscription->invoice_id,
                                   'payment_amount'=> $request->payment_amount ?: 0,
                                   'mode'=> isset($request->mode) ? (int)$request->mode : 1,
                                   'note'=> ' ', ];

            $payment_details = new PaymentDetail($paymentData);
            $payment_details->createdBy()->associate(Auth::user());
            $payment_details->updatedBy()->associate(Auth::user());
            $payment_details->save();
            \Log::info('SUB_CHANGE: payment saved', ['payment_id' => $payment_details->id, 'amount' => $payment_details->payment_amount]);

            if ($request->mode == 0) {
                // Store Cheque Details
                $chequeData = ['payment_id'=> $payment_details->id,
                                    'number'=> $request->number,
                                    'date'=> $request->date,
                                    'status'=> \constChequeStatus::Recieved, ];

                $cheque_details = new ChequeDetail($chequeData);
                $cheque_details->createdBy()->associate(Auth::user());
                $cheque_details->updatedBy()->associate(Auth::user());
                $cheque_details->save();
                \Log::info('SUB_CHANGE: cheque saved', ['cheque_id' => $cheque_details->id]);
            }

            DB::commit();
            \Log::info('SUB_CHANGE: success', ['subscription_id' => $id]);
            flash()->success('Subscription was successfully changed');

            return redirect(action('MembersController@show', ['id' => $subscription->member_id]));
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('SUB_CHANGE: error', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            flash()->error('Error while changing the Subscription');

            return back();
        }
    }

    /**
     * @return array
     */
    private function generateInvoiceNumber()
    {
        //Get Numbering mode
        $invoiceNumberMode = \Utilities::getSetting('invoice_number_mode');

        //Generating Invoice number
        if ($invoiceNumberMode == \constNumberingMode::Auto) {
            $invoiceCounter = \Utilities::getSetting('invoice_last_number') + 1;
            $invoiceNumber = \Utilities::getSetting('invoice_prefix').$invoiceCounter;
        } else {
            $invoiceNumber = '';
            $invoiceCounter = '';
        }

        return [$invoiceNumberMode, $invoiceCounter, $invoiceNumber];
    }
}
