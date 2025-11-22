@extends('app')

@section('content')

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

            <form action="{{ url('subscriptions') }}" method="POST" id="subscriptionsform">
                @csrf
                <input type="hidden" name="invoiceCounter" value="{{ $invoiceCounter }}">

        <!-- Member Details -->
            <div class="row">
                <div class="col-md-12">
                    <div class="panel no-border">
                        <div class="panel-title">
                            <div class="panel-head font-size-20">Enter details of the subscription</div>
                        </div>
                        <div class="panel-body">
                            @include('subscriptions.form')
                        </div>
                    </div>
                </div>
            </div>

            @if(Request::is('subscriptions/create'))
            <!-- Invoice Details -->
                @include('subscriptions._invoice')

            <!-- Payment Details -->
                @include('subscriptions._payment')

            <!-- Submit Button Row -->
                <div class="row">
                    <div class="col-sm-2 pull-right">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary pull-right">Create</button>
                        </div>
                    </div>
                </div>

                </form>

            @endif

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
        });
    </script>
@stop