<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    /**
     * Menampilkan daftar user dengan fitur pencarian, sorting, dan paginasi.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // 1. Logika Pencarian (Nama atau Email)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // 2. Logika Pengurutan (Sorting)
        // Default diurutkan berdasarkan 'id' secara 'asc'
        $sort = $request->get('sort', 'id');
        $dir  = $request->get('dir', 'asc');
        
        // Pastikan kolom yang disortir tersedia untuk menghindari SQL Injection
        $allowedSorts = ['id', 'name', 'email'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $dir);
        }

        // 3. Paginasi (Mengatasi error firstItem does not exist)
        $perPage = $request->get('per_page', 10);
        $data = $query->paginate($perPage);

        return view('users.index', compact('data'));
    }

    public function create()
    {
        return view('users.create');
    }

    /**
     * Menyimpan user baru ke database.
     */
    public function store(Request $req)
    {
        $req->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:4',
        ]);

        // Perbaikan: Menyertakan password dan melakukan hashing
        User::create([
            'name'     => $req->name,
            'email'    => $req->email,
            'password' => Hash::make($req->password),
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil dibuat!');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Memperbarui data user.
     */
    public function update(Request $req, User $user)
    {
        $req->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $data = [
            'name'  => $req->name,
            'email' => $req->email,
        ];

        // Update password hanya jika diisi
        if ($req->filled('password')) {
            $req->validate(['password' => 'min:4']);
            $data['password'] = Hash::make($req->password);
        }

        $user->update($data);

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil diperbarui');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil dihapus');
    }
}