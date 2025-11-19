<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Carbon\Carbon;
use App\Member;
use App\Attendance;
use App\Subscription;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display attendance tracking page
     */
    public function index(Request $request)
    {
        // Real-time check for expired cancelled subscriptions
        Subscription::checkAndInactivateExpiredCancelled();

        // Get selected date (default to today, or most recent attendance date if no records today)
        $selectedDate = $request->input('date');
        
        if (!$selectedDate) {
            // Check if there are any records today
            $todayCount = Attendance::today()->count();
            
            if ($todayCount > 0) {
                // Default to today if there are records
                $selectedDate = Carbon::today()->format('Y-m-d');
            } else {
                // Get the most recent attendance date
                $latestAttendance = Attendance::orderBy('check_in_time', 'desc')->first();
                if ($latestAttendance) {
                    $selectedDate = $latestAttendance->check_in_time->format('Y-m-d');
                } else {
                    // Fallback to today if no records exist
                    $selectedDate = Carbon::today()->format('Y-m-d');
                }
            }
        }
        
        // Generate last 30 days list (today at top, going backward)
        $dateOptions = [];
        for ($i = 0; $i < 30; $i++) {
            $date = Carbon::today()->subDays($i);
            $dateOptions[] = [
                'value' => $date->format('Y-m-d'),
                'label' => $date->format('l, d M Y'), // e.g., "Monday, 05 Nov 2025"
                'short' => $date->format('d M Y'), // e.g., "05 Nov 2025"
                'is_today' => $i === 0
            ];
        }

        // Build query
        $query = Attendance::with(['member.subscriptions' => function($q) {
                $q->orderBy('created_at', 'desc');
            }])
            ->forDate($selectedDate)
            ->orderBy('check_in_time', 'desc');

        // Apply search filter
        if ($request->has('search') && !empty($request->input('search'))) {
            $searchTerm = $request->input('search');
            $query->whereHas('member', function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('member_code', 'like', "%{$searchTerm}%");
            });
        }

        // Get attendance records
        $attendances = $query->paginate(20);
        
        // Calculate total today's visits for selected date
        $totalVisits = Attendance::forDate($selectedDate)->count();

        // Calculate today's visits for each member in the results
        foreach ($attendances as $attendance) {
            $attendance->today_visits = Attendance::getTodayVisitsCount(
                $attendance->member_id, 
                $selectedDate
            );
        }

        $request->flash();

        return view('attendance.index', compact(
            'attendances', 
            'totalVisits', 
            'dateOptions', 
            'selectedDate'
        ));
    }

    /**
     * API endpoint: Get new attendance records since last check
     * Used for auto-refresh functionality
     */
    public function getNewRecords(Request $request)
    {
        try {
            $selectedDate = $request->input('date', Carbon::today()->format('Y-m-d'));
            $lastRecordId = $request->input('last_id', 0);
            
            // Get new records since last check
            $newRecords = Attendance::with(['member.subscriptions' => function($q) {
                    $q->orderBy('created_at', 'desc');
                }])
                ->forDate($selectedDate)
                ->where('id', '>', $lastRecordId)
                ->orderBy('check_in_time', 'desc')
                ->get();
            
            // Format records for JSON response
            $formattedRecords = [];
            foreach ($newRecords as $attendance) {
                // Get latest subscription
                $latestSubscription = null;
                if ($attendance->member && $attendance->member->subscriptions) {
                    foreach ($attendance->member->subscriptions->sortByDesc('created_at') as $sub) {
                        if ($sub->status == \constSubscription::onGoing) {
                            $latestSubscription = $sub;
                            break;
                        }
                    }
                    if (!$latestSubscription) {
                        $latestSubscription = $attendance->member->subscriptions->sortByDesc('created_at')->first();
                    }
                }
                
                // Calculate today's visits for this member
                $todayVisits = Attendance::getTodayVisitsCount($attendance->member_id, $selectedDate);
                
                $formattedRecords[] = [
                    'id' => $attendance->id,
                    'member_id' => $attendance->member_id,
                    'member_name' => $attendance->member ? $attendance->member->name : 'Member Deleted',
                    'member_code' => $attendance->member ? $attendance->member->member_code : 'N/A',
                    'check_in_time' => $attendance->check_in_time->format('d-m-Y H:i:s'),
                    'check_in_time_raw' => $attendance->check_in_time->format('Y-m-d H:i:s'),
                    'subscription_status' => $latestSubscription ? \Utilities::getSubscriptionStatus($latestSubscription->status) : 'No Subscription',
                    'subscription_label' => $latestSubscription ? \Utilities::getSubscriptionLabel($latestSubscription->status) : 'label-default',
                    'today_visits' => $todayVisits,
                ];
            }
            
            // Get updated total visits count
            $totalVisits = Attendance::forDate($selectedDate)->count();
            
            return response()->json([
                'success' => true,
                'new_records' => $formattedRecords,
                'total_visits' => $totalVisits,
                'count' => count($formattedRecords)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching new records: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test method: Add a random member's attendance record
     * TEMPORARY - For testing attendance sync script
     */
    public function addTestAttendance(Request $request)
    {
        try {
            // Get a random active member
            $member = Member::where('status', 1)
                ->orderByRaw('RAND()')
                ->first();
            
            if (!$member) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No active members found in the database'
                    ]);
                }
                flash()->error('No active members found in the database');
                return redirect('attendance');
            }
            
            // Create attendance record with current timestamp
            $attendance = Attendance::create([
                'member_id' => $member->id,
                'check_in_time' => Carbon::now(),
            ]);
            
            $message = "Test attendance added: {$member->member_code} - {$member->name} at " . Carbon::now()->format('H:i:s');
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'member_code' => $member->member_code,
                    'member_name' => $member->name,
                    'check_in_time' => Carbon::now()->format('H:i:s')
                ]);
            }
            
            flash()->success($message);
            return redirect('attendance');
            
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error adding test attendance: ' . $e->getMessage()
                ]);
            }
            flash()->error('Error adding test attendance: ' . $e->getMessage());
            return redirect('attendance');
        }
    }
}
