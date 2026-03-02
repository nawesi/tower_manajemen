<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Tower</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4" style="max-width:720px;">
  <h3 class="mb-3">Edit Tower</h3>

  <form method="POST" action="{{ route('towers.update', $tower) }}" class="card card-body">
    @csrf
    @method('PUT')

    <div class="mb-3">
      <label class="form-label">Nama Tower</label>
      <input name="name" class="form-control" required value="{{ old('name', $tower->name) }}">
      @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="d-flex gap-2">
      <a href="{{ route('towers.index') }}" class="btn btn-secondary">Kembali</a>
      <button class="btn btn-primary">Update</button>
    </div>
  </form>
</div>
</body>
</html>
