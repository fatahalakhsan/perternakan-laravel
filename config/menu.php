<?php

// Menu samping dan hak akses tiap halaman. Ubah di sini jika pembagian peran berubah.
// Peran: pemilik, pekerja, admin_keuangan, dokter_hewan
return [
    ['label' => 'Dashboard',   'route' => 'dashboard',       'active' => 'dashboard',    'roles' => ['pemilik', 'pekerja', 'admin_keuangan', 'dokter_hewan']],
    ['label' => 'Data Ternak', 'route' => 'ternak.index',    'active' => 'ternak.*',     'roles' => ['pemilik', 'pekerja', 'dokter_hewan']],
    ['label' => 'Perawatan',   'route' => 'perawatan.index', 'active' => 'perawatan.*',  'roles' => ['pemilik', 'pekerja', 'dokter_hewan']],
    ['label' => 'Pesanan',     'route' => 'pesanan.index',   'active' => 'pesanan.*',    'roles' => ['pemilik', 'admin_keuangan']],
    ['label' => 'Leads',       'route' => 'leads.index',     'active' => 'leads.*',      'roles' => ['pemilik', 'admin_keuangan']],
    ['label' => 'Iklan',       'route' => 'iklan.index',     'active' => 'iklan.*',      'roles' => ['pemilik', 'admin_keuangan']],
    ['label' => 'Keuangan',    'route' => 'keuangan.index',  'active' => 'keuangan.*',   'roles' => ['pemilik', 'admin_keuangan']],
    ['label' => 'Laporan',     'route' => 'laporan.index',   'active' => 'laporan.*',    'roles' => ['pemilik', 'admin_keuangan']],
];
