<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Discount;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\Response;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function home()
    {
        $discount = DB::table('discounts')->count();
        $category = DB::table('categories')->where('parent_id', '=', null)->count();
        $subcategory = DB::table('categories')->where('parent_id', '!=', null)->count();
        $item = DB::table('items')->count();
        return response()->json([
            'success' => true,
            'discount' => $discount,
            'category' => $category,
            'subcategory' => $subcategory,
            'item' => $item,
        ], 200);
    }
    public function index()
    {
        $category =  DB::table('categories')
            ->select(
                'categories.*',
                'discounts.*',
                'categories.id as category_id',
            )
            ->join('discounts', 'categories.discount_id', 'discounts.id')
            ->where('categories.parent_id', '=', null)
            ->get();
        return response()->json([
            'success' => true,
            'category' => $category
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
            'discount_id' => ['required', 'integer',],
        ]);
        if ($validator->fails()) {
            return new JsonResponse(['success' => false, 'message' => $validator->errors()], 422);
        }
        $discount = Discount::find($request['discount_id']);
        if (!$discount) {
            return response()->json([
                'success' => false,
                'message' => 'discount Not Found.'
            ], 404);
        }
        $user = $request->user();

        $category = new Category();
        $category->category_name = $request['category_name'];
        $category->category_description = $request['category_description'];
        $category->discount_id = $request['discount_id'];
        $category->user_id = $user['id'];

        $category->save();

        return response()->json([
            'success' => true,
            'message' => 'Successful created category.'
        ], 201);
    }

    public function show(string $id)
    {
        $category = Category::find($id);
        if ((!$category) || ($category['parent_id'] != null)) {
            return response()->json([
                'success' => false,
                'message' => 'category Not Found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'category' => $category
        ], 200);
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        $category = Category::find($id);
        if ((!$category) || ($category['parent_id'] != null)) {
            return response()->json([
                'success' => false,
                'message' => 'category Not Found.'
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

        $category->category_name = $request['category_name'];
        $category->category_description = $request['category_description'];
        $category->user_id = $user['id'];

        $category->save();

        return response()->json([
            'success' => true,
            'message' => 'Successful update category.'
        ], 200);
    }

    public function destroy(string $id)
    {
        $category = Category::find($id);
        if ((!$category) || ($category['parent_id'] != null)) {
            return response()->json([
                'success' => false,
                'message' => "category not found"
            ], 404);
        }

        $category->delete();
        return response()->json([
            'success' => true,
            'message' => "category successfully deleted."
        ], 200);
    }
}
