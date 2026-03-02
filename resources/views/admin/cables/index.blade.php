<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin - List Jalur Kabel</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Leaflet -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

  <!-- togeojson: KML -> GeoJSON -->
  <script src="https://unpkg.com/@tmcw/togeojson@5.8.1/dist/togeojson.umd.js"></script>

  <style>
    #map { height: 75vh; border-radius: 12px; }

    .tower-list { max-height: 52vh; overflow: auto; }
    .sidebar-sticky { position: sticky; top: 16px; }

    .form-label-sm { font-size: .85rem; color: #6c757d; margin-bottom: .25rem; }
  </style>
</head>

<body class="bg-light">
<div class="container-fluid py-4">

  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-3 px-2">
    <div>
      <div class="h5 mb-0 fw-bold">Admin - List Jalur Kabel (Uplink/Backbone)</div>
      <div class="small text-muted">Upload 1 KML aktif untuk menampilkan jalur & posisi tower</div>
    </div>
    <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.welcome') }}">Admin Dashboard</a>
  </div>

  <!-- Alerts -->
  @if(session('success'))
    <div class="alert alert-success mx-2 mb-3">{{ session('success') }}</div>
  @endif

  @if($errors->any())
    <div class="alert alert-danger mx-2 mb-3">
      <div class="fw-semibold mb-1">Upload gagal:</div>
      <ul class="mb-0">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="row g-3 px-2">
    <!-- Sidebar -->
    <div class="col-12 col-lg-3">
      <div class="sidebar-sticky">

        <!-- Upload Card -->
        <div class="card mb-3">
          <div class="card-header fw-semibold">Upload KML (1 Aktif)</div>
          <div class="card-body">
            <form method="POST" action="{{ route('admin.cables.kml.upload') }}" enctype="multipart/form-data" class="d-grid gap-2">
              @csrf

              <div>
                <div class="form-label-sm">Nama (opsional)</div>
                <input class="form-control form-control-sm" type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Link FO HARITA OBI">
              </div>

              <div>
                <div class="form-label-sm">File KML</div>
                <input class="form-control form-control-sm" type="file" name="kml" accept=".kml" required>
              </div>

              <div>
                <div class="form-label-sm">Catatan (opsional)</div>
                <textarea class="form-control form-control-sm" name="notes" rows="2" placeholder="Opsional">{{ old('notes') }}</textarea>
              </div>

              <button class="btn btn-primary btn-sm">Upload & Aktifkan</button>
            </form>

            <hr class="my-3">

            <div class="small text-muted">KML aktif:</div>
            <div class="fw-semibold">{{ $activeKml?->name ?? '-' }}</div>

            @if($kmlUrl)
              <div class="mt-2">
                <a class="small" href="{{ $kmlUrl }}" target="_blank" rel="noopener">Buka KML</a>
              </div>
            @endif
          </div>
        </div>

        <!-- Tower List -->
        <div class="card">
          <div class="card-header fw-semibold">List Tower</div>
          <div class="card-body tower-list">
            @if($towers->isEmpty())
              <div class="text-muted small">Belum ada data tower.</div>
            @else
              <ol class="mb-0">
                @foreach($towers as $t)
                  <li class="mb-2 d-flex justify-content-between align-items-center">
                    <span class="me-2">{{ $t->name }}</span>
                    <a class="btn btn-outline-dark btn-sm" href="{{ route('admin.cables.tower.detail', $t->id) }}">
                      Detail
                    </a>
                  </li>
                @endforeach
              </ol>
            @endif
          </div>
        </div>

      </div>
    </div>

    <!-- Map -->
    <div class="col-12 col-lg-9">
      <div class="card">
        <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
          <span>Mapping Jalur Kabel</span>
          @if($kmlUrl)
            <span class="small text-muted">KML aktif sedang dimuat...</span>
          @endif
        </div>

        <div class="card-body">
          <div id="map"></div>

          @if(!$kmlUrl)
            <div class="alert alert-warning mt-3 mb-0">
              Belum ada KML aktif. Upload dulu untuk menampilkan jalur & posisi tower.
            </div>
          @endif
        </div>
      </div>
    </div>

  </div>
</div>

<script>
  // Init map
  const map = L.map('map').setView([-2.5, 118.0], 5);

  // Base layers
  const street = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap'
  });

  const satellite = L.tileLayer(
    'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
    { maxZoom: 19, attribution: 'Tiles &copy; Esri' }
  );

  // Default layer: Satellite
  satellite.addTo(map);

  // Layer switcher
  L.control.layers({
    "Street": street,
    "Satellite": satellite
  }).addTo(map);

  // KML loader
  const kmlUrl = @json($kmlUrl);
  let kmlLayer = null;

  async function loadKml(url) {
    try {
      const res = await fetch(url);
      if (!res.ok) throw new Error('KML fetch failed: ' + res.status);

      const kmlText = await res.text();
      const kmlDoc = new DOMParser().parseFromString(kmlText, 'text/xml');
      const geojson = window.toGeoJSON.kml(kmlDoc);

      const featureCount = geojson?.features?.length || 0;
      if (featureCount === 0) {
        alert('KML berhasil dibaca, tapi tidak ada geometry yang bisa ditampilkan (features = 0).');
        return;
      }

      if (kmlLayer) map.removeLayer(kmlLayer);

      kmlLayer = L.geoJSON(geojson, {
        style: { weight: 3 },
        pointToLayer: (feature, latlng) => L.marker(latlng),
        onEachFeature: (feature, lyr) => {
          const name = feature?.properties?.name || 'Objek';
          lyr.bindPopup(name);
        }
      }).addTo(map);

      map.fitBounds(kmlLayer.getBounds());
    } catch (e) {
      console.error(e);
      alert('Gagal menampilkan KML. Pastikan URL KML bisa diakses (tidak 404) dan domain/port konsisten.');
    }
  }

  if (kmlUrl) loadKml(kmlUrl);
</script>

</body>
</html>