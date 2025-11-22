@extends('app')

@section('content')

    <div class="rightside bg-grey-100">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel no-border">
                        <div class="panel-title">
                            <div class="panel-head font-size-20">Enter details of the enquiry</div>
                        </div>

                        <form action="{{ action(['App\Http\Controllers\EnquiriesController@update', $enquiry->id]) }}" method="POST" id="enquiriesform" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                        <div class="panel-body">
                            @include('enquiries.form')
                            <div class="row">
                                <div class="col-sm-2 pull-right">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary pull-right">Update</button>
                                    </div>
                                </div>
                            </div>
                        </div><!-- End of panel body -->

                        </form>

                    </div><!-- / Panel no-border -->
                </div><!-- / Col-md-12 -->
            </div><!-- / row -->
        </div><!-- / container -->
    </div><!-- / rightside -->

@stop
@section('footer_scripts')
    <script src="{{ URL::asset('assets/js/enquiry.js') }}" type="text/javascript"></script>
@stop