<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Member;
use App\Enquiry;
use App\Followup;
use App\SmsTrigger;
use Illuminate\Http\Request;

class EnquiriesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Check if current user can access an enquiry
     * Admin users with 'manage-gymie' permission can access all enquiries
     * Other users can only access enquiries they created
     * 
     * @param Enquiry $enquiry
     * @return bool
     */
    private function canAccessEnquiry($enquiry)
    {
        $user = Auth::user();
        
        // If user is not authenticated, deny access
        if (!$user) {
            return false;
        }
        
        // Admin users with manage-gymie permission can access all enquiries
        if ($user->can('manage-gymie')) {
            return true;
        }
        
        // Users can only access enquiries they created
        return $enquiry->created_by == $user->id;
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

    public function index(Request $request)
    {
        $query = Enquiry::with(['Followups' => function($query) {
                $query->where('status', \constFollowUpStatus::Pending)
                      ->orderBy('due_date', 'asc');
            }])
            ->indexQuery($request->sort_field, $request->sort_direction, $request->drp_start, $request->drp_end);
        
        // Apply filter (users can only see enquiries they created, admin sees all)
        $query = $this->applyEnquiryFilter($query);
        
        $enquiries = $query->search('"'.$request->input('search').'"')->paginate(10);
        $count = $enquiries->total();

        if (! $request->has('drp_start') or ! $request->has('drp_end')) {
            $drp_placeholder = 'Select daterange filter';
        } else {
            $drp_placeholder = $request->drp_start.' - '.$request->drp_end;
        }

        $request->flash();

        return view('enquiries.index', compact('enquiries', 'count', 'drp_placeholder'));
    }

    public function show($id)
    {
        $enquiry = Enquiry::findOrFail($id);
        
        // Check if user can access this enquiry
        if (!$this->canAccessEnquiry($enquiry)) {
            flash()->error('You do not have permission to view this enquiry');
            return redirect(action('EnquiriesController@index'));
        }

        $followups = $enquiry->followups->sortByDesc('updated_at');

        return view('enquiries.show', compact('enquiry', 'followups'));
    }

    public function create()
    {
        return view('enquiries.create');
    }

    public function store(Request $request)
    {
        // unique values check
        $this->validate($request, ['email' => 'nullable|unique:mst_enquiries,email',
                                'contact' => 'unique:mst_enquiries,contact', ]);

        // Start Transaction
        DB::beginTransaction();

        try {
            // store enquiries details
            $enquiryData = ['name'=>$request->name,
                                    'DOB'=> $request->DOB ?: '1900-01-01', // Default value if not provided
                                    'age'=> $request->age,
                                    'gender'=> $request->gender,
                                    'contact'=> $request->contact,
                                    'email'=> $request->email ?: '', // Default empty value if not provided
                                    'address'=> $request->address,
                                    'opf_residence'=> $request->opf_residence ? 1 : 0,
                                    'status'=> \constEnquiryStatus::Lead,
                                    'pin_code'=> $request->pin_code ?: 0, // Default value if not provided
                                    'occupation'=> $request->occupation,
                                    'start_by'=> $request->start_by,
                                    'interested_in'=> implode(',', $request->interested_in),
                                    'aim'=> $request->aim ?: '0', // Default value if not provided
                                    'source'=> $request->source ?: '0', ]; // Default value if not provided

            $currentUser = Auth::user();
            $enquiry = new Enquiry($enquiryData);
            $enquiry->createdBy()->associate($currentUser);
            $enquiry->updatedBy()->associate($currentUser);
            
            // Store user name and email for tracking (visible only to admin)
            $enquiry->created_by_user_name = $currentUser->name;
            $enquiry->created_by_user_email = $currentUser->email;
            $enquiry->updated_by_user_name = $currentUser->name;
            $enquiry->updated_by_user_email = $currentUser->email;
            
            $enquiry->save();

            //Store the followup details
            $followupData = ['enquiry_id'=>$enquiry->id,
                                     'followup_by'=>$request->followup_by,
                                     'due_date'=>$request->due_date,
                                     'status'=> \constFollowUpStatus::Pending,
                                     'outcome'=>'', ];

            $followup = new Followup($followupData);
            $followup->createdBy()->associate(Auth::user());
            $followup->updatedBy()->associate(Auth::user());
            $followup->save();

            // SMS Trigger
            $gym_name = \Utilities::getSetting('gym_name');
            $sender_id = \Utilities::getSetting('sms_sender_id');

            $sms_trigger = SmsTrigger::where('alias', '=', 'enquiry_placement')->first();
            $message = $sms_trigger->message;
            $sms_text = sprintf($message, $enquiry->name, $gym_name);
            $sms_status = $sms_trigger->status;

            \Utilities::Sms($sender_id, $enquiry->contact, $sms_text, $sms_status);

            DB::commit();
            flash()->success('Enquiry was successfully created');

            return redirect(action('EnquiriesController@show', ['id' => $enquiry->id]));
        } catch (\Exception $e) {
            DB::rollback();
            flash()->error('Error while creating the Enquiry');

            return redirect(action('EnquiriesController@index'));
        }
    }

    //End of store method

    public function edit($id)
    {
        $enquiry = Enquiry::findOrFail($id);
        
        // Check if user can access this enquiry
        if (!$this->canAccessEnquiry($enquiry)) {
            flash()->error('You do not have permission to edit this enquiry');
            return redirect(action('EnquiriesController@index'));
        }

        return view('enquiries.edit', compact('enquiry'));
    }

    public function update($id, Request $request)
    {
        $enquiry = Enquiry::findOrFail($id);
        
        // Check if user can access this enquiry
        if (!$this->canAccessEnquiry($enquiry)) {
            flash()->error('You do not have permission to update this enquiry');
            return redirect(action('EnquiriesController@index'));
        }

        $enquiry->name = $request->name;
        $enquiry->DOB = $request->DOB ?: '1900-01-01'; // Default value if not provided
        $enquiry->age = $request->age;
        $enquiry->gender = $request->gender;
        $enquiry->contact = $request->contact;
        $enquiry->email = $request->email ?: ''; // Default empty value if not provided
        $enquiry->address = $request->address;
        $enquiry->opf_residence = $request->opf_residence ? 1 : 0;
        $enquiry->pin_code = $request->pin_code ?: 0; // Default value if not provided
        $enquiry->occupation = $request->occupation;
        $enquiry->start_by = $request->start_by;
        $enquiry->interested_in = implode(',', $request->interested_in);
        $enquiry->aim = $request->aim ?: '0'; // Default value if not provided
        $enquiry->source = $request->source ?: '0'; // Default value if not provided
        
        // Protect created_by fields - they should never be changed after creation
        // Don't update createdBy() - it should remain as the original creator
        
        $currentUser = Auth::user();
        $enquiry->updatedBy()->associate($currentUser);
        
        // Store user name and email for tracking (visible only to admin)
        $enquiry->updated_by_user_name = $currentUser->name;
        $enquiry->updated_by_user_email = $currentUser->email;
        
        $enquiry->update();

        flash()->success('Enquiry details were successfully updated');

        return redirect(action('EnquiriesController@show', ['id' => $enquiry->id]));
    }

    public function lost($id)
    {
        $enquiry = Enquiry::findOrFail($id);
        
        // Check if user can access this enquiry
        if (!$this->canAccessEnquiry($enquiry)) {
            flash()->error('You do not have permission to mark this enquiry as lost');
            return redirect(action('EnquiriesController@index'));
        }

        $currentUser = Auth::user();
        $enquiry->status = \constEnquiryStatus::Lost;
        $enquiry->updatedBy()->associate($currentUser);
        $enquiry->updated_by_user_name = $currentUser->name;
        $enquiry->updated_by_user_email = $currentUser->email;
        $enquiry->update();

        flash()->success('Enquiry was marked as lost');

        return redirect('enquiries/all');
    }

    public function markMember($id)
    {
        $enquiry = Enquiry::findOrFail($id);
        
        // Check if user can access this enquiry
        if (!$this->canAccessEnquiry($enquiry)) {
            flash()->error('You do not have permission to mark this enquiry as member');
            return redirect(action('EnquiriesController@index'));
        }

        $currentUser = Auth::user();
        $enquiry->status = \constEnquiryStatus::Member;
        $enquiry->updatedBy()->associate($currentUser);
        $enquiry->updated_by_user_name = $currentUser->name;
        $enquiry->updated_by_user_email = $currentUser->email;
        $enquiry->update();

        flash()->success('Enquiry was marked as member');

        return redirect('enquiries/all');
    }

    public function markAsLead($id)
    {
        $enquiry = Enquiry::findOrFail($id);
        
        // Check if user can access this enquiry
        if (!$this->canAccessEnquiry($enquiry)) {
            flash()->error('You do not have permission to mark this enquiry as lead');
            return redirect(action('EnquiriesController@index'));
        }

        $currentUser = Auth::user();
        $enquiry->status = \constEnquiryStatus::Lead;
        $enquiry->updatedBy()->associate($currentUser);
        $enquiry->updated_by_user_name = $currentUser->name;
        $enquiry->updated_by_user_email = $currentUser->email;
        $enquiry->update();

        flash()->success('Enquiry was marked as lead');

        return redirect(action('EnquiriesController@show', ['id' => $enquiry->id]));
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $enquiry = Enquiry::findOrFail($id);
            
            // Check if user can access this enquiry
            if (!$this->canAccessEnquiry($enquiry)) {
                DB::rollback();
                flash()->error('You do not have permission to delete this enquiry');
                return redirect(action('EnquiriesController@index'));
            }
            
            // Delete related followups
            Followup::where('enquiry_id', $id)->delete();
            
            // Delete the enquiry
            $enquiry->delete();

            DB::commit();
            flash()->success('Enquiry was successfully deleted');

            return redirect('enquiries/all');
        } catch (\Exception $e) {
            DB::rollback();
            flash()->error('Enquiry was not deleted');

            return redirect('enquiries/all');
        }
    }
}
