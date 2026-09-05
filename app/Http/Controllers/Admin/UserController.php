<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::withCount('orders');

        if ($request->filled('search')) {
            $query->where(fn($q) => $q->where('name', 'like', '%' . $request->search . '%')
                                      ->orWhere('email', 'like', '%' . $request->search . '%'));
        }

        if ($request->filled('role')) $query->where('role', $request->role);

        $users = $query->latest()->paginate(15)->appends($request->all());

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $orders = $user->orders()->with('items')->latest()->paginate(10);
        return view('admin.users.show', compact('user', 'orders'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate(['role' => 'required|in:admin,customer']);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat mengubah role sendiri.');
        }

        $user->update(['role' => $request->role]);

        return back()->with('success', 'Role user berhasil diperbarui!');
    }

    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
        }

        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "User berhasil {$status}!");
    }
}
