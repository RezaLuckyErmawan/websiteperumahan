<?php

namespace App\Config;

use CodeIgniter\Config\BaseConfig;

class Menu extends BaseConfig
{
    /**
     * Menu configuration for each role
     * Keys are role names (lowercase)
     * Values are arrays of menu items
     */
    public $menus = [
        'admin' => [
            'dashboard' => [
                'label' => 'Dashboard',
                'icon' => 'dashboard',
                'link' => '/dashboard',
                'type' => 'link'
            ],
            'marketing' => [
                'label' => 'Marketing',
                'icon' => 'analytics',
                'type' => 'dropdown',
                'items' => [
                    'data_customer' => [
                        'label' => 'Data Customer',
                        'icon' => 'groups',
                        'link' => '/data-customer'
                    ],
                    'pembatalan_transaksi' => [
                        'label' => 'Pembatalan Transaksi',
                        'icon' => 'remove_shopping_cart',
                        'link' => '/pembatalan-transaksi'
                    ]
                ]
            ],
            'manajemen_proyek' => [
                'label' => 'Manajemen Proyek',
                'icon' => 'business_center',
                'type' => 'dropdown',
                'items' => [
                    'data_bahan' => [
                        'label' => 'Bahan Bangunan',
                        'icon' => 'construction',
                        'link' => '/data-bahan'
                    ],
                    'data_rumah' => [
                        'label' => 'Data Rumah',
                        'icon' => 'home_work',
                        'link' => '/data-rumah'
                    ],
                    'rab_rumah' => [
                        'label' => 'RAB Rumah',
                        'icon' => 'description',
                        'link' => '/rab-rumah'
                    ],
                    'rab_bahan' => [
                        'label' => 'RAB Bahan',
                        'icon' => 'description',
                        'link' => '/rab-bahan'
                    ],
                    'rab_pekerja' => [
                        'label' => 'RAB Pekerja',
                        'icon' => 'description',
                        'link' => '/rab-pekerja'
                    ],
                    'realisasi_rumah' => [
                        'label' => 'Realisasi Rumah',
                        'icon' => 'description',
                        'link' => '/realisasi-rumah'
                    ],
                    'realisasi_bahan' => [
                        'label' => 'Realisasi Bahan',
                        'icon' => 'description',
                        'link' => '/realisasi-bahan'
                    ],
                    'realisasi_pekerja' => [
                        'label' => 'Realisasi Pekerja',
                        'icon' => 'description',
                        'link' => '/realisasi-pekerja'
                    ],
                    'data_bahan_pembangunan' => [
                        'label' => 'Data Bahan Pembangunan',
                        'icon' => 'business',
                        'link' => '/data-bahan-pembangunan'
                    ],
                    'pekerjaan_insidentil' => [
                        'label' => 'Data Pekerjaan Insidentil',
                        'icon' => 'architecture',
                        'link' => '/pekerjaan-insidentil'
                    ]
                ]
            ],
            'manajemen_logistik' => [
                'label' => 'Manajemen Logistik',
                'icon' => 'fact_check',
                'type' => 'dropdown',
                'items' => [
                    'data_pembelian_bahan' => [
                        'label' => 'Data Pembelian Bahan',
                        'icon' => 'shopping_cart',
                        'link' => 'data-pembelian-bahan'
                    ],
                    'detail_pembelian_bahan' => [
                        'label' => 'Detail Pembelian Bahan',
                        'icon' => 'receipt_long',
                        'link' => '/detail-pembelian-bahan'
                    ]
                ]
            ],
            'keuangan' => [
                'label' => 'Keuangan',
                'icon' => 'monetization_on',
                'type' => 'dropdown',
                'items' => [
                    'data_pembelian_rumah' => [
                        'label' => 'Data Pembelian Rumah',
                        'icon' => 'real_estate_agent',
                        'link' => '/pembelian-rumah'
                    ],
                    'pembayaran_rumah' => [
                        'label' => 'Pembayaran Cicilan Rumah',
                        'icon' => 'payments',
                        'link' => '/pembayaran-rumah'
                    ],
                    'progres_pembayaran_rumah' => [
                        'label' => 'Data Progres Pembayaran Rumah',
                        'icon' => 'timeline',
                        'link' => '/progres-pembayaran-rumah'
                    ],
                    'laporan' => [
                        'label' => 'Laporan',
                        'icon' => 'picture_as_pdf',
                        'link' => '/laporan'
                    ]
                ]
            ],
            'menu_master' => [
                'label' => 'Menu Master',
                'icon' => 'folder_open',
                'type' => 'dropdown',
                'items' => [
                    'data_user' => [
                        'label' => 'Data User',
                        'icon' => 'groups',
                        'link' => '/data-user'
                    ],
                    'data_mandor' => [
                        'label' => 'Data Mandor',
                        'icon' => 'engineering',
                        'link' => '/data-mandor'
                    ]
                ]
            ]
        ],
        'mandor' => [
            'dashboard' => [
                'label' => 'Dashboard',
                'icon' => 'dashboard',
                'link' => '/dashboard',
                'type' => 'link'
            ],
            'manajemen_proyek' => [
                'label' => 'Manajemen Proyek',
                'icon' => 'business_center',
                'type' => 'dropdown',
                'items' => [
                    'data_bahan_pembangunan' => [
                        'label' => 'Data Bahan Pembangunan',
                        'icon' => 'business',
                        'link' => '/data-bahan-pembangunan'
                    ]
                ]
            ]
        ],
        'customer' => [
            'dashboard' => [
                'label' => 'Dashboard',
                'icon' => 'dashboard',
                'link' => '/dashboard',
                'type' => 'link'
            ],
            'perumahan' => [
                'label' => 'Perumahan',
                'icon' => 'home_work',
                'type' => 'dropdown',
                'items' => [
                    'data_rumah' => [
                        'label' => 'Data Rumah',
                        'icon' => 'house',
                        'link' => '/perumahan/data-rumah'
                    ],
                    'rumah_booking' => [
                        'label' => 'Rumah yang booking',
                        'icon' => 'bookmark',
                        'link' => '/perumahan/rumah-booking'
                    ]
                ]
            ],
            'keuangan' => [
                'label' => 'Keuangan',
                'icon' => 'monetization_on',
                'type' => 'dropdown',
                'items' => [
                    'detail_pembelian_rumah' => [
                        'label' => 'Detail Pembelian Rumah',
                        'icon' => 'person',
                        'link' => '/detail-pembelian-list'
                    ],
                    'pembayaran_rumah' => [
                        'label' => 'Pembayaran Cicilan Rumah',
                        'icon' => 'payments',
                        'link' => '/pembayaran-rumah'
                    ],
                    'progres_pembayaran_rumah' => [
                        'label' => 'Data Progres Pembayaran Rumah',
                        'icon' => 'timeline',
                        'link' => '/progres-pembayaran-rumah'
                    ]
                ]
            ]
        ],
        'spv' => [
            'dashboard' => [
                'label' => 'Dashboard',
                'icon' => 'dashboard',
                'link' => '/dashboard',
                'type' => 'link'
            ],
            'manajemen_proyek' => [
                'label' => 'Manajemen Proyek',
                'icon' => 'business_center',
                'type' => 'dropdown',
                'items' => [
                    'data_rumah' => [
                        'label' => 'Data Rumah',
                        'icon' => 'home_work',
                        'link' => '/data-rumah'
                    ],
                    'rab_rumah' => [
                        'label' => 'RAB Rumah',
                        'icon' => 'description',
                        'link' => '/rab-rumah'
                    ],
                    'realisasi_rumah' => [
                        'label' => 'Realisasi Rumah',
                        'icon' => 'description',
                        'link' => '/realisasi-rumah'
                    ]
                ]
            ]
        ],
        'owner' => [
            'dashboard' => [
                'label' => 'Dashboard',
                'icon' => 'dashboard',
                'link' => '/dashboard',
                'type' => 'link'
            ],
            'marketing' => [
                'label' => 'Marketing',
                'icon' => 'analytics',
                'type' => 'dropdown',
                'items' => [
                    'data_customer' => [
                        'label' => 'Data Customer',
                        'icon' => 'groups',
                        'link' => '/data-customer'
                    ]
                ]
            ],
            'keuangan' => [
                'label' => 'Keuangan',
                'icon' => 'monetization_on',
                'type' => 'dropdown',
                'items' => [
                    'laporan' => [
                        'label' => 'Laporan',
                        'icon' => 'picture_as_pdf',
                        'link' => '/laporan'
                    ]
                ]
            ]
        ]
    ];

    /**
     * Get menu items for a specific role
     */
    public function getMenuForRole(string $role): array
    {
        $role = strtolower($role);

        // Fallback to admin menu for unknown roles
        return $this->menus[$role] ?? $this->menus['admin'];
    }

    /**
     * Check if a menu item is active based on current URL
     */
    public function isMenuItemActive(string $link, string $currentUrl): bool
    {
        // Remove leading slashes and compare
        $link = ltrim($link, '/');
        $currentUrl = ltrim($currentUrl, '/');

        // Exact match or starts with (for dropdown items)
        return $link === $currentUrl || strpos($currentUrl, $link) === 0;
    }
}
