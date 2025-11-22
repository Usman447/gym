<?php use Carbon\Carbon; ?>
<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" value="{{ old('name', isset($expense) ? $expense->name : '') }}" class="form-control" id="name">
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <?php $expenseCategories = App\ExpenseCategory::where('status', '=', '1')->pluck('name', 'id'); ?>
            <label for="category_id">Category</label>
            <select name="category_id" class="form-control selectpicker show-tick show-menu-arrow" id="category_id" data-live-search="true">
                @foreach($expenseCategories as $id => $name)
                    <option value="{{ $id }}" {{ old('category_id', isset($expense) ? $expense->category_id : '') == $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <label for="due_date">Due date / Payment date</label>
            <input type="text" name="due_date" value="{{ old('due_date', isset($expense->due_date) ? $expense->due_date->format('Y-m-d') : Carbon::today()->format('Y-m-d')) }}" class="form-control datepicker-default" id="due_date">
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
        <label for="repeat">Repeat</label>
        <!--0 for inactive , 1 for active-->
            <select name="repeat" class="form-control selectpicker show-tick show-menu-arrow" id="repeat">
                <option value="0" {{ old('repeat', isset($expense) ? $expense->repeat : '') == '0' ? 'selected' : '' }}>Never repeat</option>
                <option value="1" {{ old('repeat', isset($expense) ? $expense->repeat : '') == '1' ? 'selected' : '' }}>Every Day</option>
                <option value="2" {{ old('repeat', isset($expense) ? $expense->repeat : '') == '2' ? 'selected' : '' }}>Every Week</option>
                <option value="3" {{ old('repeat', isset($expense) ? $expense->repeat : '') == '3' ? 'selected' : '' }}>Every Month</option>
                <option value="4" {{ old('repeat', isset($expense) ? $expense->repeat : '') == '4' ? 'selected' : '' }}>Every Year</option>
            </select>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <label for="note">Note</label>
            <input type="text" name="note" value="{{ old('note', isset($expense) ? $expense->note : '') }}" class="form-control" id="note">
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <label for="amount">Amount</label>
            <div class="input-group">
                <div class="input-group-addon"><i class="fa fa-inr"></i></div>
                <input type="text" name="amount" value="{{ old('amount', isset($expense) ? $expense->amount : '') }}" class="form-control" id="amount">
            </div>
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