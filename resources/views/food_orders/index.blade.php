@extends('app')

@section('content')

    <div class="rightside bg-grey-100">

        <!-- BEGIN PAGE HEADING -->
        <div class="page-head bg-grey-100 padding-top-15 no-padding-bottom">
            @include('flash::message')
            <h1 class="page-title no-line-height">Food / Inventory Orders
                @permission(['manage-gymie','manage-food','order-food'])
                <a href="{{ action('FoodOrdersController@create') }}" class="page-head-btn btn-sm btn-primary active" role="button">Add Order</a>
                <small>Details of all food and inventory orders</small>
            </h1>
            @permission(['manage-gymie','manage-food','order-food','pagehead-stats'])
            <div class="row margin-top-10">
                <div class="col-sm-6">
                    <h3 class="font-size-24 text-left color-blue-grey-600">
                        <span data-toggle="counter" data-start="0"
                              data-from="0" data-to="{{ $totalOrders }}"
                              data-speed="600"
                              data-refresh-interval="10"></span>
                        <small class="color-blue-grey-600 display-block margin-top-5 font-size-14">Total Orders</small>
                    </h3>
                </div>
                <div class="col-sm-6">
                    <h3 class="font-size-24 text-left color-blue-grey-600">
                        Rs. <span data-toggle="counter" data-start="0"
                              data-from="0" data-to="{{ $totalAmount }}"
                              data-speed="600"
                              data-refresh-interval="10"></span>
                        <small class="color-blue-grey-600 display-block margin-top-5 font-size-14">Total Amount</small>
                    </h3>
                </div>
            </div>
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
                                        <form method="GET">

                                        <div class="col-sm-6">
                                            <label for="food-order-daterangepicker">Date range</label>
                                            <div id="food-order-daterangepicker"
                                                 class="gymie-daterangepicker btn bg-grey-50 daterange-padding no-border color-grey-600 hidden-xs no-shadow">
                                                <i class="ion-calendar margin-right-10"></i>
                                                <span>{{$drp_placeholder}}</span>
                                                <i class="ion-ios-arrow-down margin-left-5"></i>
                                            </div>
                                            <input type="text" name="drp_start" value="" class="hidden" id="drp_start">
                                            <input type="text" name="drp_end" value="" class="hidden" id="drp_end">
                                        </div>

                                        <div class="col-sm-4">
                                            <label for="sort_direction">Order</label>
                                            <select name="sort_direction" class="form-control selectpicker show-tick show-menu-arrow" id="sort_direction">
                                                <option value="desc" {{ old('sort_direction', 'desc') == 'desc' ? 'selected' : '' }}>Descending</option>
                                                <option value="asc" {{ old('sort_direction', 'desc') == 'asc' ? 'selected' : '' }}>Ascending</option>
                                            </select>
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

                            @if($foodOrders->count() == 0)
                                <h4 class="text-center padding-top-15">Sorry! No records found</h4>
                            @else
                                <table id="foodOrders" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Amount</th>
                                        <th>Created At</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    @foreach ($foodOrders as $foodOrder)
                                        <tr>
                                            <td><a href="{{ action('FoodOrdersController@show',['id' => $foodOrder->id]) }}">{{ $foodOrder->order_number}}</a></td>
                                            <td>{{ number_format($foodOrder->total_amount, 0) }}</td>
                                            <td>{{ $foodOrder->created_at->toDayDateTimeString()}}</td>
                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-info">Actions</button>
                                                    <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                        <span class="caret"></span>
                                                        <span class="sr-only">Toggle Dropdown</span>
                                                    </button>
                                                    <ul class="dropdown-menu" role="menu">
                                                        <li>
                                                            @permission(['manage-gymie','manage-food','order-food'])
                                                            <a href="{{ action('FoodOrdersController@show',['id' => $foodOrder->id]) }}">View</a>
                                                            @endpermission
                                                        </li>
                                                        <li>
                                                            @permission(['manage-gymie','manage-food','order-food'])
                                                            <a href="{{ action('FoodOrdersController@edit',['id' => $foodOrder->id]) }}">Edit</a>
                                                            @endpermission
                                                        </li>
                                                        <li>
                                                            @permission(['manage-gymie','manage-food','delete-food'])
                                                            <a href="#" class="delete-record" data-delete-url="{{ url('food/orders/'.$foodOrder->id.'/delete') }}"
                                                               data-record-id="{{$foodOrder->id}}">Delete</a>
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
                                            Showing page {{ $foodOrders->currentPage() }} of {{ $foodOrders->lastPage() }}
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="gymie_paging pull-right">
                                            {!! str_replace('/?', '?', $foodOrders->appends(request()->all())->render()) !!}
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
            gymie.loaddaterangepicker();
        });
    </script>
@stop

