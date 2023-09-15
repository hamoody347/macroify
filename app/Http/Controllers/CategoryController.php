<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    function index()
    {
        $categories = Category::with(['sops'])->where('status', b'1')->get();

        return response()->json($categories);
    }

    function show($id)
    {
        try {
            $category = Category::with(['sops'])->findOrFail($id);

            return response()->json($category);
        } catch (\Exception $e) {
            return response()->json(['error' => $e, 'message' => 'User Not Found'], 404);
        }
    }

    function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'description' => 'string',
            'type' => 'required|in:SOP,LMS,FAQ,WIKI,Policy,OKR',
            'status' => 'boolean',
        ]);

        Category::create($data);

        return response()->json(['message' => 'User Created Successfully!'], 201);
    }

    function update(Request $request)
    {
        try {

            $category = Category::findOrFail($request->id);

            $data = $request->validate([
                'name' => 'required|string',
                'description' => 'string',
                'type' => 'required|in:SOP,LMS,FAQ,WIKI,Policy,OKR',
                'status' => 'boolean',
            ]);

            $category->update($data);

            return response()->json(['message' => 'Updated successfully!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e, 'message' => 'User Not Found'], 404);
        }
    }
}
