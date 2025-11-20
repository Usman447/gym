<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            {!! Form::label('name','Name',['class'=>'control-label']) !!}
            {!! Form::text('name',null,['class'=>'form-control', 'id' => 'name']) !!}
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            {!! Form::label('contact','Contact') !!}
            {!! Form::text('contact',null,['class'=>'form-control', 'id' => 'contact']) !!}
        </div>
    </div>
</div>


<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            {!! Form::label('age','Age') !!}
            {!! Form::number('age',null,['class'=>'form-control', 'id' => 'age', 'min' => '1', 'max' => '150']) !!}
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
            {!! Form::label('occupation','Occupation') !!}
            {!! Form::select('occupation',array('0' => 'Student', '1' => 'Housewife','2' => 'Self Employed','3' => 'Professional','4' => 'Freelancer','5' => 'Others'),null,['class' => 'form-control selectpicker show-tick show-menu-arrow', 'id' => 'occupation']) !!}
        </div>
    </div>

    <div class="col-sm-6">
        <div class="form-group">
            {!! Form::label('start_by','Start By') !!}
            {!! Form::text('start_by',null,['class'=>'form-control datepicker-default', 'id' => 'start_by']) !!}
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
            {!! Form::label('interested_in','Interested In') !!}
            {!! Form::select('interested_in[]',$plans, $selectedPlans,['class'=>'form-control selectpicker show-tick show-menu-arrow','multiple' => 'multiple','id' => 'interested_in']) !!}
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

{{-- Hidden fields to preserve database structure (not shown in form) --}}
{!! Form::hidden('aim', '0') !!}
{!! Form::hidden('source', '0') !!}
{!! Form::hidden('pin_code', '0') !!}
{!! Form::hidden('DOB', '1900-01-01') !!}
{!! Form::hidden('email', '') !!}
