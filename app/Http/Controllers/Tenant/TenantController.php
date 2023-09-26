<?php

namespace App\Http\Controllers\Tenant;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Http\Controllers\Controller;

use App\Models\Tenant;
use App\Models\User;

class TenantController extends Controller
{
    function index()
    {
        try {
            $tenants = Tenant::with(['domains'])->get();
            return response()->json($tenants);
        } catch (\Exception $e) {
            return response()->json(['error' => $e, 'message' => 'Something Went Wrong'], 500);
        }
    }

    function show($id)
    {
        try {
            // Get tenant with it's domain.
            $tenant = Tenant::with(['domains'])->findOrFail($id);

            return response()->json($tenant);
        } catch (\Exception $e) {
            return response()->json(['error' => $e, 'message' => 'Tenant Not Found'], 404);
        }
    }

    function store(Request $request)
    {
        try {

            $data = $request->validate([
                'name' => 'required|string',
                'email' => 'required|email|unique:tenants',
                // 'email' => 'required|email',
                'domain' => 'required|unique:domains'
            ]);

            $tenant = Tenant::create(['name' => $request->name, 'email' => $request->email]);

            $tenant->domains()->create(['domain' => $request->domain]);

            $requestCopy = $request;

            $tenant->run(function () use ($requestCopy) {
                User::create([
                    'name' => $requestCopy->name,
                    'email' => $requestCopy->email,
                    'password' => Hash::make($requestCopy->password),
                    'role' => 'admin',
                    'department' => null,
                ]);
            });

            $tenant->save();

            return response()->json(['message' => 'Tenant Created Successfully!'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e, 'message' => 'Addition Failed'], 500);
        }
    }

    function update(Request $request)
    {
        $tenant = Tenant::with(['domains'])->findOrFail($request->id);

        $request->validate([
            'name' => 'string',
            // 'email' => 'email|unique:tenants,email,' . $tenant->id,
            // 'email' => 'email',
        ]);

        $tenant->name = $request->name;

        $tenant->save();

        $tenant->domains()->first()->update(['domain' => $request->domain]);

        return response()->json(['message' => 'Tenant updated successfully!'], 200);
    }
}
