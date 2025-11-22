<div class="panel-body">
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="plan_code">Plan Code</label>
                <input type="text" name="plan_code" value="{{ old('plan_code', isset($plan) ? $plan->plan_code : '') }}" class="form-control" id="plan_code">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="plan_name">Plan Name</label>
                <input type="text" name="plan_name" value="{{ old('plan_name', isset($plan) ? $plan->plan_name : '') }}" class="form-control" id="plan_name">
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="plan_details">Plan Details</label>
                <input type="text" name="plan_details" value="{{ old('plan_details', isset($plan) ? $plan->plan_details : '') }}" class="form-control" id="plan_details">
            </div>
        </div>
    </div>

    {{-- Service field removed - using plan name instead --}}
    <input type="hidden" name="service_id" value="">

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="days">Days</label>
                <input type="text" name="days" value="{{ old('days', isset($plan) ? $plan->days : '') }}" class="form-control" id="days">
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="amount">Amount (without taxes)</label>
                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-inr"></i></div>
                    <input type="text" name="amount" value="{{ old('amount', isset($plan) ? $plan->amount : '') }}" class="form-control" id="amount">
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
            <label for="status">Status</label>
            <!--0 for inactive , 1 for active-->
                <select name="status" class="form-control selectpicker show-tick show-menu-arrow" id="status">
                    <option value="1" {{ old('status', isset($plan) ? $plan->status : '') == '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('status', isset($plan) ? $plan->status : '') == '0' ? 'selected' : '' }}>InActive</option>
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <button type="submit" class="btn btn-primary pull-right">{{ $submitButtonText }}</button>
            </div>
        </div>
    </div>
</div>
                            