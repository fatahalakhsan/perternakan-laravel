@php $baru = ! $ternak->exists; @endphp
@extends('layouts.app')
@section('title', $baru ? 'Tambah Ternak' : 'Ubah Ternak')
@section('content')
  <div class="head">
    <div><h1>{{ $baru ? 'Tambah ternak' : 'Ubah '.$ternak->kode }}</h1><p class="sub">Isi data ternak. Tanda * wajib diisi.</p></div>
  </div>

  <form class="card" method="POST" action="{{ $baru ? route('ternak.store') : route('ternak.update', $ternak) }}" novalidate>
    @csrf
    @unless ($baru) @method('PUT') @endunless

    <div class="form">
      <div class="fld">
        <label for="kode">Kode *</label>
        <input id="kode" name="kode" class="in {{ $errors->has('kode') ? 'err' : '' }}" value="{{ old('kode', $ternak->kode) }}" placeholder="KB-030" {{ $baru ? '' : 'readonly' }} maxlength="6">
        @error('kode')<p class="err-t">{{ $message }}</p>@enderror
        @if ($baru)<p class="hint">Dua huruf, strip, tiga angka. Contoh: KB-030.</p>@endif
      </div>

      <div class="fld">
        <label for="jenis">Jenis *</label>
        <select id="jenis" name="jenis" class="in {{ $errors->has('jenis') ? 'err' : '' }}">
          <option value="">Pilih jenis</option>
          @foreach (\App\Models\Ternak::JENIS as $o)
            <option value="{{ $o }}" @selected(old('jenis', $ternak->jenis) === $o)>{{ $o }}</option>
          @endforeach
        </select>
        @error('jenis')<p class="err-t">{{ $message }}</p>@enderror
      </div>

      <div class="fld">
        <label for="ras">Ras</label>
        <input id="ras" name="ras" class="in {{ $errors->has('ras') ? 'err' : '' }}" value="{{ old('ras', $ternak->ras) }}" placeholder="Contoh: Jawarandu">
        @error('ras')<p class="err-t">{{ $message }}</p>@enderror
      </div>

      <div class="fld">
        <label for="jenis_kelamin">Jenis kelamin *</label>
        <select id="jenis_kelamin" name="jenis_kelamin" class="in {{ $errors->has('jenis_kelamin') ? 'err' : '' }}">
          @foreach (\App\Models\Ternak::KELAMIN as $o)
            <option value="{{ $o }}" @selected(old('jenis_kelamin', $ternak->jenis_kelamin) === $o)>{{ $o }}</option>
          @endforeach
        </select>
        @error('jenis_kelamin')<p class="err-t">{{ $message }}</p>@enderror
      </div>

      <div class="fld">
        <label for="tanggal_lahir">Tanggal lahir</label>
        <input id="tanggal_lahir" name="tanggal_lahir" type="date" class="in {{ $errors->has('tanggal_lahir') ? 'err' : '' }}" value="{{ old('tanggal_lahir', optional($ternak->tanggal_lahir)->format('Y-m-d')) }}" max="{{ now()->toDateString() }}">
        @error('tanggal_lahir')<p class="err-t">{{ $message }}</p>@enderror
        <p class="hint">Umur dihitung otomatis dari tanggal lahir.</p>
      </div>

      <div class="fld">
        <label for="bobot_kg">Bobot (kg) *</label>
        <input id="bobot_kg" name="bobot_kg" type="number" step="0.1" class="in {{ $errors->has('bobot_kg') ? 'err' : '' }}" value="{{ old('bobot_kg', $ternak->bobot_kg) }}">
        @error('bobot_kg')<p class="err-t">{{ $message }}</p>@enderror
      </div>

      <div class="fld">
        <label for="status">Status *</label>
        <select id="status" name="status" class="in {{ $errors->has('status') ? 'err' : '' }}">
          @foreach (\App\Models\Ternak::STATUS as $o)
            <option value="{{ $o }}" @selected(old('status', $ternak->status) === $o)>{{ $o }}</option>
          @endforeach
        </select>
        @error('status')<p class="err-t">{{ $message }}</p>@enderror
      </div>

      <div class="fld">
        <label for="harga_beli">Harga beli (Rp)</label>
        <input id="harga_beli" name="harga_beli" type="number" class="in {{ $errors->has('harga_beli') ? 'err' : '' }}" value="{{ old('harga_beli', $ternak->harga_beli) }}">
        @error('harga_beli')<p class="err-t">{{ $message }}</p>@enderror
      </div>

      <div class="fld full">
        <label for="catatan">Catatan</label>
        <textarea id="catatan" name="catatan" class="in {{ $errors->has('catatan') ? 'err' : '' }}">{{ old('catatan', $ternak->catatan) }}</textarea>
        @error('catatan')<p class="err-t">{{ $message }}</p>@enderror
      </div>
    </div>

    <div class="acts">
      <button type="submit" class="btn p">✓ Simpan</button>
      <a class="btn" href="{{ route('ternak.index') }}">Batal</a>
    </div>
  </form>
@endsection
