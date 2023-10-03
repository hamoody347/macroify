<?php

namespace App\Http\Controllers;

use App\Models\FAQ;
use App\Models\Category;
use App\Models\Department;
use App\Models\JobFunction;
use Illuminate\Http\Request;

class FAQController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role == 'user') {

            $userJobFunctions = $user->jobFunctions()->pluck('job_functions.id');

            // Retrieve FAQs that match the user's job functions
            $faqs = FAQ::with(['category', 'department', 'createdBy', 'editedBy', 'jobFunctions'])
                ->whereHas('jobFunctions', function ($query) use ($userJobFunctions) {
                    $query->whereIn('job_functions.id', $userJobFunctions);
                })->orWhere('general', true)
                ->where('status', 'published')
                ->get();
        }

        if ($user->role == 'admin') {

            $faqs = FAQ::with(['category', 'department', 'createdBy', 'editedBy', 'jobFunctions'])->get();
        }

        return response()->json($faqs);
    }

    public function store(Request $request)
    {
        try {
            // Validate the request data (you can add validation rules)
            $validatedData = $request->validate([
                'question' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
                'department_id' => 'required|exists:departments,id',
                'answer' => 'required|string',
                'created_by' => 'exists:users,id',
                'status' => 'required|in:published,draft,unpublished',
                'general' => 'boolean',
            ]);

            // Set creating user
            $validatedData['created_by'] = $request->user()->id;

            // Create a new FAQ
            $faq = FAQ::create($validatedData);

            $faq->jobFunctions()->sync($request->jobFunctions);

            $faq->save();

            return response()->json(['message' => 'FAQ created successfully!'], 201);
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
            $faq = FAQ::with(['category', 'department', 'createdBy', 'editedBy', 'jobFunctions'])->findOrFail($id);

            return response()->json($faq);
        } catch (\Exception $e) {
            return response()->json(['error' => $e, 'message' => 'Not Found'], 404);
        }
    }

    public function update(Request $request)
    {
        try {
            $faq = FAQ::findOrFail($request->id);
            // Validate the request data (you can add validation rules)
            $validatedData = $request->validate([
                'question' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
                'department_id' => 'required|exists:departments,id',
                'answer' => 'required|string',
                'status' => 'required|in:published,draft,unpublished',
                'general' => 'boolean',
            ]);


            // Set editing user
            $validatedData['edited_by'] = $request->user()->id;

            // Update the FAQ with the validated data
            $faq->update($validatedData);

            $faq->jobFunctions()->sync($request->jobFunctions);

            $faq->save();

            return response()->json(['message' => 'FAQ updated successfully!'], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation failed, handle the exception
            return response()->json(['errors' => $e->validator->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed', 'error' => $e], 500);
        }
    }

    public function destroy($id)
    {
        try {
            // Find the wiki
            $wiki = FAQ::findOrFail($id);
            // Delete the wiki
            $wiki->delete();

            // Return a JSON response with a success message
            return response()->json(['message' => 'FAQ deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e, 'message' => 'Something Went Wrong'], 500);
        }
    }

    function data(Request $request)
    {
        $departments = Department::where('status', b'1')->get();

        $job_functions = JobFunction::where('status', b'1')->get();

        $categories = Category::where('status', b'1')->where('type', 'FAQ')->get();

        return response()->json([
            'departments' => $departments,
            'job_functions' => $job_functions,
            'categories' => $categories,
            'user' => $request->user()
        ], 200);
    }
}
