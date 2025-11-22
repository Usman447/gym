@extends('app')

@section('content')

    <div class="rightside bg-grey-100">

        <!-- BEGIN PAGE HEADING -->
        <div class="page-head bg-grey-100 padding-top-15 no-padding-bottom">
            @include('flash::message')
            <h1 class="page-title no-line-height">Add Food Items
                <small>Manage food items list</small>
            </h1>
        </div><!-- / PageHead -->

        <div class="container-fluid">
            <!-- Main row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel no-border">
                        
                        <!-- Create Food List Box -->
                        <div class="panel-title bg-blue-grey-50">
                            <div class="panel-head font-size-15">
                                <div class="panel-title-text">Create Food List</div>
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

                            <form action="{{ url('food/items') }}" method="POST" id="foodItemsForm">
                                @csrf
                            
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" name="name" value="" class="form-control" placeholder="Enter food name" required>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="amount">Amount</label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-inr"></i></div>
                                            <input type="text" name="amount" value="" class="form-control" placeholder="Enter amount" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <br/>
                                        <button type="submit" class="btn btn-primary active no-border">Add</button>
                                    </div>
                                </div>
                            </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="panel no-border">
                        <!-- Food List Box -->
                        <div class="panel-title bg-blue-grey-50 margin-top-20">
                            <div class="panel-head font-size-15">
                                <div class="panel-title-text">Food List</div>
                            </div>
                        </div>
                        
                        <div class="panel-body bg-white">
                            @if($foodItems->count() == 0)
                                <h4 class="text-center padding-top-15">No food items added yet</h4>
                            @else
                                <table id="foodItems" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>Food</th>
                                        <th>Amount</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($foodItems as $foodItem)
                                        <tr>
                                            <td>{{ $foodItem->name }}</td>
                                            <td>{{ number_format($foodItem->amount, 0) }}</td>
                                            <td class="text-center">
                                                <a href="#" onclick="if(confirm('Are you sure you want to remove this food item?')) { var form = document.createElement('form'); form.method = 'POST'; form.action = '{{ url('food/items/' . $foodItem->id) }}'; var token = document.createElement('input'); token.type = 'hidden'; token.name = '_token'; token.value = '{{ csrf_token() }}'; form.appendChild(token); var method = document.createElement('input'); method.type = 'hidden'; method.name = '_method'; method.value = 'DELETE'; form.appendChild(method); document.body.appendChild(form); form.submit(); } return false;" class="btn btn-danger">
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
            $('#foodItemsForm').on('submit', function() {
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

