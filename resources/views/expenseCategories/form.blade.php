<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <label for="name">Category Name</label>
            <input type="text" name="name" value="{{ old('name', isset($expenseCategory) ? $expenseCategory->name : '') }}" class="form-control" id="name">
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
        <label for="status">Status</label>
        <!--0 for inactive , 1 for active-->
            <select name="status" class="form-control" id="status">
                <option value="1" {{ old('status', isset($expenseCategory) ? $expenseCategory->status : '') == '1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ old('status', isset($expenseCategory) ? $expenseCategory->status : '') == '0' ? 'selected' : '' }}>InActive</option>
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