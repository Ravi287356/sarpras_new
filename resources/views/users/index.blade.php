<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Data Users</title>
</head>
<body>
  <h2>Data Users</h2>


  <a href="{{ route('dashboard') }}">⬅ Kembali</a>
  <br><br>

  @if(session('success'))
    <p style="color:green">{{ session('success') }}</p>
  @endif

  @if(session('error'))
    <p style="color:red">{{ session('error') }}</p>
  @endif

  <p>
    <a href="{{ route('users.create') }}">+ Tambah User</a>
  </p>

  <table border="1" cellpadding="8" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>No</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($users as $u)
            <tr>
                <td>{{ $users->firstItem() + $loop->index }}</td>
                <td>{{ $u->username }}</td>
                <td>{{ $u->email }}</td>
                <td>{{ $u->role->nama ?? '-' }}</td>
                <td>
                    <a href="{{ route('users.edit', $u->id) }}">Edit</a>

                    <form action="{{ route('users.destroy', $u->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Yakin hapus user ini?')">Hapus</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

{{ $users->links() }}
