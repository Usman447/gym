@extends('app')

@section('content')

    <div class="rightside bg-grey-100">
        <div class="container-fluid">
            @include('flash::message')

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

            {!! Form::Open(['url' => 'food/orders','id'=>'foodOrderForm']) !!}
            
            <!-- Order Details -->
            <div class="row">
                <div class="col-md-12">
                    <div class="panel no-border">
                        <div class="panel-title">
                            <div class="panel-head font-size-20">Enter details of the food order</div>
                        </div>
                        <div class="panel-body">
                            
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('order_number', 'Order Number') !!}
                                        {!! Form::text('order_number', $orderNumber, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('payment_mode', 'Payment Mode') !!}
                                        {!! Form::select('payment_mode', [1 => 'Cash', 2 => 'Online'], 1, ['class' => 'form-control selectpicker show-tick show-menu-arrow', 'id' => 'payment_mode']) !!}
                                    </div>
                                </div>
                            </div>

                            <!-- Items Selection -->
                            <div class="row margin-top-20">
                                <div class="col-sm-12">
                                    <h4>Select Items</h4>
                                </div>
                            </div>

                            <div id="itemsContainer">
                                <div class="row">
                                    <div class="col-sm-5">
                                        <div class="form-group item-id">
                                            <label>Item</label>
                                            <select name="items[0][id]" class="form-control selectpicker show-tick show-menu-arrow item-select" data-live-search="true">
                                                <option value="">-- Select Item --</option>
                                                <optgroup label="Food Items">
                                                    @foreach($foodItems as $foodItem)
                                                        <option value="food_{{ $foodItem->id }}" data-type="food" data-price="{{ $foodItem->amount }}">{{ $foodItem->name }} - ₹{{ number_format($foodItem->amount, 0) }}</option>
                                                    @endforeach
                                                </optgroup>
                                                <optgroup label="Inventory Items">
                                                    @foreach($inventoryItems as $inventoryItem)
                                                        <option value="inventory_{{ $inventoryItem->id }}" data-type="inventory" data-price="{{ $inventoryItem->amount }}" data-quantity="{{ $inventoryItem->quantity }}">{{ $inventoryItem->name }} - ₹{{ number_format($inventoryItem->amount, 0) }}</option>
                                                    @endforeach
                                                </optgroup>
                                            </select>
                                            <small class="inventory-quantity-info text-muted" style="display: none;">Available Quantity: <span class="available-quantity">0</span></small>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label>Quantity</label>
                                            <input type="number" name="items[0][quantity]" value="1" min="1" class="form-control item-quantity" onchange="calculateTotal()">
                                            <small class="inventory-quantity-error text-danger" style="display: none;"></small>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label>Amount</label>
                                            <input type="text" class="form-control item-amount" readonly value="0">
                                        </div>
                                    </div>
                                    <div class="col-sm-1">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <span class="btn btn-sm btn-danger pull-right hide remove-item">
                                                <i class="fa fa-times"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-2 pull-right">
                                    <div class="form-group">
                                        <span class="btn btn-sm btn-primary pull-right" id="addItem">Add Item</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Total Amount -->
                            <div class="row margin-top-20">
                                <div class="col-sm-12">
                                    <div class="panel no-border bg-blue-grey-50">
                                        <div class="panel-body">
                                            <div class="row">
                                                <div class="col-sm-6 text-right">
                                                    <strong>Total Amount:</strong>
                                                </div>
                                                <div class="col-sm-6">
                                                    <h3 class="no-margin">Rs. <span id="totalAmount">0</span></h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="row">
                <div class="col-sm-2 pull-right">
                    <div class="form-group">
                        {!! Form::submit('Create Order', ['class' => 'btn btn-primary pull-right']) !!}
                    </div>
                </div>
            </div>

            {!! Form::Close() !!}

        </div>
    </div>

@stop

@section('footer_scripts')
    <script type="text/javascript">
        var itemCounter = 0;
        var maxItems = 100; // Maximum items per order

        $(document).ready(function () {
            gymie.loadbsselect();
            
            // Process form before submission - separate items into food_items and inventory_items
            $('#foodOrderForm').on('submit', function(e) {
                // Remove rows where no item is selected
                $('#itemsContainer .row').each(function() {
                    var $row = $(this);
                    var selectedValue = $row.find('.item-select').val();
                    if (!selectedValue || selectedValue === '') {
                        $row.remove();
                    }
                });
                
                // Check for inventory quantity errors
                var hasInventoryErrors = false;
                $('#itemsContainer .item-quantity').each(function() {
                    var $quantityInput = $(this);
                    if ($quantityInput.hasClass('has-error')) {
                        hasInventoryErrors = true;
                        return false;
                    }
                });
                
                if (hasInventoryErrors) {
                    e.preventDefault();
                    alert('Please fix inventory quantity errors before submitting.');
                    return false;
                }
                
                // Check for duplicate inventory items and validate total quantity
                var inventoryQuantities = {};
                var hasDuplicateErrors = false;
                var duplicateErrorMsg = '';
                
                $('#itemsContainer .row').each(function() {
                    var $row = $(this);
                    var selectedValue = $row.find('.item-select').val();
                    
                    if (selectedValue && selectedValue.startsWith('inventory_')) {
                        var parts = selectedValue.split('_');
                        var inventoryId = parts[1];
                        var quantity = parseInt($row.find('.item-quantity').val()) || 0;
                        var selectedOption = $row.find('.item-select option:selected');
                        var availableQuantity = parseInt(selectedOption.data('quantity')) || 0;
                        var itemName = selectedOption.text().split(' - ')[0];
                        
                        if (!inventoryQuantities[inventoryId]) {
                            inventoryQuantities[inventoryId] = {
                                total: 0,
                                available: availableQuantity,
                                name: itemName
                            };
                        }
                        
                        inventoryQuantities[inventoryId].total += quantity;
                    }
                });
                
                // Validate total quantities for each inventory item
                for (var invId in inventoryQuantities) {
                    var invData = inventoryQuantities[invId];
                    if (invData.total > invData.available) {
                        hasDuplicateErrors = true;
                        duplicateErrorMsg = 'Total quantity for "' + invData.name + '" exceeds available quantity. Available: ' + invData.available + ', Requested: ' + invData.total;
                        break;
                    }
                }
                
                if (hasDuplicateErrors) {
                    e.preventDefault();
                    alert(duplicateErrorMsg);
                    return false;
                }
                
                // Check if at least one item is selected
                var hasItems = $('#itemsContainer .item-select').filter(function() {
                    return $(this).val() !== '';
                }).length > 0;
                
                if (!hasItems) {
                    e.preventDefault();
                    alert('Please select at least one item.');
                    return false;
                }
                
                // Separate items into food_items and inventory_items arrays
                var foodItemsIndex = 0;
                var inventoryItemsIndex = 0;
                
                $('#itemsContainer .row').each(function() {
                    var $row = $(this);
                    var selectedValue = $row.find('.item-select').val();
                    var quantity = $row.find('.item-quantity').val();
                    
                    if (selectedValue && selectedValue !== '') {
                        var parts = selectedValue.split('_');
                        var type = parts[0];
                        var id = parts[1];
                        
                        if (type === 'food') {
                            // Create hidden inputs for food items
                            $row.append('<input type="hidden" name="food_items[' + foodItemsIndex + '][id]" value="' + id + '">');
                            $row.append('<input type="hidden" name="food_items[' + foodItemsIndex + '][quantity]" value="' + quantity + '">');
                            foodItemsIndex++;
                        } else if (type === 'inventory') {
                            // Create hidden inputs for inventory items
                            $row.append('<input type="hidden" name="inventory_items[' + inventoryItemsIndex + '][id]" value="' + id + '">');
                            $row.append('<input type="hidden" name="inventory_items[' + inventoryItemsIndex + '][quantity]" value="' + quantity + '">');
                            inventoryItemsIndex++;
                        }
                    }
                });
            });
            
            // Add item row
            $('#addItem').click(function () {
                if (itemCounter < maxItems) {
                    itemCounter++;
                    
                    // Clone first row (without events to avoid issues)
                    var $clonedRow = $('#itemsContainer > .row:first-child').clone();
                    
                    // Remove Bootstrap selectpicker wrapper from cloned row
                    $clonedRow.find('.bootstrap-select').remove();
                    
                    // Clear values
                    $clonedRow.find('select.item-select').val('');
                    $clonedRow.find('.item-quantity').val(1);
                    $clonedRow.find('.item-amount').val(0);
                    $clonedRow.find('.inventory-quantity-info').hide();
                    $clonedRow.find('.inventory-quantity-error').hide();
                    $clonedRow.find('.item-quantity').removeClass('has-error');
                    
                    // Update names and IDs
                    $clonedRow.find('select, input').each(function() {
                        var $this = $(this);
                        var name = $this.attr('name');
                        if (name) {
                            name = name.replace(/\[0\]/, '[' + itemCounter + ']');
                            $this.attr('name', name);
                        }
                        var id = $this.attr('id');
                        if (id) {
                            id = id.replace(/_0$/, '_' + itemCounter);
                            $this.attr('id', id);
                        }
                    });
                    
                    // Show remove button
                    $clonedRow.find('.remove-item').removeClass('hide');
                    
                    // Append to container
                    $('#itemsContainer').append($clonedRow);
                    
                    // Reinitialize selectpicker on the new row only
                    $clonedRow.find('select.selectpicker').selectpicker();
                    
                    // Reattach event handlers
                    attachItemHandlers();
                }
            });
            
            // Remove item row
            $('#itemsContainer').on('click', '.remove-item', function () {
                $(this).closest('.row').remove();
                itemCounter--; // Decrease counter
                calculateTotal();
            });
            
            // Item handlers
            attachItemHandlers();
        });
        
        function attachItemHandlers() {
            $('#itemsContainer').off('change', '.item-select').on('change', '.item-select', function() {
                var $row = $(this).closest('.row');
                var selectedOption = $(this).find('option:selected');
                var itemType = selectedOption.data('type');
                var price = selectedOption.data('price') || 0;
                var quantity = parseInt($row.find('.item-quantity').val()) || 1;
                var amount = price * quantity;
                
                // Handle inventory items - show quantity info and validate
                if (itemType === 'inventory') {
                    var availableQuantity = parseInt(selectedOption.data('quantity')) || 0;
                    
                    if (selectedOption.val() !== '') {
                        $row.find('.available-quantity').text(availableQuantity);
                        $row.find('.inventory-quantity-info').show();
                        validateInventoryQuantity($row, quantity, availableQuantity);
                        // Also validate against duplicates
                        validateDuplicateInventoryItems();
                    } else {
                        $row.find('.inventory-quantity-info').hide();
                        $row.find('.inventory-quantity-error').hide();
                        $row.find('.item-quantity').removeClass('has-error');
                        validateDuplicateInventoryItems();
                    }
                } else {
                    // Hide quantity info for food items
                    $row.find('.inventory-quantity-info').hide();
                    $row.find('.inventory-quantity-error').hide();
                    $row.find('.item-quantity').removeClass('has-error');
                }
                
                $row.find('.item-amount').val(amount);
                calculateTotal();
            });
            
            $('#itemsContainer').off('change', '.item-quantity').on('change', '.item-quantity', function() {
                var $row = $(this).closest('.row');
                var selectedOption = $row.find('.item-select option:selected');
                var itemType = selectedOption.data('type');
                var price = selectedOption.data('price') || 0;
                var quantity = parseInt($(this).val()) || 1;
                var amount = price * quantity;
                
                // Validate quantity for inventory items
                if (itemType === 'inventory' && selectedOption.val() !== '') {
                    var availableQuantity = parseInt(selectedOption.data('quantity')) || 0;
                    validateInventoryQuantity($row, quantity, availableQuantity);
                    // Also validate against duplicates
                    validateDuplicateInventoryItems();
                } else {
                    $row.find('.item-quantity').removeClass('has-error');
                    $row.find('.inventory-quantity-error').hide();
                }
                
                $row.find('.item-amount').val(amount);
                calculateTotal();
            });
        }
        
        function validateDuplicateInventoryItems() {
            // Collect all inventory items and their quantities
            var inventoryQuantities = {};
            
            $('#itemsContainer .row').each(function() {
                var $row = $(this);
                var selectedValue = $row.find('.item-select').val();
                
                if (selectedValue && selectedValue.startsWith('inventory_')) {
                    var parts = selectedValue.split('_');
                    var inventoryId = parts[1];
                    var quantity = parseInt($row.find('.item-quantity').val()) || 0;
                    var selectedOption = $row.find('.item-select option:selected');
                    var availableQuantity = parseInt(selectedOption.data('quantity')) || 0;
                    
                    if (!inventoryQuantities[inventoryId]) {
                        inventoryQuantities[inventoryId] = {
                            total: 0,
                            available: availableQuantity,
                            rows: []
                        };
                    }
                    
                    inventoryQuantities[inventoryId].total += quantity;
                    inventoryQuantities[inventoryId].rows.push($row);
                }
            });
            
            // Validate and highlight errors
            for (var invId in inventoryQuantities) {
                var invData = inventoryQuantities[invId];
                
                if (invData.total > invData.available) {
                    // Mark all rows with this inventory item as having error
                    invData.rows.forEach(function($row) {
                        var $quantityInput = $row.find('.item-quantity');
                        var $errorMsg = $row.find('.inventory-quantity-error');
                        $quantityInput.addClass('has-error');
                        $errorMsg.text('Total quantity across all rows: ' + invData.total + ' (Available: ' + invData.available + ')').show();
                        $quantityInput.css('border-color', '#a94442');
                    });
                } else {
                    // Clear errors if total is valid (but keep individual row errors if any)
                    invData.rows.forEach(function($row) {
                        var $quantityInput = $row.find('.item-quantity');
                        var selectedOption = $row.find('.item-select option:selected');
                        var rowQuantity = parseInt($row.find('.item-quantity').val()) || 0;
                        var availableQuantity = parseInt(selectedOption.data('quantity')) || 0;
                        
                        // Only clear if individual row is also valid
                        if (rowQuantity <= availableQuantity) {
                            var $errorMsg = $row.find('.inventory-quantity-error');
                            // Check if error is about total quantity
                            if ($errorMsg.text().indexOf('Total quantity') !== -1) {
                                $quantityInput.removeClass('has-error');
                                $errorMsg.hide();
                                $quantityInput.css('border-color', '');
                            }
                        }
                    });
                }
            }
        }
        
        function validateInventoryQuantity($row, requestedQuantity, availableQuantity) {
            var $quantityInput = $row.find('.item-quantity');
            var $errorMsg = $row.find('.inventory-quantity-error');
            
            if (requestedQuantity > availableQuantity) {
                $quantityInput.addClass('has-error');
                $errorMsg.text('Available quantity: ' + availableQuantity).show();
                $quantityInput.css('border-color', '#a94442');
            } else {
                $quantityInput.removeClass('has-error');
                $errorMsg.hide();
                $quantityInput.css('border-color', '');
            }
        }
        
        function calculateTotal() {
            var total = 0;
            $('#itemsContainer .item-amount').each(function() {
                var amount = parseFloat($(this).val()) || 0;
                total += amount;
            });
            $('#totalAmount').text(total.toLocaleString('en-IN', {maximumFractionDigits: 0}));
        }
    </script>
@stop

@section('footer_script_init')
    <script type="text/javascript">
        $(document).ready(function () {
            calculateTotal();
        });
    </script>
@stop

