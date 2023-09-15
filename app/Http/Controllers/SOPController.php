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

        $userJobFunctions = $user->jobFunctions()->pluck('id');

        // Retrieve SOPs that match the user's job functions
        $sops = SOP::with(['category', 'department', 'createdBy', 'editedBy', 'jobFunctions'])->whereHas('jobFunctions', function ($query) use ($userJobFunctions) {
            $query->whereIn('job_functions.id', $userJobFunctions);
        })->get();

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
        $data = $request->validate([
            'name' => 'required|string',
            'content' => 'required|string',
            'department_id' => 'required|exists:departments,id',
            'category_id' => 'required|exists:categories,id',
            'created_by' => 'required|exists:users,id',
            'edit_by' => 'exists:users,id',
            'status' => 'boolean',
        ]);

        $sop = SOP::create($data);

        $sop->jobFunctions()->sync($request->jobFunctions);

        $sop->save();

        return response()->json(['message' => 'Created Successfully!'], 201);
    }

    function update(Request $request)
    {
        $sop = SOP::findOrFail($request->id);

        $data = $request->validate([
            'name' => 'required|string',
            'content' => 'required|string',
            'department_id' => 'required|exists:departments,id',
            'category_id' => 'required|exists:categories,id',
            'created_by' => 'required|exists:users,id',
            'edit_by' => 'required|exists:users,id',
            'status' => 'boolean',
        ]);

        $sop->update($data);

        $sop->jobFunctions()->sync($request->jobFunctions);

        $sop->save();

        return response()->json(['message' => 'User updated successfully!'], 200);
    }

    function data(Request $request)
    {
        $departments = Department::where('status', b'1')->get();

        $jobFunctions = JobFunction::where('status', b'1')->get();

        $categories = Category::where('status', b'1')->get();

        return response()->json(['data' => [
            'departments' => $departments,
            'jobFunctions' => $jobFunctions,
            'categories' => $categories,
            'user' => $request->user()
        ]], 200);
    }
}
