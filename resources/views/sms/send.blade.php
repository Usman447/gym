@extends('app')

@section('content')

    <div class="rightside bg-grey-100">
        <div class="container-fluid">

        @include('flash::message')

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

            <div class="row">
                <div class="col-md-12">
                    <div class="panel no-border">
                        <div class="panel-title">
                            <div class="panel-head font-size-20">Enter details of the message</div>
                        </div>

                        <form action="{{ url('sms/shoot') }}" method="POST" id="sendform">
                            @csrf
                        <?php
                        $count = collect(array_filter(explode(',', \Utilities::getSetting('sender_id_list'))))->count();
                        $senderIds = explode(',', \Utilities::getSetting('sender_id_list'));
                        ?>

                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="send_to">Send To</label> </br>
                                        <div class="checkbox checkbox-theme display-inline-block">
                                            <input type="checkbox" name="send[]" id="activeMembers" value="0">
                                            <label for="activeMembers" class="padding-left-30">Active members</label>
                                        </div>

                                        <div class="checkbox checkbox-theme display-inline-block">
                                            <input type="checkbox" name="send[]" id="inactiveMembers" value="1">
                                            <label for="inactiveMembers" class="padding-left-30">Inactive members</label>
                                        </div>

                                        <div class="checkbox checkbox-theme display-inline-block margin-right-5">
                                            <input type="checkbox" name="send[]" id="leadEnquiries" value="2">
                                            <label for="leadEnquiries" class="padding-left-30">Lead enquiries</label>
                                        </div>

                                        <div class="checkbox checkbox-theme display-inline-block margin-right-11">
                                            <input type="checkbox" name="send[]" id="lostEnquiries" value="3">
                                            <label for="lostEnquiries" class="padding-left-30">Lost enquiries</label>
                                        </div>

                                        <div class="checkbox checkbox-theme display-inline-block margin-right-5">
                                            <input type="checkbox" name="send[]" id="custom" value="4">
                                            <label for="custom" class="padding-left-30">Custom</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($count == 1)

                                <input type="hidden" name="sender_id" value="{{ \Utilities::getSetting('sms_sender_id') }}">

                            @elseif($count > 1)

                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="sender_id">Sender Id</label>
                                            <select id="sender_id" name="sender_id" class="form-control selectpicker show-tick">
                                                @foreach($senderIds as $senderId)
                                                    <option value="{{ $senderId }}">{{ $senderId }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                            @endif

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group" id="customcontactsdiv">
                                        <label for="customcontacts">Contact numbers</label>
                                        <input type="text" name="customcontacts" value="" class="form-control tokenfield" id="customcontacts" placeholder="Type 10 digit contact numbers and hit enter">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="message">Message text</label>
                                        <textarea name="message" class="form-control" id="message" rows="5"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary pull-right">Send Now</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>


@stop

@section('footer_scripts')
    <script src="{{ URL::asset('assets/js/send.js') }}" type="text/javascript"></script>
@stop

@section('footer_script_init')
    <script type="text/javascript">
        $(document).ready(function () {
            gymie.loadBsTokenInput();
            gymie.customsendmessage();
        });
    </script>
@stop     