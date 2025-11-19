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
    {!! Form::hidden('invoiceCounter',$invoiceCounter) !!}
    {!! Form::hidden('memberCounter',$memberCounter) !!}
@endif

<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            {!! Form::label('member_code','Member code') !!}
            {!! Form::text('member_code',$member_code,['class'=>'form-control', 'id' => 'member_code', ($member_number_mode == \constNumberingMode::Auto ? 'readonly' : '')]) !!}
        </div>
    </div>

    <div class="col-sm-6">
        <div class="form-group">
            {!! Form::label('name','Name',['class'=>'control-label']) !!}
            {!! Form::text('name',null,['class'=>'form-control', 'id' => 'name']) !!}
        </div>
    </div>
</div>

<div class="row">

    <div class="col-sm-6">
        <div class="form-group">
            {!! Form::label('age','Age') !!}
            {!! Form::number('age',null,['class'=>'form-control', 'id' => 'age', 'min'=>0, 'max'=>150]) !!}
        </div>
    </div>

    <div class="col-sm-6">
        <div class="form-group">
            {!! Form::label('gender','Gender') !!}
            {!! Form::select('gender',array('m' => 'Male', 'f' => 'Female'),null,['class'=>'form-control selectpicker show-tick show-menu-arrow', 'id' => 'gender']) !!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            {!! Form::label('contact','Contact') !!}
            {!! Form::text('contact',null,['class'=>'form-control', 'id' => 'contact', 'pattern'=>'03[0-9]{9}', 'title'=>'Enter number in 03xxxxxxxxx format']) !!}
        </div>
    </div>

    <div class="col-sm-6">
        <div class="form-group">
            {!! Form::label('timings','Timings') !!}
            {!! Form::select('timings', $timingsOptions, $selectedTimings, ['class'=>'form-control selectpicker show-tick show-menu-arrow', 'id' => 'timings', $disabled ? 'disabled' : '']) !!}
            @if($disabled && !$isAdmin && isset($member) && isset($member->timings))
                {!! Form::hidden('timings', $member->timings) !!}
                <small class="text-muted">Timings cannot be changed</small>
            @endif
        </div>
    </div>
</div>

<div class="row" style="display:none;">
    <div class="col-sm-6">
        <div class="form-group">
            {!! Form::label('health_issues','Health issues') !!}
            {!! Form::text('health_issues','NA',['class'=>'form-control', 'id' => 'health_issues']) !!}
        </div>
    </div>
</div>


<div class="row">
    <div class="col-sm-4" style="display:none;">
        <div class="form-group">
            {!! Form::label('height_ft','Height (ft)') !!}
            {!! Form::number('height_ft',0,['class'=>'form-control', 'id' => 'height_ft','min' => 0, 'max'=>9]) !!}
        </div>
    </div>
    <div class="col-sm-4" style="display:none;">
        <div class="form-group">
            {!! Form::label('height_in','Height (in)') !!}
            {!! Form::number('height_in',0,['class'=>'form-control', 'id' => 'height_in','min'=>0, 'max'=>12]) !!}
        </div>
    </div>
    <div class="col-sm-4" style="display:none;">
        <div class="form-group">
            {!! Form::label('weight_kg','Weight (kg)') !!}
            {!! Form::number('weight_kg',0,['class'=>'form-control', 'id' => 'weight_kg', 'step'=>'1', 'min'=>0]) !!}
        </div>
    </div>
</div>


<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            {!! Form::label('address','Address') !!}
            {!! Form::textarea('address',null,['class'=>'form-control', 'id' => 'address', 'rows' => 5]) !!}
        </div>
    </div>

    <div class="col-sm-4">
        <div class="checkbox">
            <label>
                {!! Form::checkbox('opf_residence',1,null,['id'=>'opf_residence']) !!} OPF Residence
            </label>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            {!! Form::label('status','Status') !!}
            {!! Form::select('status',array('1' => 'Active', '0' => 'InActive'),null,['class' => 'form-control selectpicker show-tick show-menu-arrow', 'id' => 'status']) !!}
        </div>
    </div>
</div>