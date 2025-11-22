@extends('app')

@section('content')

    <div class="rightside bg-grey-100">

        <!-- BEGIN PAGE HEADING -->
        <div class="page-head bg-grey-100 padding-top-15 no-padding-bottom">
            @include('flash::message')
            <h1 class="page-title no-line-height">Edit Inventory
                <small>Update inventory item</small>
            </h1>
        </div><!-- / PageHead -->

        <div class="container-fluid">
            <!-- Main row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel no-border">
                        
                        <!-- Edit Inventory Box -->
                        <div class="panel-title bg-blue-grey-50">
                            <div class="panel-head font-size-15">
                                <div class="panel-title-text">Edit Inventory</div>
                            </div>
                        </div>
                        
                        <div class="panel-body bg-white">
                            <!-- Error Log -->
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

                            <form action="{{ url('food/inventory/' . $inventoryItem->id) }}" method="POST" id="inventoryEditForm">
                                @csrf
                                @method('PUT')
                            
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" name="name" value="{{ $inventoryItem->name }}" class="form-control" placeholder="Enter inventory name" required>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="amount">Amount</label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-inr"></i></div>
                                            <input type="text" name="amount" value="{{ $inventoryItem->amount }}" class="form-control" placeholder="Enter amount" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="quantity">Quantity</label>
                                        <input type="text" name="quantity" value="{{ $inventoryItem->quantity }}" class="form-control" placeholder="Enter quantity" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <a href="{{ action('App\Http\Controllers\InventoryController@index') }}" class="btn btn-default">Cancel</a>
                                        <button type="submit" class="btn btn-primary active no-border">Update</button>
                                    </div>
                                </div>
                            </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

@stop

