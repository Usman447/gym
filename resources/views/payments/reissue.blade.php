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

                        <form action="{{ url('payments') }}" method="POST" id="paymentsform">
                            @csrf
                            <input type="hidden" name="previousPayment" value="{{ $payment_detail->id }}">

                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <?php  $invoiceList = App\Invoice::pluck('invoice_number', 'id'); ?>
                                        <label for="invoice_id">Invoice Number</label>
                                        <select name="invoice_id" class="form-control selectpicker show-tick" id="invoice_id" data-live-search="true">
                                            @foreach($invoiceList as $id => $invoice_number)
                                                <option value="{{ $id }}" {{ $payment_detail->invoice_id == $id ? 'selected' : '' }}>{{ $invoice_number }}</option>
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
                                            <input type="text" name="payment_amount" value="{{ $payment_detail->invoice->pending_amount }}" class="form-control" id="payment_amount">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="mode">Mode</label>
                                        <select name="mode" class="form-control selectpicker show-tick show-menu-arrow" id="mode">
                                            <option value="1" selected>Cash</option>
                                            <option value="0">Cheque</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div id="chequeDetails">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="number">Cheque number</label>
                                            <input type="text" name="number" value="" class="form-control" id="number">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="date">Cheque date</label>
                                            <input type="text" name="date" value="" class="form-control datepicker-default" id="date">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary pull-right">Accept Payment</button>
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

        @section('footer_script_init')
            <script type="text/javascript">
                $(document).ready(function () {
                    gymie.loaddatepicker();
                    gymie.chequedetails();
                });
            </script>
@stop