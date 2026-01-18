<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login</title>
</head>
<body>
  <h2>Login</h2>

  @if ($errors->any())
    <div style="color:red; margin-bottom:10px;">
      <ul>
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('login') }}" method="POST">
    @csrf

    <div style="margin-bottom:10px;">
      <label>Username</label><br>
      <input type="text" name="username" value="{{ old('username') }}" required autofocus>
    </div>

    <div style="margin-bottom:10px;">
      <label>Password</label><br>
      <input type="password" name="password" required>
    </div>

    <div style="margin-bottom:10px;">
      <label>
        <input type="checkbox" name="remember" value="1">
        Remember me
      </label>
    </div>

    <button type="submit">Login</button>
  </form>
</body>
</html>
