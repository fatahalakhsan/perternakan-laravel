<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title') • Geliat masa tua ku</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap">
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
<link rel="stylesheet" href="{{ asset('css/tambahan.css') }}">
</head>
<body>
@php
    $peran = ['pemilik' => 'Pemilik', 'pekerja' => 'Pekerja kandang', 'admin_keuangan' => 'Admin keuangan', 'dokter_hewan' => 'Dokter hewan'];
@endphp
<div class="app">
  <aside class="side">
    <div class="brand"><div class="logo">🌱</div><div><b>Geliat masa tua ku</b><small>Sistem Informasi Peternakan</small></div></div>
    <nav class="nav" aria-label="Navigasi utama">
      @foreach (config('menu') as $m)
        @if (in_array(auth()->user()->role, $m['roles'], true))
          <a href="{{ route($m['route']) }}" class="{{ request()->routeIs($m['active']) ? 'on' : '' }}" @if(request()->routeIs($m['active'])) aria-current="page" @endif><i></i>{{ $m['label'] }}</a>
        @endif
      @endforeach
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="out"><i></i>Keluar</button>
      </form>
    </nav>
    <div class="loc"><b>Padepokan</b>Setu, Tangsel</div>
  </aside>
  <div class="main">
    <div class="top"><span>Padepokan Geliat masa tua ku • Setu, Tangsel</span><span>👤 {{ auth()->user()->name }} • {{ $peran[auth()->user()->role] ?? auth()->user()->role }}</span></div>
    <main class="content">
      @if (session('sukses'))
        <div class="msg ok" role="status"><span>✓</span><div><b>{{ session('sukses') }}</b></div></div>
      @endif
      @if (session('galat'))
        <div class="msg d" role="alert"><span>!</span><div><b>{{ session('galat') }}</b></div></div>
      @endif
      @yield('content')
    </main>
  </div>
</div>
</body>
</html>
