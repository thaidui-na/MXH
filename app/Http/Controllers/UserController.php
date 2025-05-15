<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    // Hàm searchAjax - dùng cho Ajax (JSON response)
    public function searchAjax(Request $request)
    {
        $query = $request->input('query');
        
        $users = User::query()
            ->when($query, function($q) use ($query) {
                $q->where(function($sub) use ($query) {
                    $sub->where('name', 'like', "%{$query}%")
                        ->orWhere('email', 'like', "%{$query}%");
                });
            })
            ->select(['id', 'name', 'email', 'avatar'])
            ->limit(10)
            ->get();

        return response()->json([
            'users' => $users
        ]);
    }

    // Hàm search - dùng để render ra view với Log ghi lại
    public function search(Request $request)
    {
        $query = $request->input('q');
        Log::info('Searching users with query: ' . $query);
        
        $users = User::when($query, function($q) use ($query) {
            $q->where(function($sub) use ($query) {
                $sub->where('name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%");
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
