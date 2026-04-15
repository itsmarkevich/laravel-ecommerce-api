<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductStoreRequest;
use App\Http\Requests\Admin\ProductUpdateRequest;
use App\Http\Resources\Admin\AdminProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;

class AdminProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): ResourceCollection
    {
        $products = Product::query()
            ->with('category')
            ->get();
        return AdminProductResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductStoreRequest $request): JsonResponse
    {
        $product = Product::query()
            ->create($request->validated())
            ->load('category');
        return response()->json(new AdminProductResource($product), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): AdminProductResource
    {
        $product = Product::query()
            ->with('category')
            ->findOrFail($id);
        return new AdminProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductUpdateRequest $request, string $id): AdminProductResource
    {
        $product = Product::query()
            ->findOrFail($id);
        $product->update($request->validated());
        return (new AdminProductResource($product->load('category')));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): Response
    {
        Product::query()
            ->findOrFail($id)
            ->delete();
        return response()->noContent();
    }
}
