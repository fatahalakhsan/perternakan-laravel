@extends('layouts.app')
@section('title', 'Data Ternak')
@section('content')
  <div class="head">
    <div><h1>Data Ternak</h1><p class="sub">Catatan ternak yang dimiliki Padepokan.@unless($bisaUbah) <b>(Hanya lihat)</b>@endunless</p></div>
    @if ($bisaUbah)
      <a class="btn p" href="{{ route('ternak.create') }}">＋ Tambah ternak</a>
    @endif
  </div>

  <div class="toolbar">
    <div class="tabs">
      <a class="btn {{ $status ? '' : 'on' }}" href="{{ route('ternak.index', array_filter(['q' => $q])) }}">Semua ({{ $jumlah->sum() }})</a>
      @foreach (\App\Models\Ternak::STATUS as $s)
        <a class="btn {{ $status === $s ? 'on' : '' }}" href="{{ route('ternak.index', array_filter(['status' => $s, 'q' => $q])) }}">{{ $s }} ({{ $jumlah[$s] ?? 0 }})</a>
      @endforeach
    </div>
    <form class="search" method="GET" action="{{ route('ternak.index') }}" role="search">
      @if ($status)<input type="hidden" name="status" value="{{ $status }}">@endif
      <input class="in" type="search" name="q" value="{{ $q }}" placeholder="Cari kode atau ras" aria-label="Cari ternak">
      <button class="btn" type="submit">Cari</button>
    </form>
  </div>

  <div class="tw">
    <table>
      <thead>
        <tr><th>Kode</th><th>Jenis</th><th>Kelamin</th><th>Umur</th><th>Bobot</th><th>Status</th>@if ($bisaUbah)<th>Tindakan</th>@endif</tr>
      </thead>
      <tbody>
        @forelse ($items as $t)
          <tr>
            <td data-l="Kode">{{ $t->kode }}</td>
            <td data-l="Jenis">{{ $t->jenis }}@if($t->ras) • {{ $t->ras }}@endif</td>
            <td data-l="Kelamin">{{ $t->jenis_kelamin }}</td>
            <td data-l="Umur">{{ $t->umur }}</td>
            <td data-l="Bobot">{{ rtrim(rtrim(number_format($t->bobot_kg, 1, ',', '.'), '0'), ',') }} kg</td>
            <td data-l="Status"><span class="badge {{ ['Tersedia' => 'ok', 'Dirawat' => 'w'][$t->status] ?? '' }}">{{ $t->status }}</span></td>
            @if ($bisaUbah)
              <td data-l="Tindakan">
                <div class="acts">
                  <a class="btn" href="{{ route('ternak.edit', $t) }}">Ubah</a>
                  <button type="button" class="btn" style="color:var(--d)" data-hapus data-kode="{{ $t->kode }}" data-url="{{ route('ternak.destroy', $t) }}" aria-label="Hapus {{ $t->kode }}">Hapus</button>
                </div>
              </td>
            @endif
          </tr>
        @empty
          <tr><td class="vacant" colspan="{{ $bisaUbah ? 7 : 6 }}">Belum ada ternak yang cocok.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="pager">
    <span>Menampilkan {{ $items->firstItem() ?? 0 }}–{{ $items->lastItem() ?? 0 }} dari {{ $items->total() }} ternak</span>
    <span class="acts">
      <a class="btn {{ $items->onFirstPage() ? 'off' : '' }}" href="{{ $items->previousPageUrl() ?? '#' }}">← Sebelumnya</a>
      <a class="btn {{ $items->hasMorePages() ? '' : 'off' }}" href="{{ $items->nextPageUrl() ?? '#' }}">Berikutnya →</a>
    </span>
  </div>

  @if ($bisaUbah)
    <dialog id="dlgHapus" aria-labelledby="hapusJudul">
      <form method="POST" id="frmHapus" class="card">
        @csrf
        @method('DELETE')
        <span class="badge d">Tindakan tidak dapat dibatalkan</span>
        <h2 id="hapusJudul"></h2>
        <p>Catatan ternak ini beserta riwayat perawatannya akan dihapus. Ternak yang sudah ada di pesanan tidak bisa dihapus.</p>
        <div class="acts">
          <button type="button" class="btn" id="batalHapus" style="border:2px solid var(--g)">Batal</button>
          <button type="submit" class="btn d">Ya, hapus ternak</button>
        </div>
      </form>
    </dialog>
    <script>
      (function () {
        var dlg = document.getElementById('dlgHapus'), frm = document.getElementById('frmHapus');
        document.querySelectorAll('[data-hapus]').forEach(function (b) {
          b.addEventListener('click', function () {
            frm.action = b.dataset.url;
            document.getElementById('hapusJudul').textContent = 'Hapus ' + b.dataset.kode + '?';
            dlg.showModal();
          });
        });
        document.getElementById('batalHapus').addEventListener('click', function () { dlg.close(); });
      })();
    </script>
  @endif
@endsection
