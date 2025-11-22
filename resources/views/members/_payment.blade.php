<div class="row">
    <div class="col-md-12">
        <div class="panel no-border">
            <div class="panel-title">
                <div class="panel-head font-size-20">Enter details of the payment</div>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="payment_amount">Amount Received</label>
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-inr"></i></div>
                                <input type="text" name="payment_amount" value="" class="form-control" id="payment_amount" data-amounttotal="0">
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="payment_amount_pending">Amount Pending</label>
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-inr"></i></div>
                                <input type="text" name="payment_amount_pending" value="" class="form-control" id="payment_amount_pending" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="mode">Mode</label>
                            <select name="mode" class="form-control selectpicker show-tick show-menu-arrow" id="mode">
                                <option value="1" selected>Cash</option>
                                <option value="2">Online</option>
                            </select>
                        </div>
                    </div>
                </div> <!-- /Row -->


            </div> <!-- /Panel-body -->

        </div> <!-- /Panel-no-border -->
    </div> <!-- /Main Column -->
</div> <!-- /Main Row -->
