<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderByDesc('created_at')->get();

        return view('admin.users', compact('users'));
    }

   
    public function toggleRole(User $user)
    {
       
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa mengubah role akun sendiri.');
        }

        $user->role = $user->role === 'admin' ? 'user' : 'admin';
        $user->save();

        return back()->with('success', "Role {$user->name} diubah jadi {$user->role}.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        $user->delete();

        return back()->with('success', 'User berhasil dihapus.');
    }
}
