<?php

namespace App\Http\Controllers;

use App\Models\SuperUser;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Stancl\Tenancy\Database\Models\Domain;

class DashboardController extends Controller
{
    function index(Request $request)
    {
        // return 'hi';
        $user = $request->user();

        if ($user->role === 'super-admin') {
            return $this->superAdmin();
        } else if ($user->role === 'admin') {
            return $this->admin();
        } else {
            return $this->user();
        }
    }

    function superAdmin()
    {
        $tenants = Tenant::all();
        $domains = Domain::all();
        $users = SuperUser::all();

        $data = ['users' => $users, 'tenants' => $tenants, 'domains' => $domains];

        return response()->json($data);
    }

    function admin()
    {
    }

    function user()
    {
        return 'failed!';
    }
}
