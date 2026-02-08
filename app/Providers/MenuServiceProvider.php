<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class MenuServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        View::composer('*', function ($view) {

            $menus = [];
            $panelTitle = 'Dashboard';
            $roleName = null;

            if (Auth::check()) {
                $roleName = Auth::user()?->role?->nama; // admin/operator/user

                $panelTitle = match ($roleName) {
                    'admin' => 'Dashboard Admin',
                    'operator' => 'Dashboard Operator',
                    'user' => 'Dashboard User',
                    default => 'Dashboard',
                };

                if ($roleName === 'admin') {
                    $menus = [
                        [
                            'label' => 'Dashboard',
                            'route' => route('admin.dashboard'),
                            'active' => 'admin',
                        ],
                        [
                            'label' => 'Sarpras Tersedia',
                            'route' => route('sarpras.available'),
                            'active' => 'sarpras-tersedia*',
                        ],

                        // ✅ MENU PEMINJAMAN (ADMIN)
                        [
                            'label' => 'Manajemen Peminjaman',
                            'active' => 'admin/peminjaman*',
                            'children' => [
                                [
                                    'label' => 'Permintaan Peminjaman',
                                    'route' => route('admin.peminjaman.permintaan'),
                                    'active' => 'admin/peminjaman-permintaan*',
                                ],
                                [
                                    'label' => 'Peminjaman Aktif',
                                    'route' => route('admin.peminjaman.aktif'),
                                    'active' => 'admin/peminjaman-aktif*',
                                ],
                                [
                                    'label' => 'Riwayat Peminjaman',
                                    'route' => route('admin.peminjaman.riwayat'),
                                    'active' => 'admin/peminjaman-riwayat*',
                                ],
                            ],
                        ],

                        // ✅ MENU PENGEMBALIAN (ADMIN)
                        [
                            'label' => 'Manajemen Pengembalian',
                            'active' => 'pengembalian*',
                            'children' => [
                                [
                                    'label' => 'Pengembalian Sarpras',
                                    'route' => route('pengembalian.index'),
                                    'active' => 'pengembalian*',
                                ],
                                [
                                    'label' => 'Riwayat Pengembalian',
                                    'route' => route('pengembalian.riwayat'),
                                    'active' => 'pengembalian/riwayat*',
                                ],
                            ],
                        ],

                        [
                            'label' => 'Manajemen User',
                            'active' => 'admin/manage_user*',
                            'children' => [
                                [
                                    'label' => 'Daftar User',
                                    'route' => route('admin.users.index'),
                                    'active' => 'admin/manage_user*',
                                ],
                                [
                                    'label' => 'Tambah User',
                                    'route' => route('admin.users.create'),
                                    'active' => 'admin/create_user*',
                                ],
                            ],
                        ],
                        [
                            'label' => 'Kategori Sarpras',
                            'route' => route('admin.kategori_sarpras.index'),
                            'active' => 'admin/kategori_sarpras*',
                        ],
                        [
                            'label' => 'Lokasi',
                            'route' => route('admin.lokasi.index'),
                            'active' => 'admin/lokasi*',
                        ],
                        [
                            'label' => 'Manajemen Sarpras',
                            'active' => 'admin/sarpras*|admin/maintenance*',
                            'children' => [
                                [
                                    'label' => 'Data Sarpras',
                                    'route' => route('admin.sarpras.index'),
                                    'active' => 'admin/sarpras*',
                                ],
                                [
                                    'label' => 'Maintenance Alat',
                                    'route' => route('admin.maintenance.index'),
                                    'active' => 'admin/maintenance*',
                                ],
                            ],
                        ],

                        // (Kalau activity log mau dihiraukan, boleh dihapus dari sini)
                        [
                            'label' => 'Activity Log',
                            'route' => route('admin.activity_logs.index'),
                            'active' => 'admin/activity-logs*',
                        ],
                        [
                            'label' => 'Laporan',
                            'route' => route('admin.laporan.index'),
                            'active' => 'admin/laporan*',
                        ],
                    ];
                } elseif ($roleName === 'operator') {
                    $menus = [
                        [
                            'label' => 'Dashboard',
                            'route' => route('operator.dashboard'),
                            'active' => 'operator',
                        ],
                        [
                            'label' => 'Sarpras Tersedia',
                            'route' => route('sarpras.available'),
                            'active' => 'sarpras-tersedia*',
                        ],

                        // ✅ MENU PEMINJAMAN (OPERATOR)
                        [
                            'label' => 'Manajemen Peminjaman',
                            'active' => 'operator/peminjaman*',
                            'children' => [
                                [
                                    'label' => 'Permintaan Peminjaman',
                                    'route' => route('operator.peminjaman.permintaan'),
                                    'active' => 'operator/peminjaman-permintaan*',
                                ],
                                [
                                    'label' => 'Peminjaman Aktif',
                                    'route' => route('operator.peminjaman.aktif'),
                                    'active' => 'operator/peminjaman-aktif*',
                                ],
                                [
                                    'label' => 'Riwayat Peminjaman',
                                    'route' => route('operator.peminjaman.riwayat'),
                                    'active' => 'operator/peminjaman-riwayat*',
                                ],
                            ],
                        ],

                        // ✅ MENU PENGEMBALIAN (OPERATOR)
                        [
                            'label' => 'Manajemen Pengembalian',
                            'active' => 'pengembalian*',
                            'children' => [
                                [
                                    'label' => 'Pengembalian Sarpras',
                                    'route' => route('pengembalian.index'),
                                    'active' => 'pengembalian*',
                                ],
                                [
                                    'label' => 'Riwayat Pengembalian',
                                    'route' => route('pengembalian.riwayat'),
                                    'active' => 'pengembalian/riwayat*',
                                ],
                            ],
                        ],
                    ];
                } elseif ($roleName === 'user') {
                    $menus = [
                        [
                            'label' => 'Dashboard',
                            'route' => route('user.dashboard'),
                            'active' => 'user',
                        ],
                        [
                            'label' => 'Sarpras Tersedia',
                            'route' => route('sarpras.available'),
                            'active' => 'sarpras-tersedia*',
                        ],

                        // ✅ MENU USER PEMINJAMAN
                        [
                            'label' => 'Sarpras Bisa Dipinjam',
                            'route' => route('user.peminjaman.available'),
                            'active' => 'user/sarpras-bisa-dipinjam*',
                        ],
                        [
                            'label' => 'Riwayat Peminjaman',
                            'route' => route('user.peminjaman.riwayat'),
                            'active' => 'user/riwayat-peminjaman*',
                        ],
                    ];
                }
            }

            $view->with([
                'menus' => $menus,
                'panelTitle' => $panelTitle,
                'roleName' => $roleName,
            ]);
        });
    }
}
