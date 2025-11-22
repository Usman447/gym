@extends('app')

@section('content')

    <div class="rightside bg-grey-100">
        <!-- BEGIN PAGE HEADING -->
        <div class="page-head bg-grey-100 padding-top-15 no-padding-bottom">
            @include('flash::message')
            <h1 class="page-title">Settings</h1>
        </div>

        <div class="container-fluid">
        <form action="{{ url('settings/save') }}" method="POST" id="settingsform" enctype="multipart/form-data">
            @csrf
        <!-- General Settings -->
            <div class="row">
                <div class="col-md-12">
                    <div class="panel no-border">
                        <div class="panel-title">
                            <div class="panel-head font-size-15"><i class="fa fa-cogs"></i> General</div>
                        </div>

                        <div class="panel-body">
                            <div class="row">
                                <!--Main row start-->
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="gym_name">Gym Name</label>
                                        <input type="text" name="gym_name" value="{{ $settings['gym_name'] }}" class="form-control" id="gym_name">
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="financial_start">Financial year start</label>
                                        <input type="text" name="financial_start" value="{{ $settings['financial_start'] }}" class="form-control datepicker-default" id="financial_start">
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="financial_end">Financial year end</label>
                                        <input type="text" name="financial_end" value="{{ $settings['financial_end'] }}" class="form-control datepicker-default" id="financial_end">
                                    </div>
                                </div>


                            </div>

                            <div class="row">
                                @if($settings['gym_logo'] != "")
                                    <div class="col-sm-4">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label for="gym_logo">Gym Logo</label><br>
                                                    <img alt="gym logo" src="{{url('/images/Invoice/'.'gym_logo'.'.jpg') }}"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <input type="file" name="gym_logo" class="form-control" id="gym_logo">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="gym_logo">Gym Logo</label>
                                            <input type="file" name="gym_logo" class="form-control" id="gym_logo">
                                        </div>
                                    </div>
                                @endif

                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="gym_address_1">Gym Address line 1</label>
                                                <input type="text" name="gym_address_1" value="{{ $settings['gym_address_1'] }}" class="form-control" id="gym_address_1">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="gym_address_2">Gym Address Line 2</label>
                                                <input type="text" name="gym_address_2" value="{{ $settings['gym_address_2'] }}" class="form-control" id="gym_address_2">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!--Invoice Settings -->
            <div class="row">
                <div class="col-md-12">
                    <div class="panel no-border">
                        <div class="panel-title">
                            <div class="panel-head font-size-15"><i class="fa fa-file"></i> Invoice</div>
                        </div>
                        <div class="panel-body">
                            <div class="row">                <!--Main row start-->
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="invoice_prefix">Invoice prefix</label>
                                                <input type="text" name="invoice_prefix" value="{{ $settings['invoice_prefix'] }}" class="form-control" id="invoice_prefix">
                                            </div>
                                        </div>

                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="invoice_last_number">Invoice Last Number</label>
                                                <input type="text" name="invoice_last_number" value="{{ $settings['invoice_last_number'] }}" class="form-control" id="invoice_last_number" readonly>
                                            </div>
                                        </div>

                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="invoice_name_type">Invoice name type</label>
                                                <select name="invoice_name_type" class="form-control selectpicker show-tick show-menu-arrow" id="invoice_name_type">
                                                    <option value="gym_logo" {{ $settings['invoice_name_type'] == 'gym_logo' ? 'selected' : '' }}>Gym Logo</option>
                                                    <option value="gym_name" {{ $settings['invoice_name_type'] == 'gym_name' ? 'selected' : '' }}>Gym Name</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="invoice_number_mode">Invoice number mode</label>
                                                <select name="invoice_number_mode" class="form-control selectpicker show-tick show-menu-arrow" id="invoice_number_mode">
                                                    <option value="0" {{ $settings['invoice_number_mode'] == '0' ? 'selected' : '' }}>Manual</option>
                                                    <option value="1" {{ $settings['invoice_number_mode'] == '1' ? 'selected' : '' }}>Automatic</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- member Settings -->
            <div class="row">
                <div class="col-md-12">
                    <div class="panel no-border">
                        <div class="panel-title">
                            <div class="panel-head font-size-15"><i class="fa fa-users"></i> Member</div>
                        </div>

                        <div class="panel-body">
                            <div class="row"><!--Main row start-->
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="member_prefix">Member Prefix</label>
                                                <input type="text" name="member_prefix" value="{{ $settings['member_prefix'] }}" class="form-control" id="member_prefix">
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="member_last_number">Member Last Number</label>
                                                <input type="text" name="member_last_number" value="{{ $settings['member_last_number'] }}" class="form-control" id="member_last_number" readonly>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="member_number_mode">Member number mode</label>
                                                <select name="member_number_mode" class="form-control selectpicker show-tick show-menu-arrow" id="member_number_mode">
                                                    <option value="0" {{ $settings['member_number_mode'] == '0' ? 'selected' : '' }}>Manual</option>
                                                    <option value="1" {{ $settings['member_number_mode'] == '1' ? 'selected' : '' }}>Automatic</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charges Settings -->
            <div class="row">
                <div class="col-md-12">
                    <div class="panel no-border">
                        <div class="panel-title">
                            <div class="panel-head font-size-15"><i class="fa fa-dollar"></i> Charges</div>
                        </div>

                        <div class="panel-body">
                            <div class="row"><!--Main row start-->
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="admission_fee">Admission Fee</label>
                                                <div class="input-group">
                                                    <div class="input-group-addon"><i class="fa fa-inr"></i></div>
                                                    <input type="text" name="admission_fee" value="{{ $settings['admission_fee'] }}" class="form-control" id="admission_fee" readonly>
                                                </div>
                                            </div>
                                        </div>

                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- WhatsApp Settings -->
            <div class="row">
                <div class="col-md-12">
                    <div class="panel no-border">
                        <div class="panel-title">
                            <div class="panel-head font-size-15"><i class="fa fa-whatsapp"></i> WhatsApp Automation</div>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="whatsapp_automation_enabled">Enable WhatsApp Automation</label>
                                                <select name="whatsapp_automation_enabled" class="form-control selectpicker show-tick show-menu-arrow" id="whatsapp_automation_enabled">
                                                    <option value="0" {{ ($settings['whatsapp_automation_enabled'] ?? '0') == '0' ? 'selected' : '' }}>Disabled</option>
                                                    <option value="1" {{ ($settings['whatsapp_automation_enabled'] ?? '0') == '1' ? 'selected' : '' }}>Enabled</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="whatsapp_automation_interval">Automation Interval (minutes)</label>
                                                <input type="text" name="whatsapp_automation_interval" value="{{ $settings['whatsapp_automation_interval'] ?? '30' }}" class="form-control" id="whatsapp_automation_interval" readonly>
                                                <small class="help-block">How often the automation script runs (default: 30 minutes)</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="whatsapp_start_time">Start Time</label>
                                                <input type="time" name="whatsapp_start_time" value="{{ $settings['whatsapp_start_time'] ?? '09:00' }}" class="form-control" id="whatsapp_start_time">
                                                <small class="help-block">Earliest time to send messages (24-hour format, e.g., 09:00)</small>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="whatsapp_end_time">End Time</label>
                                                <input type="time" name="whatsapp_end_time" value="{{ $settings['whatsapp_end_time'] ?? '21:00' }}" class="form-control" id="whatsapp_end_time">
                                                <small class="help-block">Latest time to send messages (24-hour format, e.g., 21:00)</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <h4 class="margin-top-20">API Configuration</h4>
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="whatsapp_api_key">WhatsApp API Key (Account SID)</label>
                                                <input type="text" name="whatsapp_api_key" value="{{ $settings['whatsapp_api_key'] ?? '' }}" class="form-control" id="whatsapp_api_key">
                                                <small class="help-block">Your Twilio Account SID (starts with "AC...")</small>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="whatsapp_api_secret">WhatsApp API Secret (Auth Token)</label>
                                                @if(isset($settings['whatsapp_api_secret']) && !empty($settings['whatsapp_api_secret']))
                                                    <input type="text" name="whatsapp_api_secret" value="{{ $settings['whatsapp_api_secret'] }}" class="form-control" id="whatsapp_api_secret" placeholder="Enter your Twilio Auth Token">
                                                    <small class="help-block">Your Twilio Auth Token (currently set - enter new value to update)</small>
                                                @else
                                                    <input type="text" name="whatsapp_api_secret" value="" class="form-control" id="whatsapp_api_secret" placeholder="Enter your Twilio Auth Token">
                                                    <small class="help-block">Your Twilio Auth Token (required for sending messages)</small>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="whatsapp_from_number">From Number</label>
                                                <input type="text" name="whatsapp_from_number" value="{{ $settings['whatsapp_from_number'] ?? '' }}" class="form-control" id="whatsapp_from_number">
                                                <small class="help-block">WhatsApp Business number to send from (e.g., +923001234567 for Pakistan)</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <h4 class="margin-top-20">Reminder Settings</h4>
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="whatsapp_reminder_2_days">Days for 2nd Reminder</label>
                                                <input type="text" name="whatsapp_reminder_2_days" value="{{ $settings['whatsapp_reminder_2_days'] ?? '5' }}" class="form-control" id="whatsapp_reminder_2_days">
                                                <small class="help-block">Days after end date to send 2nd reminder</small>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="whatsapp_reminder_3_days">Days for 3rd Reminder</label>
                                                <input type="text" name="whatsapp_reminder_3_days" value="{{ $settings['whatsapp_reminder_3_days'] ?? '7' }}" class="form-control" id="whatsapp_reminder_3_days">
                                                <small class="help-block">Days after 2nd reminder to send 3rd reminder</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <h4 class="margin-top-20">Message Templates</h4>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="whatsapp_reminder_1_message">First Reminder Message</label>
                                                <textarea name="whatsapp_reminder_1_message" class="form-control" id="whatsapp_reminder_1_message" rows="3">{{ $settings['whatsapp_reminder_1_message'] ?? '' }}</textarea>
                                                <small class="help-block">Variables: {member_name}, {end_date}</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="whatsapp_reminder_2_message">Second Reminder Message</label>
                                                <textarea name="whatsapp_reminder_2_message" class="form-control" id="whatsapp_reminder_2_message" rows="3">{{ $settings['whatsapp_reminder_2_message'] ?? '' }}</textarea>
                                                <small class="help-block">Variables: {member_name}, {end_date}, {days_ago}</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="whatsapp_reminder_3_message">Third Reminder Message</label>
                                                <textarea name="whatsapp_reminder_3_message" class="form-control" id="whatsapp_reminder_3_message" rows="3">{{ $settings['whatsapp_reminder_3_message'] ?? '' }}</textarea>
                                                <small class="help-block">Variables: {member_name}, {end_date}, {days_ago}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Submission -->
            <div class="row">
                <div class="col-sm-2 pull-right">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary pull-right">Save</button>
                    </div>
                </div>
            </div>
            </form>
        </div>
    </div>
@stop

@section('footer_scripts')
    <script src="{{ URL::asset('assets/js/setting.js') }}" type="text/javascript"></script>
@stop

@section('footer_script_init')
    <script type="text/javascript">
        gymie.loadBsTokenInput();
    </script>
@stop
