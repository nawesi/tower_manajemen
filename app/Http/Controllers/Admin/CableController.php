<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CableMap;
use App\Models\OtbPort;
use App\Models\Tower;
use App\Models\TowerOtb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CableController extends Controller
{
    public function index()
    {
        $towers = Tower::orderBy('name')->get();

        $activeKml = CableMap::where('is_active', true)->latest()->first();
        $kmlUrl = $activeKml ? asset('storage/' . $activeKml->kml_path) : null;

        return view('admin.cables.index', compact('towers', 'activeKml', 'kmlUrl'));
    }

public function uploadKml(Request $request)
{
    $data = $request->validate([
        'name'  => ['nullable', 'string', 'max:120'],
        'kml'   => ['required', 'file', 'max:10240'],
        'notes' => ['nullable', 'string', 'max:2000'],
    ]);

    // Validasi ekstensi manual (Windows kadang MIME-nya gak kebaca)
    $ext = strtolower($request->file('kml')->getClientOriginalExtension());
    if ($ext !== 'kml') {
        return back()->withErrors([
            'kml' => 'File harus berekstensi .kml (bukan .kmz).',
        ])->withInput();
    }

    $path = $request->file('kml')->store('kml', 'public');

    DB::transaction(function () use ($data, $path) {
        CableMap::where('is_active', true)->update(['is_active' => false]);

        CableMap::create([
            'name' => $data['name'] ?? 'KML Aktif',
            'kml_path' => $path,
            'is_active' => true,
            'notes' => $data['notes'] ?? null,
        ]);
    });

    return redirect()->route('admin.cables.index')
        ->with('success', 'KML berhasil di-upload dan diaktifkan.');
}

    public function updatePort(Request $request, OtbPort $port)
    {
    $data = $request->validate([
        'status' => ['required', 'in:ready,used,broken'],
        'note'   => ['nullable', 'string', 'max:255'],
    ]);

    $port->update([
        'status' => $data['status'],
        'note'   => $data['note'] ?? null,
    ]);

    return back()->with('success', 'Port berhasil diupdate.');
    }


    public function towerDetail(Tower $tower)
    {
        $otbs = $tower->otbs()->with('ports')->get();

        if ($otbs->isEmpty()) {
            DB::transaction(function () use ($tower) {
                $otb = TowerOtb::create([
                    'tower_id' => $tower->id,
                    'name' => 'OTB 1',
                    'total_ports' => 12,
                ]);

                for ($i = 1; $i <= 12; $i++) {
                    OtbPort::create([
                        'tower_otb_id' => $otb->id,
                        'port_no' => $i,
                        'status' => 'ready',
                    ]);
                }
            });

            $otbs = $tower->otbs()->with('ports')->get();
        }

        return view('admin.cables.tower-detail', compact('tower', 'otbs'));
    }

    public function generateOtbPorts(Request $request, \App\Models\Tower $tower)
{
    $data = $request->validate([
        'otb_count' => ['required', 'integer', 'min:1', 'max:20'],
        'ports_per_otb' => ['required', 'integer', 'min:1', 'max:96'],
        'mode' => ['nullable', 'in:skip,reset'], // opsional
    ]);

    $otbCount = (int) $data['otb_count'];
    $portsPerOtb = (int) $data['ports_per_otb'];
    $mode = $data['mode'] ?? 'skip';

    DB::transaction(function () use ($tower, $otbCount, $portsPerOtb, $mode) {
        if ($mode === 'reset') {
            // hapus semua OTB + port untuk tower ini
            $tower->otbs()->delete(); // cascade delete ports karena FK cascadeOnDelete
        }

        // ambil OTB yang ada
        $existingOtbs = $tower->otbs()->with('ports')->get();

        // buat OTB sampai jumlah terpenuhi
        for ($i = $existingOtbs->count() + 1; $i <= $otbCount; $i++) {
            \App\Models\TowerOtb::create([
                'tower_id' => $tower->id,
                'name' => "OTB {$i}",
                'total_ports' => $portsPerOtb,
            ]);
        }

        // refresh OTB setelah create
        $otbs = $tower->otbs()->with('ports')->orderBy('id')->get();

        foreach ($otbs as $otb) {
            // update total_ports biar sesuai input
            $otb->update(['total_ports' => $portsPerOtb]);

            $existingPorts = $otb->ports->pluck('port_no')->all();

            // mode skip: hanya menambah port yang belum ada
            // mode reset sudah menghapus semua, jadi akan create semua port
            for ($p = 1; $p <= $portsPerOtb; $p++) {
                if (in_array($p, $existingPorts, true)) {
                    continue;
                }
                \App\Models\OtbPort::create([
                    'tower_otb_id' => $otb->id,
                    'port_no' => $p,
                    'status' => 'ready',
                ]);
            }
        }
    });

    return redirect()
        ->route('admin.cables.tower.detail', $tower->id)
        ->with('success', 'OTB & Port berhasil digenerate.');
}



}