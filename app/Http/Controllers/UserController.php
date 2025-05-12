<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
<<<<<<< Updated upstream
    public function search(Request $request)
    {
        $query = $request->input('q');
        Log::info('Searching users with query: ' . $query);
        
        $users = User::when($query, function($q) use ($query) {
            $q->where(function($sub) use ($query) {
                $sub->where('name', 'like', "%$query%")
                    ->orWhere('email', 'like', "%$query%");
            });
        })
        ->latest()
        ->paginate(10);
        
        Log::info('Found ' . $users->count() . ' users');
        
        return view('users.search', [
            'users' => $users,
            'query' => $query
        ]);
=======
    // Hàm searchAjax - dùng cho Ajax (JSON response)
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
>>>>>>> Stashed changes
    }

    // Hàm search - dùng để render ra view với Log ghi lại
    public function search(Request $request)
    {
        $query = $request->input('q');
        Log::info('Searching users with query: ' . $query);
        
        $users = User::when($query, function($q) use ($query) {
            $q->where(function($sub) use ($query) {
                $sub->where('name', 'like', "%$query%")
                    ->orWhere('email', 'like', "%$query%");
            });
        })
        ->latest()
        ->paginate(10);
        
        Log::info('Found ' . $users->count() . ' users');
        
        return view('users.search', [
            'users' => $users,
            'query' => $query
        ]);
    }
}
