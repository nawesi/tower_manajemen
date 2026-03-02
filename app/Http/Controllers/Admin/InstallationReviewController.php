<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstallationRequest;
use Illuminate\Http\Request;

class InstallationReviewController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status'); // pending/approved/rejected/null

        $q = InstallationRequest::with('tower')->latest();

        if (in_array($status, ['pending', 'approved', 'rejected'], true)) {
            $q->where('status', $status);
        }

        $requests = $q->paginate(12)->withQueryString();

        return view('admin.installations.index', compact('requests', 'status'));
    }

    public function update(Request $request, InstallationRequest $installation)
    {
        $data = $request->validate([
            'status' => ['required', 'in:approved,rejected'],
            'admin_comment' => ['nullable', 'string', 'max:2000'],
        ]);

        $installation->update([
            'status' => $data['status'],
            'admin_comment' => $data['admin_comment'] ?? null,
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Status pengajuan berhasil diupdate.');
    }


    public function export(Request $request)
    {
        $status = $request->query('status'); // pending/approved/rejected/null

        $query = InstallationRequest::with('tower')->latest();

        // Samakan rule filter dengan halaman index (aman dari status aneh/typo)
        if (in_array($status, ['pending', 'approved', 'rejected'], true)) {
            $query->where('status', $status);
        } else {
            $status = null; // biar nama file konsisten "semua"
        }

        $filename = 'pengajuan_pemasangan_' . ($status ?? 'semua') . '_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');

            // BOM supaya Excel Windows kebaca UTF-8
            fwrite($out, "\xEF\xBB\xBF");

            // Header kolom
            fputcsv($out, [
                'Tanggal Pengajuan',
                'Tower',
                'Vendor/Dept',
                'Perangkat',
                'Stack',
                'Status',
                'Tanggal Review',
                'Komentar Admin',
                'Link Foto',
            ]);

            // Chunk biar hemat memory
            $query->chunk(500, function ($rows) use ($out) {
                foreach ($rows as $r) {
                    $photoUrl = $r->device_photo_path ? asset('storage/' . $r->device_photo_path) : '';

                    fputcsv($out, [
                        optional($r->created_at)->format('Y-m-d H:i:s'),
                        $r->tower?->name,
                        $r->vendor_department,
                        $r->device_name,
                        'STACK ' . $r->stack_no,
                        $r->status,
                        optional($r->reviewed_at)->format('Y-m-d H:i:s'),
                        $r->admin_comment,
                        $photoUrl,
                    ]);
                }
            });

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
