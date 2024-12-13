<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryStoreRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return CategoryResource::collection(Category::all());
    }

    public function create()
    {
        return view('categories');
    }

    /**
     * Store a newly created category in storage.
     *
     * @param  \App\Http\Requests\CategoryStoreRequest  $request
     * @return \App\Http\Resources\CategoryResource
     */
    public function store(CategoryStoreRequest $request)
    {
        $category = Category::create($request->validated());
        return new CategoryResource($category);
    }
}
