<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\JobFunction;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    function index()
    {
        $users = User::with(['department'])->where('status', b'1')->get();

        return response()->json($users);
    }

    function show($id)
    {
        try {
            // Get user with it's department and job functions.
            $user = User::with(['department', 'jobFunctions'])->findOrFail($id);

            return response()->json($user);
        } catch (\Exception $e) {
            return response()->json(['error' => $e, 'message' => 'User Not Found'], 404);
        }
    }

    function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'min:8',
            'role' => 'required|in:admin,user,super-admin',
            'department_id' => 'required|exists:departments,id',
            'status' => 'boolean',
        ]);

        $data['password'] = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'; // password

        $user = User::create($data);

        $user->updatePolicyAssignments($request->jobFunctions);
        $user->jobFunctions()->sync($request->jobFunctions);

        $user->save();

        return response()->json(['message' => 'User Created Successfully!'], 201);
    }

    function update(Request $request)
    {
        $user = User::findOrFail($request->id);

        $data = $request->validate([
            'name' => 'string',
            'email' => 'email|unique:users,email,' . $user->id,
            'password' => 'min:8',
            'role' => 'in:admin,user',
            'department_id' => 'exists:departments,id',
            'status' => 'boolean',
        ]);

        $user->update($data);

        $user->updatePolicyAssignments($request->jobFunctions);
        $user->jobFunctions()->sync($request->jobFunctions);

        $user->save();


        return response()->json(['message' => 'User updated successfully!'], 200);
    }

    function data()
    {
        $departments = Department::where('status', b'1')->get();

        $jobFunctions = JobFunction::where('status', b'1')->get();

        return response()->json(['departments' => $departments, 'job_functions' => $jobFunctions], 200);
    }

    function delete($id)
    {
        $sop = User::findOrFail($id);

        $sop->delete();

        return response()->json(['message' => 'Deleted successfully!'], 200);
    }
}
