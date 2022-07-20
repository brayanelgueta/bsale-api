<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function return_data($request){
        $data = [
            'products' => $this->get_products($request),
        ];
        return $data;
    }

    public function index(Request $request)
    {
        return response()->json($this->return_data($request), 200);
    }

    public function get_products($request){

        $search = $request->search;
        $order = $request->order;
        
        $select = [
            'product.name                       as product_name',
            'product.price                      as price',
            'coalesce(product.url_image, "")    as image',
            'product.discount                   as discount',
            'category.name                      as category_name'
        ];

        $products = Product::join('category', 'product.category', '=', 'category.id')
            ->where(function ($query) use ($search) {
                if($search && $search != ''){
                    $query->orWhere('product.name', 'like', '%' . $search . '%');
                    $query->orWhere('product.price', 'like', '%' . $search . '%');
                    $query->orWhere('category.name', 'like', '%' . $search . '%');
                }
            })
            ->selectRaw(implode(',', $select))
            ->orderBy('product.name', $order ? $order : 'asc')
            ->paginate(50);

        return $products;
    }

  
}
