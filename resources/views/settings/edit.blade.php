@extends('app')

@section('content')

    <div class="rightside bg-grey-100">
        <!-- BEGIN PAGE HEADING -->
        <div class="page-head bg-grey-100 padding-top-15 no-padding-bottom">
            @include('flash::message')
            <h1 class="page-title">Settings</h1>
        </div>

        <div class="container-fluid">
        {!! Form::Open(['url' => 'settings/save','id'=>'settingsform','files'=>'true']) !!}
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
                                        {!! Form::label('gym_name','Gym Name') !!}
                                        {!! Form::text('gym_name',$settings['gym_name'],['class'=>'form-control', 'id' => 'gym_name']) !!}
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('financial_start','Financial year start') !!}
                                        {!! Form::text('financial_start',$settings['financial_start'],['class'=>'form-control datepicker-default', 'id' => 'financial_start']) !!}
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('financial_end','Financial year end') !!}
                                        {!! Form::text('financial_end',$settings['financial_end'],['class'=>'form-control datepicker-default', 'id' => 'financial_end']) !!}
                                    </div>
                                </div>


                            </div>

                            <div class="row">
                                @if($settings['gym_logo'] != "")
                                    <div class="col-sm-4">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    {!! Form::label('gym_logo','Gym Logo') !!}<br>
                                                    <img alt="gym logo" src="{{url('/images/Invoice/'.'gym_logo'.'.jpg') }}"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    {!! Form::file('gym_logo',['class'=>'form-control', 'id' => 'gym_logo']) !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            {!! Form::label('gym_logo','Gym Logo') !!}
                                            {!! Form::file('gym_logo',['class'=>'form-control', 'id' => 'gym_logo']) !!}
                                        </div>
                                    </div>
                                @endif

                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                {!! Form::label('gym_address_1','Gym Address line 1') !!}
                                                {!! Form::text('gym_address_1',$settings['gym_address_1'],['class'=>'form-control', 'id' => 'gym_address_1']) !!}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                {!! Form::label('gym_address_2','Gym Address Line 2') !!}
                                                {!! Form::text('gym_address_2',$settings['gym_address_2'],['class'=>'form-control', 'id' => 'gym_address_2']) !!}
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
                                                {!! Form::label('invoice_prefix','Invoice prefix') !!}
                                                {!! Form::text('invoice_prefix',$settings['invoice_prefix'],['class'=>'form-control', 'id' => 'invoice_prefix']) !!}
                                            </div>
                                        </div>

                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                {!! Form::label('invoice_last_number','Invoice Last Number') !!}
                                                {!! Form::text('invoice_last_number',$settings['invoice_last_number'],['class'=>'form-control', 'id' => 'invoice_last_number', 'readonly' => '']) !!}
                                            </div>
                                        </div>

                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                {!! Form::label('invoice_name_type','Invoice name type') !!}
                                                {!! Form::select('invoice_name_type',array('gym_logo' => 'Gym Logo', 'gym_name' => 'Gym Name'),$settings['invoice_name_type'],['class'=>'form-control selectpicker show-tick show-menu-arrow', 'id' => 'invoice_name_type']) !!}
                                            </div>
                                        </div>

                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                {!! Form::label('invoice_number_mode','Invoice number mode') !!}
                                                {!! Form::select('invoice_number_mode',array('0' => 'Manual', '1' => 'Automatic'),$settings['invoice_number_mode'],['class'=>'form-control selectpicker show-tick show-menu-arrow', 'id' => 'invoice_number_mode']) !!}
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
                                                {!! Form::label('member_prefix','Member Prefix') !!}
                                                {!! Form::text('member_prefix',$settings['member_prefix'],['class'=>'form-control', 'id' => 'member_prefix']) !!}
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                {!! Form::label('member_last_number','Member Last Number') !!}
                                                {!! Form::text('member_last_number',$settings['member_last_number'],['class'=>'form-control', 'id' => 'member_last_number', 'readonly' => '']) !!}
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                {!! Form::label('member_number_mode','Member number mode') !!}
                                                {!! Form::select('member_number_mode',array('0' => 'Manual', '1' => 'Automatic'),$settings['member_number_mode'],['class'=>'form-control selectpicker show-tick show-menu-arrow', 'id' => 'member_number_mode']) !!}
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
                                                {!! Form::label('admission_fee','Admission Fee') !!}
                                                <div class="input-group">
                                                    <div class="input-group-addon"><i class="fa fa-inr"></i></div>
                                                    {!! Form::text('admission_fee',$settings['admission_fee'],['class'=>'form-control', 'id' => 'admission_fee', 'readonly' => '']) !!}
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
                                                {!! Form::label('whatsapp_automation_enabled','Enable WhatsApp Automation') !!}
                                                {!! Form::select('whatsapp_automation_enabled',array('0' => 'Disabled', '1' => 'Enabled'),$settings['whatsapp_automation_enabled'] ?? '0',['class'=>'form-control selectpicker show-tick show-menu-arrow', 'id' => 'whatsapp_automation_enabled']) !!}
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                {!! Form::label('whatsapp_automation_interval','Automation Interval (minutes)') !!}
                                                {!! Form::text('whatsapp_automation_interval',$settings['whatsapp_automation_interval'] ?? '30',['class'=>'form-control', 'id' => 'whatsapp_automation_interval', 'readonly' => '']) !!}
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
                                                {!! Form::label('whatsapp_start_time','Start Time') !!}
                                                {!! Form::time('whatsapp_start_time',$settings['whatsapp_start_time'] ?? '09:00',['class'=>'form-control', 'id' => 'whatsapp_start_time']) !!}
                                                <small class="help-block">Earliest time to send messages (24-hour format, e.g., 09:00)</small>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                {!! Form::label('whatsapp_end_time','End Time') !!}
                                                {!! Form::time('whatsapp_end_time',$settings['whatsapp_end_time'] ?? '21:00',['class'=>'form-control', 'id' => 'whatsapp_end_time']) !!}
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
                                                {!! Form::label('whatsapp_api_key','WhatsApp API Key (Account SID)') !!}
                                                {!! Form::text('whatsapp_api_key',$settings['whatsapp_api_key'] ?? '',['class'=>'form-control', 'id' => 'whatsapp_api_key']) !!}
                                                <small class="help-block">Your Twilio Account SID (starts with "AC...")</small>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                {!! Form::label('whatsapp_api_secret','WhatsApp API Secret (Auth Token)') !!}
                                                @if(isset($settings['whatsapp_api_secret']) && !empty($settings['whatsapp_api_secret']))
                                                    {!! Form::text('whatsapp_api_secret',$settings['whatsapp_api_secret'],['class'=>'form-control', 'id' => 'whatsapp_api_secret', 'placeholder' => 'Enter your Twilio Auth Token']) !!}
                                                    <small class="help-block">Your Twilio Auth Token (currently set - enter new value to update)</small>
                                                @else
                                                    {!! Form::text('whatsapp_api_secret','',['class'=>'form-control', 'id' => 'whatsapp_api_secret', 'placeholder' => 'Enter your Twilio Auth Token']) !!}
                                                    <small class="help-block">Your Twilio Auth Token (required for sending messages)</small>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                {!! Form::label('whatsapp_from_number','From Number') !!}
                                                {!! Form::text('whatsapp_from_number',$settings['whatsapp_from_number'] ?? '',['class'=>'form-control', 'id' => 'whatsapp_from_number']) !!}
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
                                                {!! Form::label('whatsapp_reminder_2_days','Days for 2nd Reminder') !!}
                                                {!! Form::text('whatsapp_reminder_2_days',$settings['whatsapp_reminder_2_days'] ?? '5',['class'=>'form-control', 'id' => 'whatsapp_reminder_2_days']) !!}
                                                <small class="help-block">Days after end date to send 2nd reminder</small>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                {!! Form::label('whatsapp_reminder_3_days','Days for 3rd Reminder') !!}
                                                {!! Form::text('whatsapp_reminder_3_days',$settings['whatsapp_reminder_3_days'] ?? '7',['class'=>'form-control', 'id' => 'whatsapp_reminder_3_days']) !!}
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
                                                {!! Form::label('whatsapp_reminder_1_message','First Reminder Message') !!}
                                                {!! Form::textarea('whatsapp_reminder_1_message',$settings['whatsapp_reminder_1_message'] ?? '',['class'=>'form-control', 'id' => 'whatsapp_reminder_1_message', 'rows' => 3]) !!}
                                                <small class="help-block">Variables: {member_name}, {end_date}</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                {!! Form::label('whatsapp_reminder_2_message','Second Reminder Message') !!}
                                                {!! Form::textarea('whatsapp_reminder_2_message',$settings['whatsapp_reminder_2_message'] ?? '',['class'=>'form-control', 'id' => 'whatsapp_reminder_2_message', 'rows' => 3]) !!}
                                                <small class="help-block">Variables: {member_name}, {end_date}, {days_ago}</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                {!! Form::label('whatsapp_reminder_3_message','Third Reminder Message') !!}
                                                {!! Form::textarea('whatsapp_reminder_3_message',$settings['whatsapp_reminder_3_message'] ?? '',['class'=>'form-control', 'id' => 'whatsapp_reminder_3_message', 'rows' => 3]) !!}
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
                        {!! Form::submit('Save', ['class' => 'btn btn-primary pull-right']) !!}
                    </div>
                </div>
            </div>
            {!! Form::Close() !!}
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
