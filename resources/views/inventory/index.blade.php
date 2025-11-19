@extends('app')

@section('content')

    <div class="rightside bg-grey-100">

        <!-- BEGIN PAGE HEADING -->
        <div class="page-head bg-grey-100 padding-top-15 no-padding-bottom">
            @include('flash::message')
            <h1 class="page-title no-line-height">Add Inventory
                <small>Manage inventory list</small>
            </h1>
        </div><!-- / PageHead -->

        <div class="container-fluid">
            <!-- Main row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel no-border">
                        
                        <!-- Create Inventory Box -->
                        <div class="panel-title bg-blue-grey-50">
                            <div class="panel-head font-size-15">
                                <div class="panel-title-text">Create Inventory</div>
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

                            {!! Form::Open(['url' => 'food/inventory', 'id' => 'inventoryForm']) !!}
                            
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        {!! Form::label('name', 'Name') !!}
                                        {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Enter inventory name', 'required']) !!}
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        {!! Form::label('amount', 'Amount') !!}
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-inr"></i></div>
                                            {!! Form::text('amount', null, ['class' => 'form-control', 'placeholder' => 'Enter amount', 'required']) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        {!! Form::label('quantity', 'Quantity') !!}
                                        {!! Form::text('quantity', null, ['class' => 'form-control', 'placeholder' => 'Enter quantity', 'required']) !!}
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        {!! Form::label('&nbsp;') !!}
                                        <br/>
                                        <button type="submit" class="btn btn-primary active no-border">Add</button>
                                    </div>
                                </div>
                            </div>

                            {!! Form::Close() !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="panel no-border">
                        <!-- Inventory List Box -->
                        <div class="panel-title bg-blue-grey-50 margin-top-20">
                            <div class="panel-head font-size-15">
                                <div class="panel-title-text">Inventory List</div>
                            </div>
                        </div>
                        
                        <div class="panel-body bg-white">
                            @if($inventoryItems->count() == 0)
                                <h4 class="text-center padding-top-15">No inventory items added yet</h4>
                            @else
                                <table id="inventoryItems" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Amount</th>
                                        <th>Quantity</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($inventoryItems as $inventoryItem)
                                        <tr>
                                            <td>{{ $inventoryItem->name }}</td>
                                            <td>{{ number_format($inventoryItem->amount, 0) }}</td>
                                            <td>{{ $inventoryItem->quantity }}</td>
                                            <td class="text-center">
                                                <a href="{{ action('InventoryController@edit', $inventoryItem->id) }}" class="btn btn-primary">
                                                    Edit
                                                </a>
                                                <a href="#" onclick="if(confirm('Are you sure you want to remove this inventory item?')) { var form = document.createElement('form'); form.method = 'POST'; form.action = '{{ url('food/inventory/' . $inventoryItem->id) }}'; var token = document.createElement('input'); token.type = 'hidden'; token.name = '_token'; token.value = '{{ csrf_token() }}'; form.appendChild(token); var method = document.createElement('input'); method.type = 'hidden'; method.name = '_method'; method.value = 'DELETE'; form.appendChild(method); document.body.appendChild(form); form.submit(); } return false;" class="btn btn-danger">
                                                    Remove
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    </div>
                </div>
            </div> <!-- / Second row -->

        </div>
    </div>

@stop

@section('footer_script_init')
    <script type="text/javascript">
        $(document).ready(function () {
            // Reset form after successful submission
            $('#inventoryForm').on('submit', function() {
                var $form = $(this);
                setTimeout(function() {
                    if (!$('.alert-danger').length) {
                        $form[0].reset();
                    }
                }, 100);
            });
        });
    </script>
@stop

