<div class="panel-body">
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="name">Service Name</label>
                <input type="text" name="name" value="{{ old('name', isset($service) ? $service->name : '') }}" class="form-control" id="name">
            </div>
        </div>

        <div class="col-sm-6">
            <div class="form-group">
                <label for="description">Service Description</label>
                <input type="text" name="description" value="{{ old('description', isset($service) ? $service->description : '') }}" class="form-control" id="description">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <button type="submit" class="btn btn-primary pull-right">{{ $submitButtonText }}</button>
            </div>
        </div>
    </div>
</div>
                            