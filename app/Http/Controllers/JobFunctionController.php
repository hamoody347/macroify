<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\JobFunction;
use Illuminate\Http\Request;

class JobFunctionController extends Controller
{
    function index()
    {
        $jobFunctions = JobFunction::with(['department', 'sops'])->where('status', b'1')->get();

        return response()->json($jobFunctions);
    }

    function show($id)
    {
        try {
            $jobFunction = JobFunction::with(['department', 'sops'])->findOrFail($id);

            return response()->json($jobFunction);
        } catch (\Exception $e) {
            return response()->json(['error' => $e, 'message' => 'Not Found'], 404);
        }
    }

    function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'department_id' => 'required|exists:departments,id',
            'status' => 'boolean',
        ]);

        JobFunction::create($data);

        return response()->json(['message' => 'Created Successfully!'], 201);
    }

    function update(Request $request)
    {
        $jobFunction = JobFunction::findOrFail($request->id);

        $data = $request->validate([
            'name' => 'required|string',
            'department_id' => 'required|exists:departments,id',
            'status' => 'boolean',
        ]);

        $jobFunction->update($data);

        return response()->json(['message' => 'Updated successfully!'], 200);
    }

    function data()
    {
        $departments = Department::where('status', b'1')->get();

        return response()->json(['data' => ['departments' => $departments]], 200);
    }
}
