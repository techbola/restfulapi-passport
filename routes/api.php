<?php

use Illuminate\Http\Request;

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::resource('buyers', 'Buyer\BuyerController', ['only' => ['index', 'show']]);
Route::resource('categories', 'Category\CategoryController', ['except' => ['create', 'edit']]);
Route::resource('products', 'Product\ProductController', ['only' => ['index', 'show']]);
Route::resource('sellers', 'Seller\SellerController', ['only' => ['index', 'show']]);
Route::resource('transactions', 'Transaction\TransactionController', ['only' => ['index', 'show']]);
Route::resource('users', 'User\UserController', ['except' => ['create', 'edit ']]);