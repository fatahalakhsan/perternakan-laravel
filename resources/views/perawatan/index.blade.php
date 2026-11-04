@extends('layouts.app')
@section('title', 'Perawatan')
@section('content')
  <div class="head">
    <div><h1>Perawatan</h1><p class="sub">Jadwal dan catatan perawatan ternak.</p></div>
    <a class="btn p" href="{{ route('perawatan.create') }}">＋ Jadwalkan perawatan</a>
  </div>

  @if ($jumlah['terlambat'] > 0)
    <div class="msg"><span>🕒</span><div><b>Ada {{ $jumlah['terlambat'] }} perawatan yang terlambat</b><small>Periksa ternaknya dan catat perawatannya.</small></div></div>
  @endif
  @if ($jumlah['hari_ini'] > 0)
    <div class="msg ok"><span>📅</span><div><b>{{ $jumlah['hari_ini'] }} perawatan dijadwalkan hari ini</b></div></div>
  @endif

  <div class="toolbar">
    <div class="tabs">
      <a class="btn {{ $s ? '' : 'on' }}" href="{{ route('perawatan.index', array_filter(['q' => $q])) }}">Semua ({{ $jumlah['semua'] }})</a>
      @foreach ($filter as $kunci => $label)
        <a class="btn {{ $s === $kunci ? 'on' : '' }}" href="{{ route('perawatan.index', array_filter(['s' => $kunci, 'q' => $q])) }}">{{ $label }} ({{ $jumlah[$kunci] }})</a>
      @endforeach
    </div>
    <form class="search" method="GET" action="{{ route('perawatan.index') }}" role="search">
      @if ($s)<input type="hidden" name="s" value="{{ $s }}">@endif
      <input class="in" type="search" name="q" value="{{ $q }}" placeholder="Cari kode ternak" aria-label="Cari perawatan">
      <button class="btn" type="submit">Cari</button>
    </form>
  </div>

  <div class="tw">
    <table>
      <thead><tr><th>Ternak</th><th>Perawatan</th><th>Jadwal</th><th>Status</th><th>Tindakan</th></tr></thead>
      <tbody>
        @forelse ($items as $p)
          <tr>
            <td data-l="Ternak"><a href="{{ route('ternak.riwayat', $p->ternak_id) }}" style="color:var(--g)" title="Lihat riwayat kesehatan">{{ $p->ternak->kode }}</a></td>
            <td data-l="Perawatan">{{ $p->jenis }}</td>
            <td data-l="Jadwal">{{ \App\Models\Perawatan::tglIndo($p->jadwal) }}</td>
            <td data-l="Status"><span class="badge {{ $p->kelas_badge }}">{{ $p->status_tampil }}</span></td>
            <td data-l="Tindakan">
              <div class="acts">
                @if ($p->status !== 'Selesai')
                  <button type="button" class="btn p" data-selesai data-judul="{{ $p->jenis }} • {{ $p->ternak->kode }}" data-url="{{ route('perawatan.selesai', $p) }}" data-biaya="{{ $p->biaya }}" data-catatan="{{ $p->catatan }}">Catat perawatan</button>
                @endif
                <a class="btn" href="{{ route('perawatan.edit', $p) }}">Ubah</a>
                @if ($bisaHapus)
                  <button type="button" class="btn" style="color:var(--d)" data-hapus data-judul="{{ $p->jenis }} • {{ $p->ternak->kode }}" data-url="{{ route('perawatan.destroy', $p) }}">Hapus</button>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr><td class="vacant" colspan="5">Belum ada jadwal perawatan yang cocok.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="pager">
    <span>Menampilkan {{ $items->firstItem() ?? 0 }}–{{ $items->lastItem() ?? 0 }} dari {{ $items->total() }} jadwal</span>
    <span class="acts">
      <a class="btn {{ $items->onFirstPage() ? 'off' : '' }}" href="{{ $items->previousPageUrl() ?? '#' }}">← Sebelumnya</a>
      <a class="btn {{ $items->hasMorePages() ? '' : 'off' }}" href="{{ $items->nextPageUrl() ?? '#' }}">Berikutnya →</a>
    </span>
  </div>

  <dialog id="dlgSelesai" aria-labelledby="selesaiJudul">
    <form method="POST" id="frmSelesai" class="card">
      @csrf
      @method('PATCH')
      <h2 id="selesaiJudul"></h2>
      <p class="sub">Catat bahwa perawatan ini sudah dilakukan.</p>
      <div class="fld"><label for="sBiaya">Biaya (Rp, opsional)</label><input id="sBiaya" name="biaya" type="number" min="0" class="in"></div>
      <div class="fld"><label for="sCatatan">Catatan (opsional)</label><textarea id="sCatatan" name="catatan" class="in" placeholder="Contoh: kondisi sehat, dosis, reaksi"></textarea></div>
      <div class="acts">
        <button type="button" class="btn" data-tutup style="border:2px solid var(--g)">Batal</button>
        <button type="submit" class="btn p">✓ Simpan</button>
      </div>
    </form>
  </dialog>

  @if ($bisaHapus)
    <dialog id="dlgHapus" aria-labelledby="hapusJudul">
      <form method="POST" id="frmHapus" class="card">
        @csrf
        @method('DELETE')
        <span class="badge d">Tindakan tidak dapat dibatalkan</span>
        <h2 id="hapusJudul"></h2>
        <p>Jadwal dan catatan perawatan ini akan dihapus dari riwayat ternak.</p>
        <div class="acts">
          <button type="button" class="btn" data-tutup style="border:2px solid var(--g)">Batal</button>
          <button type="submit" class="btn d">Ya, hapus</button>
        </div>
      </form>
    </dialog>
  @endif

  <script>
    (function () {
      function pasang(atribut, dlgId, frmId, judulId, awalan, isi) {
        var dlg = document.getElementById(dlgId); if (!dlg) return;
        var frm = document.getElementById(frmId);
        document.querySelectorAll('[' + atribut + ']').forEach(function (b) {
          b.addEventListener('click', function () {
            frm.action = b.dataset.url;
            document.getElementById(judulId).textContent = awalan + b.dataset.judul + (awalan === 'Hapus ' ? '?' : '');
            if (isi) isi(b);
            dlg.showModal();
          });
        });
        dlg.querySelectorAll('[data-tutup]').forEach(function (t) { t.addEventListener('click', function () { dlg.close(); }); });
      }
      pasang('data-selesai', 'dlgSelesai', 'frmSelesai', 'selesaiJudul', 'Catat perawatan ', function (b) {
        document.getElementById('sBiaya').value = b.dataset.biaya || '';
        document.getElementById('sCatatan').value = b.dataset.catatan || '';
      });
      pasang('data-hapus', 'dlgHapus', 'frmHapus', 'hapusJudul', 'Hapus ');
    })();
  </script>
@endsection
