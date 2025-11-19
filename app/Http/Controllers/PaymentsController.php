<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Invoice;
use App\SmsTrigger;
use App\ChequeDetail;
use App\PaymentDetail;
use Illuminate\Http\Request;

class PaymentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Check if current user can access a payment based on member timings
     * Admin users with 'manage-gymie' permission can access all payments
     * Other users can only access payments for members with matching timings
     * 
     * @param PaymentDetail $paymentDetail
     * @return bool
     */
    private function canAccessPayment($paymentDetail)
    {
        $user = Auth::user();
        
        // If user is not authenticated, deny access
        if (!$user) {
            return false;
        }
        
        // Admin users with manage-gymie permission can access all payments
        if ($user->can('manage-gymie')) {
            return true;
        }
        
        // Load invoice and member relationships if not already loaded
        if (!$paymentDetail->relationLoaded('invoice')) {
            $paymentDetail->load('invoice.member');
        } elseif (!$paymentDetail->invoice->relationLoaded('member')) {
            $paymentDetail->invoice->load('member');
        }
        
        $invoice = $paymentDetail->invoice;
        if (!$invoice) {
            return false;
        }
        
        $member = $invoice->member;
        if (!$member) {
            return false;
        }
        
        // If user has no timings set, they can't access any payments (except admin)
        if (empty($user->timings)) {
            return false;
        }
        
        // If member has no timings set, only admin can access
        if (empty($member->timings)) {
            return false;
        }
        
        // User can only access payments for members with matching timings
        return $user->timings === $member->timings;
    }

    /**
     * Apply timings filter to payment query
     * Admin users with 'manage-gymie' permission see all payments
     * Other users only see payments for members with matching timings
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
        
        // Admin users with manage-gymie permission see all payments
        if ($user->can('manage-gymie')) {
            return $query;
        }
        
        // If user has no timings set, they see no payments (except admin)
        if (empty($user->timings)) {
            return $query->whereRaw('1=0'); // Return empty result
        }
        
        // Filter payments by joining with invoices -> members and matching timings
        // PaymentDetail already joins with invoices and members in indexQuery
        return $query->where('mst_members.timings', $user->timings);
    }

    public function index(Request $request)
    {
        $query = PaymentDetail::indexQuery($request->sort_field, $request->sort_direction, $request->drp_start, $request->drp_end);
        
        // Apply timings filter (users can only see payments for members with their timings, admin sees all)
        $query = $this->applyTimingsFilter($query);
        
        $payment_details = $query->search('"'.$request->input('search').'"')->paginate(10);
        $paymentTotal = PaymentDetail::indexQuery($request->sort_field, $request->sort_direction, $request->drp_start, $request->drp_end);
        $paymentTotal = $this->applyTimingsFilter($paymentTotal);
        $paymentTotal = $paymentTotal->search('"'.$request->input('search').'"')->get();
        $count = $paymentTotal->sum('payment_amount');

        if (! $request->has('drp_start') or ! $request->has('drp_end')) {
            $drp_placeholder = 'Select daterange filter';
        } else {
            $drp_placeholder = $request->drp_start.' - '.$request->drp_end;
        }

        $request->flash();

        return view('payments.index', compact('payment_details', 'count', 'drp_placeholder'));
    }

    public function create()
    {
        return view('payments.create');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // Get invoice to check access
            $invoice = Invoice::findOrFail($request->invoice_id);
            
            // Check if user can access this invoice (same logic as InvoicesController)
            $user = Auth::user();
            if (!$user) {
                DB::rollback();
                flash()->error('You must be logged in to create a payment');
                return redirect(action('PaymentsController@index'));
            }
            
            // Admin users with manage-gymie permission can create payments for any invoice
            if (!$user->can('manage-gymie')) {
                // Load member relationship if not already loaded
                if (!$invoice->relationLoaded('member')) {
                    $invoice->load('member');
                }
                
                $member = $invoice->member;
                // Non-admin users can only create payments for invoices of members with matching timings
                if (!$member || empty($user->timings) || empty($member->timings) || $user->timings !== $member->timings) {
                    DB::rollback();
                    flash()->error('You do not have permission to create a payment for this invoice');
                    return redirect(action('PaymentsController@index'));
                }
            }
            
            // Storing Payment Details
            $payment_detail = new PaymentDetail($request->all());
            $payment_detail->createdBy()->associate(Auth::user());
            $payment_detail->updatedBy()->associate(Auth::user());
            $payment_detail->save();

            // Updating Invoice Status and amounts for both Cash (1) and Online (2)
            $invoice_total = $payment_detail->invoice->total;
            $payment_total = PaymentDetail::where('invoice_id', $payment_detail->invoice_id)->sum('payment_amount');
            $amount_due = $invoice_total - $payment_total;

            $payment_detail->invoice->pending_amount = $amount_due;
            $payment_detail->invoice->status = \Utilities::setInvoiceStatus($amount_due, $invoice_total);
            $payment_detail->invoice->save();
            
            // Sync member credit balance after payment
            if ($payment_detail->invoice->member) {
                $payment_detail->invoice->member->syncCreditBalance();
            }

            //If cheque reissued , set the status of the previous cheque detail to Reissued
            // Cheque reissue flow removed

            // SMS Trigger
            $sender_id = \Utilities::getSetting('sms_sender_id');
            $gym_name = \Utilities::getSetting('gym_name');

            if ($request->mode == \constPaymentMode::Cash || $request->mode == 2) { // 2 = Online
                $sms_trigger = SmsTrigger::where('alias', '=', 'payment_recieved')->first();
                $message = $sms_trigger->message;
                $sms_text = sprintf($message, $payment_detail->invoice->member->name, $payment_detail->payment_amount, $payment_detail->invoice->invoice_number);
                $sms_status = $sms_trigger->status;
                $sender_id = \Utilities::getSetting('sms_sender_id');

                \Utilities::Sms($sender_id, $payment_detail->invoice->member->contact, $sms_text, $sms_status);
            }

            DB::commit();
            flash()->success('Payment Details were successfully stored');

            return redirect(action('InvoicesController@show', ['id' => $payment_detail->invoice_id]));
        } catch (Exception $e) {
            DB::rollback();
            flash()->error('Payment Details weren\'t stored succesfully');

            return redirect('payments/all');
        }
    }

    public function edit($id)
    {
        $payment_detail = PaymentDetail::findOrFail($id);
        
        // Check if user can access this payment
        if (!$this->canAccessPayment($payment_detail)) {
            flash()->error('You do not have permission to edit this payment');
            return redirect(action('PaymentsController@index'));
        }
        
        $cheque_detail = ChequeDetail::where('payment_id', $id)->first();

        return view('payments.edit', compact('payment_detail', 'cheque_detail'));
    }

    public function update($id, Request $request)
    {
        DB::beginTransaction();

        try {
            // Storing Payment Details
            $payment_detail = PaymentDetail::findOrFail($id);
            
            // Check if user can access this payment
            if (!$this->canAccessPayment($payment_detail)) {
                DB::rollback();
                flash()->error('You do not have permission to update this payment');
                return redirect(action('PaymentsController@index'));
            }
            
            $payment_detail->update($request->all());
            $payment_detail->updatedBy()->associate(Auth::user());
            $payment_detail->save();

            if ($request->mode == \constPaymentMode::Cash || $request->mode == 0) {
                // Updating Invoice Status and amounts
                $invoice_total = $payment_detail->invoice->total;
                $payment_total = PaymentDetail::where('invoice_id', $payment_detail->invoice_id)->sum('payment_amount');
                $amount_due = $invoice_total - $payment_total;

                $payment_detail->invoice->pending_amount = $amount_due;
                $payment_detail->invoice->status = \Utilities::setInvoiceStatus($amount_due, $invoice_total);
                $payment_detail->invoice->updatedBy()->associate(Auth::user());
                $payment_detail->invoice->save();
            }

            DB::commit();
            flash()->success('Payment Details were successfully updated');

            return redirect(action('InvoicesController@show', ['id' => $payment_detail->invoice_id]));
        } catch (Exception $e) {
            DB::rollback();
            flash()->error('Payment Details weren\'t updated succesfully');

            return redirect('payments');
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();

        try {
            $payment_detail = PaymentDetail::findOrFail($id);
            
            // Check if user can access this payment
            if (!$this->canAccessPayment($payment_detail)) {
                DB::rollback();
                flash()->error('You do not have permission to delete this payment');
                return back();
            }
            
            $invoice = Invoice::where('id', $payment_detail->invoice_id)->first();
            $cheque_details = ChequeDetail::where('payment_id', $payment_detail->id)->get();

            foreach ($cheque_details as $cheque_detail) {
                $cheque_detail->delete();
            }

            $payment_detail->delete();

            $invoice_total = $invoice->total;
            $payment_total = PaymentDetail::leftJoin('trn_cheque_details', 'trn_payment_details.id', '=', 'trn_cheque_details.payment_id')
                                           ->whereRaw("trn_payment_details.invoice_id = $invoice->id AND (trn_cheque_details.`status` = 2 or trn_cheque_details.`status` IS NULL)")
                                           ->sum('trn_payment_details.payment_amount');

            $amount_due = $invoice_total - $payment_total;

            $invoice->pending_amount = $amount_due;
            $invoice->status = \Utilities::setInvoiceStatus($amount_due, $invoice_total);
            $invoice->updatedBy()->associate(Auth::user());
            $invoice->save();

            DB::commit();

            return redirect('payments/all');
        } catch (\Exception $e) {
            DB::rollback();

            return redirect('payments/all');
        }
    }

    public function depositCheque($id)
    {
        $payment_detail = PaymentDetail::findOrFail($id);
        
        // Check if user can access this payment
        if (!$this->canAccessPayment($payment_detail)) {
            flash()->error('You do not have permission to deposit this cheque');
            return back();
        }
        
        ChequeDetail::where('payment_id', $id)->update(['status' => \constChequeStatus::Deposited]);

        flash()->success('Cheque has been marked as deposited');

        return back();
    }

    public function clearCheque($id)
    {
        DB::beginTransaction();
        try {
            $payment_detail = PaymentDetail::findOrFail($id);
            
            // Check if user can access this payment
            if (!$this->canAccessPayment($payment_detail)) {
                DB::rollback();
                flash()->error('You do not have permission to clear this cheque');
                return back();
            }

            // Updating cheque status
            $cheque_detail = ChequeDetail::where('payment_id', $id)->first();
            $cheque_detail->status = \constChequeStatus::Cleared;
            $cheque_detail->updatedBy()->associate(Auth::user());
            $cheque_detail->save();

            // Updating Invoice Status and amounts
            $invoice_total = $payment_detail->invoice->total;

            $payment_total = PaymentDetail::leftJoin('trn_cheque_details', 'trn_payment_details.id', '=', 'trn_cheque_details.payment_id')
                                           ->whereRaw("trn_payment_details.invoice_id = $payment_detail->invoice_id AND (trn_cheque_details.`status` = 2 or trn_cheque_details.`status` IS NULL)")
                                           ->sum('trn_payment_details.payment_amount');

            $amount_due = $invoice_total - $payment_total;

            $payment_detail->invoice->pending_amount = $amount_due;
            $payment_detail->invoice->status = \Utilities::setInvoiceStatus($amount_due, $invoice_total);
            $payment_detail->invoice->save();

            DB::commit();
            flash()->success('Cheque has been marked as cleared');

            return back();
        } catch (Exception $e) {
            DB::rollback();
            flash()->error('Error while marking the cheque as cleared');

            return back();
        }
    }

    public function chequeBounce($id)
    {
        $payment_detail = PaymentDetail::findOrFail($id);
        
        // Check if user can access this payment
        if (!$this->canAccessPayment($payment_detail)) {
            flash()->error('You do not have permission to mark this cheque as bounced');
            return back();
        }
        
        ChequeDetail::where('payment_id', $id)->update(['status' => \constChequeStatus::Bounced]);

        flash()->success('Cheque has been marked as bounced');

        return back();
    }

    public function chequeReissue($id)
    {
        $payment_detail = PaymentDetail::findOrFail($id);
        
        // Check if user can access this payment
        if (!$this->canAccessPayment($payment_detail)) {
            flash()->error('You do not have permission to reissue this cheque');
            return redirect(action('PaymentsController@index'));
        }

        return view('payments.reissue', compact('payment_detail'));
    }
}
