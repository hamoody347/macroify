<?php

namespace App\Http\Controllers;

use App\Models\SuperUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SuperUserController extends Controller
{
    function index()
    {
        $users = SuperUser::where('status', b'1')->get();

        return response()->json($users);
    }

    function show($id)
    {
        try {
            // Get user with it's department and job functions.
            $user = SuperUser::findOrFail($id);

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

        $data['password'] = Hash::make($request->password);

        $user = SuperUser::create($data);

        $user->save();

        return response()->json(['message' => 'User Created Successfully!'], 201);
    }

    function update(Request $request)
    {
        $user = SuperUser::findOrFail($request->id);

        $data = $request->validate([
            'name' => 'string',
            'email' => 'email|unique:users,email,' . $user->id,
            'password' => 'min:8',
            'role' => 'in:super-admin',
            'status' => 'boolean',
        ]);

        $user->update($data);

        $user->save();

        return response()->json(['message' => 'User updated successfully!'], 200);
    }
}
