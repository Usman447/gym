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

                            {!! Form::Open(['url' => 'food/inventory/' . $inventoryItem->id, 'method' => 'PUT', 'id' => 'inventoryEditForm']) !!}
                            
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('name', 'Name') !!}
                                        {!! Form::text('name', $inventoryItem->name, ['class' => 'form-control', 'placeholder' => 'Enter inventory name', 'required']) !!}
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('amount', 'Amount') !!}
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-inr"></i></div>
                                            {!! Form::text('amount', $inventoryItem->amount, ['class' => 'form-control', 'placeholder' => 'Enter amount', 'required']) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('quantity', 'Quantity') !!}
                                        {!! Form::text('quantity', $inventoryItem->quantity, ['class' => 'form-control', 'placeholder' => 'Enter quantity', 'required']) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <a href="{{ action('InventoryController@index') }}" class="btn btn-default">Cancel</a>
                                        <button type="submit" class="btn btn-primary active no-border">Update</button>
                                    </div>
                                </div>
                            </div>

                            {!! Form::Close() !!}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

@stop

