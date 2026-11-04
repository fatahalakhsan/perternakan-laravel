@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
  <div class="head">
    <div><h1>Dashboard</h1><p class="sub">Selamat datang, {{ auth()->user()->name }}.</p></div>
  </div>

  <div class="grid4">
    <div class="card kpi"><small>Total ternak</small><strong>{{ $ternak }}</strong><span>{{ $tersedia }} tersedia</span></div>
    <div class="card kpi"><small>Total pesanan</small><strong>{{ $pesanan }}</strong></div>
    <div class="card kpi"><small>Leads baru</small><strong>{{ $leads }}</strong><span>belum dihubungi</span></div>
    <div class="card kpi"><small>Peran Anda</small><strong style="font-size:22px">{{ ucwords(str_replace('_', ' ', auth()->user()->role)) }}</strong></div>
  </div>

  <div class="msg ok"><span>✓</span><div><b>Login dan hak akses berfungsi</b><small>Menu di samping hanya menampilkan halaman yang boleh dibuka oleh peran Anda. Dashboard lengkap dibuat pada tahap WBS 9.0.</small></div></div>
@endsection
