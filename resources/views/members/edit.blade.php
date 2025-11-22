@extends('app')

@section('content')

    <div class="rightside bg-grey-100">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <form action="{{ action([App\Http\Controllers\MembersController::class, 'update'], ['id' => $member->id]) }}" method="POST" id="membersform" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')


                    <div class="panel no-border">
                        <div class="panel-title">
                            <div class="panel-head font-size-20">Enter details of the member</div>
                        </div>
                        <div class="panel-body">

                            @include('members.form')
                        </div><!-- / Panel Body -->
                    </div><!-- / Panel-no-border -->

                    <div class="row">
                        <div class="col-sm-2 pull-right">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary pull-right">Update</button>
                            </div>
                        </div>
                    </div>

                    </form>

                </div><!-- / Main Col -->
            </div><!-- / Main Row -->
        </div><!-- / Container -->
    </div><!-- / Rightside -->

@stop
@section('footer_scripts')
    <script src="{{ URL::asset('assets/js/member.js') }}" type="text/javascript"></script>
@stop