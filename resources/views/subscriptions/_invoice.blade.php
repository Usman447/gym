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
                            {!! Form::label('invoice_number','Invoice Number') !!}
                            {!! Form::text('invoice_number',$invoice_number,['class'=>'form-control', 'id' => 'invoice_number', ($invoice_number_mode == \constNumberingMode::Auto ? 'readonly' : '')]) !!}
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="form-group">
                            {!! Form::label('subscription_amount','Gym subscription fee') !!}
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-inr"></i></div>
                                {!! Form::text('subscription_amount',null,['class'=>'form-control', 'id' => 'subscription_amount','readonly' => 'readonly']) !!}
                            </div>
                        </div>
                    </div>

                    
                </div> <!-- /Row -->

                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            {!! Form::label('discount_amount','Discount amount') !!}
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-inr"></i></div>
                                {!! Form::text('discount_amount',null,['class'=>'form-control', 'id' => 'discount_amount']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            {!! Form::label('discount_note','Discount note') !!}
                            {!! Form::text('discount_note',null,['class'=>'form-control', 'id' => 'discount_note']) !!}
                        </div>
                    </div>
                </div>

            </div> <!-- /Box-body -->

        </div> <!-- /Box -->
    </div> <!-- /Main Column -->
</div> <!-- /Main Row -->