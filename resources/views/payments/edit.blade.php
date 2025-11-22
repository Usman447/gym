@extends('app')

@section('content')

    <div class="rightside bg-grey-100">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel no-border">
                        <div class="panel-title">
                            <div class="panel-head font-size-20">Enter details of the payment</div>
                        </div>

                        <form action="{{ action([App\Http\Controllers\PaymentsController::class, 'update'], ['id' => $payment_detail->id]) }}" method="POST" id="paymentsform">
                            @csrf
                            @method('PUT')

                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <?php  $invoiceList = App\Invoice::pluck('invoice_number', 'id'); ?>
                                        <label for="invoice_id">Invoice Number</label>
                                        <select name="invoice_id" class="form-control selectpicker show-tick show-menu-arrow" id="invoice_id" data-live-search="true">
                                            @foreach($invoiceList as $id => $invoice_number)
                                                <option value="{{ $id }}" {{ (isset($invoice) && $invoice->id == $id) ? 'selected' : '' }}>{{ $invoice_number }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="payment_amount">Amount</label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-inr"></i></div>
                                            <input type="text" name="payment_amount" value="{{ isset($invoice) ? $invoice->pending_amount : '' }}" class="form-control" id="payment_amount">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="mode">Mode</label>
                                        <select name="mode" class="form-control selectpicker show-tick show-menu-arrow" id="mode">
                                            <option value="1" {{ (isset($payment_detail) && $payment_detail->mode == 1) ? 'selected' : '' }}>Cash</option>
                                            <option value="0" {{ (isset($payment_detail) && $payment_detail->mode == 0) ? 'selected' : '' }}>Online</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            @if($payment_detail->mode == 0)
                                <div id="chequeDetails">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="number">Cheque number</label>
                                                <input type="text" name="number" value="{{ isset($cheque_detail) ? $cheque_detail->number : '' }}" class="form-control" id="number">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="date">Cheque date</label>
                                                <input type="text" name="date" value="{{ isset($cheque_detail) ? $cheque_detail->date : '' }}" class="form-control datepicker-default" id="date">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary pull-right">Update</button>
                                    </div>
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
            <script src="{{ URL::asset('assets/js/payment.js') }}" type="text/javascript"></script>
@stop