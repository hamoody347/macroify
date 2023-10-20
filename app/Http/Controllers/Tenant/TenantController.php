<?php

namespace App\Http\Controllers\Tenant;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Http\Controllers\Controller;

use App\Models\Tenant;
use App\Models\TenantDetails;
use App\Models\User;
use Stancl\Tenancy\Database\Models\Domain;

class TenantController extends Controller
{
    function index()
    {
        try {
            $tenants = Tenant::with(['domains', 'details'])->get();
            return response()->json($tenants);
        } catch (\Exception $e) {
            return response()->json(['error' => $e, 'message' => 'Something Went Wrong'], 500);
        }
    }

    function show($id)
    {
        try {
            // Get tenant with it's domain.
            $tenant = Tenant::with(['domains', 'details'])->findOrFail($id);

            return response()->json($tenant);
        } catch (\Exception $e) {
            return response()->json(['error' => $e, 'message' => 'Tenant Not Found'], 404);
        }
    }

    function store(Request $request)
    {
        try {

            $data = $request->validate([
                'is_active' => 'boolean',
                'name' => 'required|string',
                'domain' => 'required|unique:domains',
                'email' => 'required|email|unique:tenant_details',
                'password' => 'required|min:8'
            ]);

            $tenant = Tenant::create();

            $tenant->details()->create($data);

            $tenant->domains()->create(['domain' => $request->domain]);

            $requestCopy = $data;

            $tenant->run(function () use ($requestCopy) {
                User::create([
                    'name' => $requestCopy['name'],
                    'email' => $requestCopy['email'],
                    'password' => Hash::make($requestCopy['password']),
                    'role' => 'admin',
                    'department' => null,
                ]);
            });

            $tenant->save();

            return response()->json(['message' => 'Tenant Created Successfully!'], 201);
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

            $tenant = Tenant::findOrFail($request->id);
            $domain = $tenant->domains()->first();

            $request->merge(['name' => $request->input('details.name')]);

            $data = $request->validate([
                'is_active' => 'boolean',
                'name' => 'required|string',
                'domain' => 'required|unique:domains,domain,' . $domain->id,
                'email' => 'email|unique:tenant_details,email,' . $tenant->id,
            ]);

            $domainData = ['domain' => $data['domain']];
            $detailsData = array_diff_key($data, $domainData);

            $tenant->details()->update($detailsData);

            $tenant->domains()->first()->update($domainData);

            return response()->json(['message' => 'Tenant updated successfully!', 'data' => $tenant->name, 'tenant' => $tenant], 200);
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
            $tenant = Tenant::findOrFail($id);

            $tenant->delete();

            return response()->json(['message' => 'Tenant deleted successfully!'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed', 'error' => $e], 500);
        }
    }

    function domains()
    {
        try {
            $domains = Domain::all();

            return response()->json($domains);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed', 'error' => $e], 500);
        }
    }
}
