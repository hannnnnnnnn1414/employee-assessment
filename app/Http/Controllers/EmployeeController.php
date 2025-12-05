<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::all();

        $departments = Employee::select('dept')->distinct()->pluck('dept');

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
