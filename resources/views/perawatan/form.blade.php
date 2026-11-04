@php $baru = ! $perawatan->exists; @endphp
@extends('layouts.app')
@section('title', $baru ? 'Jadwalkan Perawatan' : 'Ubah Perawatan')
@section('content')
  <div class="head">
    <div><h1>{{ $baru ? 'Jadwalkan perawatan' : 'Ubah jadwal perawatan' }}</h1><p class="sub">Tanda * wajib diisi.</p></div>
  </div>

  <form class="card" method="POST" action="{{ $baru ? route('perawatan.store') : route('perawatan.update', $perawatan) }}" novalidate>
    @csrf
    @unless ($baru) @method('PUT') @endunless

    <div class="form">
      <div class="fld">
        <label for="ternak_id">Ternak *</label>
        <select id="ternak_id" name="ternak_id" class="in {{ $errors->has('ternak_id') ? 'err' : '' }}">
          <option value="">Pilih ternak</option>
          @foreach ($daftarTernak as $t)
            <option value="{{ $t->id }}" @selected((string) old('ternak_id', $perawatan->ternak_id) === (string) $t->id)>{{ $t->kode }} • {{ $t->jenis }}@if($t->ras) {{ $t->ras }}@endif</option>
          @endforeach
        </select>
        @error('ternak_id')<p class="err-t">{{ $message }}</p>@enderror
      </div>

      <div class="fld">
        <label for="jenis">Jenis perawatan *</label>
        <select id="jenis" name="jenis" class="in {{ $errors->has('jenis') ? 'err' : '' }}">
          <option value="">Pilih jenis</option>
          @foreach (\App\Models\Perawatan::JENIS as $o)
            <option value="{{ $o }}" @selected(old('jenis', $perawatan->jenis) === $o)>{{ $o }}</option>
          @endforeach
        </select>
        @error('jenis')<p class="err-t">{{ $message }}</p>@enderror
      </div>

      <div class="fld">
        <label for="jadwal">Jadwal *</label>
        <input id="jadwal" name="jadwal" type="datetime-local" class="in {{ $errors->has('jadwal') ? 'err' : '' }}" value="{{ old('jadwal', optional($perawatan->jadwal)->format('Y-m-d\TH:i')) }}">
        @error('jadwal')<p class="err-t">{{ $message }}</p>@enderror
      </div>

      <div class="fld">
        <label for="biaya">Biaya (Rp)</label>
        <input id="biaya" name="biaya" type="number" min="0" class="in {{ $errors->has('biaya') ? 'err' : '' }}" value="{{ old('biaya', $perawatan->biaya) }}">
        @error('biaya')<p class="err-t">{{ $message }}</p>@enderror
      </div>

      <div class="fld full">
        <label for="catatan">Catatan</label>
        <textarea id="catatan" name="catatan" class="in {{ $errors->has('catatan') ? 'err' : '' }}">{{ old('catatan', $perawatan->catatan) }}</textarea>
        @error('catatan')<p class="err-t">{{ $message }}</p>@enderror
      </div>
    </div>

    <div class="acts">
      <button type="submit" class="btn p">✓ Simpan</button>
      <a class="btn" href="{{ route('perawatan.index') }}">Batal</a>
    </div>
  </form>
@endsection
