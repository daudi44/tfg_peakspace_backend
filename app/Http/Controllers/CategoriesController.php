<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Enums\CategoryType;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    // add Category
    public function addCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        if ($request->parent_category_id) {
            $parentCategory = Category::find($request->parent_category_id);
            if (!$parentCategory || $parentCategory->parent_category_id != null) {
                return response()->json(['message' => 'The selected parent category is not usable.'], 404);
            }
        }

        $category = new Category();
        $category->name = $request->name;
        $category->type = $request->type;
        $category->user_id = auth()->id();
        $category->parent_category_id = $request->parent_category_id ? $request->parent_category_id : null;
        $category->save();

        return response()->json(['message' => 'Category created successfully', 'category' => $category], 201);
    }

    // edit Category
    
    // delete Category
    public function deleteCategory(Request $request)
    {
        $request->validate([
            'category_id' => 'required|integer|exists:categories,id',
        ]);

        $category = Category::find($request->category_id);
        if ($category->user_id != auth()->id() || $category->children()->count() > 0) {
            return response()->json(['message' => 'You are not authorized to delete this category.'], 403);
        }

        $category->delete();

        return response()->json(['message' => 'Category deleted successfully'], 200);
    }

    // get economy categories
    public function getEconomyCategories(Request $request)
    {
        $categories = Category::where('user_id', auth()->id())
            ->where('type', CategoryType::ECONOMY)
            ->with(['parent', 'children'])
            ->get();

        return response()->json(['categories' => $categories], 200);
    }
    // get productive categories
    public function getProductivityCategories(Request $request)
    {
        $categories = Category::where('user_id', auth()->id())
            ->where('type', CategoryType::PRODUCTIVITY)
            ->with(['parent', 'children'])
            ->get();

        return response()->json(['categories' => $categories], 200);
    }
}
