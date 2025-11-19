<?php

namespace App\Http\Controllers;

use Auth;
use App\Member;
use App\SmsLog;
use JavaScript;
use App\Enquiry;
use App\Expense;
use App\Setting;
use App\Followup;
use App\ChequeDetail;
use App\Subscription;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Apply timings filter to subscription query
     * Admin users with 'manage-gymie' permission see all subscriptions
     * Other users only see subscriptions for members with matching timings
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function applyTimingsFilterToSubscriptions($query)
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

    /**
     * Apply timings filter to member query
     * Admin users with 'manage-gymie' permission see all members
     * Other users only see members with matching timings
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function applyTimingsFilterToMembers($query)
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
     * Apply filter to enquiry query
     * Admin users with 'manage-gymie' permission see all enquiries
     * Other users only see enquiries they created
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function applyEnquiryFilter($query)
    {
        $user = Auth::user();
        
        // If user is not authenticated, return empty result
        if (!$user) {
            return $query->whereRaw('1=0');
        }
        
        // Admin users with manage-gymie permission see all enquiries
        if ($user->can('manage-gymie')) {
            return $query;
        }
        
        // Users can only see enquiries they created
        return $query->where('mst_enquiries.created_by', $user->id);
    }

    /**
     * Apply filter to reminder query
     * Admin users with 'manage-gymie' permission see all reminders
     * Other users only see reminders for enquiries they created
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function applyReminderFilter($query)
    {
        $user = Auth::user();
        
        // If user is not authenticated, return empty result
        if (!$user) {
            return $query->whereRaw('1=0');
        }
        
        // Admin users with manage-gymie permission see all reminders
        if ($user->can('manage-gymie')) {
            return $query;
        }
        
        // Users can only see reminders for enquiries they created
        // Followup::reminders() already joins with mst_enquiries, so we can filter by enquiry created_by
        return $query->where('mst_enquiries.created_by', $user->id);
    }

    public function index()
    {
        // Calculate filtered statistics
        $totalMembersQuery = Member::where('status', 1);
        $totalMembersQuery = $this->applyTimingsFilterToMembers($totalMembersQuery);
        $totalMembersCount = $totalMembersQuery->count();
        
        // Monthly joinings (this month)
        $monthlyJoiningsQuery = Member::whereMonth('created_at', '=', \Carbon\Carbon::today()->month)
            ->whereYear('created_at', '=', \Carbon\Carbon::today()->year);
        $monthlyJoiningsQuery = $this->applyTimingsFilterToMembers($monthlyJoiningsQuery);
        $monthlyJoiningsCount = $monthlyJoiningsQuery->count();
        
        // Registration trend (filtered by timings)
        $jsRegistraionsCount = $this->getFilteredRegistrationsTrend();
        
        // Members per plan (filtered by timings)
        $jsMembersPerPlan = $this->getFilteredMembersPerPlan();
        
        JavaScript::put([
            'jsRegistraionsCount' => $jsRegistraionsCount,
            'jsMembersPerPlan' => $jsMembersPerPlan,
        ]);

        // Apply timings filter to expiring subscriptions
        $expiringsQuery = Subscription::dashboardExpiring();
        $expiringsQuery = $this->applyTimingsFilterToSubscriptions($expiringsQuery);
        $expirings = $expiringsQuery->paginate(5);
        $expiringCount = $expirings->total();
        
        // Apply timings filter to expired subscriptions
        $allExpiredQuery = Subscription::dashboardExpired();
        $allExpiredQuery = $this->applyTimingsFilterToSubscriptions($allExpiredQuery);
        $allExpired = $allExpiredQuery->paginate(5);
        $expiredCount = $allExpired->total();
        
        // Apply timings filter to birthdays
        $birthdaysQuery = Member::birthday();
        $birthdaysQuery = $this->applyTimingsFilterToMembers($birthdaysQuery);
        $birthdays = $birthdaysQuery->get();
        $birthdayCount = $birthdays->count();
        
        // Apply timings filter to recent members
        $recentsQuery = Member::recent();
        $recentsQuery = $this->applyTimingsFilterToMembers($recentsQuery);
        $recents = $recentsQuery->get();
        
        // Apply filter to enquiries (users can only see enquiries they created, admin sees all)
        $enquiriesQuery = Enquiry::onlyLeads();
        $enquiriesQuery = $this->applyEnquiryFilter($enquiriesQuery);
        $enquiries = $enquiriesQuery->get();
        
        // Apply filter to reminders (users can only see reminders for enquiries they created, admin sees all)
        $remindersQuery = Followup::reminders();
        $remindersQuery = $this->applyReminderFilter($remindersQuery);
        $reminders = $remindersQuery->get();
        $reminderCount = $reminders->count();
        $dues = Expense::dueAlerts()->get();
        $outstandings = Expense::outstandingAlerts()->get();
        $smsRequestSetting = \Utilities::getSetting('sms_request');
        $smslogs = SmsLog::dashboardLogs()->get();
        $recievedCheques = ChequeDetail::where('status', \constChequeStatus::Recieved)->get();
        $recievedChequesCount = $recievedCheques->count();
        $depositedCheques = ChequeDetail::where('status', \constChequeStatus::Deposited)->get();
        $depositedChequesCount = $depositedCheques->count();
        $bouncedCheques = ChequeDetail::where('status', \constChequeStatus::Bounced)->get();
        $bouncedChequesCount = $bouncedCheques->count();
        $membersPerPlan = json_decode($jsMembersPerPlan);

        return view('dashboard.index', compact('expirings', 'allExpired', 'birthdays', 'recents', 'enquiries', 'reminders', 'dues', 'outstandings', 'smsRequestSetting', 'smslogs', 'expiringCount', 'expiredCount', 'birthdayCount', 'reminderCount', 'recievedCheques', 'recievedChequesCount', 'depositedCheques', 'depositedChequesCount', 'bouncedCheques', 'bouncedChequesCount', 'membersPerPlan', 'totalMembersCount', 'monthlyJoiningsCount'));
    }

    /**
     * Get filtered registration trend based on user timings
     * 
     * @return string JSON encoded data
     */
    private function getFilteredRegistrationsTrend()
    {
        $user = Auth::user();
        $startDate = new \Carbon\Carbon(\App\Setting::where('key', '=', 'financial_start')->pluck('value'));
        $data = [];

        for ($i = 1; $i <= 12; $i++) {
            $membersQuery = Member::whereMonth('created_at', '=', $startDate->month)
                ->whereYear('created_at', '=', $startDate->year);
            
            // Apply timings filter
            $membersQuery = $this->applyTimingsFilterToMembers($membersQuery);
            
            $members = $membersQuery->count();
            $data[] = ['month' => $startDate->format('Y-m'), 'registrations' => $members];
            $startDate->addMonth();
        }

        return json_encode($data);
    }

    /**
     * Get filtered members per plan based on user timings
     * 
     * @return string JSON encoded data
     */
    private function getFilteredMembersPerPlan()
    {
        $user = Auth::user();
        $data = [];

        $plans = \App\Plan::onlyActive()->get();

        foreach ($plans as $plan) {
            $subscriptionsQuery = Subscription::where('status', '=', \constSubscription::onGoing)
                ->where('plan_id', '=', $plan->id);
            
            // Apply timings filter to subscriptions
            $subscriptionsQuery = $this->applyTimingsFilterToSubscriptions($subscriptionsQuery);
            
            $subscriptions = $subscriptionsQuery->count();
            $data[] = ['label' => $plan->plan_name, 'value' => $subscriptions];
        }

        return json_encode($data);
    }

    public function smsRequest(Request $request)
    {
        $contact = 9820461665;
        $sms_text = 'A request for '.$request->smsCount.' sms has came from '.\Utilities::getSetting('gym_name').' by '.Auth::user()->name;
        $sms_status = 1;
        \Utilities::Sms($contact, $sms_text, $sms_status);

        Setting::where('key', '=', 'sms_request')->update(['value' => 1]);

        flash()->success('Request has been successfully sent, a confirmation call will be made soon');

        return redirect('/dashboard');
    }
}
