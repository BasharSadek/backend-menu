<?php

use App\Http\Controllers\api\DiscountController;
use App\Http\Controllers\api\CategoryController;
use App\Http\Controllers\api\ItemController;
use App\Http\Controllers\api\SubcategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/home', [CategoryController::class, 'home']);

// discount routes
Route::group(['prefix' => 'discount', 'as' => 'discount.'], function () {
    Route::get('index', [DiscountController::class, 'index']);
    Route::post('store', [DiscountController::class, 'store']);
    Route::post('update/{id}', [DiscountController::class, 'update']);
    Route::get('show/{id}', [DiscountController::class, 'show']);
    Route::get('destroy/{id}', [DiscountController::class, 'destroy']);
});

// category routes
Route::group(['prefix' => 'category', 'as' => 'category.'], function () {
    Route::get('index', [CategoryController::class, 'index']);
    Route::post('store', [CategoryController::class, 'store']);
    Route::post('update/{id}', [CategoryController::class, 'update']);
    Route::get('show/{id}', [CategoryController::class, 'show']);
    Route::get('destroy/{id}', [CategoryController::class, 'destroy']);
});

// subcategory routes
Route::group(['prefix' => 'subcategory', 'as' => 'subcategory.'], function () {
    Route::get('index', [SubcategoryController::class, 'index']);
    Route::post('store', [SubcategoryController::class, 'store']);
    Route::post('update/{id}', [SubcategoryController::class, 'update']);
    Route::get('show/{id}', [SubcategoryController::class, 'show']);
    Route::get('destroy/{id}', [SubcategoryController::class, 'destroy']);
    Route::get('getcategory', [SubcategoryController::class, 'getcategory']);
});

// item routes
Route::group(['prefix' => 'item', 'as' => 'item.'], function () {
    Route::get('index', [ItemController::class, 'index']);
    Route::post('store', [ItemController::class, 'store']);
    Route::post('update/{id}', [ItemController::class, 'update']);
    Route::get('show/{id}', [ItemController::class, 'show']);
    Route::get('destroy/{id}', [ItemController::class, 'destroy']);
    Route::get('getcategory', [ItemController::class, 'getcategory']);
});