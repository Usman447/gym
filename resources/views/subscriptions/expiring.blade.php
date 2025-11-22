@extends('app')

@section('content')


    <div class="rightside bg-grey-100">
        <!-- BEGIN PAGE HEADING -->
        <div class="page-head bg-grey-100 padding-top-15 no-padding-bottom">
            @include('flash::message')
            <h1 class="page-title no-line-height">Expiring subscriptions
                <small>Details of all expiring subscriptions</small>
            </h1>
            @permission(['manage-gymie','pagehead-stats'])
            <h1 class="font-size-30 text-right color-blue-grey-600 animated fadeInDown total-count pull-right"><span data-toggle="counter" data-start="0"
                                                                                                                     data-from="0" data-to="{{ $count }}"
                                                                                                                     data-speed="600"
                                                                                                                     data-refresh-interval="10"></span>
                <small class="color-blue-grey-600 display-block margin-top-5 font-size-14">Expiring Subscriptions</small>
            </h1>
            @endpermission
        </div><!-- / PageHead -->


        <div class="container-fluid">
            <!-- Main row -->
            <div class="row">
                <div class="col-md-12">
                    <div class="panel no-border ">

                        <div class="panel-title bg-blue-grey-50">
                            <div class="panel-head font-size-15">

                                <div class="row">
                                    <div class="col-sm-12 no-padding">
                                        <form method="GET">

                                        <div class="col-sm-3">

                                            <label for="subscription-daterangepicker">Date range</label>

                                            <div id="subscription-daterangepicker"
                                                 class="gymie-daterangepicker btn bg-grey-50 daterange-padding no-border color-grey-600 hidden-xs no-shadow">
                                                <i class="ion-calendar margin-right-10"></i>
                                                <span>{{$drp_placeholder}}</span>
                                                <i class="ion-ios-arrow-down margin-left-5"></i>
                                            </div>

                                            <input type="text" name="drp_start" value="" class="hidden" id="drp_start">
                                            <input type="text" name="drp_end" value="" class="hidden" id="drp_end">
                                        </div>

                                        <div class="col-sm-2">
                                            <label for="sort_field">Sort By</label>
                                            <select name="sort_field" class="form-control selectpicker show-tick show-menu-arrow" id="sort_field">
                                                <option value="created_at" {{ old('sort_field') == 'created_at' ? 'selected' : '' }}>Date</option>
                                                <option value="plan_name" {{ old('sort_field') == 'plan_name' ? 'selected' : '' }}>Plan name</option>
                                            </select>
                                        </div>

                                        <div class="col-sm-2">
                                            <label for="sort_direction">Order</label>
                                            <select name="sort_direction" class="form-control selectpicker show-tick show-menu-arrow" id="sort_direction">
                                                <option value="desc" {{ old('sort_direction') == 'desc' ? 'selected' : '' }}>Descending</option>
                                                <option value="asc" {{ old('sort_direction') == 'asc' ? 'selected' : '' }}>Ascending</option>
                                            </select>
                                        </div>

                                        <div class="col-xs-3">
                                            <label for="search">Keyword</label>
                                            <input value="{{ old('search') }}" name="search" id="search" type="text" class="form-control padding-right-35"
                                                   placeholder="Search...">
                                        </div>

                                        <div class="col-xs-2">
                                            <label>&nbsp;</label> <br/>
                                            <button type="submit" class="btn btn-primary active no-border">GO</button>
                                        </div>

                                        </form>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="panel-body bg-white">
                            @if($expirings->count() == 0)
                                <h4 class="text-center padding-top-15">Sorry! No records found</h4>
                            @else
                                <table id="expiring" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>Member Code</th>
                                        <th>Member Name</th>
                                        <th>Plan Name</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    <tr>

                                        @foreach ($expirings as $expiring)

                                            <td>
                                                <a href="{{ action('MembersController@show',['id' => $expiring->member->id]) }}">{{ $expiring->member->member_code }}</a>
                                            </td>
                                            <td>
                                                <a href="{{ action('MembersController@show',['id' => $expiring->member->id]) }}">{{ $expiring->member->name }}</a>
                                            </td>
                                            <td>{{ $expiring->plan->plan_name }}</td>
                                            <td>{{ $expiring->start_date->format('d-m-Y') }}</td>
                                            <td>{{ $expiring->end_date->format('d-m-Y') }}</td>
                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-info">Actions</button>
                                                    <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                        <span class="caret"></span>
                                                        <span class="sr-only">Toggle Dropdown</span>
                                                    </button>
                                                    <ul class="dropdown-menu" role="menu">
                                                        <li>
                                                            @permission(['manage-gymie','manage-subscriptions','renew-subscription'])
                                                            <a href="{{ action('SubscriptionsController@renew',['id' => $expiring->invoice_id]) }}">
                                                                Renew subscription
                                                            </a>
                                                            @endpermission
                                                        </li>
                                                        @if($expiring->status == \constSubscription::onGoing)
                                                        <li>
                                                            @permission(['manage-gymie','manage-subscriptions','cancel-subscription'])
                                                            <a href="#" class="cancel-subscription"
                                                               data-cancel-url="{{ action('SubscriptionsController@cancelSubscription', $expiring->id) }}"
                                                               data-record-id="{{$expiring->id}}">
                                                                Cancel subscription
                                                            </a>
                                                            @endpermission
                                                        </li>
                                                        @endif
                                                        <li>
                                                            @permission(['manage-gymie','manage-subscriptions','delete-subscription'])
                                                            <a href="#" class="delete-record"
                                                               data-delete-url="{{ url('subscriptions/'.$expiring->id.'/delete') }}"
                                                               data-record-id="{{$expiring->id}}">
                                                                Delete subscription
                                                            </a>
                                                            @endpermission
                                                        </li>
                                                    </ul>
                                                </div>

                                            </td>

                                            </td>
                                    </tr>

                                    @endforeach

                                    </tbody>
                                </table>

                                <div class="row">
                                    <div class="col-xs-6">
                                        <div class="gymie_paging_info">
                                            Showing page{{ $expirings->currentPage() }} of {{ $expirings->lastPage() }}
                                        </div>
                                    </div>

                                    <div class="col-xs-6">
                                        <div class="gymie_paging pull-right">

                                            {!! str_replace('/?', '?', $expirings->appends(request()->only('search'))->render()) !!}
                                        </div>
                                    </div>
                                </div>

                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('footer_script_init')
    <script type="text/javascript">
        $(document).ready(function () {
            gymie.deleterecord();
            gymie.cancelsubscription();
        });
    </script>
@stop