@extends('app')

@section('content')

    <div class="rightside bg-grey-100">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <strong>Whoops!</strong> There were some problems with your input.<br><br>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="panel no-border">
                        <div class="panel-title">
                            <div class="panel-head">Enter Details of the role</div>
                        </div>

                        <form action="{{ url('user/role') }}" method="POST" id="rolesform" enctype="multipart/form-data">
                            @csrf

                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="name">Role Name</label>
                                        <input type="text" name="name" value="{{ old('name') }}" class="form-control" id="name">
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="display_name">Display Name</label>
                                        <input type="text" name="display_name" value="{{ old('display_name') }}" class="form-control" id="display_name">
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <input type="text" name="description" value="{{ old('description') }}" class="form-control" id="description">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="panel no-border">
                        <div class="panel-title">
                            <div class="panel-head">Enter Permissions</div>
                        </div>
                        <div class="panel-body">

                            @foreach($permissions->groupBy('group_key') as $permission_group)
                                <h5>{{$permission_group->pluck('group_key')->pop()}}</h5>
                                <div class="row">
                                    @foreach($permission_group as $permission)
                                        <div class="col-xs-4">
                                            <div class="checkbox checkbox-theme">
                                                <input type="checkbox" name="permissions[]" id="permission_{{$permission->id}}" value="{{$permission->id}}">
                                                <label for="permission_{{$permission->id}}">{{ $permission->display_name }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-2 pull-right">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary pull-right">Create</button>
                            </div>
                        </div>
                    </div>

                    </form>


                </div>
            </div>
        </div>
    </div>
    </div>

@stop