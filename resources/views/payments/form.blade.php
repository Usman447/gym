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
                    <option value="2" {{ (isset($payment_detail) && $payment_detail->mode == 2) ? 'selected' : '' }}>Online</option>
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <button type="submit" class="btn btn-primary pull-right">{{ $submitButtonText }}</button>
            </div>
        </div>
    </div>
</div>
