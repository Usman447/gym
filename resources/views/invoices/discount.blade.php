@extends('app')
@section('content')

    <div class="rightside bg-grey-100">
        <div class="container-fluid">

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

            <form action="{{ action([App\Http\Controllers\InvoicesController::class, 'applyDiscount'], ['id' => $invoice->id]) }}" method="POST" id="invoicediscountform">
                @csrf

            <div class="row">
                <div class="col-md-12">
                    <div class="panel no-border">
                        <div class="panel-title">
                            <div class="panel-head font-size-20">Enter details of the discount</div>
                        </div>

                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="invoice_number">Invoice Number</label>
                                        <input type="text" name="invoice_number" value="{{ $invoice->invoice_number }}" class="form-control" id="invoice_number" readonly="readonly">
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="subscription_amount">Subscription fee</label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-inr"></i></div>
                                            <input type="text" name="subscription_amount" value="{{ $invoice->invoiceDetails->sum('item_amount') }}" class="form-control" id="subscription_amount" readonly="readonly">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="additional_fees">Admission fees</label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-inr"></i></div>
                                            <input type="text" name="additional_fees" value="{{ $invoice->additional_fees }}" class="form-control" id="additional_fees" readonly="readonly">
                                        </div>
                                    </div>
                                </div>

                                
                            </div> <!-- /Row -->

                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="discount_amount">Discount amount</label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-inr"></i></div>
                                            <input type="text" name="discount_amount" value="{{ $invoice->discount_amount }}" class="form-control" id="discount_amount">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="discount_note">Discount note</label>
                                        <input type="text" name="discount_note" value="{{ $invoice->discount_note }}" class="form-control" id="discount_note">
                                    </div>
                                </div>
                            </div>

                        </div> <!-- /Panel-body -->

                    </div> <!-- /Panel-no-border -->

                    <div class="row">
                        <div class="col-sm-2 pull-right">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary pull-right">Apply Discount</button>
                            </div>
                        </div>
                    </div>

                </div> <!-- /Main Column -->
            </div> <!-- /Main Row -->

            </form>

        </div>
    </div>

@stop

@section('footer_script_init')
    <script type="text/javascript">
        $(document).ready(function () {
            gymie.applyDiscount();
        });
    </script>
@stop