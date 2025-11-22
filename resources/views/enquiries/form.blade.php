<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <label for="name" class="control-label">Name</label>
            <input type="text" name="name" value="{{ old('name', isset($enquiry) ? $enquiry->name : '') }}" class="form-control" id="name">
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label for="contact">Contact</label>
            <input type="text" name="contact" value="{{ old('contact', isset($enquiry) ? $enquiry->contact : '') }}" class="form-control" id="contact">
        </div>
    </div>
</div>


<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <label for="age">Age</label>
            <input type="number" name="age" value="{{ old('age', isset($enquiry) ? $enquiry->age : '') }}" class="form-control" id="age" min="1" max="150">
        </div>
    </div>

    <div class="col-sm-6">
        <div class="form-group">
            <label for="gender">Gender</label>
            <select name="gender" class="form-control selectpicker show-tick show-menu-arrow" id="gender">
                <option value="m" {{ old('gender', isset($enquiry) ? $enquiry->gender : '') == 'm' ? 'selected' : '' }}>Male</option>
                <option value="f" {{ old('gender', isset($enquiry) ? $enquiry->gender : '') == 'f' ? 'selected' : '' }}>Female</option>
            </select>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <label for="occupation">Occupation</label>
            <select name="occupation" class="form-control selectpicker show-tick show-menu-arrow" id="occupation">
                <option value="0" {{ old('occupation', isset($enquiry) ? $enquiry->occupation : '') == '0' ? 'selected' : '' }}>Student</option>
                <option value="1" {{ old('occupation', isset($enquiry) ? $enquiry->occupation : '') == '1' ? 'selected' : '' }}>Housewife</option>
                <option value="2" {{ old('occupation', isset($enquiry) ? $enquiry->occupation : '') == '2' ? 'selected' : '' }}>Self Employed</option>
                <option value="3" {{ old('occupation', isset($enquiry) ? $enquiry->occupation : '') == '3' ? 'selected' : '' }}>Professional</option>
                <option value="4" {{ old('occupation', isset($enquiry) ? $enquiry->occupation : '') == '4' ? 'selected' : '' }}>Freelancer</option>
                <option value="5" {{ old('occupation', isset($enquiry) ? $enquiry->occupation : '') == '5' ? 'selected' : '' }}>Others</option>
            </select>
        </div>
    </div>

    <div class="col-sm-6">
        <div class="form-group">
            <label for="start_by">Start By</label>
            <input type="text" name="start_by" value="{{ old('start_by', isset($enquiry) ? $enquiry->start_by : '') }}" class="form-control datepicker-default" id="start_by">
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <?php 
                $plans = App\Plan::where('status', '=', '1')->pluck('plan_name', 'id');
                // For edit: pre-select plans if they exist in interested_in
                $selectedPlans = [];
                if (isset($enquiry) && $enquiry->interested_in) {
                    $selectedPlans = explode(',', $enquiry->interested_in);
                }
            ?>
            <label for="interested_in">Interested In</label>
            <select name="interested_in[]" class="form-control selectpicker show-tick show-menu-arrow" multiple="multiple" id="interested_in">
                @foreach($plans as $id => $plan_name)
                    <option value="{{ $id }}" {{ in_array($id, $selectedPlans) ? 'selected' : '' }}>{{ $plan_name }}</option>
                @endforeach
            </select>
        </div>
    </div>

</div>


<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <label for="address">Address</label>
            <textarea name="address" class="form-control" id="address" rows="5">{{ old('address', isset($enquiry) ? $enquiry->address : '') }}</textarea>
        </div>
    </div>

    <div class="col-sm-4">
        <div class="checkbox">
            <label>
                <input type="checkbox" name="opf_residence" value="1" id="opf_residence" {{ old('opf_residence', isset($enquiry) && $enquiry->opf_residence == 1 ? 'checked' : '') }}> OPF Residence
            </label>
        </div>
    </div>
</div>

{{-- Hidden fields to preserve database structure (not shown in form) --}}
<input type="hidden" name="aim" value="0">
<input type="hidden" name="source" value="0">
<input type="hidden" name="pin_code" value="0">
<input type="hidden" name="DOB" value="1900-01-01">
<input type="hidden" name="email" value="">
