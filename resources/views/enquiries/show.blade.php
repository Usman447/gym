@extends('app')

@section('content')


    <div class="rightside bg-grey-100">
        <div class="container-fluid">

            @include('flash::message')

            <div class="row"><!-- Main row -->
                <div class="col-md-12"><!-- Main col -->
                    <div class="panel no-border ">
                        <div class="panel-title">

                            <div class="panel-head font-size-20">Enquiry details</div>
                            <div class="pull-right no-margin">
                                @if($enquiry->status == 1)
                                    @permission(['manage-gymie','manage-enquiries','view-enquiry'])
                                    <a href="#" class="mark-enquiry-as btn btn-sm btn-primary active pull-right margin-right-5"
                                       data-goto-url="{{ url('enquiries/'.$enquiry->id.'/markMember') }}" data-record-id="{{$enquiry->id}}"><i
                                                class="fa fa-user"></i> Mark as member</a>
                                    <a href="#" class="mark-enquiry-as btn btn-sm btn-primary active pull-right margin-right-5"
                                       data-goto-url="{{ url('enquiries/'.$enquiry->id.'/lost') }}" data-record-id="{{$enquiry->id}}"><i
                                                class="fa fa-times"></i> Mark Lost</a>
                                    @endpermission
                                @elseif($enquiry->status == 0 || $enquiry->status == 2)
                                    @permission(['manage-gymie','manage-enquiries','view-enquiry'])
                                    <a href="#" class="mark-enquiry-as btn btn-sm btn-primary active pull-right margin-right-5"
                                       data-goto-url="{{ url('enquiries/'.$enquiry->id.'/markAsLead') }}" data-record-id="{{$enquiry->id}}"><i
                                                class="fa fa-arrow-left"></i> Mark as Lead</a>
                                    @endpermission
                                @endif

                                @permission(['manage-gymie','manage-enquiries','edit-enquiry'])
                                <a class="btn btn-sm btn-primary pull-right margin-right-5"
                                   href="{{ action('EnquiriesController@edit',['id' => $enquiry->id]) }}"><span>Edit</span></a>
                                @endpermission
                            </div>
                        </div>

                        <div class="panel-body">

                            <div class="row">                <!--inner row start-->
                                <div class="col-sm-8">          <!-- inner column start -->
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <i class="fa fa-user center-icons color-blue-grey-100 fa-7x"></i>
                                        </div>

                                        <div class="col-sm-8">

                                            <!-- Spacer -->
                                            <div class="row visible-md visible-lg">
                                                <div class="col-sm-4">
                                                    <label>&nbsp;</label>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <label>Name</label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <span class="show-data">{{$enquiry->name}}</span>
                                                </div>
                                            </div>
                                            <hr class="margin-top-0 margin-bottom-10">
                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <label>Contact</label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <span class="show-data">{{$enquiry->contact}}</span>
                                                </div>
                                            </div>
                                            <hr class="margin-top-0 margin-bottom-10">
                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <label>Age</label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <span class="show-data">{{$enquiry->age ? $enquiry->age . ' years' : 'N/A'}}</span>
                                                </div>
                                            </div>
                                            <hr class="margin-top-0 margin-bottom-10">
                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <label>Gender</label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <span class="show-data">{{Utilities::getGender($enquiry->gender)}}</span>
                                                </div>
                                            </div>
                                            <hr class="margin-top-0 margin-bottom-10">
                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <label>Address</label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <span class="show-data">{{$enquiry->address}}</span>
                                                </div>
                                            </div>
                                            <hr class="margin-top-0 margin-bottom-10">
                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <label>OPF Residence</label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <span class="show-data">{{ $enquiry->opf_residence ? 'Yes' : 'No' }}</span>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="row"><!-- Main row -->
                                        <div class="col-md-12"><!-- Main Col -->
                                            <div class="panel bg-grey-50">
                                                <div class="panel-title margin-top-5 bg-transparent">
                                                    <div class="panel-head"><strong><span class="fa-stack">
							  <i class="fa fa-circle-thin fa-stack-2x"></i>
							  <i class="fa fa-ellipsis-h fa-stack-1x"></i>
							</span> Additional Details</strong></div>
                                                </div>
                                                <div class="panel-body">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            <label>Start by</label>
                                                        </div>
                                                        <div class="col-sm-8">
                                                            <span class="show-data">{{$enquiry->start_by}}</span>
                                                        </div>
                                                    </div>
                                                    <hr class="margin-top-0 margin-bottom-10">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            <label>Interested In</label>
                                                        </div>
                                                        <div class="col-sm-8">
                                                            <?php
                                                            $Int1 = array();
                                                            $InName = App\Plan::whereIn('id', explode(',', $enquiry->interested_in))->get();

                                                            foreach ($InName as $Int2) {
                                                                $Int1[] = $Int2->plan_name;
                                                            }
                                                            ?>
                                                            <span class="show-data">{{ implode(",",$Int1) }}</span>
                                                        </div>
                                                    </div>
                                                    <hr class="margin-top-0 margin-bottom-10">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            <label>Occupation</label>
                                                        </div>
                                                        <div class="col-sm-8">
                                                            <span class="show-data">{{Utilities::getOccupation($enquiry->occupation)}}</span>
                                                        </div>
                                                    </div>
                                                    <hr class="margin-top-0 margin-bottom-10">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            <label>Status</label>
                                                        </div>
                                                        <div class="col-sm-8">
                                                            <span class="show-data">{{Utilities::getEnquiryStatus ($enquiry->status)}}</span>
                                                        </div>
                                                    </div>
                                                    <hr class="margin-top-0 margin-bottom-10">
                                                    
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            <label>Created At</label>
                                                        </div>
                                                        <div class="col-sm-8">
                                                            <span class="show-data">{{$enquiry->created_at->toFormattedDateString()}}</span>
                                                        </div>
                                                    </div>
                                                    <hr class="margin-top-0 margin-bottom-10">
                                                    
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            <label>Last Updated At</label>
                                                        </div>
                                                        <div class="col-sm-8">
                                                            <span class="show-data">{{$enquiry->updated_at->toFormattedDateString()}}</span>
                                                        </div>
                                                    </div>
                                                    
                                                    @permission('manage-gymie')
                                                    @if($enquiry->created_by_user_name || $enquiry->updated_by_user_name)
                                                    <hr class="margin-top-0 margin-bottom-10">
                                                    
                                                    <div class="row">
                                                        <div class="col-sm-12">
                                                            <label><strong>Entry Tracking</strong></label>
                                                        </div>
                                                    </div>
                                                    
                                                    @if($enquiry->created_by_user_name)
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            <label>Created By</label>
                                                        </div>
                                                        <div class="col-sm-8">
                                                            <span class="show-data">
                                                                {{ $enquiry->created_by_user_name }}
                                                                @if($enquiry->created_by_user_email)
                                                                    <br><small class="text-muted">{{ $enquiry->created_by_user_email }}</small>
                                                                @endif
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <hr class="margin-top-0 margin-bottom-10">
                                                    @endif
                                                    
                                                    @if($enquiry->updated_by_user_name && ($enquiry->updated_by_user_name != $enquiry->created_by_user_name || $enquiry->updated_by_user_email != $enquiry->created_by_user_email))
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            <label>Last Updated By</label>
                                                        </div>
                                                        <div class="col-sm-8">
                                                            <span class="show-data">
                                                                {{ $enquiry->updated_by_user_name }}
                                                                @if($enquiry->updated_by_user_email)
                                                                    <br><small class="text-muted">{{ $enquiry->updated_by_user_email }}</small>
                                                                @endif
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <hr class="margin-top-0 margin-bottom-10">
                                                    @endif
                                                    @endif
                                                    @endpermission

                                                </div>   <!-- End of inner Column -->
                                            </div>
                                        </div>
                                    </div>
                                </div>   <!-- End Of inner Row -->
                            </div>    <!-- / Panel-body -->
                        </div><!-- / Panel-no-border -->
                    </div><!-- / Main-col -->
                </div><!-- / Main-row -->
            </div>

            <!-- Already created followups -->

            <!-- ############################ Already created followups Timeline ######################### -->

            <div class="row"><!-- Main row -->
                <div class="col-md-12">
                    <div class="panel no-border">
                        <div class="panel-title bg-white no-border">
                            <div class="panel-head"><i class="fa fa-bookmark-o"></i> <span> Follow Up Timeline</span></div>
                            @permission(['manage-gymie','manage-enquiries','add-enquiry-followup'])
                            <button class="btn btn-primary btn-sm pull-right" data-toggle="modal" data-target="#createFollowupModal"
                                    data-id="createFollowupModal">
                                Add Followup
                            </button>
                            @endpermission
                        </div>

                        <div class="panel-body">

                            @if($followups->count() != 0)
                                <div class="timeline-centered">
                                    @foreach($followups as $followup)
                                        <article class="timeline-entry">
                                            <div class="timeline-entry-inner">
                                                <time class="timeline-time"><span
                                                            class="followup-time">{{ $followup->updated_at->toFormattedDateString() }}</span></time>
                                                <div class="timeline-icon {{ Utilities::getIconBg($followup->status) }}">
                                                    <i class="{{ Utilities::getStatusIcon($followup->status) }}"></i>
                                                </div>
                                                <div class="timeline-label">
                                                    <p>Via {{ Utilities::getFollowupBy($followup->followup_by) }}
                                                        @if($followup->status == 0)
                                                            @permission(['manage-gymie','manage-enquiries','edit-enquiry-followup'])
                                                            <button class="btn btn-info btn-sm pull-right" data-toggle="modal"
                                                                    data-target="#editFollowupModal-{{$followup->id}}" data-id="{{$followup->id}}">
                                                                Edit
                                                            </button>
                                                            @endpermission
                                                        @else
                                                            <span class="label label-primary pull-right followup-label">Done</span>
                                                        @endif
                                                    </p>
                                                    @if($followup->status == 0)
                                                        <p>Due Date: {{ $followup->due_date->format('Y-m-d') }}</p>
                                                    @else
                                                        <p>{{ $followup->outcome }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                            @else
                                <h2 class="text-center padding-top-15">No followups yet.</h2>
                            @endif
                        </div><!-- Panel Body End -->

                    </div><!-- Panel End -->
                </div><!-- Col End -->
            </div><!-- / Row End -->

            <!-- Edit Followup Modal -->
            @if($followups->count() != 0)
                @foreach($followups as $followup)
                    <div id="editFollowupModal-{{$followup->id}}" class="modal fade" role="dialog">
                        <div class="modal-dialog">

                            <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">Kindly update the status and outcome</h4>
                                </div>
                                <form action="{{ action(['App\Http\Controllers\FollowupsController@update', $followup->id]) }}" method="POST" id="followupform">
                                    @csrf
                                    @method('PUT')
                                <div class="modal-body">

                                    <input type="hidden" name="enquiry_id" value="{{ $followup->enquiry->id }}">

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="date">Date</label>
                                                <input type="text" name="date" value="{{ $followup->created_at->format('Y-m-d') }}" class="form-control" id="date" readonly>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="followup_by">Follow Up By</label>
                                                <select name="followup_by" class="form-control selectpicker show-tick show-menu-arrow" id="followup_by">
                                                    <option value="0" {{ $followup->followup_by == 0 ? 'selected' : '' }}>Call</option>
                                                    <option value="1" {{ $followup->followup_by == 1 ? 'selected' : '' }}>SMS</option>
                                                    <option value="2" {{ $followup->followup_by == 2 ? 'selected' : '' }}>Personal</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="due_date">Due Date</label>
                                                <input type="text" name="due_date" value="{{ $followup->due_date->format('Y-m-d') }}" class="form-control" id="due_date" readonly>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="status">Status</label>
                                                <select name="status" class="form-control selectpicker show-tick show-menu-arrow" id="status">
                                                    <option value="0" {{ $followup->status == 0 ? 'selected' : '' }}>Pending</option>
                                                    <option value="1" {{ $followup->status == 1 ? 'selected' : '' }}>Done</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="outcome">Outcome</label>
                                                <input type="text" name="outcome" value="{{ $followup->outcome }}" class="form-control" id="outcome">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <input type="submit" class="btn btn-info" value="Done" id="btn-{{ $followup->id }}"/>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>

            @endforeach
        @endif

        <!-- Create Followup Modal -->
            <div id="createFollowupModal" class="modal fade" role="dialog">
                <div class="modal-dialog">

                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">New Followup</h4>
                        </div>
                        <div class="modal-body">
                            <form action="{{ action('App\Http\Controllers\FollowupsController@store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                            <input type="hidden" name="enquiry_id" value="{{ $enquiry->id }}">

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="followup_by">FollowUp By</label>
                                        <select name="followup_by" class="form-control selectpicker show-tick show-menu-arrow" id="followup_by">
                                            <option value="0">Call</option>
                                            <option value="1">SMS</option>
                                            <option value="2">Personal</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="due_date">Due Date</label>
                                        <input type="text" name="due_date" value="" class="form-control datepicker-default" id="due_date">
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <input type="submit" class="btn btn-info" value="Create" id="createFollowup"/>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@stop
@section('footer_scripts')
    <script src="{{ URL::asset('assets/js/followup.js') }}" type="text/javascript"></script>
@stop
@section('footer_script_init')
    <script type="text/javascript">
        $(document).ready(function () {
            gymie.markEnquiryAs();
        });
    </script>
@stop