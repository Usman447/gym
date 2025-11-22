<?php use Carbon\Carbon; 

// Initialize timings variables at the top to ensure they're always defined
$timingsOptions = array('Morning' => 'Morning', 'Ladies' => 'Ladies', 'Evening' => 'Evening');
$disabled = false;
$isAdmin = false;

// Check user and permissions
try {
    $user = Auth::user();
    if ($user && method_exists($user, 'can')) {
        $isAdmin = $user->can('manage-gymie');
    }
} catch (\Exception $e) {
    $isAdmin = false;
}

// Set selected timings: when editing use member's timings, when creating use logged-in user's timings
if (isset($member) && isset($member->timings)) {
    // Editing: use member's existing timings
    $selectedTimings = $member->timings;
} else {
    // Creating: default to logged-in user's timings
    $selectedTimings = ($user && isset($user->timings) && !empty($user->timings)) ? $user->timings : null;
}

// When editing, non-admin users cannot change timings
if (isset($member) && !$isAdmin) {
    $disabled = true;
}
?>

<!-- Hidden Fields -->
@if(Request::is('members/create'))
    <input type="hidden" name="invoiceCounter" value="{{ $invoiceCounter }}">
    <input type="hidden" name="memberCounter" value="{{ $memberCounter }}">
@endif

<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <label for="member_code">Member code</label>
            <input type="text" name="member_code" value="{{ $member_code }}" class="form-control" id="member_code" {{ $member_number_mode == \constNumberingMode::Auto ? 'readonly' : '' }}>
        </div>
    </div>

    <div class="col-sm-6">
        <div class="form-group">
            <label for="name" class="control-label">Name</label>
            <input type="text" name="name" value="{{ old('name', isset($member) ? $member->name : '') }}" class="form-control" id="name">
        </div>
    </div>
</div>

<div class="row">

    <div class="col-sm-6">
        <div class="form-group">
            <label for="age">Age</label>
            <input type="number" name="age" value="{{ old('age', isset($member) ? $member->age : '') }}" class="form-control" id="age" min="0" max="150">
        </div>
    </div>

    <div class="col-sm-6">
        <div class="form-group">
            <label for="gender">Gender</label>
            <select name="gender" class="form-control selectpicker show-tick show-menu-arrow" id="gender">
                <option value="m" {{ old('gender', isset($member) ? $member->gender : '') == 'm' ? 'selected' : '' }}>Male</option>
                <option value="f" {{ old('gender', isset($member) ? $member->gender : '') == 'f' ? 'selected' : '' }}>Female</option>
            </select>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <label for="contact">Contact</label>
            <input type="text" name="contact" value="{{ old('contact', isset($member) ? $member->contact : '') }}" class="form-control" id="contact" pattern="03[0-9]{9}" title="Enter number in 03xxxxxxxxx format">
        </div>
    </div>

    <div class="col-sm-6">
        <div class="form-group">
            <label for="timings">Timings</label>
            <select name="timings" class="form-control selectpicker show-tick show-menu-arrow" id="timings" {{ $disabled ? 'disabled' : '' }}>
                @foreach($timingsOptions as $key => $value)
                    <option value="{{ $key }}" {{ $selectedTimings == $key ? 'selected' : '' }}>{{ $value }}</option>
                @endforeach
            </select>
            @if($disabled && !$isAdmin && isset($member) && isset($member->timings))
                <input type="hidden" name="timings" value="{{ $member->timings }}">
                <small class="text-muted">Timings cannot be changed</small>
            @endif
        </div>
    </div>
</div>

<div class="row" style="display:none;">
    <div class="col-sm-6">
        <div class="form-group">
            <label for="health_issues">Health issues</label>
            <input type="text" name="health_issues" value="{{ old('health_issues', isset($member) ? $member->health_issues : 'NA') }}" class="form-control" id="health_issues">
        </div>
    </div>
</div>


<div class="row">
    <div class="col-sm-4" style="display:none;">
        <div class="form-group">
            <label for="height_ft">Height (ft)</label>
            <input type="number" name="height_ft" value="{{ old('height_ft', isset($member) ? $member->height_ft : 0) }}" class="form-control" id="height_ft" min="0" max="9">
        </div>
    </div>
    <div class="col-sm-4" style="display:none;">
        <div class="form-group">
            <label for="height_in">Height (in)</label>
            <input type="number" name="height_in" value="{{ old('height_in', isset($member) ? $member->height_in : 0) }}" class="form-control" id="height_in" min="0" max="12">
        </div>
    </div>
    <div class="col-sm-4" style="display:none;">
        <div class="form-group">
            <label for="weight_kg">Weight (kg)</label>
            <input type="number" name="weight_kg" value="{{ old('weight_kg', isset($member) ? $member->weight_kg : 0) }}" class="form-control" id="weight_kg" step="1" min="0">
        </div>
    </div>
</div>


<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <label for="address">Address</label>
            <textarea name="address" class="form-control" id="address" rows="5">{{ old('address', isset($member) ? $member->address : '') }}</textarea>
        </div>
    </div>

    <div class="col-sm-4">
        <div class="checkbox">
            <label>
                <input type="checkbox" name="opf_residence" value="1" id="opf_residence" {{ old('opf_residence', isset($member) && $member->opf_residence == 1 ? 'checked' : '') }}> OPF Residence
            </label>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <label for="status">Status</label>
            <select name="status" class="form-control selectpicker show-tick show-menu-arrow" id="status">
                <option value="1" {{ old('status', isset($member) ? $member->status : '') == '1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ old('status', isset($member) ? $member->status : '') == '0' ? 'selected' : '' }}>InActive</option>
            </select>
        </div>
    </div>
</div>