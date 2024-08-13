<?php

namespace App\Http\Controllers;

use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class ServiceCategoryController extends Controller
{
    public function create()
    {
        return view('book-categories/add-category');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],

        ]);

        $category = ServiceCategory::create([
            'name' => $validatedData['name'],
            'created_by' => auth()->id(),

        ]);

        return redirect()->route('category.all')->with(
            'status',
            'New Category was added successfully.'
        );
    }

    public function index()
    {
        $categories = ServiceCategory::select('id', 'name', 'slug')->get();
        return view('book-categories/category-index', ['categories' => $categories]);
    }
}
