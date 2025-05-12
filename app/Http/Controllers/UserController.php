<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function searchAjax(Request $request)
    {
        $q = $request->q;
        $users = User::query()
            ->when($q, function($query) use ($q) {
                $query->where(function($sub) use ($q) {
                    $sub->where('name', 'like', "%$q%")
                         ->orWhere('email', 'like', "%$q%");
                });
            })
            ->limit(10)
            ->get(['id', 'name', 'email']);
        return response()->json($users);
    }
} 