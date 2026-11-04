@extends('layouts.app')
@section('title', $judul)
@section('content')
  <div class="head"><div><h1>{{ $judul }}</h1><p class="sub">Halaman ini akan dibuat pada tahap WBS {{ $wbs }}.</p></div></div>
  <div class="card"><p>Anda bisa membuka halaman ini karena peran <b>{{ auth()->user()->role }}</b> memiliki akses ke modul {{ $judul }}.</p></div>
@endsection
