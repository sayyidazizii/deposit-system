<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.add');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role'     => 'required|in:user,admin,supervisor',
        ]);

        DB::beginTransaction();
        try {
            User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ])->assignRole($request->role);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('User creation failed: ' . $e->getMessage());
            return redirect()->route('user.index')->with('error', 'Gagal menambah user: ' . $e->getMessage());
        }

        return redirect()->route('user.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('users.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name'  => 'required',
            'email' => 'required|email|unique:users,email,' . $request->id,
            'role'  => 'required|in:user,admin,supervisor',
        ]);

        DB::beginTransaction();
        try {
            $user = User::findOrFail($request->id);
            $user->update([
                'name'  => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->syncRoles([$request->role]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('User update failed: ' . $e->getMessage());
            return redirect()->route('users.index')->with('error', 'Gagal mengupdate user: ' . $e->getMessage());
        }

        return redirect()->route('user.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        DB::beginTransaction();
        try {
            $user->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('User deletion failed: ' . $e->getMessage());
            return redirect()->route('user.index')->with('error', 'Gagal menghapus user: ' . $e->getMessage());
        }

        return redirect()->route('user.index')->with('success', 'User berhasil dihapus.');
    }
}
