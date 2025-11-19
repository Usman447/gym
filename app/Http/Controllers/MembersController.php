<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Member;
use JavaScript;
use App\Enquiry;
use App\Invoice;
use App\Plan;
use App\Setting;
use Carbon\Carbon;
use App\SmsTrigger;
use App\ChequeDetail;
use App\Subscription;
use App\InvoiceDetail;
use App\PaymentDetail;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class MembersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Check if current user can access a member based on timings
     * Admin users with 'manage-gymie' permission can access all members
     * Other users can only access members with matching timings
     * 
     * @param Member $member
     * @return bool
     */
    private function canAccessMember($member)
    {
        $user = Auth::user();
        
        // If user is not authenticated, deny access
        if (!$user) {
            return false;
        }
        
        // Admin users with manage-gymie permission can access all members
        if ($user->can('manage-gymie')) {
            return true;
        }
        
        // If user has no timings set, they can't access any members (except admin)
        if (empty($user->timings)) {
            return false;
        }
        
        // If member has no timings set, only admin can access
        if (empty($member->timings)) {
            return false;
        }
        
        // User can only access members with matching timings
        return $user->timings === $member->timings;
    }

    /**
     * Apply timings filter to member query
     * Admin users with 'manage-gymie' permission see all members
     * Other users only see members with matching timings
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
        
        // Admin users with manage-gymie permission see all members
        if ($user->can('manage-gymie')) {
            return $query;
        }
        
        // If user has no timings set, they see no members (except admin)
        if (empty($user->timings)) {
            return $query->whereRaw('1=0'); // Return empty result
        }
        
        // Filter members by matching timings
        return $query->where('mst_members.timings', $user->timings);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        // Real-time check for expired cancelled subscriptions
        Subscription::checkAndInactivateExpiredCancelled();
        
        $query = Member::indexQuery($request->sort_field, $request->sort_direction, $request->drp_start, $request->drp_end)
            ->with(['subscriptions.plan']); // Eager load subscriptions and their plans
        
        // Apply timings filter (users can only see members with their timings, admin sees all)
        $query = $this->applyTimingsFilter($query);
        
        // Apply search
        $query->search('"'.$request->input('search').'"');
        
        // Apply status filter if provided and sort_field is 'status'
        $statusFilter = null;
        $needsRuntimeFilter = false;
        
        if ($request->has('status_filter') && $request->status_filter !== '' && $request->sort_field === 'status') {
            $statusFilter = $request->status_filter;
            
            // For runtime-calculated statuses, we need to filter in memory
            if (in_array($statusFilter, ['8', '9', 'none'])) {
                $needsRuntimeFilter = true;
            }
            // For Cancelled, we also need runtime filtering to match display logic
            // (shows last subscription if no ongoing, so cancelled might be the last one)
            elseif ($statusFilter == '3') {
                $needsRuntimeFilter = true;
            }
            // For OnGoing, we can filter in query (member must have at least one ongoing subscription)
            elseif ($statusFilter == '1') {
                $query->whereHas('subscriptions', function($q) {
                    $q->where('status', \constSubscription::onGoing);
                });
            }
        }
        
        // For runtime-calculated statuses or Cancelled, filter in memory
        if ($needsRuntimeFilter && $statusFilter) {
            // Get all members matching the base query
            $allMembers = $query->get();
            
            // Filter by calculated status
            $filteredMembers = $allMembers->filter(function($member) use ($statusFilter) {
                $calculatedStatus = $this->getMemberSubscriptionStatus($member);
                return $calculatedStatus == $statusFilter;
            });
            
            // Manual pagination
            $perPage = 10;
            $currentPage = Paginator::resolveCurrentPage();
            $total = $filteredMembers->count();
            $items = $filteredMembers->slice(($currentPage - 1) * $perPage, $perPage)->values();
            
            $members = new LengthAwarePaginator($items, $total, $perPage, $currentPage, [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'page',
            ]);
            $members->appends($request->only(['search', 'sort_field', 'sort_direction', 'status_filter', 'drp_start', 'drp_end']));
        } else {
            // For OnGoing or no filter, use normal pagination
            $members = $query->paginate(10);
            $members->appends($request->only(['search', 'sort_field', 'sort_direction', 'status_filter', 'drp_start', 'drp_end']));
        }
        
        $count = $members->total();

        $drp_placeholder = $this->drpPlaceholder($request);

        $request->flash();

        return view('members.index', compact('members', 'count', 'drp_placeholder'));
    }
    
    /**
     * Get the calculated subscription status for a member
     * Returns: '1' (OnGoing), '3' (Cancelled), '8' (Expiring), '9' (Pending), or 'none' (No Active Plan)
     */
    private function getMemberSubscriptionStatus($member)
    {
        $subscriptions = $member->subscriptions;
        
        if (!$subscriptions || $subscriptions->count() == 0) {
            return 'none';
        }
        
        // Filter for ongoing subscriptions
        $ongoingSubscriptions = $subscriptions->filter(function($subscription) {
            return $subscription->status == \constSubscription::onGoing;
        });
        
        $sub = null;
        if ($ongoingSubscriptions->count() > 0) {
            // Use the first ongoing subscription
            $sub = $ongoingSubscriptions->first();
        } else {
            // Use the last subscription
            $sub = $subscriptions->sortByDesc('created_at')->first();
        }
        
        if (!$sub || !isset($sub->status)) {
            return 'none';
        }
        
        $status = $sub->status;
        
        // Check for runtime-calculated statuses (Expiring, Pending)
        if ($status == \constSubscription::onGoing) {
            $endingDate = Carbon::parse($sub->end_date);
            $today = Carbon::today();
            
            // "Pending": after end date, still OnGoing
            if ($endingDate->lt($today)) {
                return '9'; // pending
            }
            // "Expiring": end date within next 6 days including today (and not past)
            elseif ($endingDate->gte($today) && $endingDate->diffInDays($today) <= 3) {
                return '8'; // expiring
            }
        }
        
        // Return database status (OnGoing=1, Cancelled=3, etc.)
        return (string)$status;
    }

    public function active(Request $request)
    {
        $query = Member::active($request->sort_field, $request->sort_direction, $request->drp_start, $request->drp_end);
        
        // Apply timings filter (users can only see members with their timings, admin sees all)
        $query = $this->applyTimingsFilter($query);
        
        $members = $query->search('"'.$request->input('search').'"')->paginate(10);
        $count = $members->total();

        $drp_placeholder = $this->drpPlaceholder($request);

        $request->flash();

        return view('members.active', compact('members', 'count', 'drp_placeholder'));
    }

    public function inactive(Request $request)
    {
        $query = Member::inactive($request->sort_field, $request->sort_direction, $request->drp_start, $request->drp_end);
        
        // Apply timings filter (users can only see members with their timings, admin sees all)
        $query = $this->applyTimingsFilter($query);
        
        $members = $query->search('"'.$request->input('search').'"')->paginate(10);
        $count = $members->total();

        $drp_placeholder = $this->drpPlaceholder($request);

        $request->flash();

        return view('members.inactive', compact('members', 'count', 'drp_placeholder'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        // Real-time check for expired cancelled subscriptions
        Subscription::checkAndInactivateExpiredCancelled();
        
        // Re-fetch member to get latest status after potential inactivation
        // Eager load subscriptions, invoices, and payment details to avoid N+1 queries
        $member = Member::with([
            'subscriptions.invoice.paymentDetails',
            'subscriptions.plan'
        ])->find($id);
        
        if (! $member) {
            flash()->error('Member not found');
            return redirect(action('MembersController@index'));
        }
        
        // Check if user can access this member
        if (!$this->canAccessMember($member)) {
            flash()->error('You do not have permission to view this member');
            return redirect(action('MembersController@index'));
        }

        // Note: Biometric device and fingerprint status are now read directly from database
        // These values are populated by background scripts that sync with the device

        return view('members.show', compact('member'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        JavaScript::put([
            'gymieToday' => Carbon::today()->format('Y-m-d'),
            'servicesCount' => Plan::count(),
        ]);

        //Get Numbering mode
        $invoice_number_mode = \Utilities::getSetting('invoice_number_mode');
        $member_number_mode = \Utilities::getSetting('member_number_mode');

        //Generating Invoice number
        if ($invoice_number_mode == \constNumberingMode::Auto) {
            $invoiceCounter = \Utilities::getSetting('invoice_last_number') + 1;
            $invoicePrefix = \Utilities::getSetting('invoice_prefix');
            $invoice_number = $invoicePrefix.$invoiceCounter;
        } else {
            $invoice_number = '';
            $invoiceCounter = '';
        }

        //Generating Member Counter
        if ($member_number_mode == \constNumberingMode::Auto) {
            $memberCounter = \Utilities::getSetting('member_last_number') + 1;
            $memberPrefix = \Utilities::getSetting('member_prefix');
            $member_code = $memberPrefix.$memberCounter;
        } else {
            $member_code = '';
            $memberCounter = '';
        }

        return view('members.create', compact('invoice_number', 'invoiceCounter', 'member_code', 'memberCounter', 'member_number_mode', 'invoice_number_mode'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        // Member Model Validation
        $this->validate($request, [
                                   'contact' => 'unique:mst_members,contact',
                                   'member_code' => 'unique:mst_members,member_code', ]);

        // Start Transaction
        DB::beginTransaction();

        try {
            // Store member's personal details
            // Users can create members with any timings, but can only see members with their own timings
            $memberData = [
                                    'name' => $request->name,
                                    'age' => $request->age,
                                    'gender' => $request->gender,
                                    'contact' => $request->contact,
                                    'timings' => $request->timings,
                                    'health_issues' => $request->health_issues ?? 'NA',
                                    'address' => $request->address,
                                    'member_id' => $request->member_id,
                                    'member_code' => $request->member_code,
                                    'status' => $request->has('status') ? $request->status : \constStatus::Active,
                                    'height_ft' => $request->height_ft ?? 0,
                                    'height_in' => $request->height_in ?? 0,
                                    'weight_kg' => $request->weight_kg ?? 0,
                                    'opf_residence' => $request->opf_residence ? 1 : 0,
                                ];

            // Get current user info for tracking
            $currentUser = Auth::user();
            
            $member = new Member($memberData);
            $member->createdBy()->associate($currentUser);
            $member->updatedBy()->associate($currentUser);
            
            // Store user name and email for tracking (visible only to admin)
            $member->created_by_user_name = $currentUser->name;
            $member->created_by_user_email = $currentUser->email;
            $member->updated_by_user_name = $currentUser->name;
            $member->updated_by_user_email = $currentUser->email;
            
            $member->save();

            // No media uploads for member on create as per new requirements

            // If plans were submitted, proceed with invoice/subscription/payment. Otherwise, skip.
            if ($request->has('plan') && is_array($request->plan) && count($request->plan) > 0) {
                // Helper function for calculating payment status
                $invoice_total = ($request->admission_amount ?: 0)
                                 + ($request->subscription_amount ?: 0)
                                 + ($request->additional_fees ?: 0)
                                 - ($request->discount_amount ?: 0);
                $paymentStatus = \constPaymentStatus::Unpaid;
                $pending = $invoice_total - ($request->payment_amount ?: 0);

                // Compute payment status for Cash (1) and Online (2)
                if ((int) $request->mode === 1 || (int) $request->mode === 2) {
                    if ((float) $request->payment_amount === (float) $invoice_total) {
                        $paymentStatus = \constPaymentStatus::Paid;
                    } elseif (($request->payment_amount ?: 0) > 0 && (float) $request->payment_amount < (float) $invoice_total) {
                        $paymentStatus = \constPaymentStatus::Partial;
                    } elseif (($request->payment_amount ?: 0) == 0) {
                        $paymentStatus = \constPaymentStatus::Unpaid;
                    } else {
                        $paymentStatus = \constPaymentStatus::Overpaid;
                    }
                }

                // Storing Invoice
                $invoiceData = ['invoice_number'=> $request->invoice_number,
                                         'member_id'=> $member->id,
                                         'total'=> $invoice_total,
                                         'status'=> $paymentStatus,
                                         'pending_amount'=> $pending,
                                         'discount_amount'=> $request->discount_amount ?: 0,
                                         'discount_note'=> $request->discount_note ?: '',
                                         'additional_fees'=> $request->additional_fees ?: 0,
                                         'note'=>' ', ];

                $invoice = new Invoice($invoiceData);
                $invoice->createdBy()->associate(Auth::user());
                $invoice->updatedBy()->associate(Auth::user());
                $invoice->save();

                // Storing subscription
                foreach ($request->plan as $plan) {
                    $subscriptionData = ['member_id'=> $member->id,
                                                'invoice_id'=> $invoice->id,
                                                'plan_id'=> $plan['id'],
                                                'start_date'=> $plan['start_date'],
                                                'end_date'=> $plan['end_date'],
                                                'status'=> \constSubscription::onGoing,
                                                'is_renewal'=>'0', ];

                    $subscription = new Subscription($subscriptionData);
                    $subscription->createdBy()->associate(Auth::user());
                    $subscription->updatedBy()->associate(Auth::user());
                    $subscription->save();

                    //Adding subscription to invoice(Invoice Details)
                    $detailsData = ['invoice_id'=> $invoice->id,
                                           'plan_id'=> $plan['id'],
                                           'item_amount'=> $plan['price'], ];

                    $invoiceDetails = new InvoiceDetail($detailsData);
                    $invoiceDetails->createdBy()->associate(Auth::user());
                    $invoiceDetails->updatedBy()->associate(Auth::user());
                    $invoiceDetails->save();
                }

                // Store Payment Details
                $paymentData = ['invoice_id'=> $invoice->id,
                                         'payment_amount'=> $request->payment_amount ?: 0,
                                         'mode'=> isset($request->mode) ? (int)$request->mode : 1,
                                         'note'=> ' ', ];

                $paymentDetails = new PaymentDetail($paymentData);
                $paymentDetails->createdBy()->associate(Auth::user());
                $paymentDetails->updatedBy()->associate(Auth::user());
                $paymentDetails->save();

                // No cheque flow; mode 0 is Online
            }

            // On member transfer update enquiry Status
            if ($request->has('transfer_id')) {
                $enquiry = Enquiry::findOrFail($request->transfer_id);
                $enquiry->status = \constEnquiryStatus::Member;
                $enquiry->updatedBy()->associate(Auth::user());
                $enquiry->save();
            }

            //Updating Numbering Counters
            Setting::where('key', '=', 'invoice_last_number')->update(['value' => $request->invoiceCounter]);
            Setting::where('key', '=', 'member_last_number')->update(['value' => $request->memberCounter]);
            $sender_id = \Utilities::getSetting('sms_sender_id');
            $gym_name = \Utilities::getSetting('gym_name');

            if (isset($invoice)) {
                //SMS Trigger
                if ($invoice->status == \constPaymentStatus::Paid) {
                    $sms_trigger = SmsTrigger::where('alias', '=', 'member_admission_with_paid_invoice')->first();
                    $message = $sms_trigger->message;
                    $sms_text = sprintf($message, $member->name, $gym_name, $paymentDetails->payment_amount, $invoice->invoice_number);
                    $sms_status = $sms_trigger->status;

                    \Utilities::Sms($sender_id, $member->contact, $sms_text, $sms_status);
                } elseif ($invoice->status == \constPaymentStatus::Partial) {
                    $sms_trigger = SmsTrigger::where('alias', '=', 'member_admission_with_partial_invoice')->first();
                    $message = $sms_trigger->message;
                    $sms_text = sprintf($message, $member->name, $gym_name, $paymentDetails->payment_amount, $invoice->invoice_number, $invoice->pending_amount);
                    $sms_status = $sms_trigger->status;

                    \Utilities::Sms($sender_id, $member->contact, $sms_text, $sms_status);
                } elseif ($invoice->status == \constPaymentStatus::Unpaid) {
                    // For Online or Cash unpaid, use generic unpaid template (no cheque details)
                    $sms_trigger = SmsTrigger::where('alias', '=', 'member_admission_with_unpaid_invoice')->first();
                    $message = $sms_trigger->message;
                    $sms_text = sprintf($message, $member->name, $gym_name, $invoice->pending_amount, $invoice->invoice_number);
                    $sms_status = $sms_trigger->status;

                    \Utilities::Sms($sender_id, $member->contact, $sms_text, $sms_status);
                }

                if (isset($subscription) && $subscription->start_date < $member->created_at) {
                    $member->created_at = $subscription->start_date;
                    $member->updated_at = $subscription->start_date;
                    $member->save();

                    $invoice->created_at = $subscription->start_date;
                    $invoice->updated_at = $subscription->start_date;
                    $invoice->save();

                    foreach ($invoice->invoiceDetails as $invoiceDetail) {
                        $invoiceDetail->created_at = $subscription->start_date;
                        $invoiceDetail->updated_at = $subscription->start_date;
                        $invoiceDetail->save();
                    }

                    $paymentDetails->created_at = $subscription->start_date;
                    $paymentDetails->updated_at = $subscription->start_date;
                    $paymentDetails->save();

                    $subscription->created_at = $subscription->start_date;
                    $subscription->updated_at = $subscription->start_date;
                    $subscription->save();
                }
            }

            DB::commit();
            
            flash()->success('Member was successfully created');

            return redirect(action('MembersController@show', ['id' => $member->id]));
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Member create failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            flash()->error('Error while creating the member');

            return redirect(action('MembersController@index'));
        }
    }

    //End of new Member

    // End of store method

    /**
     * Edit a created resource in storage.
     *
     * @return Response
     */
    public function edit($id)
    {
        $member = Member::findOrFail($id);
        
        // Check if user can access this member
        if (!$this->canAccessMember($member)) {
            flash()->error('You do not have permission to edit this member');
            return redirect(action('MembersController@index'));
        }
        
        $member_number_mode = \Utilities::getSetting('member_number_mode');
        $member_code = $member->member_code;

        return view('members.edit', compact('member', 'member_number_mode', 'member_code'));
    }

    /**
     * Update an edited resource in storage.
     *
     * @return Response
     */
    public function update($id, Request $request)
    {
        $member = Member::findOrFail($id);
        
        // Check if user can access this member
        if (!$this->canAccessMember($member)) {
            flash()->error('You do not have permission to update this member');
            return redirect(action('MembersController@index'));
        }
        
        // Get old status before update
        $oldStatus = $member->status;
        $newStatus = $request->has('status') ? $request->status : $oldStatus;
        
        // Determine member timings based on user permissions
        $user = Auth::user();
        $updateData = $request->all();
        
        // If user is not admin, they cannot change member timings
        if (!$user->can('manage-gymie')) {
            // Keep the existing member timings (cannot be changed by non-admin)
            $updateData['timings'] = $member->timings;
        }
        
        // Protect created_by fields - they should never be changed after creation
        unset($updateData['created_by_user_name']);
        unset($updateData['created_by_user_email']);
        
        $member->update($updateData);
        // No media updates under new requirements

        // Get current user info for tracking
        $currentUser = Auth::user();
        $member->updatedBy()->associate($currentUser);
        
        // Store user name and email for tracking (visible only to admin)
        $member->updated_by_user_name = $currentUser->name;
        $member->updated_by_user_email = $currentUser->email;
        
        $member->save();


        flash()->success('Member details were successfully updated');

        return redirect(action('MembersController@show', ['id' => $member->id]));
    }

    /**
     * Archive a resource in storage.
     *
     * @return Response
     */
    public function archive($id, Request $request)
    {
        $member = Member::findOrFail($id);
        
        // Check if user can access this member
        if (!$this->canAccessMember($member)) {
            flash()->error('You do not have permission to delete this member');
            return redirect(action('MembersController@index'));
        }
        
        Subscription::where('member_id', $id)->delete();

        $invoices = Invoice::where('member_id', $id)->get();

        foreach ($invoices as $invoice) {
            InvoiceDetail::where('invoice_id', $invoice->id)->delete();
            $paymentDetails = PaymentDetail::where('invoice_id', $invoice->id)->get();

            foreach ($paymentDetails as $paymentDetail) {
                ChequeDetail::where('payment_id', $paymentDetail->id)->delete();
                $paymentDetail->delete();
            }

            $invoice->delete();
        }

        
        $member->clearMediaCollection('profile');
        $member->clearMediaCollection('proof');

        $member->delete();

        return back();
    }

    public function transfer($id, Request $request)
    {
        JavaScript::put([
            'gymieToday' => Carbon::today()->format('Y-m-d'),
            'servicesCount' => Plan::count(),
        ]);

        //Get Numbering mode
        $invoice_number_mode = \Utilities::getSetting('invoice_number_mode');
        $member_number_mode = \Utilities::getSetting('member_number_mode');

        //Generating Invoice number
        if ($invoice_number_mode == \constNumberingMode::Auto) {
            $invoiceCounter = \Utilities::getSetting('invoice_last_number') + 1;
            $invoicePrefix = \Utilities::getSetting('invoice_prefix');
            $invoice_number = $invoicePrefix.$invoiceCounter;
        } else {
            $invoice_number = '';
            $invoiceCounter = '';
        }

        //Generating Member Counter
        if ($member_number_mode == \constNumberingMode::Auto) {
            $memberCounter = \Utilities::getSetting('member_last_number') + 1;
            $memberPrefix = \Utilities::getSetting('member_prefix');
            $member_code = $memberPrefix.$memberCounter;
        } else {
            $member_code = '';
            $memberCounter = '';
        }

        $enquiry = Enquiry::findOrFail($id);

        return view('members.transfer', compact('enquiry', 'invoice_number', 'invoiceCounter', 'member_code', 'memberCounter', 'member_number_mode', 'invoice_number_mode'));
    }


    /**
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    private function drpPlaceholder(Request $request)
    {
        if ($request->has('drp_start') and $request->has('drp_end')) {
            return $request->drp_start.' - '.$request->drp_end;
        }

        return 'Select daterange filter';
    }
}
