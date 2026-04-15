<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryWithProductsResource;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductController extends Controller
{
    public function index(): ResourceCollection
    {
        $categories = Category::query()->with('products')->get();
        return CategoryWithProductsResource::collection($categories);
    }

    public function category(string $slug): CategoryWithProductsResource
    {
        $category = Category::query()->findBySlug($slug)
            ->with('products')
            ->firstOrFail();
        return new CategoryWithProductsResource($category);
    }

    public function show(string $category_slug, string $product_slug): ProductResource
    {
        $product = Product::whereHas('category', function ($query) use ($category_slug) {
            $query->where('slug', $category_slug);
        })->where('slug', $product_slug)->firstOrFail();
        return new ProductResource($product);
    }
}
