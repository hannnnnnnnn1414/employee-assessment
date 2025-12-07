<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index()
    {
        $users = User::byDepartment(request('dept'))->get();

        $departments = User::select('dept')
            ->whereNotNull('dept')
            ->distinct()
            ->pluck('dept');

        return view('employee', compact('users', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'npk' => 'required|unique:users,npk',
            'nama' => 'required',
            'dept' => 'required',
            // 'email' => 'required|email|unique:users,email',
            // 'password' => 'required|min:6',
        ]);

        $email = $request->email ?? $request->npk . '@company.com';

        User::create([
            'npk' => $request->npk,
            'nama' => $request->nama,
            'email' => $email,
            'password' => Hash::make($request->password),
            'dept' => $request->dept,
            'jabatan' => $request->jabatan,
            'golongan' => $request->golongan,
        ]);

        return back()->with('success', 'Employee berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'npk' => 'required|unique:users,npk,' . $id,
            'nama' => 'required',
            'dept' => 'required',
        ]);

        $user = User::findOrFail($id);
        $user->update([
            'npk' => $request->npk,
            'nama' => $request->nama,
            'dept' => $request->dept,
            'jabatan' => $request->jabatan,
            'golongan' => $request->golongan,
        ]);

        return back()->with('success', 'Employee berhasil diupdate!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return back()->with('success', 'Employee berhasil dihapus!');
    }
}
