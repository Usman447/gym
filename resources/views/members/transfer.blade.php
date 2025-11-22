@extends('app')

@section('content')

    <?php
    use Carbon\Carbon;
    ?>
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

            <form action="{{ url('members') }}" method="POST" id="membersform" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="transfer_id" value="{{ $enquiry->id }}">
                <input type="hidden" name="memberCounter" value="{{ $memberCounter }}">
                <input type="hidden" name="invoiceCounter" value="{{ $invoiceCounter }}">
        <!-- Member Details -->
            <div class="row">
                <div class="col-md-12">
                    <div class="panel no-border">
                        <div class="panel-title">
                            <div class="panel-head font-size-20">Enter details of the member</div>
                        </div>

                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="name" class="control-label">Name</label>
                                        <input type="text" name="name" value="{{ $enquiry->name }}" class="form-control" id="name">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="photo">Photo</label>
                                        <input type="file" name="photo" class="form-control" id="photo">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="DOB">Date Of Birth</label>
                                        <input type="text" name="DOB" value="{{ $enquiry->DOB }}" class="form-control datepicker" id="DOB">
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="gender">Gender</label>
                                        <select name="gender" class="form-control selectpicker show-tick show-menu-arrow" id="gender">
                                            <option value="m">Male</option>
                                            <option value="f">Female</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="contact">Contact</label>
                                        <input type="text" name="contact" value="{{ $enquiry->contact }}" class="form-control" id="contact">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="text" name="email" value="{{ $enquiry->email }}" class="form-control" id="email">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="emergency_contact">Emergency contact</label>
                                        <input type="text" name="emergency_contact" value="" class="form-control" id="emergency_contact">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="health_issues">Health issues</label>
                                        <input type="text" name="health_issues" value="" class="form-control" id="health_issues">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="proof_name">Proof Name</label>
                                        <input type="text" name="proof_name" value="" class="form-control" id="proof_name">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="proof_photo">Proof Photo</label>
                                        <input type="file" name="proof_photo" class="form-control" id="proof_photo">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="member_code">Member Code</label>
                                        <input type="text" name="member_code" value="{{ $member_code }}" class="form-control" id="member_code" {{ $member_number_mode == \constNumberingMode::Auto ? 'readonly' : '' }}>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                    <label for="status">Status</label>
                                    <!--0 for inactive , 1 for active-->
                                        <select name="status" class="form-control selectpicker show-tick show-menu-arrow" id="status">
                                            <option value="1">Active</option>
                                            <option value="0">InActive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="pin_code" class="control-label">Pin Code</label>
                                        <input type="text" name="pin_code" value="{{ $enquiry->pin_code }}" class="form-control" id="pin_code">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="occupation">Occupation</label>
                                        <select name="occupation" class="form-control selectpicker show-tick show-menu-arrow" id="occupation">
                                            <option value="0">Student</option>
                                            <option value="1">Housewife</option>
                                            <option value="2">Self Employed</option>
                                            <option value="3">Professional</option>
                                            <option value="4">Freelancer</option>
                                            <option value="5">Others</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="aim" class="control-label">Why do you plan to join?</label>
                                        <select name="aim" class="form-control selectpicker show-tick show-menu-arrow" id="aim">
                                            <option value="0" {{ $enquiry->aim == '0' ? 'selected' : '' }}>Fitness</option>
                                            <option value="1" {{ $enquiry->aim == '1' ? 'selected' : '' }}>Networking</option>
                                            <option value="2" {{ $enquiry->aim == '2' ? 'selected' : '' }}>Body Building</option>
                                            <option value="3" {{ $enquiry->aim == '3' ? 'selected' : '' }}>Fatloss</option>
                                            <option value="4" {{ $enquiry->aim == '4' ? 'selected' : '' }}>Weightgain</option>
                                            <option value="5" {{ $enquiry->aim == '5' ? 'selected' : '' }}>Others</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="source" class="control-label">How do you came to know about us?</label>
                                        <select name="source" class="form-control selectpicker show-tick show-menu-arrow" id="source">
                                            <option value="0" {{ $enquiry->source == '0' ? 'selected' : '' }}>Promotions</option>
                                            <option value="1" {{ $enquiry->source == '1' ? 'selected' : '' }}>Word Of Mouth</option>
                                            <option value="2" {{ $enquiry->source == '2' ? 'selected' : '' }}>Others</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="address">Address</label>
                                        <textarea name="address" class="form-control" id="address">{{ $enquiry->address }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Subscription Details -->
            <div class="row">
                <div class="col-md-12">
                    <div class="panel no-border">
                        <div class="panel-title">
                            <div class="panel-head font-size-20">Enter details of the subscription</div>
                        </div>

                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-5">
                                    <label for="plan_0">Plan</label>
                                </div>

                                <div class="col-sm-3">
                                    <label for="start_date_0">Start Date</label>
                                </div>

                                <div class="col-sm-3">
                                    <label for="end_date_0">End Date</label>
                                </div>

                                <div class="col-sm-1">
                                    <label>&nbsp;</label><br/>
                                </div>
                            </div> <!-- / Row -->
                            <div id="servicesContainer">
                                <div class="row">
                                    <div class="col-sm-5">
                                        <div class="form-group plan-id">
                                            <?php $plans = App\Plan::where('status', '=', '1')->get(); ?>

                                            <select id="plan_0" name="plan[0][id]" class="form-control selectpicker show-tick show-menu-arrow childPlan"
                                                    data-live-search="true" data-row-id="0">
                                                @foreach($plans as $plan)
                                                    <option value="{{ $plan->id }}" data-price="{{ $plan->amount }}" data-days="{{ $plan->days }}"
                                                            data-row-id="0">{{ $plan->plan_display }} </option>
                                                @endforeach
                                            </select>
                                            <div class="plan-price">
                                                <input type="hidden" name="plan[0][price]" value="" id="price_0">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-3">
                                        <div class="form-group plan-start-date">
                                            <input type="text" name="plan[0][start_date]" value="{{ Carbon::today()->format('Y-m-d') }}" class="form-control datepicker-startdate childStartDate" id="start_date_0" data-row-id="0">
                                        </div>
                                    </div>

                                    <div class="col-sm-3">
                                        <div class="form-group plan-end-date">
                                            <input type="text" name="plan[0][end_date]" value="" class="form-control childEndDate" id="end_date_0" readonly="readonly" data-row-id="0">
                                        </div>
                                    </div>

                                    <div class="col-sm-1">
                                        <div class="form-group">
                            <span class="btn btn-sm btn-danger pull-right hide remove-service">
                              <i class="fa fa-times"></i>
                            </span>
                                        </div>
                                    </div>
                                </div> <!-- / Row -->
                            </div>
                            <div class="row">
                                <div class="col-sm-2 pull-right">
                                    <div class="form-group">
                                        <span class="btn btn-sm btn-primary pull-right" id="addSubscription">Add</span>
                                    </div>
                                </div>
                            </div>

                        </div> <!-- / Panel Body -->

                    </div> <!-- /Panel -->
                </div> <!-- /Main Column -->
            </div> <!-- /Main Row -->

            <!-- Invoice Details -->
            <div class="row">
                <div class="col-md-12">
                    <div class="panel no-border">
                        <div class="panel-title">
                            <div class="panel-head font-size-20">Enter details of the invoice</div>
                        </div>

                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="invoice_number">Invoice Number</label>
                                        <input type="text" name="invoice_number" value="{{ $invoice_number }}" class="form-control" id="invoice_number" {{ $invoice_number_mode == \constNumberingMode::Auto ? 'readonly' : '' }}>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="admission_amount">Admission</label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-inr"></i></div>
                                            <input type="text" name="admission_amount" value="{{ Utilities::getSetting('admission_fee') }}" class="form-control" id="admission_amount">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="subscription_amount">Gym subscription fee</label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-inr"></i></div>
                                            <input type="text" name="subscription_amount" value="" class="form-control" id="subscription_amount" readonly="readonly">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="taxes_amount">{{ sprintf('Tax @ %s %%',Utilities::getSetting('taxes')) }}</label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-inr"></i></div>
                                            <input type="text" name="taxes_amount" value="0" class="form-control" id="taxes_amount" readonly="readonly">
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- /Row -->

                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="discount_percent">Discount</label>
                                        <?php
                                        $discounts = explode(",", str_replace(" ", "", (Utilities::getSetting('discounts'))));
                                        $discounts_list = array_combine($discounts, $discounts);
                                        ?>
                                        <select id="discount_percent" name="discount_percent" class="form-control selectpicker show-tick show-menu-arrow">
                                            <option value="0">None</option>
                                            @foreach($discounts_list as $list)
                                                <option value="{{ $list }}">{{ $list.'%' }}</option>
                                            @endforeach
                                            <option value="custom">Custom(Rs.)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="discount_amount">Discount amount</label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-inr"></i></div>
                                            <input type="text" name="discount_amount" value="" class="form-control" id="discount_amount" readonly="readonly">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="discount_note">Discount note</label>
                                        <input type="text" name="discount_note" value="" class="form-control" id="discount_note">
                                    </div>
                                </div>
                            </div>

                        </div> <!-- /Panel-body -->

                    </div> <!-- /Panel -->
                </div> <!-- /Main Column -->
            </div> <!-- /Main Row -->

            <!-- Payment Details -->
            <div class="row">
                <div class="col-md-12">
                    <div class="panel no-border">
                        <div class="panel-title">
                            <div class="panel-head font-size-20">Enter details of the payment</div>
                        </div>

                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="payment_amount">Amount Received</label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-inr"></i></div>
                                            <input type="text" name="payment_amount" value="" class="form-control" id="payment_amount" data-amounttotal="0">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="payment_amount_pending">Amount Pending</label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-inr"></i></div>
                                            <input type="text" name="payment_amount_pending" value="" class="form-control" id="payment_amount_pending" readonly>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="mode">Mode</label>
                                        <select name="mode" class="form-control selectpicker show-tick" id="mode">
                                            <option value="1" selected>Cash</option>
                                            <option value="0">Cheque</option>
                                        </select>
                                    </div>
                                </div>
                            </div> <!-- /Row -->
                            <div class="row" id="chequeDetails">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="number">Cheque number</label>
                                        <input type="text" name="number" value="" class="form-control" id="number">
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="date">Cheque date</label>
                                        <input type="text" name="date" value="" class="form-control datepicker-default" id="date">
                                    </div>
                                </div>
                            </div>

                        </div> <!-- /Panel-body -->

                    </div> <!-- /Panel -->
                </div> <!-- /Main Column -->
            </div> <!-- /Main Row -->

            <!-- Submit Button Row -->
            <div class="row">
                <div class="col-sm-2 pull-right">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary pull-right">Create</button>
                    </div>
                </div>
            </div>

            </form>

        </div> <!-- content -->
    </div> <!-- rightside -->

@stop
@section('footer_scripts')
    <script src="{{ URL::asset('assets/js/member.js') }}" type="text/javascript"></script>
@stop
@section('footer_script_init')
    <script type="text/javascript">
        $(document).ready(function () {
            gymie.loaddatepickerstart();
            gymie.chequedetails();
            gymie.subscription();
        });
    </script>
@stop
