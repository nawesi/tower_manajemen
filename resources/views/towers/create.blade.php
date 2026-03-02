<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Tambah Tower</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4" style="max-width:720px;">
  <h3 class="mb-3">Tambah Tower</h3>

  <form method="POST" action="{{ route('towers.store') }}" class="card card-body">
    @csrf
    <div class="mb-3">
      <label class="form-label">Nama Tower</label>
      <input name="name" class="form-control" required placeholder="Tower Main Office 909 LBA 102" value="{{ old('name') }}">
      @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="d-flex gap-2">
      <a href="{{ route('towers.index') }}" class="btn btn-secondary">Batal</a>
      <button class="btn btn-primary">Simpan</button>
    </div>
  </form>
</div>
</body>
</html>
