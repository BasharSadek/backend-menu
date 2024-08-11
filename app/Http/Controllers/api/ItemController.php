<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Discount;
use App\Models\Item;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\Response;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ItemController extends Controller
{
    public function index()
    {
        $item = DB::table('items')
            ->select(
                'items.*',
                'categories.*',
                'discounts.*',
                'items.id as item_id',
            )
            ->join('categories', 'categories.id', 'items.category_id')
            ->join('discounts', 'items.discount_id', 'discounts.id')
            ->get();
        return response()->json([
            'success' => true,
            'item' => $item
        ], 200);
    }
    public function getcategory()
    {
        $myCategory = [];
        $category = Category::all();

        $category_id_in_category = [];
        foreach ($category as $key => $value) {
            array_push($category_id_in_category, $value['parent_id']);
        }
        foreach ($category as $key => $value) {
            if (in_array($value['id'], $category_id_in_category)) {
                continue;
            }
            array_push($myCategory, $value);
        }
        return response()->json([
            'success' => true,
            'category' => $myCategory
        ], 200);
    }
    public function create()
    {
        //
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_name' => ['required', 'string',],
            'item_description' => ['required', 'string',],
            'category_id' => ['required', 'integer',],
            'price' => ['required', 'integer',],
            'discount_id' => ['nullable', 'integer',],
        ]);
        if ($validator->fails()) {
            return new JsonResponse(['success' => false, 'message' => $validator->errors()], 422);
        }
     
        ///////////////////////////////// get categories that can added to items
        $myCategory = [];
        $category = Category::all();

        $category_id_in_category = [];
        foreach ($category as $key => $value) {
            array_push($category_id_in_category, $value['parent_id']);
        }
        foreach ($category as $key => $value) {
            if (in_array($value['id'], $category_id_in_category)) {
                continue;
            }
            array_push($myCategory, $value);
        }
        /////////////////////////////////
        $parent_id = 0;
        $discount_id = 0;  //discount for your parent
        foreach ($myCategory as $key => $value) {
            if ($value['id'] == $request['category_id']) {
                $parent_id = $request['category_id'];
                $current = Category::find($parent_id);
                $discount_id = $current['discount_id'];
                break;
            }
        }

        if ($parent_id == 0) {
            return response()->json([
                'success' => false,
                'message' => "category not found"
            ], 404);
        }

        $user = $request->user();
        $item = new Item();
        $item->item_name = $request['item_name'];
        $item->item_description = $request['item_description'];
        $item->price = $request['price'];
        $item->category_id = $request['category_id'];

        if ($request['discount_id'] == null) {
            $item['discount_id'] = $discount_id;
        } else {
            $discount = Discount::find($request['discount_id']);
            if (!$discount) {
                return response()->json([
                    'success' => false,
                    'message' => 'discount Not Found.'
                ], 404);
            }
            $item['discount_id'] = $request['discount_id'];
        }
        $item->save();

        return response()->json([
            'success' => true,
            'message' => 'Successful create item.'
        ], 200);
    }

    public function show(string $id)
    {
        $item = Item::find($id);
        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'item Not Found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'item' => $item
        ], 200);
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        $item = Item::find($id);
        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'item Not Found.'
            ], 404);
        }
        $validator = Validator::make($request->all(), [
            'item_name' => ['required', 'string',],
            'item_description' => ['required', 'string',],
            // 'discount_id' => ['required', 'integer',],
            // 'category_id' => ['required', 'integer',],
            'price' => ['required', 'integer',],
        ]);
        if ($validator->fails()) {
            return new JsonResponse(['success' => false, 'message' => $validator->errors()], 422);
        }
        // $discount = Discount::find($request['discount_id']);
        // if (!$discount) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'discount Not Found.'
        //     ], 404);
        // }
        // $category = Category::find($request['category_id']);
        // if (!$category) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'category Not Found.'
        //     ], 404);
        // }

        $user = $request->user();

        $item->item_name = $request['item_name'];
        $item->item_description = $request['item_description'];
        $item->price = $request['price'];
        // $item->user_id = $user['id'];

        $item->save();

        return response()->json([
            'success' => true,
            'message' => 'Successful update category.'
        ], 200);
    }
    public function destroy(string $id)
    {
        $item = Item::find($id);
        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => "item not found"
            ], 404);
        }

        $item->delete();
        return response()->json([
            'success' => true,
            'message' => "item successfully deleted."
        ], 200);
    }
}
