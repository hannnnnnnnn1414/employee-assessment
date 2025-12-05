<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Data statistik dummy (kosong atau nilai 0)
        $data = [
            'totalDocuments' => 0,
            'approvedDocuments' => 0,
            'rejectedDocuments' => 0,
            'pendingDocuments' => 0,

            // Data chart bulanan dummy
            'monthlyChartData' => [
                'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                'data' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
            ],

            // Data chart mingguan dummy
            'weeklyChartData' => [
                'labels' => ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4'],
                'data' => [0, 0, 0, 0]
            ],

            // Data chart departemen dummy
            'departmentChartData' => [
                'labels' => ['IT', 'HR', 'Finance', 'Marketing', 'Operations'],
                'data' => [0, 0, 0, 0, 0],
                'colors' => ['#0d6efd', '#198754', '#dc3545', '#ffc107', '#6f42c1']
            ]
        ];

        return view('dashboard', $data);
    }
}
