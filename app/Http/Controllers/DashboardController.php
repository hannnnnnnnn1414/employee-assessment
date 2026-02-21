<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Assessment;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Stats berdasarkan role user
        if ($user->dept === 'HRD') {
            $totalAssessments = Assessment::count();
            $totalEmployees = User::count();
            $avgNilai = Assessment::avg('nilai_akhir') ?? 0;
            $recentAssessments = Assessment::with('user')->latest()->take(5)->get();
        } else {
            $totalAssessments = Assessment::whereHas('user', function ($query) use ($user) {
                $query->where('dept', $user->dept);
            })->count();

            $totalEmployees = User::where('dept', $user->dept)->count();

            $avgNilai = Assessment::whereHas('user', function ($query) use ($user) {
                $query->where('dept', $user->dept);
            })->avg('nilai_akhir') ?? 0;

            $recentAssessments = Assessment::whereHas('user', function ($query) use ($user) {
                $query->where('dept', $user->dept);
            })->with('user')->latest()->take(5)->get();
        }

        // Distribution by Nilai Mutu
        $nilaiMutuDistribution = $this->getNilaiMutuDistribution($user);

        // Monthly chart data
        $monthlyData = $this->getMonthlyAssessmentData($user);

        // Department distribution (untuk HRD)
        $departmentData = $user->dept === 'HRD' ? $this->getDepartmentDistribution() : [];

        // Recent activities
        $recentActivities = $this->getRecentActivities($user);

        return view('dashboard', [
            // Stats cards
            'totalAssessments' => $totalAssessments,
            'totalEmployees' => $totalEmployees,
            'avgNilai' => round($avgNilai, 2),
            'recentAssessments' => $recentAssessments,

            // Distribution by nilai mutu
            'nilaiMutuDistribution' => $nilaiMutuDistribution,

            // Chart data
            'monthlyChartData' => $monthlyData,
            'weeklyChartData' => $this->getWeeklyAssessmentData($user),
            'departmentChartData' => $departmentData,

            // Additional data
            'recentActivities' => $recentActivities,
            'topPerformers' => $this->getTopPerformers($user),
            'needImprovement' => $this->getNeedImprovement($user),

            // User info
            'userDept' => $user->dept,
        ]);
    }

    private function getMonthlyAssessmentData($user)
    {
        $currentYear = date('Y');
        $data = [];

        for ($month = 1; $month <= 12; $month++) {
            $query = Assessment::whereYear('tanggal_penilaian', $currentYear)
                ->whereMonth('tanggal_penilaian', $month);

            if ($user->dept !== 'HRD') {
                $query->whereHas('user', function ($query) use ($user) {
                    $query->where('dept', $user->dept);
                });
            }

            $data[] = $query->count();
        }

        return [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            'data' => $data
        ];
    }

    private function getWeeklyAssessmentData($user)
    {
        $currentMonth = date('n');
        $currentYear = date('Y');
        $weeks = [];
        $data = [];

        // Get weeks in current month
        $date = Carbon::create($currentYear, $currentMonth, 1);
        $endDate = $date->copy()->endOfMonth();

        $week = 1;
        while ($date->lte($endDate)) {
            $startOfWeek = $date->copy()->startOfWeek();
            $endOfWeek = $date->copy()->endOfWeek();

            $query = Assessment::whereBetween('tanggal_penilaian', [$startOfWeek, $endOfWeek]);

            if ($user->dept !== 'HRD') {
                $query->whereHas('user', function ($query) use ($user) {
                    $query->where('dept', $user->dept);
                });
            }

            $data[] = $query->count();
            $weeks[] = "Minggu " . $week;

            $date->addWeek();
            $week++;
        }

        return [
            'labels' => $weeks,
            'data' => $data
        ];
    }

    private function getDepartmentDistribution()
    {
        $departments = User::select('dept')
            ->whereNotNull('dept')
            ->distinct()
            ->pluck('dept');

        $data = [];
        $colors = [
            '#0d6efd',
            '#198754',
            '#dc3545',
            '#ffc107',
            '#6f42c1',
            '#20c997',
            '#fd7e14',
            '#e83e8c'
        ];

        foreach ($departments as $index => $dept) {
            $count = Assessment::whereHas('user', function ($query) use ($dept) {
                $query->where('dept', $dept);
            })->count();

            if ($count > 0) {
                $data['labels'][] = $dept;
                $data['data'][] = $count;
                $data['colors'][] = $colors[$index % count($colors)] ?? '#6c757d';
            }
        }

        return $data;
    }

    private function getNilaiMutuDistribution($user)
    {
        $mutuLabels = ['BS', 'B', 'C', 'K', 'KS'];
        $mutuColors = [
            'BS' => '#198754', // Green
            'B'  => '#0dcaf0', // Blue
            'C'  => '#fd7e14', // Orange
            'K'  => '#ffc107', // Yellow
            'KS' => '#dc3545', // Red
        ];

        $data = [];

        foreach ($mutuLabels as $mutu) {
            $query = Assessment::where('nilai_mutu', $mutu);

            if ($user->dept !== 'HRD') {
                $query->whereHas('user', function ($query) use ($user) {
                    $query->where('dept', $user->dept);
                });
            }

            $count = $query->count();

            if ($count > 0) {
                $data['labels'][] = $mutu;
                $data['data'][] = $count;
                $data['colors'][] = $mutuColors[$mutu];
                $data['descriptions'][$mutu] = $this->getMutuDescription($mutu);
            }
        }

        return $data;
    }

    private function getMutuDescription($mutu)
    {
        $descriptions = [
            'BS' => 'Baik Sekali (≥90)',
            'B'  => 'Baik (80-89)',
            'C'  => 'Cukup (70-79)',
            'K'  => 'Kurang (60-69)',
            'KS' => 'Kurang Sekali (<60)'
        ];

        return $descriptions[$mutu] ?? 'Tidak diketahui';
    }

    private function getRecentActivities($user)
    {
        $query = Assessment::with('user')->latest('updated_at')->limit(10);

        if ($user->dept !== 'HRD') {
            $query->whereHas('user', function ($query) use ($user) {
                $query->where('dept', $user->dept);
            });
        }

        return $query->get();
    }

    private function getTopPerformers($user)
    {
        $query = Assessment::with('user')
            ->orderBy('nilai_akhir', 'desc')
            ->limit(5);

        if ($user->dept !== 'HRD') {
            $query->whereHas('user', function ($query) use ($user) {
                $query->where('dept', $user->dept);
            });
        }

        return $query->get();
    }

    private function getNeedImprovement($user)
    {
        $query = Assessment::with('user')
            ->where('nilai_mutu', 'KS')
            ->orWhere('nilai_mutu', 'K')
            ->orderBy('nilai_akhir', 'asc')
            ->limit(5);

        if ($user->dept !== 'HRD') {
            $query->whereHas('user', function ($query) use ($user) {
                $query->where('dept', $user->dept);
            });
        }

        return $query->get();
    }
}
