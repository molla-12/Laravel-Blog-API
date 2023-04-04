<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Models\Image;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{

    public function index(Category $category)
    {
        return response([
            'categories' => $category->orderBy('created_at', 'desc')->get(),
        ], 200);

    }

    public function show(Category $category)
    {
        return response([
            'categories' => $category->where('id', $category->id)->get(),
        ], 200);
    }

    public function store(StoreCategoryRequest $request)
    {
        $category = DB::transaction(function () use ($request) {
            $category = Category::create([
                'name' => $request->validated('name'),
            ]);

            if ($request->hasfile('images')) {
                foreach ($request->file('images') as $file) {
                    $name = $file->getClientOriginalName();
                    $file->move(public_path() . '/Category/', $name);
                    $imgData[] = $name;
                }

                $ImageModal = new Image();
                $ImageModal->parentable_id = $category->id;
                $ImageModal->parentable_type = Category::class;
                $ImageModal->name = json_encode($imgData);
                $ImageModal->image_path = json_encode($imgData);

                $ImageModal->save();
            }

            return $category;
        });

        return response([
            'message' => 'Catgeory created.',
            'category' => $category,
        ], 200);
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category = $category->find($category->id);

        if (!$category) {
            return response([
                'message' => 'Category not found.',
            ], 403);
        }

        if ($category->user_id != auth()->user()->id) {
            return response([
                'message' => 'Permission denied.',
            ], 403);
        }

        $category->update([
            'name' => $request->validated('name'),
        ]);

        return response([
            'message' => 'Category updated.',
            'category' => $category,
        ], 200);
    }

    public function destroy(Category $category)
    {
        $category = $category->find($category->id);

        if (!$category) {
            return response([
                'message' => 'Categroy not found.',
            ], 403);
        }

        if ($category->user_id != auth()->user()->id) {
            return response([
                'message' => 'Permission denied.',
            ], 403);
        }

        $category->forceDelete();

        return response([
            'message' => 'Category deleted.',
        ], 200);
    }
}