<?php

namespace App\Http\Controllers;

use App\Models\Wiki;
use App\Models\Category;
use App\Models\Department;
use App\Models\JobFunction;
use Illuminate\Http\Request;

class WikiController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role == 'user') {

            $userJobFunctions = $user->jobFunctions()->pluck('job_functions.id');

            // Retrieve Wikis that match the user's job functions
            $wikis = Wiki::with(['category', 'department', 'createdBy', 'editedBy', 'jobFunctions'])
                ->whereHas('jobFunctions', function ($query) use ($userJobFunctions) {
                    $query->whereIn('job_functions.id', $userJobFunctions);
                })->orWhere('general', true)
                ->where('status', 'published')
                ->get();
        }

        if ($user->role == 'admin') {

            $wikis = Wiki::with(['category', 'department', 'createdBy', 'editedBy', 'jobFunctions'])->get();
        }

        return response()->json($wikis);
    }

    public function store(Request $request)
    {
        try {

            // Validate the request data (you can add validation rules)
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
                'department_id' => 'required|exists:departments,id',
                'content' => 'required|string',
                'created_by' => 'exists:users,id',
                'edited_by' => 'exists:users,id',
                'status' => 'required|in:published,draft,unpublished',
                'general' => 'boolean',
            ]);

            // Set creating user
            $validatedData['edited_by'] = $request->user()->id;

            // Create a new wiki
            $wiki = Wiki::create($validatedData);

            $wiki->jobFunctions()->sync($request->jobFunctions);

            $wiki->save();

            return response()->json(['message' => 'Wiki created successfully!'], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation failed, handle the exception
            return response()->json(['errors' => $e->validator->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed', 'error' => $e], 500);
        }
    }

    function show($id)
    {
        try {
            $wiki = Wiki::with(['category', 'department', 'createdBy', 'editedBy', 'jobFunctions'])->findOrFail($id);

            return response()->json($wiki);
        } catch (\Exception $e) {
            return response()->json(['error' => $e, 'message' => 'Not Found'], 404);
        }
    }

    public function update(Request $request)
    {
        try {
            $wiki = Wiki::findOrFail($request->id);
            // Validate the request data (you can add validation rules)
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
                'department_id' => 'required|exists:departments,id',
                'content' => 'required|string',
                'created_by' => 'required|exists:users,id',
                'edited_by' => 'exists:users,id',
                'status' => 'required|in:published,draft,unpublished',
                'general' => 'boolean',
            ]);

            // return response()->json($validatedData);

            // Set editing user
            $validatedData['created_by'] = $request->user()->id;

            // Update the wiki with the validated data
            $wiki->update($validatedData);

            $wiki->jobFunctions()->sync($request->jobFunctions);

            $wiki->save();

            return response()->json(['message' => 'Wiki updated successfully!'], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation failed, handle the exception
            return response()->json(['errors' => $e->validator->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed', 'error' => $e], 500);
        }
    }

    public function delete($id)
    {
        try {
            // Find the wiki
            $wiki = Wiki::findOrFail($id);
            // Delete the wiki
            $wiki->delete();

            // Return a JSON response with a success message
            return response()->json(['message' => 'Wiki deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e, 'message' => 'Something Went Wrong'], 500);
        }
    }

    function data(Request $request)
    {
        $departments = Department::where('status', b'1')->get();

        $job_functions = JobFunction::where('status', b'1')->get();

        $categories = Category::where('status', b'1')->where('type', 'WIKI')->get();

        return response()->json([
            'departments' => $departments,
            'job_functions' => $job_functions,
            'categories' => $categories,
            'user' => $request->user()
        ], 200);
    }
}
