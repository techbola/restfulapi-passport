<?php

namespace App\Http\Controllers\Seller;

use App\Seller;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class SellerController extends ApiController
{

    public function index()
    {
        $sellers = Seller::has('products')->get();

//        return response()->json(['data' => $sellers], 200);
        return $this->showAll($sellers);
    }

    public function show(Seller $seller)
    {
//        $seller = Seller::has('products')->findOrFail($id);

//        return response()->json(['data' => $seller], 200);
        return $this->showOne($seller);
    }

}
