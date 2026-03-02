<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Towers</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Master Tower</h3>
    <a class="btn btn-primary" href="{{ route('towers.create') }}">+ Tambah Tower</a>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="card">
    <div class="table-responsive">
      <table class="table table-striped mb-0 align-middle">
        <thead>
          <tr>
            <th style="width:80px;">ID</th>
            <th>Nama Tower</th>
            <th style="width:220px;" class="text-end">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($towers as $t)
            <tr>
              <td>{{ $t->id }}</td>
              <td>{{ $t->name }}</td>
              <td class="text-end">
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('devices.index', ['tower_id' => $t->id]) }}">Perangkat</a>
                <a class="btn btn-sm btn-outline-primary" href="{{ route('towers.edit', $t) }}">Edit</a>
                <form method="POST" action="{{ route('towers.destroy', $t) }}" class="d-inline"
                      onsubmit="return confirm('Hapus tower ini? Semua perangkat & gambar akan ikut terhapus.')">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger">Hapus</button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="3" class="text-center text-muted py-4">Belum ada tower.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="mt-3">
    {{ $towers->links() }}
  </div>
</div>
</body>
</html>
