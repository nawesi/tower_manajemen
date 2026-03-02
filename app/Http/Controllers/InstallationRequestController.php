<?php

namespace App\Http\Controllers;

use App\Models\Tower;
use App\Models\InstallationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InstallationRequestController extends Controller
{
    public function dashboard()
    {
        // dashboard vendor (gambar 1)
        return view('user.dashboard');
    }

    public function create()
    {
        $towers = Tower::orderBy('name')->get();

        return view('user.installation.create', compact('towers'));
    }

    /**
     * Riwayat pengajuan vendor yang sedang login
     */
    public function history()
    {
        $requests = InstallationRequest::with('tower')
            ->where('user_id', Auth::id()) // penting: hanya milik vendor ini
            ->latest()
            ->paginate(10);

        return view('user.installation.index', compact('requests'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tower_id' => ['required', 'exists:towers,id'],
            'vendor_department' => ['required', 'string', 'max:255'],
            'device_name' => ['required', 'string', 'max:255'],
            'stack_no' => ['required', 'integer', 'min:1', 'max:7'],
            'height_from_ground_m' => ['nullable', 'numeric', 'min:0', 'max:9999'],
            'device_photo' => ['nullable', 'image', 'max:5120'], // max 5MB
        ]);

        $photoPath = null;
        if ($request->hasFile('device_photo')) {
            $photoPath = $request->file('device_photo')
                ->store('installation_requests', 'public');
        }

        InstallationRequest::create([
            'user_id' => Auth::id(), // penting: simpan siapa yang submit
            'tower_id' => $data['tower_id'],
            'vendor_department' => $data['vendor_department'],
            'device_name' => $data['device_name'],
            'stack_no' => $data['stack_no'],
            'height_from_ground_m' => $data['height_from_ground_m'] ?? null,
            'device_photo_path' => $photoPath,
             'status' => 'pending',
        ]);

        return redirect()->route('vendor.dashboard')
            ->with('success', 'Pengajuan pemasangan berhasil dikirim.');
    }
}
