<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\FoodOrder;
use App\FoodItem;
use App\Inventory;
use App\FoodOrderItem;
use App\Setting;
use Illuminate\Http\Request;

class FoodOrdersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of food orders.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        // Build query with filters
        $sorting_field = $request->sort_field ?: 'created_at';
        $sorting_direction = $request->sort_direction ?: 'desc';

        $query = FoodOrder::query();

        // Apply date range filter
        if ($request->drp_start && $request->drp_end) {
            $query->whereBetween('created_at', [$request->drp_start, $request->drp_end]);
        }

        // Apply sorting
        $query->orderBy($sorting_field, $sorting_direction);

        // Get paginated results
        $foodOrders = $query->paginate(10);
        $count = $foodOrders->total();

        // Calculate stats (synced with filters)
        $statsQuery = FoodOrder::query();
        if ($request->drp_start && $request->drp_end) {
            $statsQuery->whereBetween('created_at', [$request->drp_start, $request->drp_end]);
        }
        $totalOrders = $statsQuery->count();
        $totalAmount = $statsQuery->sum('total_amount');

        // Date range placeholder
        if (!$request->has('drp_start') || !$request->has('drp_end')) {
            $drp_placeholder = 'Select daterange filter';
        } else {
            $drp_placeholder = $request->drp_start.' - '.$request->drp_end;
        }

        $request->flash();

        return view('food_orders.index', compact('foodOrders', 'count', 'drp_placeholder', 'totalOrders', 'totalAmount'));
    }

    /**
     * Show the form for creating a new food order.
     *
     * @return Response
     */
    public function create()
    {
        $foodItems = FoodItem::orderBy('name', 'asc')->get();
        
        // Get inventory items with quantity > 0
        $inventoryItems = Inventory::where('quantity', '>', 0)->orderBy('name', 'asc')->get();
        
        // Generate order number (ORD1, ORD2, etc.)
        $lastOrderNumber = FoodOrder::orderBy('id', 'desc')->first();
        $nextOrderNumber = $lastOrderNumber ? ((int)str_replace('ORD', '', $lastOrderNumber->order_number)) + 1 : 1;
        $orderNumber = 'ORD'.$nextOrderNumber;

        return view('food_orders.create', compact('foodItems', 'inventoryItems', 'orderNumber'));
    }

    /**
     * Store a newly created food order in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            // Filter out empty food items (where id is not selected) BEFORE validation
            $validFoodItems = array_filter($request->food_items ?: [], function($item) {
                return !empty($item['id']);
            });
            
            // Filter out empty inventory items (where id is not selected) BEFORE validation
            $validInventoryItems = array_filter($request->inventory_items ?: [], function($item) {
                return !empty($item['id']);
            });
            
            if (empty($validFoodItems) && empty($validInventoryItems)) {
                return back()->withErrors(['food_items' => 'Please select at least one food item or inventory item.'])->withInput();
            }

            // Validate basic fields
            $this->validate($request, [
                'order_number' => 'required|string|max:50',
                'payment_mode' => 'required|integer',
            ]);

            // Validate each valid food item
            foreach ($validFoodItems as $index => $item) {
                $validator = \Validator::make($item, [
                    'id' => 'required|exists:mst_food_items,id',
                    'quantity' => 'required|integer|min:1',
                ]);
                
                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }
            }

            // Group inventory items by ID to check for duplicates and total quantity
            $inventoryItemGroups = [];
            foreach ($validInventoryItems as $index => $item) {
                $validator = \Validator::make($item, [
                    'id' => 'required|exists:mst_inventory,id',
                    'quantity' => 'required|integer|min:1',
                ]);
                
                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }
                
                $inventoryId = $item['id'];
                if (!isset($inventoryItemGroups[$inventoryId])) {
                    $inventoryItemGroups[$inventoryId] = [
                        'item' => Inventory::find($inventoryId),
                        'total_quantity' => 0,
                    ];
                }
                
                $inventoryItemGroups[$inventoryId]['total_quantity'] += (int)$item['quantity'];
            }
            
            // Validate total quantities for each inventory item
            foreach ($inventoryItemGroups as $inventoryId => $group) {
                $inventoryItem = $group['item'];
                $totalRequested = $group['total_quantity'];
                
                if ($inventoryItem->quantity < $totalRequested) {
                    return back()->withErrors(['inventory_items' => "Insufficient quantity for {$inventoryItem->name}. Available: {$inventoryItem->quantity}, Requested: {$totalRequested}"])->withInput();
                }
            }

            // Calculate total amount
            $totalAmount = 0;
            foreach ($validFoodItems as $item) {
                $foodItem = FoodItem::find($item['id']);
                $itemTotal = $foodItem->amount * (int)$item['quantity'];
                $totalAmount += $itemTotal;
            }
            
            foreach ($validInventoryItems as $item) {
                $inventoryItem = Inventory::find($item['id']);
                $itemTotal = $inventoryItem->amount * (int)$item['quantity'];
                $totalAmount += $itemTotal;
            }

            // Create food order
            $foodOrder = new FoodOrder([
                'order_number' => $request->order_number,
                'total_amount' => (int)$totalAmount,
                'payment_mode' => (int)$request->payment_mode,
            ]);

            $foodOrder->createdBy()->associate(Auth::user());
            $foodOrder->updatedBy()->associate(Auth::user());
            $foodOrder->save();

            // Create food order items
            foreach ($validFoodItems as $item) {
                $foodItem = FoodItem::find($item['id']);
                
                $orderItem = new FoodOrderItem([
                    'food_order_id' => $foodOrder->id,
                    'food_item_id' => $foodItem->id,
                    'inventory_id' => null,
                    'quantity' => (int)$item['quantity'],
                    'item_amount' => $foodItem->amount,
                ]);

                $orderItem->createdBy()->associate(Auth::user());
                $orderItem->updatedBy()->associate(Auth::user());
                $orderItem->save();
            }
            
            // Create inventory order items and subtract quantities
            // Use grouped quantities to ensure we only subtract once per inventory item
            $processedInventoryIds = [];
            foreach ($validInventoryItems as $item) {
                $inventoryId = $item['id'];
                
                // Skip if we've already processed this inventory item
                if (in_array($inventoryId, $processedInventoryIds)) {
                    continue;
                }
                
                $inventoryItem = Inventory::find($inventoryId);
                $orderQuantity = $inventoryItemGroups[$inventoryId]['total_quantity'];
                
                // Double-check quantity is still available (prevent race conditions)
                if ($inventoryItem->quantity < $orderQuantity) {
                    DB::rollback();
                    return back()->withErrors(['inventory_items' => "Insufficient quantity for {$inventoryItem->name}. Available: {$inventoryItem->quantity}, Requested: {$orderQuantity}"])->withInput();
                }
                
                // Subtract quantity from inventory (ensure it doesn't go negative)
                $newQuantity = max(0, $inventoryItem->quantity - $orderQuantity);
                $inventoryItem->quantity = $newQuantity;
                $inventoryItem->updatedBy()->associate(Auth::user());
                $inventoryItem->save();
                
                $processedInventoryIds[] = $inventoryId;
            }
            
            // Now create order items for all inventory items
            foreach ($validInventoryItems as $item) {
                $inventoryItem = Inventory::find($item['id']);
                $orderQuantity = (int)$item['quantity'];
                
                $orderItem = new FoodOrderItem([
                    'food_order_id' => $foodOrder->id,
                    'food_item_id' => null,
                    'inventory_id' => $inventoryItem->id,
                    'quantity' => $orderQuantity,
                    'item_amount' => $inventoryItem->amount,
                ]);

                $orderItem->createdBy()->associate(Auth::user());
                $orderItem->updatedBy()->associate(Auth::user());
                $orderItem->save();
            }

            DB::commit();
            flash()->success('Food order was successfully created');

            return redirect('food/orders');
        } catch (\Exception $e) {
            DB::rollback();
            flash()->error('Error while creating the food order: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    /**
     * Display the specified food order (invoice view).
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $foodOrder = FoodOrder::with(['orderItems.foodItem', 'orderItems.inventory'])->findOrFail($id);
        $settings = \Utilities::getSettings();

        return view('food_orders.show', compact('foodOrder', 'settings'));
    }

    /**
     * Show the form for editing the specified food order.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $foodOrder = FoodOrder::with(['orderItems.foodItem', 'orderItems.inventory'])->findOrFail($id);
        $foodItems = FoodItem::orderBy('name', 'asc')->get();
        $inventoryItems = Inventory::orderBy('name', 'asc')->get();

        return view('food_orders.edit', compact('foodOrder', 'foodItems', 'inventoryItems'));
    }

    /**
     * Update the specified food order in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id, Request $request)
    {
        DB::beginTransaction();

        try {
            $foodOrder = FoodOrder::findOrFail($id);

            // Get existing order items to restore inventory quantities if needed
            $existingOrderItems = FoodOrderItem::where('food_order_id', $foodOrder->id)->get();
            
            // Restore inventory quantities from existing order items
            foreach ($existingOrderItems as $existingItem) {
                if ($existingItem->inventory_id) {
                    $inventoryItem = Inventory::find($existingItem->inventory_id);
                    if ($inventoryItem) {
                        $inventoryItem->quantity += $existingItem->quantity;
                        $inventoryItem->save();
                    }
                }
            }

            // Filter out empty food items (where id is not selected) BEFORE validation
            $validFoodItems = array_filter($request->food_items ?: [], function($item) {
                return !empty($item['id']);
            });
            
            // Filter out empty inventory items (where id is not selected) BEFORE validation
            $validInventoryItems = array_filter($request->inventory_items ?: [], function($item) {
                return !empty($item['id']);
            });
            
            if (empty($validFoodItems) && empty($validInventoryItems)) {
                return back()->withErrors(['food_items' => 'Please select at least one food item or inventory item.'])->withInput();
            }

            // Validate basic fields
            $this->validate($request, [
                'payment_mode' => 'required|integer',
            ]);

            // Validate each valid food item
            foreach ($validFoodItems as $index => $item) {
                $validator = \Validator::make($item, [
                    'id' => 'required|exists:mst_food_items,id',
                    'quantity' => 'required|integer|min:1',
                ]);
                
                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }
            }

            // Group inventory items by ID to check for duplicates and total quantity
            $inventoryItemGroups = [];
            foreach ($validInventoryItems as $index => $item) {
                $validator = \Validator::make($item, [
                    'id' => 'required|exists:mst_inventory,id',
                    'quantity' => 'required|integer|min:1',
                ]);
                
                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }
                
                $inventoryId = $item['id'];
                if (!isset($inventoryItemGroups[$inventoryId])) {
                    $inventoryItemGroups[$inventoryId] = [
                        'item' => Inventory::find($inventoryId),
                        'total_quantity' => 0,
                    ];
                }
                
                $inventoryItemGroups[$inventoryId]['total_quantity'] += (int)$item['quantity'];
            }
            
            // Validate total quantities for each inventory item
            foreach ($inventoryItemGroups as $inventoryId => $group) {
                $inventoryItem = $group['item'];
                $totalRequested = $group['total_quantity'];
                
                if ($inventoryItem->quantity < $totalRequested) {
                    return back()->withErrors(['inventory_items' => "Insufficient quantity for {$inventoryItem->name}. Available: {$inventoryItem->quantity}, Requested: {$totalRequested}"])->withInput();
                }
            }

            // Calculate total amount
            $totalAmount = 0;
            foreach ($validFoodItems as $item) {
                $foodItem = FoodItem::find($item['id']);
                $itemTotal = $foodItem->amount * (int)$item['quantity'];
                $totalAmount += $itemTotal;
            }
            
            foreach ($validInventoryItems as $item) {
                $inventoryItem = Inventory::find($item['id']);
                $itemTotal = $inventoryItem->amount * (int)$item['quantity'];
                $totalAmount += $itemTotal;
            }

            // Update food order
            $foodOrder->total_amount = (int)$totalAmount;
            $foodOrder->payment_mode = (int)$request->payment_mode;
            $foodOrder->updatedBy()->associate(Auth::user());
            $foodOrder->save();

            // Delete existing order items
            FoodOrderItem::where('food_order_id', $foodOrder->id)->delete();

            // Create new food order items
            foreach ($validFoodItems as $item) {
                $foodItem = FoodItem::find($item['id']);
                
                $orderItem = new FoodOrderItem([
                    'food_order_id' => $foodOrder->id,
                    'food_item_id' => $foodItem->id,
                    'inventory_id' => null,
                    'quantity' => (int)$item['quantity'],
                    'item_amount' => $foodItem->amount,
                ]);

                $orderItem->createdBy()->associate(Auth::user());
                $orderItem->updatedBy()->associate(Auth::user());
                $orderItem->save();
            }
            
            // Create inventory order items and subtract quantities
            // Use grouped quantities to ensure we only subtract once per inventory item
            $processedInventoryIds = [];
            foreach ($validInventoryItems as $item) {
                $inventoryId = $item['id'];
                
                // Skip if we've already processed this inventory item
                if (in_array($inventoryId, $processedInventoryIds)) {
                    continue;
                }
                
                $inventoryItem = Inventory::find($inventoryId);
                $orderQuantity = $inventoryItemGroups[$inventoryId]['total_quantity'];
                
                // Double-check quantity is still available (prevent race conditions)
                if ($inventoryItem->quantity < $orderQuantity) {
                    DB::rollback();
                    return back()->withErrors(['inventory_items' => "Insufficient quantity for {$inventoryItem->name}. Available: {$inventoryItem->quantity}, Requested: {$orderQuantity}"])->withInput();
                }
                
                // Subtract quantity from inventory (ensure it doesn't go negative)
                $newQuantity = max(0, $inventoryItem->quantity - $orderQuantity);
                $inventoryItem->quantity = $newQuantity;
                $inventoryItem->updatedBy()->associate(Auth::user());
                $inventoryItem->save();
                
                $processedInventoryIds[] = $inventoryId;
            }
            
            // Now create order items for all inventory items
            foreach ($validInventoryItems as $item) {
                $inventoryItem = Inventory::find($item['id']);
                $orderQuantity = (int)$item['quantity'];
                
                $orderItem = new FoodOrderItem([
                    'food_order_id' => $foodOrder->id,
                    'food_item_id' => null,
                    'inventory_id' => $inventoryItem->id,
                    'quantity' => $orderQuantity,
                    'item_amount' => $inventoryItem->amount,
                ]);

                $orderItem->createdBy()->associate(Auth::user());
                $orderItem->updatedBy()->associate(Auth::user());
                $orderItem->save();
            }

            DB::commit();
            flash()->success('Food order was successfully updated');

            return redirect('food/orders');
        } catch (\Exception $e) {
            DB::rollback();
            flash()->error('Error while updating the food order: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    /**
     * Remove the specified food order from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            // Get order items to restore inventory quantities
            $orderItems = FoodOrderItem::where('food_order_id', $id)->get();
            
            // Restore inventory quantities
            foreach ($orderItems as $orderItem) {
                if ($orderItem->inventory_id) {
                    $inventoryItem = Inventory::find($orderItem->inventory_id);
                    if ($inventoryItem) {
                        $inventoryItem->quantity += $orderItem->quantity;
                        $inventoryItem->save();
                    }
                }
            }
            
            // Delete order items
            FoodOrderItem::where('food_order_id', $id)->delete();
            
            // Delete food order
            FoodOrder::destroy($id);

            DB::commit();
            flash()->success('Food order was successfully deleted');

            return redirect('food/orders');
        } catch (\Exception $e) {
            DB::rollback();
            flash()->error('Error while deleting the food order: ' . $e->getMessage());
            return back();
        }
    }
}
