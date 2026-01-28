<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login - Sarpras</title>

  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-950 text-white flex items-center justify-center px-4">

  <div class="w-full max-w-md">
    <div class="rounded-2xl bg-slate-900/40 ring-1 ring-slate-800 shadow-2xl p-7">
      <h1 class="text-2xl font-semibold tracking-tight">Login</h1>
      <p class="text-slate-400 text-sm mt-1">Silakan login untuk masuk ke sistem Sarpras</p>

      {{-- Error --}}
      @if ($errors->any())
        <div class="mt-5 rounded-xl bg-red-500/10 ring-1 ring-red-500/30 p-4 text-red-200 text-sm">
          <b>Login gagal:</b>
          <ul class="list-disc list-inside mt-2 space-y-1">
            @foreach ($errors->all() as $e)
              <li>{{ $e }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
        @csrf

        <div>
          <label class="text-sm text-slate-300">Username</label>
          <input type="text" name="username" value="{{ old('username') }}" required autofocus
            class="mt-2 w-full rounded-xl bg-slate-950/60 ring-1 ring-slate-800 px-4 py-3
                   placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/60 transition">
        </div>

        <div>
          <label class="text-sm text-slate-300">Password</label>
          <input type="password" name="password" required
            class="mt-2 w-full rounded-xl bg-slate-950/60 ring-1 ring-slate-800 px-4 py-3
                   placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/60 transition">
        </div>

        <div class="flex items-center justify-between">
          <label class="flex items-center gap-2 text-sm text-slate-400 select-none">
            <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}
              class="rounded border-slate-700 bg-slate-950/60 text-emerald-500 focus:ring-emerald-500/60">
            Remember me
          </label>
        </div>

        <button type="submit"
          class="w-full px-4 py-3 rounded-xl bg-emerald-600/20 hover:bg-emerald-600/25
                 text-emerald-200 ring-1 ring-emerald-500/30 transition font-medium">
          Login
        </button>
      </form>

    
    </div>
  </div>

</body>
</html>
