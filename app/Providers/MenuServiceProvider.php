<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class MenuServiceProvider extends ServiceProvider
{
    public function register(): void {}

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

                // ✅ PROFIL TIDAK MASUK MENU (untuk semua role)
                if ($roleName === 'admin') {
                    $menus = [
                        [
                            'label'  => 'Dashboard',
                            'route'  => route('admin.dashboard'),
                            'active' => 'admin',
                        ],
                        [
                            'label'  => 'Manajemen User',
                            'active' => 'admin/manage_user*',
                            'children' => [
                                [
                                    'label'  => 'Daftar User',
                                    'route'  => url('/admin/manage_user'),
                                    'active' => 'admin/manage_user*',
                                ],
                                [
                                    'label'  => 'Tambah User',
                                    'route'  => url('/admin/create_user'),
                                    'active' => 'admin/create_user*',
                                ],
                            ],
                        ],
                        [
                            'label'  => 'Kategori Sarpras',
                            'route'  => route('admin.kategori_sarpras.index'),
                            'active' => 'admin/kategori_sarpras*',
                        ],
                    ];
                }

                if ($roleName === 'operator') {
                    $menus = [
                        [
                            'label'  => 'Dashboard',
                            'route'  => route('operator.dashboard'),
                            'active' => 'operator',
                        ],
                    ];
                }

                if ($roleName === 'user') {
                    $menus = [
                        [
                            'label'  => 'Dashboard',
                            'route'  => route('user.dashboard'),
                            'active' => 'user',
                        ],
                    ];
                }
            }

            $view->with([
                'menus'      => $menus,
                'panelTitle' => $panelTitle,
                'roleName'   => $roleName,
            ]);
        });
    }
}
