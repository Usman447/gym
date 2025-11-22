@extends('app')

@section('content')
    <?php use Carbon\Carbon; ?>
    <div class="rightside bg-grey-100">
        <div class="container-fluid">
            <form action="{{ action([App\Http\Controllers\SubscriptionsController::class, 'modify'], ['id' => $subscription->id]) }}" method="POST" id="subscriptionschangeform">
                @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="panel no-border">
                        <div class="panel-title">
                            <div class="panel-head font-size-20">Enter details of the subscription</div>
                        </div>


                        <div class="panel-body">

                            <div class="row">
                                <div class="col-sm-3">

                                    <label for="member_id">Member Code</label>

                                </div>

                                <div class="col-sm-3">
                                    <label for="plan_0">Plan</label>
                                </div>

                                <div class="col-sm-3">
                                    <label for="start_date_0">Start Date</label>
                                </div>

                                <div class="col-sm-3">
                                    <label for="end_date_0">End Date</label>
                                </div>


                            </div> <!-- / Row -->
                            <div id="servicesContainer">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <input type="text" name="member_id" value="{{ $subscription->member->member_code }}" class="form-control" id="member_id" readonly>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group plan-id">
                                            <?php $plans = App\Plan::where('status', '=', '1')->get(); ?>

                                            <select id="plan_0" name="plan[0][id]" class="form-control selectpicker show-tick show-menu-arrow childPlan"
                                                    data-live-search="true" data-row-id="0">
                                                @foreach($plans as $plan)
                                                    <option value="{{ $plan->id }}"
                                                            {{ ($plan->id == $subscription->plan_id ? "selected" : "") }} data-price="{{ $plan->amount }}"
                                                            data-days="{{ $plan->days }}" data-row-id="0">{{ $plan->plan_display }} </option>
                                                @endforeach
                                            </select>
                                            <div class="plan-price">
                                                <input type="hidden" name="plan[0][price]" value="" id="price_0">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-3">
                                        <div class="form-group plan-start-date">
                                            <input type="text" name="plan[0][start_date]" value="{{ $subscription->start_date->format('Y-m-d') }}" class="form-control datepicker-startdate childStartDate" id="start_date_0" data-row-id="0">
                                        </div>
                                    </div>

                                    <div class="col-sm-3">
                                        <div class="form-group plan-end-date">
                                            <input type="text" name="plan[0][end_date]" value="{{ $subscription->end_date->format('Y-m-d') }}" class="form-control childEndDate" id="end_date_0" readonly="readonly" data-row-id="0">
                                        </div>
                                    </div>

                                </div> <!-- / Row -->
                            </div>

                        </div>


                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel no-border">
                        <div class="panel-title">
                            <div class="panel-head font-size-20">Enter details of the invoice</div>
                        </div>

                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="invoice_number">Invoice Number</label>
                                        <input type="text" name="invoice_number" value="{{ $subscription->invoice->invoice_number }}" class="form-control" id="invoice_number" readonly>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="subscription_amount">Gym subscription fee</label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-inr"></i></div>
                                            <input type="text" name="subscription_amount" value="{{ $subscription->invoice->total }}" class="form-control" id="subscription_amount" readonly="readonly">
                                        </div>
                                    </div>
                                </div>

                                
                            </div> <!-- /Row -->

                            <div class="row">
                                
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="discount_amount">Discount amount</label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-inr"></i></div>
                                            <input type="text" name="discount_amount" value="{{ $subscription->invoice->discount_amount }}" class="form-control" id="discount_amount">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="discount_note">Discount note</label>
                                        <input type="text" name="discount_note" value="{{ $subscription->invoice->discount_note }}" class="form-control" id="discount_note">
                                    </div>
                                </div>
                            </div>

                        </div> <!-- /Box-body -->

                    </div> <!-- /Box -->
                </div> <!-- /Main Column -->
            </div> <!-- /Main Row -->


            <div class="row">
                <div class="col-md-12">
                    <div class="panel no-border">
                        <div class="panel-title">
                            <div class="panel-head font-size-20">Enter details of the payment</div>
                        </div>

                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="previous_payment">Already paid</label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-inr"></i></div>
                                            <input type="text" name="previous_payment" value="{{ $already_paid == null ? '0' : $already_paid }}" class="form-control" id="previous_payment">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="payment_amount">Amount Received</label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-inr"></i></div>
                                            <input type="text" name="payment_amount" value="" class="form-control" id="payment_amount" data-amounttotal="0">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="payment_amount_pending">Amount Pending</label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-inr"></i></div>
                                            <input type="text" name="payment_amount_pending" value="" class="form-control" id="payment_amount_pending" readonly>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="mode">Mode</label>
                                        <select name="mode" class="form-control selectpicker show-tick show-menu-arrow" id="mode">
                                            <option value="1" selected>Cash</option>
                                            <option value="2">Online</option>
                                        </select>
                                    </div>
                                </div>

                            </div> <!-- /Row -->

                        </div> <!-- /Box-body -->

                    </div> <!-- /Box -->
                </div> <!-- /Main Column -->
            </div> <!-- /Main Row -->
            <div class="row">
                <div class="col-sm-2 pull-right">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary pull-right">Change</button>
                    </div>
                </div>
            </div>

            </form>
        </div>
    </div>

@stop

@section('footer_scripts')
    <script src="{{ URL::asset('assets/js/subscriptionChange.js') }}" type="text/javascript"></script>
@stop

@section('footer_script_init')
    <script type="text/javascript">
        $(document).ready(function () {
            gymie.loaddatepickerstart();
            gymie.chequedetails();
            gymie.subscription();
            gymie.subscriptionChange();
        });
    </script>
@stop
