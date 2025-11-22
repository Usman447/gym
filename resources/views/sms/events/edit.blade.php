@extends('app')

@section('content')

    <div class="rightside bg-grey-100">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel no-border">
                        <div class="panel-title">
                            <div class="panel-head font-size-20">Enter details of the sms event</div>
                        </div>

                        <form action="{{ action(['App\Http\Controllers\SmsController@updateEvent', $event->id]) }}" method="POST" id="smseventsform">
                            @csrf
                            @method('PUT')

                        @include('sms.events._form',['submitButtonText' => 'Update'])

                        </form>

                    </div>
                </div>
            </div>
        </div>

        @stop
        @section('footer_scripts')
            <script src="{{ URL::asset('assets/js/event.js') }}" type="text/javascript"></script>
@stop