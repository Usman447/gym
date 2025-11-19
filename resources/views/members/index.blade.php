@extends('app')

@section('content')

    <div class="rightside bg-grey-100">

        <!-- BEGIN PAGE HEADING -->
        <div class="page-head bg-grey-100 padding-top-15 no-padding-bottom">
            @include('flash::message')
            <h1 class="page-title no-line-height">Members
                @permission(['manage-gymie','manage-members','add-member'])
                <a href="{{ action('MembersController@create') }}" class="page-head-btn btn-sm btn-primary active" role="button">Add New</a>
                <small>Details of all gym members</small>
            </h1>
            @permission(['manage-gymie','pagehead-stats'])
            <h1 class="font-size-30 text-right color-blue-grey-600 animated fadeInDown total-count pull-right"><span data-toggle="counter" data-start="0"
                                                                                                                     data-from="0" data-to="{{ $count }}"
                                                                                                                     data-speed="600"
                                                                                                                     data-refresh-interval="10"></span>
                <small class="color-blue-grey-600 display-block margin-top-5 font-size-14">Total Members</small>
            </h1>
            @endpermission
            @endpermission
        </div><!-- / PageHead -->

        <div class="container-fluid">
            <div class="row"><!-- Main row -->
                <div class="col-lg-12"><!-- Main Col -->
                    <div class="panel no-border ">
                        <div class="panel-title bg-blue-grey-50">
                            <div class="panel-head font-size-15">

                                <div class="row">
                                    <div class="col-sm-12 no-padding">
                                        {!! Form::Open(['method' => 'GET']) !!}

                                        <div class="col-sm-3">

                                            {!! Form::label('member-daterangepicker','Date range') !!}

                                            <div id="member-daterangepicker"
                                                 class="gymie-daterangepicker btn bg-grey-50 daterange-padding no-border color-grey-600 hidden-xs no-shadow">
                                                <i class="ion-calendar margin-right-10"></i>
                                                <span>{{$drp_placeholder}}</span>
                                                <i class="ion-ios-arrow-down margin-left-5"></i>
                                            </div>

                                            {!! Form::text('drp_start',null,['class'=>'hidden', 'id' => 'drp_start']) !!}
                                            {!! Form::text('drp_end',null,['class'=>'hidden', 'id' => 'drp_end']) !!}
                                        </div>

                                        <div class="col-sm-2">
                                            {!! Form::label('sort_field','Sort By') !!}

                                            @if(Auth::user()->can('manage-gymie'))
                                                {!! Form::select('sort_field',array('status' => 'Status', 'created_at' => 'Date','name' => 'Name', 'member_code' => 'Member code', 'timings' => 'Timings'),old('sort_field'),['class' => 'form-control selectpicker show-tick show-menu-arrow', 'id' => 'sort_field']) !!}
                                            @else
                                                {!! Form::select('sort_field',array('status' => 'Status', 'created_at' => 'Date','name' => 'Name', 'member_code' => 'Member code'),old('sort_field'),['class' => 'form-control selectpicker show-tick show-menu-arrow', 'id' => 'sort_field']) !!}
                                            @endif
                                        </div>

                                        <div class="col-sm-2" id="status_filter_container" style="display: none;">
                                            {!! Form::label('status_filter','Status Filter') !!}
                                            {!! Form::select('status_filter',array('' => 'All Statuses', '1' => 'OnGoing', '8' => 'Expiring', '9' => 'Pending', '3' => 'Cancelled', 'none' => 'No Active Plan'),old('status_filter'),['class' => 'form-control selectpicker show-tick show-menu-arrow', 'id' => 'status_filter']) !!}
                                        </div>

                                        <div class="col-sm-2">
                                            {!! Form::label('sort_direction','Order') !!}
                                            {!! Form::select('sort_direction',array('desc' => 'Descending','asc' => 'Ascending'),old('sort_direction'),['class' => 'form-control selectpicker show-tick show-menu-arrow', 'id' => 'sort_direction']) !!}</span>
                                        </div>

                                        <div class="col-xs-3">
                                            {!! Form::label('search','Keyword') !!}
                                            <input value="{{ old('search') }}" name="search" id="search" type="text" class="form-control padding-right-35"
                                                   placeholder="Search...">
                                        </div>

                                        <div class="col-xs-2">
                                            {!! Form::label('&nbsp;') !!} <br/>
                                            <button type="submit" class="btn btn-primary active no-border">GO</button>
                                        </div>

                                        {!! Form::Close() !!}
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="panel-body bg-white">

                            @if($members->count() == 0)
                                <h4 class="text-center padding-top-15">Sorry! No records found</h4>
                            @else
                                <table id="members" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <!-- <th>Photo</th> -->
                                        <th>Name</th>
                                        <!-- <th>Code</th> -->
                                        <th>Contact</th>
                                        <th>Timings</th>
                                        <th>Plan name</th>
                                        <th>Plan Status</th>
                                        <th>Member since</th>
                                        <th>Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    @foreach ($members as $member)
                                        <?php
                                        $subscriptions = $member->subscriptions;
                                        $plansArray = array();
                                        $sub = null; // Subscription to use for status display
                                        
                                        if ($subscriptions && $subscriptions->count() > 0) {
                                            // Filter for ongoing subscriptions
                                            $ongoingSubscriptions = $subscriptions->filter(function($subscription) {
                                                return $subscription->status == \constSubscription::onGoing;
                                            });
                                            
                                            if ($ongoingSubscriptions->count() > 0) {
                                                // Show all ongoing subscriptions
                                                foreach ($ongoingSubscriptions as $subscription) {
                                                    if ($subscription->plan) {
                                                        $plansArray[] = $subscription->plan->plan_name;
                                                    }
                                                }
                                                // Use the first ongoing subscription for status display
                                                $sub = $ongoingSubscriptions->first();
                                            } else {
                                                // If no ongoing subscriptions, show the last subscription
                                                $lastSubscription = $subscriptions->sortByDesc('created_at')->first();
                                                if ($lastSubscription && $lastSubscription->plan) {
                                                    $plansArray[] = $lastSubscription->plan->plan_name;
                                                }
                                                // Use the last subscription for status display
                                                $sub = $lastSubscription;
                                            }
                                        }
                                        
                                        $images = $member->getMedia('profile');
                                        $profileImage = ($images->isEmpty() ? 'https://placeholdit.imgix.net/~text?txtsize=18&txt=NA&w=50&h=50' : url($images[0]->getUrl('thumb')));
                                        ?>
                                        <tr>
                                            <!-- <td><a href="{{ action('MembersController@show',['id' => $member->id]) }}"><img src="{{ $profileImage }}"/></a></td> -->
                                            <td><a href="{{ action('MembersController@show',['id' => $member->id]) }}">{{ $member->name}}</a></td>
                                            <!-- <td><a href="{{ action('MembersController@show',['id' => $member->id]) }}">{{ $member->member_code}}</a></td> -->
                                            <td>{{ $member->contact}}</td>
                                            <td>{{ $member->timings ?: '-' }}</td>
                                            <td>{{ implode(",",$plansArray) }}</td>
                                            <td>
                                                <?php
                                                    // Initialize variables with default values
                                                    $labelClass = 'label label-default';
                                                    $statusText = 'No Active Plan';
                                                    
                                                    // Use the same subscription that's displayed in plan name column
                                                    if ($sub && isset($sub->status)) {
                                                        try {
                                                            $status = $sub->status;
                                                            $labelClass = Utilities::getSubscriptionLabel((string)$status);
                                                            $statusText = Utilities::getSubscriptionStatus((string)$status);

                                                            // Only apply for OnGoing
                                                            if ($status == \constSubscription::onGoing) {
                                                                $endingDate = \Carbon\Carbon::parse($sub->end_date);
                                                                $today = \Carbon\Carbon::today();
                                                                
                                                                // "Pending": after end date, still OnGoing
                                                                if ($endingDate->lt($today)) {
                                                                    $status = 9; // pending
                                                                    $labelClass = Utilities::getSubscriptionLabel('9');
                                                                    $statusText = Utilities::getSubscriptionStatus('9');
                                                                }
                                                                // "Expiring": end date within next 6 days including today (and not past)
                                                                elseif ($endingDate->gte($today) && $endingDate->diffInDays($today) <= 3) {
                                                                    $status = 8; // expiring
                                                                    $labelClass = Utilities::getSubscriptionLabel('8');
                                                                    $statusText = Utilities::getSubscriptionStatus('8');
                                                                }
                                                            }
                                                        } catch (\Exception $e) {
                                                            // If any error occurs, use default values
                                                            $labelClass = 'label label-default';
                                                            $statusText = 'No Active Plan';
                                                        }
                                                    }
                                                ?>
                                                <span class="{{ $labelClass }}">
                                                    {{ $statusText }}
                                                </span>
                                            </td>
                                            <td>{{ $member->created_at->format('d-m-Y')}}</td>
                                            <td>
                                                <span class="{{ Utilities::getActiveInactive ($member->status) }}">{{ Utilities::getStatusValue ($member->status) }}</span>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-info">Actions</button>
                                                    <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                        <span class="caret"></span>
                                                        <span class="sr-only">Toggle Dropdown</span>
                                                    </button>
                                                    <ul class="dropdown-menu" role="menu">
                                                        <li>
                                                            @permission(['manage-gymie','manage-members','view-member'])
                                                            <a href="{{ action('MembersController@show',['id' => $member->id]) }}">View details</a>
                                                            @endpermission
                                                        </li>
                                                        <li>
                                                            @permission(['manage-gymie','manage-members','edit-member'])
                                                            <a href="{{ action('MembersController@edit',['id' => $member->id]) }}">Edit details</a>
                                                            @endpermission
                                                        </li>
                                                        <li>
                                                            @permission(['manage-gymie','manage-members','delete-member'])
                                                            <a href="#" class="delete-record" data-delete-url="{{ url('members/'.$member->id.'/archive') }}"
                                                               data-record-id="{{$member->id}}">Delete member</a>
                                                            @endpermission
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                                <div class="row">
                                    <div class="col-xs-6">
                                        <div class="gymie_paging_info">
                                            Showing page {{ $members->currentPage() }} of {{ $members->lastPage() }}
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="gymie_paging pull-right">
                                            {!! str_replace('/?', '?', $members->appends(Input::only(['search', 'sort_field', 'sort_direction', 'status_filter', 'drp_start', 'drp_end']))->render()) !!}
                                        </div>
                                    </div>
                                </div>

                        </div><!-- / Panel Body -->
                        @endif
                    </div><!-- / Panel-no-border -->
                </div><!-- / Main Col -->
            </div><!-- / Main Row -->
        </div><!-- / Container -->
    </div><!-- / RightSide -->
@stop
@section('footer_script_init')
    <script type="text/javascript">
        $(document).ready(function () {
            gymie.deleterecord();
            
            // Show/hide status filter based on sort_field selection
            function toggleStatusFilter() {
                var sortField = $('#sort_field').val();
                if (sortField === 'status') {
                    $('#status_filter_container').show();
                    // Refresh selectpicker to ensure proper rendering
                    $('#status_filter').selectpicker('refresh');
                } else {
                    $('#status_filter_container').hide();
                    // Clear status filter when hidden
                    $('#status_filter').val('').selectpicker('refresh');
                }
            }
            
            // Check on page load
            toggleStatusFilter();
            
            // Check when sort_field changes
            $('#sort_field').on('change', function() {
                toggleStatusFilter();
            });
        });
    </script>
@stop        
