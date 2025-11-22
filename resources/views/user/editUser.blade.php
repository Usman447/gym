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

                    <form action="{{ action(['App\Http\Controllers\AclController@updateUser', $user->id]) }}" method="POST" id="usersform" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                    <div class="panel no-border">
                        <div class="panel-title bg-white no-border">
                            <div class="panel-head">Enter Details of the user</div>
                        </div>

                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" id="name">
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="text" name="email" value="{{ old('email', $user->email) }}" class="form-control" id="email">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                    <label for="status">Status</label>
                                    <!--0 for inactive , 1 for active-->
                                        <select name="status" class="form-control" id="status">
                                            <option value="1" {{ old('status', $user->status) == '1' ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ old('status', $user->status) == '0' ? 'selected' : '' }}>InActive</option>
                                        </select>
                                    </div>
                                </div>

                                @if(isset($user))
                                    <?php
                                    $media = $user->getMedia('staff');
                                    $image = ($media->isEmpty() ? 'https://placeholdit.imgix.net/~text?txtsize=18&txt=NA&w=70&h=70' : url($media[0]->getUrl('form')));
                                    ?>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="photo">Photo</label>
                                            <input type="file" name="photo" class="form-control" id="photo">
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <img alt="staff photo" class="pull-right" src="{{ $image }}"/>
                                    </div>
                                @else
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="photo">Photo</label>
                                            <input type="file" name="photo" class="form-control" id="photo">
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="password">Password</label>
                                        <input type="password" name="password" class="form-control" id="password">
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="password_confirmation">Confirm Password</label>
                                        <input type="password" name="password_confirmation" class="form-control" id="password_confirmation">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="timings">Timings</label>
                                        <select name="timings" class="form-control selectpicker show-tick show-menu-arrow" id="timings">
                                            <option value="Morning" {{ old('timings', $user->timings) == 'Morning' ? 'selected' : '' }}>Morning</option>
                                            <option value="Ladies" {{ old('timings', $user->timings) == 'Ladies' ? 'selected' : '' }}>Ladies</option>
                                            <option value="Evening" {{ old('timings', $user->timings) == 'Evening' ? 'selected' : '' }}>Evening</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="panel no-border">
                        <div class="panel-title bg-white no-border">
                            <div class="panel-head">Enter Role of the user</div>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <?php
                                        $withoutGymie = App\Role::where('name', '!=', 'Gymie')->pluck('name', 'id');
                                        $withGymie = App\Role::pluck('name', 'id');
                                        $roleList = Auth::User()->hasRole('Gymie') ? $withGymie : $withoutGymie;
                                        ?>
                                        <label for="role_id">Role</label>
                                        <select name="role_id" class="form-control selectpicker show-tick" id="role_id">
                                            @foreach($roleList as $id => $name)
                                                <option value="{{ $id }}" {{ old('role_id', $user->roleUser->role_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-2 pull-right">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary pull-right">Update</button>
                            </div>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
@section('footer_scripts')
    <script src="{{ URL::asset('assets/js/userUpdate.js') }}" type="text/javascript"></script>
@stop