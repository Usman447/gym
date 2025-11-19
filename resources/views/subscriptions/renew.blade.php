@extends('app')

@section('content')
    <?php use Carbon\Carbon; ?>
    <div class="rightside bg-grey-100">
        <div class="container-fluid">

            <!-- Error Log -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <strong>Whoops!</strong> There were some problems with your input.<br><br>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {!! Form::Open(['url' => 'subscriptions','id'=>'subscriptionsform']) !!}
            {!! Form::hidden('invoiceCounter',$invoiceCounter) !!}

        <!-- Member Details -->
            <div class="row">
                <div class="col-md-12">
                    <div class="panel no-border">
                        <div class="panel-title">
                            <div class="panel-head font-size-20">Enter details of the subscription</div>
                        </div>

                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-5">
                                    <div class="form-group">
                                        {!! Form::label('member_id','Member Code') !!}
                                        {!! Form::text('member_code_display', $member->member_code . ' - ' . $member->name, ['class'=>'form-control', 'id'=>'member_code_display', 'readonly' => 'readonly']) !!}
                                        {!! Form::hidden('member_id', $member_id) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-5">
                                    {!! Form::label('plan_0','Plan') !!}
                                </div>

                                <div class="col-sm-3">
                                    {!! Form::label('start_date_0','Start Date') !!}
                                </div>

                                <div class="col-sm-3">
                                    {!! Form::label('end_date_0','End Date') !!}
                                </div>

                                <div class="col-sm-1">
                                    <label>&nbsp;</label><br/>
                                </div>
                            </div> <!-- / Row -->
                            <div id="servicesContainer">
                                <?php $x = 0; ?>
                                @foreach($subscriptions as $subscription)
                                    <?php
                                    $startDate = $subscription->end_date->addDays(1);
                                    $dateDiff = $subscription->start_date->diffInDays($subscription->end_date);
                                    $endDate = $subscription->end_date->addDays($dateDiff);
                                    ?>
                                    <div class="row">
                                        <div class="col-sm-5">
                                            <div class="form-group plan-id">
                                                <?php $plans = App\Plan::where('status', '=', '1')->get(); ?>

                                                <select id="plan_{{$x }}" name="plan[{{$x}}][id]"
                                                        class="form-control selectpicker show-tick show-menu-arrow childPlan" data-live-search="true"
                                                        data-row-id="{{ $x }}">
                                                    @foreach($plans as $plan)
                                                        <option value="{{ $plan->id }}"
                                                                {{ ($plan->id == $subscription->plan->id ? "selected" : "") }} data-price="{{ $plan->amount }}"
                                                                data-days="{{ $plan->days }}" data-row-id="{{ $x }}">{{ $plan->plan_display }} </option>
                                                    @endforeach
                                                </select>
                                                <div class="plan-price">
                                                    {!! Form::hidden("plan[$x][price]",'', array('id' => "price_$x")) !!}
                                                    {!! Form::hidden('previousSubscriptions[]',$subscription->id) !!}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-3">
                                            <div class="form-group plan-start-date">
                                                {!! Form::text("plan[$x][start_date]",$startDate->format('Y-m-d'),['class'=>'form-control datepicker-startdate childStartDate', 'id' => "start_date_$x", 'data-row-id' => "$x"]) !!}
                                            </div>
                                        </div>

                                        <div class="col-sm-3">
                                            <div class="form-group plan-end-date">
                                                {!! Form::text("plan[$x][end_date]",$endDate->format('Y-m-d'),['class'=>'form-control childEndDate', 'id' => "end_date_$x", 'readonly' => 'readonly','data-row-id' => "$x"]) !!}
                                            </div>
                                        </div>

                                        <div class="col-sm-1">
                                            <div class="form-group">
                                                    <span class="btn btn-sm btn-danger pull-right {{ ($x == 0 ? 'hide' : '') }} remove-service">
                                                      <i class="fa fa-times"></i>
                                                    </span>
                                            </div>
                                        </div>
                                    </div> <!-- / Row -->
                                    <?php $x++; ?>
                                @endforeach
                            </div>
                            <div class="row">
                                <div class="col-sm-2 pull-right">
                                    <div class="form-group">
                                        <span class="btn btn-sm btn-primary pull-right" id="addSubscription">Add</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Invoice Details -->
            <div class="row">
                <div class="col-md-12">
                    <div class="panel no-border">
                        <div class="panel-title">
                            <div class="panel-head font-size-20">Enter invoice details</div>
                        </div>

                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('invoice_number','Invoice Number') !!}
                                        {!! Form::text('invoice_number',$invoice_number,['class'=>'form-control', 'id' => 'invoice_number', ($invoice_number_mode == \constNumberingMode::Auto ? 'readonly' : '')]) !!}
                                    </div>
                                </div>

                                <!-- <div class="col-sm-4">
                                    <div class="form-group"> -->
                            {!! Form::hidden('admission_amount','Admission') !!}
                            {!! Form::hidden('admission_amount',0,['class'=>'form-control', 'id' => 'admission_amount']) !!}
                            <!-- </div>
                                </div> -->

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('subscription_amount','Subscription fee') !!}
                                        {!! Form::text('subscription_amount',null,['class'=>'form-control', 'id' => 'subscription_amount','readonly' => 'readonly']) !!}
                                    </div>
                                </div>

                                
                            </div> <!-- /Row -->

                            <div class="row">
                                @if($availableCredit > 0)
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('available_balance','Available Balance') !!}
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-inr"></i></div>
                                            {!! Form::text('available_balance', number_format($availableCredit, 0), ['class'=>'form-control', 'id' => 'available_balance', 'readonly' => 'readonly', 'style' => 'background-color: #d4edda;', 'data-raw-value' => $availableCredit]) !!}
                                        </div>
                                        <small class="text-success">Credit available for use</small>
                                    </div>
                                </div>
                                @elseif($dueAmount > 0)
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('due_amount','Due Amount') !!}
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-inr"></i></div>
                                            {!! Form::text('due_amount', number_format($dueAmount, 0), ['class'=>'form-control', 'id' => 'due_amount', 'readonly' => 'readonly', 'style' => 'background-color: #f8d7da;', 'data-raw-value' => $dueAmount]) !!}
                                        </div>
                                        <small class="text-danger">Outstanding amount from previous invoices</small>
                                    </div>
                                </div>
                                @endif
                                
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('discount_amount','Discount amount') !!}
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-inr"></i></div>
                                            {!! Form::text('discount_amount',null,['class'=>'form-control', 'id' => 'discount_amount']) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('discount_note','Discount note') !!}
                                        {!! Form::text('discount_note',null,['class'=>'form-control', 'id' => 'discount_note']) !!}
                                    </div>
                                </div>
                            </div>
                            
                            @if(count($outstandingInvoices) > 0)
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="alert alert-info">
                                        <strong>Outstanding Invoices:</strong> This member has {{ count($outstandingInvoices) }} outstanding invoice(s) that will be paid first before applying payment to the new subscription.
                                        <ul class="margin-top-10">
                                            @foreach($outstandingInvoices as $outstandingInvoice)
                                            <li>Invoice #{{ $outstandingInvoice->invoice_number }} - {{ \Utilities::getInvoiceStatus($outstandingInvoice->status) }} - Amount Due: ₹{{ number_format($outstandingInvoice->pending_amount, 0) }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            @endif

                        </div> <!-- /Box-body -->

                    </div> <!-- /Box -->
                </div> <!-- /Main Column -->
            </div> <!-- /Main Row -->

            <!-- Payment Details -->
            <div class="row">
                <div class="col-md-12">
                    <div class="panel no-border">
                        <div class="panel-title">
                            <div class="panel-head font-size-20">Enter payment details</div>
                        </div>

                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        {!! Form::label('payment_amount','Amount') !!}
                                        {!! Form::text('payment_amount',null,['class'=>'form-control', 'id' => 'payment_amount']) !!}
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        {!! Form::label('mode','Mode') !!}
                                        {!! Form::select('mode',array('1' => 'Cash', '2' => 'Online'),1,['class'=>'form-control selectpicker show-tick', 'id' => 'mode']) !!}
                                    </div>
                                </div>

                                
                            </div> <!-- /Row -->

                        </div> <!-- /Box-body -->

                    </div> <!-- /Box -->
                </div> <!-- /Main Column -->
            </div> <!-- /Main Row -->

            <!-- Submit Button Row -->
            <div class="row">
                <div class="col-sm-2 pull-right">
                    <div class="form-group">
                        {!! Form::submit('Create', ['class' => 'btn btn-primary pull-right']) !!}
                    </div>
                </div>
            </div>

            {!! Form::Close() !!}

        </div> <!-- content -->
    </div> <!-- rightside -->

@stop
@section('footer_scripts')
    <script src="{{ URL::asset('assets/js/subscription.js') }}" type="text/javascript"></script>
@stop
@section('footer_script_init')
    <script type="text/javascript">
        $(document).ready(function () {
            gymie.loaddatepickerstart();
            gymie.chequedetails();
            gymie.subscription();
            
            // Helper function to parse integer values, handling comma-separated numbers
            function parseInteger(value) {
                if (!value) return 0;
                // Remove commas and any whitespace, then parse as integer
                var cleaned = String(value).replace(/,/g, '').trim();
                var parsed = parseInt(cleaned, 10);
                return isNaN(parsed) ? 0 : parsed;
            }
            
            // Auto-calculate payment amount based on formula:
            // Payment is used FIRST, then credit covers remaining amount
            // Default: Show full subscription fee (user can modify - credit will cover remainder)
            function calculatePaymentAmount() {
                // Parse all values as integers, handling comma-separated numbers
                // Use data-raw-value if available for accurate calculation, otherwise parse from displayed value
                var subscriptionFee = parseInteger($('#subscription_amount').val());
                var discountAmount = parseInteger($('#discount_amount').val());
                
                // Use data-raw-value attribute if available (more reliable), otherwise parse from displayed value
                var availableCredit = parseInteger($('#available_balance').val());
                // var availableCreditEl = $('#available_balance');
                // var availableCredit = 0;
                // if (availableCreditEl.length && availableCreditEl.attr('data-raw-value') !== undefined) {
                //     availableCredit = parseInt(availableCreditEl.attr('data-raw-value'), 10) || 0;
                // } else {
                //     availableCredit = parseInteger(availableCreditEl.val());
                // }
                
                var dueAmount = parseInteger($('#due_amount').val());
                // var dueAmount = 0;
                // if (dueAmountEl.length && dueAmountEl.attr('data-raw-value') !== undefined) {
                //     dueAmount = parseInt(dueAmountEl.attr('data-raw-value'), 10) || 0;
                // } else {
                //     dueAmount = parseInteger(dueAmountEl.val());
                // }
                
                // Ensure all values are integers (not floats)
                subscriptionFee = Math.floor(subscriptionFee);
                discountAmount = Math.floor(discountAmount);
                availableCredit = Math.floor(availableCredit);
                dueAmount = Math.floor(dueAmount);
                
                var paymentAmount = 0;
                
                if (availableCredit > 0) {
                    // Member has credit: Payment is used first, then credit
                    // So if user wants to pay, they can pay, and credit will be used for remaining amount
                    // Default calculation: Subscription fee - Discount (user can pay full amount or partial)
                    // Credit will be applied automatically for remaining amount
                    paymentAmount = subscriptionFee - discountAmount;
                    // Note: User can modify this amount - whatever they pay will be used first, then credit
                } else if (dueAmount > 0) {
                    // Member has due: Subscription fee + Due Amount - Discount
                    paymentAmount = subscriptionFee + dueAmount - discountAmount;
                } else {
                    // No credit or due: Subscription fee - Discount
                    paymentAmount = subscriptionFee - discountAmount;
                }
                
                // Set payment amount (only if positive, else 0, and as integer)
                // IMPORTANT: If credit fully covers subscription fee, payment should be 0
                paymentAmount = Math.max(0, Math.floor(paymentAmount));
                $('#payment_amount').val(paymentAmount);
                
                // Log calculation for debugging (console only)
                console.log('Payment Amount Calculation:', {
                    subscriptionFee: subscriptionFee,
                    availableCredit: availableCredit,
                    dueAmount: dueAmount,
                    discountAmount: discountAmount,
                    calculatedPaymentAmount: paymentAmount
                });
            }
            
            // Calculate when subscription amount, discount, credit, or due amount changes
            $('#subscription_amount, #discount_amount, #available_balance, #due_amount').on('change keyup', function() {
                calculatePaymentAmount();
            });
            
            // Initial calculation on page load
            setTimeout(function() {
                calculatePaymentAmount();
            }, 500);
        });
    </script>
@stop