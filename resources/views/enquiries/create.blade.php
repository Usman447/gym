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

            <form action="{{ url('enquiries') }}" method="POST" id="enquiriesform" enctype="multipart/form-data">
                @csrf
        <!-- Enquiry Details -->
            <div class="row">
                <div class="col-md-12">
                    <div class="panel no-border">
                        <div class="panel-title">
                            <div class="panel-head font-size-20">Enter details of the enquiry</div>
                        </div>
                        <div class="panel-body">
                            @include('enquiries.form')
                        </div>
                    </div>
                </div>
            </div>

            <!-- Follow Up form -->
            <div class="row">
                <div class="col-md-12">
                    <div class="panel no-border">
                        <div class="panel-title">
                            <div class="panel-head font-size-20">Enter details of the follow up</div>
                        </div>
                        <div class="panel-body">
                            @include('enquiries._followUp')
                        </div>
                    </div>
                </div>
            </div>

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
    <script src="{{ URL::asset('assets/js/enquiry.js') }}" type="text/javascript"></script>
@stop