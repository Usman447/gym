@extends('app')

@section('content')

    <div class="rightside bg-white">
        <!-- BEGIN PAGE HEADING -->
        <div class="page-head bg-grey-100 margin-bottom-20 hidden-print">
            @include('flash::message')
            <h1 class="page-title">Invoice</h1>
        </div>
        <!-- END PAGE HEADING -->

        <div class="container-fluid">
            <div class="row"> <!--Main Row-->
                <div class="col-lg-12"> <!-- Main column -->
                    <div class="panel"> <!-- Main Panel-->
                        <div class="panel-body">
                            <div class="border-bottom-1 border-grey-100 padding-bottom-20 margin-bottom-20 clearfix">
                                @if($settings['invoice_name_type'] == 'gym_logo')
                                    <img class="no-margin display-inline-block pull-left" src="{{url('/images/Invoice/'.'gym_logo'.'.jpg') }}" alt="Gym-logo">
                                @else
                                    <h3 class="no-margin display-inline-block pull-left"> {{ $settings['gym_name'] }}</h3>
                                @endif

                                <h4 class="pull-right no-margin">Invoice # FINV{{ $foodOrder->id}}</h4>
                            </div>

                            <div class="row"> <!-- Inner row -->
                                <div class="col-xs-6"> <!--Left Side Details -->
                                    <address>
                                        <strong>Payment Mode</strong><br>
                                        {{ \Utilities::getPaymentMode($foodOrder->payment_mode) }}<br>
                                    </address>
                                </div>
                                <div class="col-xs-6 text-right"> <!--Right Side Details -->
                                    <address>
                                        <strong>Gym Address</strong><br>
                                        {{ $settings['gym_address_1'] }}<br>
                                        {{ $settings['gym_address_2'] }}<br>
                                        <strong>Generated On</strong><br>
                                        {{ $foodOrder->created_at->toDayDateTimeString()}}<br>
                                    </address>
                                </div>
                            </div>        <!-- / inner row -->

                            <!--Food Invoice Details view -->

                            <div class="bg-amber-50 padding-md margin-bottom-20 margin-top-20" id="invoiceBlock">
                                <h4 class="margin-bottom-30 color-grey-700">Invoice Details</h4>

                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <td><strong>Item</strong></td>
                                            <td class="text-center"><strong>Quantity</strong></td>
                                            <td class="text-right"><strong>Amount</strong></td>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($foodOrder->orderItems as $orderItem)
                                            <tr>
                                                <td>
                                                    @if($orderItem->food_item_id)
                                                        {{ $orderItem->foodItem->name }}
                                                    @elseif($orderItem->inventory_id)
                                                        {{ $orderItem->inventory->name }}
                                                    @endif
                                                </td>
                                                <td class="text-center">{{ $orderItem->quantity }}</td>
                                                <td class="text-right">{{ number_format($orderItem->item_amount * $orderItem->quantity, 0) }}</td>
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <td class="text-left"><strong>Total</strong></td>
                                            <td></td>
                                            <td class="text-right"><strong>{{ number_format($foodOrder->total_amount, 0) }}</strong></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div> <!-- / Panel - body no padding -->

                        <!-- Footer buttons -->
                        <div class="panel-footer bg-white no-padding-top padding-bottom-20 hidden-print">
                            @permission(['manage-gymie','manage-food','order-food'])
                            <button class="btn btn-primary pull-right" onclick="window.print();"><i class="ion-printer margin-right-5"></i>
                                Print
                            </button>
                            @endpermission
                        </div> <!-- / Footer buttons -->

                    </div> <!-- / Main Panel-->
                </div> <!-- / Main Column -->
            </div><!-- / Main row -->

        </div> <!-- / Container Fluid -->
    </div> <!-- / Right Side -->

@stop

