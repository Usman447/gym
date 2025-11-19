<?php

namespace App\Http\Controllers;

use Auth;
use App\FoodItem;
use Illuminate\Http\Request;

class FoodItemsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the Add Food Items page with list of all food items.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $foodItems = FoodItem::orderBy('created_at', 'desc')->get();
        $count = $foodItems->count();

        return view('food_items.index', compact('foodItems', 'count'));
    }

    /**
     * Store a newly created food item in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        //Model Validation
        $this->validate($request, [
            'name' => 'required|string|max:100',
            'amount' => 'required|integer|min:0',
        ]);

        $foodItem = new FoodItem($request->all());

        $foodItem->createdBy()->associate(Auth::user());
        $foodItem->updatedBy()->associate(Auth::user());

        $foodItem->save();

        flash()->success('Food item was successfully added');

        return redirect('food/items');
    }

    /**
     * Remove the specified food item from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $foodItem = FoodItem::findOrFail($id);
        $foodItem->delete();

        flash()->success('Food item was successfully removed');

        return redirect('food/items');
    }
}
