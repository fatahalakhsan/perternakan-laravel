<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>Masuk • Geliat masa tua ku</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap">
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<div class="login">
  <form class="card" method="POST" action="{{ route('login') }}" novalidate>
    @csrf
    <div class="logo" style="width:48px;height:48px">🌱</div>
    <div>
      <h1 style="font-size:28px">Masuk</h1>
      <p class="sub">Sistem Informasi Peternakan • Padepokan Geliat masa tua ku</p>
    </div>

    @if ($errors->any())
      <div class="err-box" role="alert">{{ $errors->first() }}</div>
    @endif

    <div class="fld">
      <label for="email">Email</label>
      <input id="email" name="email" type="email" class="in {{ $errors->has('email') ? 'err' : '' }}" value="{{ old('email') }}" autocomplete="username" autofocus required>
    </div>
    <div class="fld">
      <label for="password">Kata sandi</label>
      <input id="password" name="password" type="password" class="in {{ $errors->has('password') ? 'err' : '' }}" autocomplete="current-password" required>
    </div>
    <label class="chk"><input type="checkbox" name="remember" value="1"> Ingat saya</label>
    <button type="submit" class="btn p">Masuk</button>
  </form>
</div>
</body>
</html>
