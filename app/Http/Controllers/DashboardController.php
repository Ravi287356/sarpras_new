<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\KategoriSarpras;

class DashboardController extends Controller
{
    public function adminDashboard()
    {
        return view('pages.admin.dashboard', [
            'title'         => 'Dashboard',
            'totalUsers'    => User::count(),
            // ✅ otomatis filter deleted_at
            'totalKategori' => KategoriSarpras::count(),
        ]);
    }

    public function operatorDashboard()
    {
        return view('pages.operator.dashboard', [
            'title' => 'Dashboard',
        ]);
    }

    public function userDashboard()
    {
        return view('pages.user.dashboard', [
            'title' => 'Dashboard',
        ]);
    }
}
