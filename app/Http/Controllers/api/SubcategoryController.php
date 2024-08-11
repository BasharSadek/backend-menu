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

class SubcategoryController extends Controller
{

    public function index()
    {
        $subcategory = DB::table('categories AS subcategory')
            ->select(
                'subcategory.*',
                'category.category_name as category_parent',
                'users.name',
                'discounts.*',
                'subcategory.id as subcategory_id',
            )
            ->join('users', 'users.id', 'subcategory.user_id')
            ->join('categories AS category', 'category.id', 'subcategory.parent_id')
            ->join('discounts', 'subcategory.discount_id', 'discounts.id')
            ->where('subcategory.parent_id', '!=', null)
            ->get();
        return response()->json([
            'success' => true,
            'subcategory' => $subcategory
        ], 200);
    }
    public function getcategory()
    {
        $myCategory = [];
        $category = Category::all();
        $item = Item::all();

        $category_id_in_item = [];
        foreach ($item as $key => $value) {
            array_push($category_id_in_item, $value['category_id']);
        }
        foreach ($category as $key => $value) {

            if (in_array($value['id'], $category_id_in_item)) {
                continue;
            }
            if ($value['parent_id'] == null) {
                array_push($myCategory, $value);
            } elseif (Category::find($value['parent_id']) == null) {
                array_push($myCategory, $value);
            } else {
                $cate = Category::find($value['parent_id']);
                if (Category::find($cate['parent_id']) == null) {
                    array_push($myCategory, $value);
                }
            }
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
            'category_name' => ['required', 'string',],
            'category_description' => ['required', 'string',],
            'parent_id' => ['required', 'integer',],
            'discount_id' => ['nullable', 'string',],
        ]);
        if ($validator->fails()) {
            return new JsonResponse(['success' => false, 'message' => $validator->errors()], 422);
        }
        //////////////////////////////////
        $myCategory = [];
        $category = Category::all();
        $item = Item::all();

        $category_id_in_item = [];
        foreach ($item as $key => $value) {
            array_push($category_id_in_item, $value['category_id']);
        }
        foreach ($category as $key => $value) {

            if (in_array($value['id'], $category_id_in_item)) {
                continue;
            }
            if ($value['parent_id'] == null) {
                array_push($myCategory, $value);
            } elseif (Category::find($value['parent_id']) == null) {
                array_push($myCategory, $value);
            } else {
                $cate = Category::find($value['parent_id']);
                if (Category::find($cate['parent_id']) == null) {
                    array_push($myCategory, $value);
                }
            }
        }
        //////////////////////////////////
        $parent_id = 0;
        $discount_id = 0;  //discount for your parent
        foreach ($myCategory as $key => $value) {
            if ($value['id'] == $request['parent_id']) {
                $parent_id = $request['parent_id'];
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
        $subcategory = new Category();
        $subcategory->category_name = $request['category_name'];
        $subcategory->category_description = $request['category_description'];
        $subcategory->parent_id = $request['parent_id'];
        $subcategory->user_id = $user['id'];
        if ($request['discount_id'] == null) {
            $subcategory['discount_id'] = $discount_id;
        } else {
            $discount = Discount::find($request['discount_id']);
            if (!$discount) {
                return response()->json([
                    'success' => false,
                    'message' => 'discount Not Found.'
                ], 404);
            }
            $subcategory['discount_id'] = $request['discount_id'];
        }
        $subcategory->save();

        return response()->json([
            'success' => true,
            'message' => 'Successful create subcategory.'
        ], 200);
    }
    public function show(string $id)
    {
        $subcategory = Category::find($id);
        if ((!$subcategory) || ($subcategory['parent_id'] == null)) {
            return response()->json([
                'success' => false,
                'message' => 'subcategory Not Found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'subcategory' => $subcategory
        ], 200);
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        $subcategory = Category::find($id);
        if ((!$subcategory) || ($subcategory['parent_id'] == null)) {
            return response()->json([
                'success' => false,
                'message' => 'subcategory Not Found.'
            ], 404);
        }
        $validator = Validator::make($request->all(), [
            'category_name' => ['required', 'string',],
            'category_description' => ['required', 'string',],
        ]);
        if ($validator->fails()) {
            return new JsonResponse(['success' => false, 'message' => $validator->errors()], 422);
        }

        $user = $request->user();

        $subcategory->category_name = $request['category_name'];
        $subcategory->category_description = $request['category_description'];
        $subcategory->user_id = $user['id'];

        $subcategory->save();

        return response()->json([
            'success' => true,
            'message' => 'Successful update subcategory.'
        ], 200);
    }

    public function destroy(string $id)
    {
        $subcategory = Category::find($id);
        if ((!$subcategory) || ($subcategory['parent_id'] == null)) {
            return response()->json([
                'success' => false,
                'message' => "subcategory not found"
            ], 404);
        }

        $subcategory->delete();
        return response()->json([
            'success' => true,
            'message' => "subcategory successfully deleted."
        ], 200);
    }
}
