@extends('layouts.app')
@section('title', 'Riwayat '.$ternak->kode)
@section('content')
  <div class="head">
    <div>
      <h1>Riwayat kesehatan {{ $ternak->kode }}</h1>
      <p class="sub">{{ $ternak->jenis }}@if($ternak->ras) • {{ $ternak->ras }}@endif • {{ $ternak->jenis_kelamin }} • {{ $ternak->umur }} • <span class="badge {{ ['Tersedia' => 'ok', 'Dirawat' => 'w'][$ternak->status] ?? '' }}">{{ $ternak->status }}</span></p>
    </div>
    <div class="acts">
      <a class="btn p" href="{{ route('perawatan.create', ['ternak' => $ternak->id]) }}">＋ Jadwalkan perawatan</a>
      <a class="btn" href="{{ route('ternak.index') }}">← Data Ternak</a>
    </div>
  </div>

  <div class="tw">
    <table>
      <thead><tr><th>Jadwal</th><th>Perawatan</th><th>Status</th><th>Selesai pada</th><th>Biaya</th><th>Catatan</th><th>Dicatat oleh</th></tr></thead>
      <tbody>
        @forelse ($items as $p)
          <tr>
            <td data-l="Jadwal">{{ \App\Models\Perawatan::tglIndo($p->jadwal) }}</td>
            <td data-l="Perawatan">{{ $p->jenis }}</td>
            <td data-l="Status"><span class="badge {{ $p->kelas_badge }}">{{ $p->status_tampil }}</span></td>
            <td data-l="Selesai pada">{{ \App\Models\Perawatan::tglIndo($p->tanggal_selesai) }}</td>
            <td data-l="Biaya">{{ $p->biaya !== null ? 'Rp'.number_format($p->biaya, 0, ',', '.') : '-' }}</td>
            <td data-l="Catatan">{{ $p->catatan ?: '-' }}</td>
            <td data-l="Dicatat oleh">{{ $p->user->name ?? '-' }}</td>
          </tr>
        @empty
          <tr><td class="vacant" colspan="7">Belum ada riwayat perawatan untuk ternak ini.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
@endsection
