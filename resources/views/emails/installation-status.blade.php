<p>Yth. {{ $req->user?->pic_name ?? $req->user?->name ?? 'Vendor' }},</p>

<p>
  Pengajuan pemasangan perangkat Anda sudah di
  <strong>{{ strtoupper($req->status) }}</strong>.
</p>

<ul>
  <li><strong>Tower:</strong> {{ $req->tower?->name ?? '-' }}</li>
  <li><strong>Vendor/Dept:</strong> {{ $req->vendor_department ?? '-' }}</li>
  <li><strong>Perangkat:</strong> {{ $req->device_name ?? '-' }}</li>
  <li><strong>Stack:</strong> {{ $req->stack_no ?? '-' }}</li>
  <li><strong>Tinggi:</strong> {{ $req->height_from_ground_m ?? '-' }} m</li>
  <li><strong>Tanggal Review:</strong> {{ optional($req->reviewed_at)->format('d M Y H:i') }}</li>
</ul>

@if($req->admin_comment)
  <p><strong>Catatan Admin:</strong><br>{{ $req->admin_comment }}</p>
@endif

<p>Terima kasih.</p>
<p><em>IT Obi</em></p>