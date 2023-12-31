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
        try {

            $data = $request->validate([
                'name' => 'required|string',
                'department_id' => 'required|exists:departments,id',
                'status' => 'boolean',
            ]);

            JobFunction::create($data);

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

            $jobFunction = JobFunction::findOrFail($request->id);

            $data = $request->validate([
                'name' => 'required|string',
                'department_id' => 'required|exists:departments,id',
                'status' => 'boolean',
            ]);

            $jobFunction->update($data);

            return response()->json(['message' => 'Updated successfully!'], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation failed, handle the exception
            return response()->json(['errors' => $e->validator->errors(), 'validator' => true], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed', 'error' => $e], 500);
        }
    }

    function data()
    {
        $departments = Department::where('status', b'1')->get();

        return response()->json(['departments' => $departments], 200);
    }

    function delete($id)
    {
        $sop = JobFunction::findOrFail($id);

        $sop->delete();

        return response()->json(['message' => 'Deleted successfully!'], 200);
    }
}
