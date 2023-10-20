<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Department;
use App\Models\JobFunction;
use App\Models\SOP;
use Illuminate\Http\Request;

class SOPController extends Controller
{
    function index(Request $request)
    {
        $user = $request->user();

        if ($user->role == 'user') {

            $userJobFunctions = $user->jobFunctions()->pluck('job_functions.id');

            // Retrieve SOPs that match the user's job functions
            $sops = SOP::with(['category', 'department', 'createdBy', 'editedBy', 'jobFunctions'])
                ->whereHas('jobFunctions', function ($query) use ($userJobFunctions) {
                    $query->whereIn('job_functions.id', $userJobFunctions);
                })->orWhere('general', true)
                ->get();
        }

        if ($user->role == 'admin') {

            $sops = SOP::with(['category', 'department', 'createdBy', 'editedBy', 'jobFunctions'])->get();
        }

        return response()->json($sops);
    }

    function show($id)
    {
        try {
            $sop = SOP::with(['category', 'department', 'createdBy', 'editedBy', 'jobFunctions'])->findOrFail($id);

            return response()->json($sop);
        } catch (\Exception $e) {
            return response()->json(['error' => $e, 'message' => 'Not Found'], 404);
        }
    }

    function store(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string',
                'content' => 'required|string',
                'department_id' => 'required|exists:departments,id',
                'category_id' => 'required|exists:categories,id',
                'created_by' => 'exists:users,id',
                'edited_by' => 'exists:users,id',
                'status' => 'boolean',
                'general' => 'boolean',
            ]);

            $data['created_by'] = $request->user()->id;

            $sop = SOP::create($data);

            $sop->jobFunctions()->sync($request->jobFunctions);

            $sop->save();

            return response()->json(['message' => 'Created Successfully!'], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation failed, handle the exception
            return response()->json(['errors' => $e->validator->errors(), 'validator' => true], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed', 'error' => $e], 500);
        }
    }

    function update(Request $request)
    {
        try {

            $sop = SOP::findOrFail($request->id);

            $data = $request->validate([
                'name' => 'required|string',
                'content' => 'required|string',
                'department_id' => 'required|exists:departments,id',
                'category_id' => 'required|exists:categories,id',
                'created_by' => 'exists:users,id',
                'edited_by' => 'exists:users,id',
                'status' => 'boolean',
                'general' => 'boolean',
            ]);

            $data['edited_by'] = $request->user()->id;

            $sop->update($data);

            $sop->jobFunctions()->sync($request->jobFunctions);

            $sop->save();

            return response()->json(['message' => 'Updated successfully!'], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation failed, handle the exception
            return response()->json(['errors' => $e->validator->errors(), 'validator' => true], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed', 'error' => $e], 500);
        }
    }

    function data(Request $request)
    {
        $departments = Department::where('status', b'1')->get();

        $job_functions = JobFunction::where('status', b'1')->get();

        $categories = Category::where('status', b'1')->where('type', 'SOP')->get();

        return response()->json([
            'departments' => $departments,
            'job_functions' => $job_functions,
            'categories' => $categories,
            'user' => $request->user()
        ], 200);
    }

    function delete($id)
    {
        try {
            $sop = SOP::findOrFail($id);

            $sop->delete();

            return response()->json(['message' => 'Deleted successfully!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e, 'message' => 'Something Went Wrong'], 500);
        }
    }
}
