<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Tambah User</title>
</head>
<body>
  <h2>Tambah User</h2>

  @if ($errors->any())
    <div style="color:red; margin-bottom:10px;">
      <b>Terjadi kesalahan:</b>
      <ul>
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('users.store') }}" method="POST">
    @csrf

    <div style="margin-bottom:10px;">
      <label>Username</label><br>
      <input type="text" name="username" value="{{ old('username') }}" required>
    </div>

    <div style="margin-bottom:10px;">
      <label>Email</label><br>
      <input type="email" name="email" value="{{ old('email') }}" required>
    </div>

    <div style="margin-bottom:10px;">
      <label>Role</label><br>
      <select name="role_id" required>
    <option value="">-- Pilih Role --</option>
    @foreach($roles as $r)
        <option value="{{ $r->id }}">{{ $r->nama }}</option>
    @endforeach
    </select>
    </div>

    <div style="margin-bottom:10px;">
      <label>Password</label><br>
      <input type="password" name="password" required>
    </div>

    <div style="margin-bottom:10px;">
      <label>Konfirmasi Password</label><br>
      <input type="password" name="password_confirmation" required>
    </div>

    <button type="submit">Simpan</button>
    <a href="{{ route('users.index') }}">Kembali</a>
  </form>
</body>
</html>
