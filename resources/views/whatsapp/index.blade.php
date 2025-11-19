@extends('app')

@section('content')

    <div class="rightside bg-grey-100">
        <!-- BEGIN PAGE HEADING -->
        <div class="page-head bg-grey-100 padding-top-15 no-padding-bottom">
            @include('flash::message')
            <h1 class="page-title no-line-height">WhatsApp Message History
                <small>History of all WhatsApp subscription reminders sent</small>
            </h1>
            @permission(['manage-gymie','pagehead-stats'])
            <h1 class="font-size-30 text-right color-blue-grey-600 animated fadeInDown total-count pull-right">
                <span data-toggle="counter" data-start="0"
                      data-from="0" data-to="{{ $totalCount }}"
                      data-speed="600"
                      data-refresh-interval="10"></span>
                <small class="color-blue-grey-600 display-block margin-top-5 font-size-14">Total Messages</small>
            </h1>
            @endpermission
        </div><!-- / PageHead -->

        <div class="container-fluid">
            <!-- Main row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel no-border ">

                        <div class="panel-title bg-blue-grey-50">
                            <div class="panel-head font-size-15">

                                <div class="row">
                                    <div class="col-sm-12 no-padding">
                                        {!! Form::Open(['method' => 'GET']) !!}

                                        <div class="col-sm-3">

                                            {!! Form::label('whatsapp-daterangepicker','Date range') !!}

                                            <div id="whatsapp-daterangepicker"
                                                 class="gymie-daterangepicker btn bg-grey-50 daterange-padding no-border color-grey-600 hidden-xs no-shadow">
                                                <i class="ion-calendar margin-right-10"></i>
                                                <span>{{$drp_placeholder}}</span>
                                                <i class="ion-ios-arrow-down margin-left-5"></i>
                                            </div>

                                            {!! Form::text('drp_start',null,['class'=>'hidden', 'id' => 'drp_start']) !!}
                                            {!! Form::text('drp_end',null,['class'=>'hidden', 'id' => 'drp_end']) !!}
                                        </div>

                                        <div class="col-sm-2">
                                            {!! Form::label('sort_field','Sort By') !!}
                                            {!! Form::select('sort_field',array('sent_at' => 'Sent Date','member_name' => 'Member Name','reminder_number' => 'Reminder'),old('sort_field'),['class' => 'form-control selectpicker show-tick show-menu-arrow', 'id' => 'sort_field']) !!}
                                        </div>

                                        <div class="col-sm-2">
                                            {!! Form::label('sort_direction','Order') !!}
                                            {!! Form::select('sort_direction',array('desc' => 'Descending','asc' => 'Ascending'),old('sort_direction'),['class' => 'form-control selectpicker show-tick show-menu-arrow', 'id' => 'sort_direction']) !!}</span>
                                        </div>

                                        <div class="col-xs-4">
                                            {!! Form::label('search','Keyword') !!}
                                            <input value="{{ old('search') }}" name="search" id="search" type="text" class="form-control padding-right-35"
                                                   placeholder="Search by member name, phone number...">
                                        </div>

                                        <div class="col-xs-1">
                                            {!! Form::label('&nbsp;') !!} <br/>
                                            <button type="submit" class="btn btn-primary active no-border">GO</button>
                                        </div>

                                        {!! Form::Close() !!}
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="panel-body bg-white">
                            @if($messages->count() == 0)
                                <h4 class="text-center padding-top-15">Sorry! No records found</h4>
                            @else
                                <table id="whatsapp-messages" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>Member Name</th>
                                        <th>Phone Number</th>
                                        <th>Reminder</th>
                                        <th>Message</th>
                                        <th>Status</th>
                                        <th>Sent At</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    @foreach ($messages as $message)

                                        <tr>
                                            <td>
                                                @if($message->member)
                                                    <a href="{{ action('MembersController@show',['id' => $message->member_id]) }}">{{ $message->member_name }}</a>
                                                @else
                                                    {{ $message->member_name ?? 'N/A' }}
                                                @endif
                                            </td>
                                            <td>{{ $message->phone_number }}</td>
                                            <td>
                                                @if($message->reminder_number == 1)
                                                    <span class="label label-primary">First Reminder</span>
                                                @elseif($message->reminder_number == 2)
                                                    <span class="label label-warning">Second Reminder</span>
                                                @elseif($message->reminder_number == 3)
                                                    <span class="label label-danger">Third Reminder</span>
                                                @else
                                                    <span class="label label-default">Unknown</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div style="max-width: 300px; word-wrap: break-word;">
                                                    {{ $message->message }}
                                                </div>
                                            </td>
                                            <td>
                                                @if($message->status == 'sent')
                                                    <span class="label label-success">Sent</span>
                                                @elseif($message->status == 'failed')
                                                    <span class="label label-danger">Failed</span>
                                                    @if($message->error_message)
                                                        <br><small class="text-danger">{{ str_limit($message->error_message, 50) }}</small>
                                                    @endif
                                                @else
                                                    <span class="label label-default">{{ ucfirst($message->status) }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $message->sent_at ? $message->sent_at->format('d-m-Y H:i:s') : 'N/A' }}</td>
                                        </tr>

                                    @endforeach

                                    </tbody>
                                </table>

                                <div class="row">
                                    <div class="col-xs-6">
                                        <div class="gymie_paging_info">
                                            Showing page {{ $messages->currentPage() }} of {{ $messages->lastPage() }}
                                        </div>
                                    </div>

                                    <div class="col-xs-6">
                                        <div class="gymie_paging pull-right">
                                            {!! str_replace('/?', '?', $messages->appends(Input::all())->render()) !!}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('footer_script_init')
    <script type="text/javascript">
        $(document).ready(function () {
            // Initialize date range picker
            if ($('#whatsapp-daterangepicker').length) {
                $('#whatsapp-daterangepicker').daterangepicker({
                    format: 'DD-MM-YYYY',
                    startDate: moment().subtract(30, 'days'),
                    endDate: moment(),
                    locale: {
                        applyLabel: 'Apply',
                        cancelLabel: 'Clear',
                        fromLabel: 'From',
                        toLabel: 'To',
                    }
                }, function(start, end, label) {
                    $('#drp_start').val(start.format('YYYY-MM-DD'));
                    $('#drp_end').val(end.format('YYYY-MM-DD'));
                    $('#whatsapp-daterangepicker span').html(start.format('DD-MM-YYYY') + ' - ' + end.format('DD-MM-YYYY'));
                });

                // Handle clear
                $('#whatsapp-daterangepicker').on('cancel.daterangepicker', function(ev, picker) {
                    $('#drp_start').val('');
                    $('#drp_end').val('');
                    $('#whatsapp-daterangepicker span').html('Select daterange filter');
                });

                // Set initial values if they exist
                @if(request('drp_start') && request('drp_end'))
                    var start = moment('{{ request('drp_start') }}', 'YYYY-MM-DD');
                    var end = moment('{{ request('drp_end') }}', 'YYYY-MM-DD');
                    $('#whatsapp-daterangepicker').data('daterangepicker').setStartDate(start);
                    $('#whatsapp-daterangepicker').data('daterangepicker').setEndDate(end);
                @endif
            }
        });
    </script>
@stop

