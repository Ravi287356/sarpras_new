<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Dashboard</title>
</head>
<body>
  <h2>Dashboard</h2>
    <h2>Data Users</h2>

    <p>Login sebagai: <b>{{ auth()->user()->username }}</b></p>
    Role: <b>{{ auth()->user()->role->nama ?? '-' }}</b>

  @if((auth()->user()->role->nama ?? '') != 'User')
    <a href="{{ route('users.index') }}">Kelola User</a>
@endif


  <form action="{{ route('logout') }}" method="POST">
    @csrf
    <button type="submit">Logout</button>
  </form>
</body>
</html>
