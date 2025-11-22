@extends('app')

@section('content')

    <div class="rightside bg-grey-100">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel no-border">
                        <div class="panel-title">
                            <div class="panel-head font-size-20">Enter details of the expense category</div>
                        </div>
                        <div class="panel-body">
                            <form action="{{ action(['App\Http\Controllers\ExpenseCategoriesController@update', $expenseCategory->id]) }}" method="POST" id="expensecategoriesform">
                                @csrf
                                @method('PUT')

                            @include('expenseCategories.form',['submitButtonText' => 'Update'])

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@stop
@section('footer_scripts')
    <script src="{{ URL::asset('assets/js/expenseCategory.js') }}" type="text/javascript"></script>
@stop