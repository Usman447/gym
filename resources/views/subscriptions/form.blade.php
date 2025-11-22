<?php
use Carbon\Carbon;
?>

<div class="row">
    <div class="col-sm-5">
        <div class="form-group">
            <?php
            $user = Auth::user();
            $membersQuery = App\Member::where('status', '=', '1');
            
            // Filter members by timings (admin sees all, others see only matching timings)
            if ($user && method_exists($user, 'can') && $user->can('manage-gymie')) {
                // Admin sees all members
                $members = $membersQuery->get();
            } elseif ($user && !empty($user->timings)) {
                // Non-admin users see only members with matching timings
                $members = $membersQuery->where('timings', $user->timings)->get();
            } else {
                // User has no timings or not authenticated - show no members
                $members = collect([]);
            }

            $memberArray = [];
            foreach ($members as $member) {
                $memberArray[$member['id']] = $member['member_code'].' - '.$member['name'];
            }
            ?>
            <label for="member_id">Member Code</label>
            <select name="member_id" class="form-control selectpicker show-tick show-menu-arrow" id="member_id" data-live-search="true">
                @foreach($memberArray as $id => $display)
                    <option value="{{ $id }}">{{ $display }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-5">
        <label for="plan_0">Plan</label>
    </div>

    <div class="col-sm-3">
        <label for="start_date_0">Start Date</label>
    </div>

    <div class="col-sm-3">
        <label for="end_date_0">End Date</label>
    </div>

    <div class="col-sm-1">
        <label>&nbsp;</label><br/>
    </div>
</div> <!-- / Row -->
<div id="servicesContainer">
    <div class="row">
        <div class="col-sm-5">
            <div class="form-group plan-id">
                <?php $plans = App\Plan::where('status', '=', '1')->get(); ?>

                <select id="plan_0" name="plan[0][id]" class="form-control selectpicker show-tick show-menu-arrow childPlan" data-live-search="true"
                        data-row-id="0">
                    @foreach($plans as $plan)
                        <option value="{{ $plan->id }}" data-price="{{ $plan->amount }}" data-days="{{ $plan->days }}"
                                data-row-id="0">{{ $plan->plan_display }} </option>
                    @endforeach
                </select>
                <div class="plan-price">
                    <input type="hidden" name="plan[0][price]" value="" id="price_0">
                </div>
            </div>
        </div>

        <div class="col-sm-3">
            <div class="form-group plan-start-date">
                <input type="text" name="plan[0][start_date]" value="{{ Carbon::today()->format('Y-m-d') }}" class="form-control datepicker-startdate childStartDate" id="start_date_0" data-row-id="0">
            </div>
        </div>

        <div class="col-sm-3">
            <div class="form-group plan-end-date">
                <input type="text" name="plan[0][end_date]" value="" class="form-control childEndDate" id="end_date_0" readonly="readonly" data-row-id="0">
            </div>
        </div>

        <div class="col-sm-1">
            <div class="form-group">
                    <span class="btn btn-sm btn-danger pull-right hide remove-service">
                      <i class="fa fa-times"></i>
                    </span>
            </div>
        </div>

    </div> <!-- / Row -->
</div>
<div class="row">
    <div class="col-sm-2 pull-right">
        <div class="form-group">
            <span class="btn btn-sm btn-primary pull-right" id="addSubscription">Add</span>
        </div>
    </div>
</div>
