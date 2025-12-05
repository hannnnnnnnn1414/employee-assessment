<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = [
            (object)[
                'id' => 1,
                'npk' => '1001',
                'nama' => 'Budi Santoso',
                'dept' => 'IT',
                'jabatan' => 'Programmer',
                'golongan' => 'III'
            ],
            (object)[
                'id' => 2,
                'npk' => '1002',
                'nama' => 'Siti Rahayu',
                'dept' => 'HR',
                'jabatan' => 'HR Staff',
                'golongan' => 'II'
            ],
            (object)[
                'id' => 3,
                'npk' => '1003',
                'nama' => 'Ahmad Wijaya',
                'dept' => 'Finance',
                'jabatan' => 'Accountant',
                'golongan' => 'IV'
            ]
        ];

        $departments = ['IT', 'HR', 'Finance', 'Marketing', 'Operations', 'Production'];

        return view('employee', compact('employees', 'departments'));
    }

    public function store(Request $request)
    {
        return back()->with('success', 'Employee berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        return back()->with('success', 'Employee berhasil diupdate!');
    }

    public function destroy($id)
    {
        return back()->with('success', 'Employee berhasil dihapus!');
    }
}
