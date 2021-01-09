<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCollection as ProductResourceCollection;
use App\Http\Resources\Product as ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Récupère la liste des produits suivis
     *
     * @return ProductResourceCollection
     */
    public function index(): ProductResourceCollection
    {
        return new ProductResourceCollection(Product::all());
    }

    /**
     * Récupère un produit et ses articles
     *
     * @param Product $product
     *
     * @return ProductResource
     */
    public function show(Product $product): ProductResource
    {
        return new ProductResource($product->load('items'));
    }
}
