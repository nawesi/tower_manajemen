<?php

namespace App\Http\Controllers;

use App\Models\Tower;
use Illuminate\Http\Request;

class TowerController extends Controller
{
    public function index()
    {
        $towers = Tower::orderBy('name')->paginate(10);
        return view('towers.index', compact('towers'));
    }

    public function create()
    {
        return view('towers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        Tower::create($data);

        return redirect()->route('towers.index')->with('success', 'Tower berhasil ditambahkan.');
    }

    public function edit(Tower $tower)
    {
        return view('towers.edit', compact('tower'));
    }

    public function update(Request $request, Tower $tower)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $tower->update($data);

        return redirect()->route('towers.index')->with('success', 'Tower berhasil diupdate.');
    }

    public function destroy(Tower $tower)
    {
        $tower->delete();
        return redirect()->route('towers.index')->with('success', 'Tower berhasil dihapus.');
    }
}
