<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    function index()
    {
        $departments = Department::with(['sops'])->where('status', b'1')->get();

        return response()->json($departments);
    }

    function show($id)
    {
        try {
            $department = Department::with(['sops'])->findOrFail($id);

            return response()->json($department);
        } catch (\Exception $e) {
            return response()->json(['error' => $e, 'message' => 'Not Found'], 404);
        }
    }

    function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'description' => 'string',
            'status' => 'boolean',
        ]);

        Department::create($data);

        return response()->json(['message' => 'Created Successfully!'], 201);
    }

    function update(Request $request)
    {
        try {

            $user = Department::findOrFail($request->id);

            $data = $request->validate([
                'name' => 'string',
                'description' => 'string',
                'status' => 'boolean',
            ]);

            $user->update($data);

            return response()->json(['message' => 'Updated successfully!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e, 'message' => 'Not Found'], 404);
        }
    }

    function delete($id)
    {
        $sop = Department::findOrFail($id);

        $sop->delete();

        return response()->json(['message' => 'Deleted successfully!'], 200);
    }
}
