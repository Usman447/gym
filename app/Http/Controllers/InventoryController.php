<?php

namespace App\Http\Controllers;

use Auth;
use App\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the Add Inventory page with list of all inventory items.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $inventoryItems = Inventory::orderBy('created_at', 'desc')->get();
        $count = $inventoryItems->count();

        return view('inventory.index', compact('inventoryItems', 'count'));
    }

    /**
     * Store a newly created inventory item in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        //Model Validation
        $this->validate($request, [
            'name' => 'required|string|max:100',
            'amount' => 'required|integer|min:0',
            'quantity' => 'required|integer|min:0',
        ]);

        $inventoryItem = new Inventory($request->all());

        $inventoryItem->createdBy()->associate(Auth::user());
        $inventoryItem->updatedBy()->associate(Auth::user());

        $inventoryItem->save();

        flash()->success('Inventory item was successfully added');

        return redirect('food/inventory');
    }

    /**
     * Show the form for editing the specified inventory item.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $inventoryItem = Inventory::findOrFail($id);
        return view('inventory.edit', compact('inventoryItem'));
    }

    /**
     * Update the specified inventory item in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id, Request $request)
    {
        //Model Validation
        $this->validate($request, [
            'name' => 'required|string|max:100',
            'amount' => 'required|integer|min:0',
            'quantity' => 'required|integer|min:0',
        ]);

        $inventoryItem = Inventory::findOrFail($id);
        $inventoryItem->fill($request->all());
        $inventoryItem->updatedBy()->associate(Auth::user());
        $inventoryItem->save();

        flash()->success('Inventory item was successfully updated');

        return redirect('food/inventory');
    }

    /**
     * Remove the specified inventory item from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $inventoryItem = Inventory::findOrFail($id);
        $inventoryItem->delete();

        flash()->success('Inventory item was successfully removed');

        return redirect('food/inventory');
    }
}
