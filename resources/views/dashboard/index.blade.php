@extends('app')

@section('content')

    <div class="rightside bg-grey-100">

        <div class="container-fluid">
            @include('flash::message')
            @permission(['manage-gymie','view-dashboard-quick-stats'])
            <!-- Stat Tile  -->
            <div class="row margin-top-10">
                <!-- Total Members -->
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    @include('dashboard._index.totalMembers')
                </div>

                <!-- Registrations This Weeks -->
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    @include('dashboard._index.registeredThisMonth')
                </div>

                <!-- Inactive Members -->
                <!--div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                    @include('dashboard._index.inActiveMembers')
                </div-->

                <!-- Members Expired -->
                <!--div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                    @include('dashboard._index.expiredMembers')
                </div-->

                @permission('manage-gymie')
                <!-- Outstanding Payments -->
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    @include('dashboard._index.outstandingPayments')
                </div>

                <!-- Collection -->
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    @include('dashboard._index.collection')
                </div>
                @endpermission
            </div>
            @endpermission

            <!--Member Quick views -->
            <div class="row"> <!--Main Row-->
                @permission(['manage-gymie','view-dashboard-members-tab'])
                <div class="col-lg-6">
                    <div class="panel">
                        <div class="panel-title">
                            <div class="panel-head"><i class="fa fa-users"></i><a href="{{ action('MembersController@index') }}">Members</a></div>
                            <div class="pull-right"><a href="{{ action('MembersController@create') }}" class="btn-sm btn-primary active" role="button"><i
                                            class="fa fa-user-plus"></i> Add</a></div>
                        </div>

                        <div class="panel-body with-nav-tabs">
                            <!-- Tabs Heads -->
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#expiring" data-toggle="tab">Expiring<span
                                                class="label label-warning margin-left-5">{{ $expiringCount }}</span></a></li>
                                <!--li><a href="#expired" data-toggle="tab">Expired<span class="label label-danger margin-left-5">{{ $expiredCount }}</span></a>
                                </li-->
                                
                                <!-- <li><a href="#recent" data-toggle="tab">Recent</a></li> -->
                            </ul>

                            <!-- Tab Content -->
                            <div class="tab-content">
                                <div class="tab-pane fade in active" id="expiring">
                                    @include('dashboard._index.expiring', ['expirings' => $expirings])
                                </div>

                                <div class="tab-pane fade" id="expired">
                                    @include('dashboard._index.expired', ['allExpired' => $allExpired])
                                </div>

                                <div class="tab-pane fade" id="recent">
                                    @include('dashboard._index.recents', ['recents' =>  $recents])
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endpermission

                @permission(['manage-gymie','view-dashboard-enquiries-tab'])
                <!--Enquiry Quick view Tabs-->
                <div class="col-lg-6">
                    <div class="panel">
                        <div class="panel-title">
                            <div class="panel-head"><i class="fa fa-phone"></i><a href="{{ action('EnquiriesController@index') }}">Enquiries</a></div>
                            <div class="pull-right"><a href="{{ action('EnquiriesController@create') }}" class="btn-sm btn-primary active" role="button"><i
                                            class="fa fa-phone"></i> Add</a></div>
                        </div>

                        <div class="panel-body with-nav-tabs">
                            <!-- Tabs Heads -->
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#enquiries" data-toggle="tab">Enquiries</a></li>
                                <li><a href="#reminders" data-toggle="tab">Reminders<span class="label label-warning margin-left-5">{{ $reminderCount }}</span></a>
                                </li>
                            </ul>

                            <!-- Tab Content -->
                            <div class="tab-content">
                                <div class="tab-pane fade in active" id="enquiries">
                                    @include('dashboard._index.enquiries', ['enquiries' => $enquiries])
                                </div>

                                <div class="tab-pane fade" id="reminders">
                                    @include('dashboard._index.reminders', ['reminders' => $reminders])
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endpermission
            </div> <!--/Main row -->


            @permission(['manage-gymie','view-dashboard-expense-tab'])
            <div class="row">
                <!--Expense Quick view Tabs-->
                <div class="col-lg-6">
                    <div class="panel">
                        <div class="panel-title">
                            <div class="panel-head"><i class="fa fa-inr"></i><a href="{{ action('ExpensesController@index') }}">Expenses</a></div>
                            <div class="pull-right"><a href="{{ action('ExpensesController@create') }}" class="btn-sm btn-primary active" role="button">
                                    <i class="fa fa-inr"></i> Add</a>
                            </div>
                        </div>

                        <div class="panel-body with-nav-tabs">
                            <!-- Tabs Heads -->
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#due" data-toggle="tab">Due</a></li>
                                <li><a href="#outstanding" data-toggle="tab">Outstanding</a></li>
                            </ul>

                            <!-- Tab Content -->
                            <div class="tab-content">
                                <div class="tab-pane fade in active" id="due">
                                    @include('dashboard._index.due', ['dues' => $dues])
                                </div>

                                <div class="tab-pane fade" id="outstanding">
                                    @include('dashboard._index.outStanding', ['outstandings' => $outstandings])
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endpermission

            @permission(['manage-gymie','view-dashboard-charts'])
            <div class="row">

                <div class="col-lg-6">
                    <div class="panel bg-white">
                        <div class="panel-title">
                            <div class="panel-head">Members Per Plan</div>
                        </div>
                        <div class="panel-body padding-top-10">
                            @if(!empty($membersPerPlan))
                                <div id="gymie-members-per-plan" class="chart"></div>
                            @else
                                <div class="tab-empty-panel font-size-24 color-grey-300">
                                    <div id="gymie-members-per-plan" class="chart"></div>
                                    No Data
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="panel bg-white">
                        <div class="panel-title bg-transparent no-border">
                            <div class="panel-head">Registration Trend</div>
                        </div>
                        <div class="panel-body no-padding-top">
                            <div id="gymie-registrations-trend" class="chart"></div>
                        </div>
                    </div>
                </div>
            </div>
            @endpermission

            


        </div>
    </div>
@stop

@section('footer_scripts')
    <script src="{{ URL::asset('assets/plugins/morris/raphael-2.1.0.min.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('assets/plugins/morris/morris.min.js') }}" type="text/javascript"></script>
@stop

@section('footer_script_init')
    <script type="text/javascript">
        $(document).ready(function () {
            gymie.loadmorris();
            gymie.cancelsubscription();
        });
    </script>
@stop
