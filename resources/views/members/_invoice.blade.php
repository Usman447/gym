<div class="row">
    <div class="col-md-12">
        <div class="panel no-border">
            <div class="panel-title">
                <div class="panel-head font-size-20">Enter details of the invoice</div>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="invoice_number">Invoice Number</label>
                            <input type="text" name="invoice_number" value="{{ $invoice_number }}" class="form-control" id="invoice_number" {{ $invoice_number_mode == \constNumberingMode::Auto ? 'readonly' : '' }}>
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="additional_fees">Additional fees</label>
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-inr"></i></div>
                                <input type="text" name="additional_fees" value="0" class="form-control" id="additional_fees">
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="subscription_amount">Subscription fee</label>
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-inr"></i></div>
                                <input type="text" name="subscription_amount" value="" class="form-control" id="subscription_amount" readonly="readonly">
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
                                <input type="text" name="discount_amount" value="0" class="form-control" id="discount_amount">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="discount_note">Discount note</label>
                            <input type="text" name="discount_note" value="" class="form-control" id="discount_note">
                        </div>
                    </div>
                </div>

            </div> <!-- /Panel-body -->

        </div> <!-- /Panel-no-border -->
    </div> <!-- /Main Column -->
</div> <!-- /Main Row -->