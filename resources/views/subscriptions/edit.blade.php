@extends('app')

@section('content')

    <div class="rightside bg-grey-100">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel no-border">
                        <div class="panel-title">
                            <div class="panel-head font-size-20">Enter details of the subscription</div>
                        </div>

                        <form action="{{ action([App\Http\Controllers\SubscriptionsController::class, 'update'], ['id' => $subscription->id]) }}" method="POST" id="subscriptionsform">
                            @csrf
                            @method('PUT')
                        <div class="panel-body">

                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="member_id">Member Code</label>

                                        <input type="text" name="member_display" value="{{ $subscription->member->member_code }}" class="form-control" id="member_display" readonly="readonly">
                                        <input type="hidden" name="member_id" value="{{ $subscription->member_id }}">

                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <?php $plans = App\Plan::where('status', '=', '1')->get(); ?>
                                        <label for="plan_id">Plan Name</label>
                                        <input type="text" name="plan_display" value="{{ $subscription->plan->plan_display }}" class="form-control plan-data" id="plan_display" readonly="readonly" data-days="{{ $subscription->plan->days }}">
                                        <input type="hidden" name="plan_id" value="{{ $subscription->plan_id }}">
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="start_date">Start Date</label>
                                        <input type="text" name="start_date" value="{{ $subscription->start_date->format('Y-m-d') }}" class="form-control" id="start_date" readonly>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="end_date">End Date</label>
                                        <input type="text" name="end_date" value="{{ $subscription->end_date->format('Y-m-d') }}" class="form-control datepicker-enddate" id="end_date">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-2 pull-right">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary pull-right">Update</button>
                    </div>
                </div>
            </div>

            </form>


        </div>
    </div>


@stop
@section('footer_scripts')
    <script src="{{ URL::asset('assets/js/subscription.js') }}" type="text/javascript"></script>
@stop
@section('footer_script_init')
    <script type="text/javascript">
        $(document).ready(function () {
            gymie.loaddatepickerend();
        });
    </script>
@stop

