<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryStoreRequest;
use App\Http\Requests\Admin\CategoryUpdateRequest;
use App\Http\Resources\Admin\AdminCategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;

class AdminCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): ResourceCollection
    {
        $categories = Category::query()->get();
        return AdminCategoryResource::collection($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryStoreRequest $request): JsonResponse
    {
        $category = Category::query()
            ->create($request->validated());
        return response()->json(new AdminCategoryResource($category), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): AdminCategoryResource
    {
        $category = Category::query()
            ->findOrFail($id);
        return new AdminCategoryResource($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryUpdateRequest $request, string $id): AdminCategoryResource
    {
        $category = Category::query()
            ->findOrFail($id);
        $category->update($request->validated());
        return new AdminCategoryResource($category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): Response
    {
        Category::query()
            ->findOrFail($id)
            ->delete();
        return response()->noContent();
    }
}
