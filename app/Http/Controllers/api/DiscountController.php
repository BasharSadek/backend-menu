<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\Response;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DiscountController extends Controller
{
    public function index()
    {
        $discount = Discount::all();
        return response()->json([
            'success' => true,
            'discount' => $discount
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'discount_code' => ['required', 'string',],
            'discount_value' => ['required', 'integer',],
        ]);
        if ($validator->fails()) {
            return new JsonResponse(['success' => false, 'message' => $validator->errors()], 422);
        }
        if (!($request['discount_value'] > 0 && $request['discount_value'] <= 100)) {
            return response()->json([
                'success' => false,
                'message' => 'please send discount value between 1-100.'
            ], 401);
        }
        $discount = Discount::all();
        foreach ($discount as $key => $value) {
            if ($value['discount_code'] == $request['discount_code']) {
                return response()->json([
                    'success' => false,
                    'message' => 'discount code is token'
                ], 401);
            }
        }
        $new = new Discount();
        $new->discount_code = $request['discount_code'];
        $new->discount_value = $request['discount_value'];
        $new->save();

        return response()->json([
            'success' => true,
            'message' => 'Successful created discount.'
        ], 201);
    }

    public function show(string $id)
    {
        $discount = Discount::find($id);
        if (!$discount) {
            return response()->json([
                'success' => false,
                'message' => 'discount Not Found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'discount' => $discount
        ], 200);
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        $discount = Discount::find($id);
        if (!$discount) {
            return response()->json([
                'success' => false,
                'message' => 'discount Not Found.'
            ], 404);
        }
        $validator = Validator::make($request->all(), [
            'discount_code' => ['required', 'string',],
            'discount_value' => ['required', 'integer',],
        ]);
        if ($validator->fails()) {
            return new JsonResponse(['success' => false, 'message' => $validator->errors()], 422);
        }
        if (!($request['discount_value'] > 0 && $request['discount_value'] <= 100)) {
            return response()->json([
                'success' => false,
                'message' => 'please send discount value between 1-100.'
            ], 401);
        }
        $all = Discount::all();
        foreach ($all as $key => $value) {
            if ($value['discount_code'] == $request['discount_code']) {
                return response()->json([
                    'success' => false,
                    'message' => 'discount code is token.'
                ], 401);
            }
        }
        $discount->discount_code = $request['discount_code'];
        $discount->discount_value = $request['discount_value'];
        $discount->save();

        return response()->json([
            'success' => true,
            'message' => 'Successful update discount.'
        ], 200);
    }

    public function destroy(string $id)
    {   
        $discount = Discount::find($id);
        if (!$discount) {
            return response()->json([
                'success' => false,
                'message' => "discount not found"
            ], 404);
        }

        $discount->delete();
        return response()->json([
            'success' => true,
            'message' => "discount successfully deleted."
        ], 200);
    }
}
