<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login - Sarpras</title>

  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
  <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
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

        <div x-data="{ showPassword: false }">
          <label class="text-sm text-slate-300">Password</label>
          <div class="mt-2 relative">
            <input :type="showPassword ? 'text' : 'password'" name="password" required
              class="w-full rounded-xl bg-slate-950/60 ring-1 ring-slate-800 px-4 py-3 pr-12
                     placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/60 transition">
            <button type="button" @click="showPassword = !showPassword"
              class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 transition">
              <i class="fa-solid" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
            </button>
          </div>
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
