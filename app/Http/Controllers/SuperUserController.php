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
        try {
            $data = $request->validate([
                'name' => 'required|string',
                'email' => 'required|email|unique:users',
                'password' => 'min:8',
                'role' => 'required|in:super-admin',
                'status' => 'boolean',
            ]);

            $data['password'] = Hash::make($request->password);

            $user = SuperUser::create($data);

            $user->save();

            return response()->json(['message' => 'User Created Successfully!'], 201);
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

            $user = SuperUser::findOrFail($request->id);

            $data = $request->validate([
                'name' => 'string',
                'email' => 'email|unique:users,email,' . $user->id,
            ]);

            $user->update($data);

            $user->save();

            return response()->json(['message' => 'User updated successfully!'], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation failed, handle the exception
            return response()->json(['errors' => $e->validator->errors(), 'validator' => true], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed', 'error' => $e], 500);
        }
    }

    function delete($id)
    {
        try {
            $user = SuperUser::findOrFail($id);

            $user->delete();

            return response()->json(['message' => 'Deleted successfully!'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed', 'error' => $e], 500);
        }
    }
}
