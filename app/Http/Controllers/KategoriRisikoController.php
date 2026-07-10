<?php

namespace App\Http\Controllers;

use App\Models\KategoriRisiko;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View; // Tambahkan import Rule di atas

class KategoriRisikoController extends Controller
{
    public function index(): View
    {
        $categories = KategoriRisiko::orderBy('created_at', 'desc')->get();

        return view('kategori-risiko.index', compact('categories'));
    }

    public function create(): View
    {
        return view('kategori-risiko.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nama_kategori' => [
                'required',
                'string',
                'max:255',
                // Validasi unik kombinasi nama_kategori dan type
                Rule::unique('kategori_risiko', 'nama_kategori')->where(function ($query) use ($request) {
                    return $query->where('type', $request->type);
                }),
            ],
            'type' => 'required|string|in:smap,departemen',
            'keterangan' => 'nullable|string',
        ], [
            'nama_kategori.unique' => 'Kategori dengan nama dan tipe alokasi tersebut sudah ada.',
        ]);

        KategoriRisiko::create($request->all());

        return redirect()->route('kategori-risiko.index')
            ->with('success', 'Kategori risiko berhasil ditambahkan.');
    }

    public function show($id): View
    {
        $category = KategoriRisiko::with([
            'risiko',
            'smapMonitorings.unitKerja',
            'depMonitorings.unitKerja',
        ])->findOrFail($id);

        return view('kategori-risiko.show', compact('category'));
    }

    public function edit($id): View
    {
        $category = KategoriRisiko::findOrFail($id);

        return view('kategori-risiko.edit', compact('category'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'nama_kategori' => [
                'required',
                'string',
                'max:255',
                // Validasi unik kombinasi dengan mengabaikan ID data yang sedang diedit (ignore)
                Rule::unique('kategori_risiko', 'nama_kategori')
                    ->ignore($id, 'id_kategori')
                    ->where(function ($query) use ($request) {
                        return $query->where('type', $request->type);
                    }),
            ],
            'type' => 'required|string|in:smap,departemen',
            'keterangan' => 'nullable|string',
        ], [
            'nama_kategori.unique' => 'Kategori dengan nama dan tipe alokasi tersebut sudah ada.',
        ]);

        $category = KategoriRisiko::findOrFail($id);
        $category->update($request->all());

        return redirect()->route('kategori-risiko.index')
            ->with('success', 'Kategori risiko berhasil diperbarui.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $category = KategoriRisiko::findOrFail($id);

        // Hapus total data di tabel anak yang bergantung pada kategori ini
        $category->risiko()->delete();
        $category->smapMonitorings()->delete();
        $category->depMonitorings()->delete();

        // Sekarang induknya bersih dari relasi dan bisa dihapus
        $category->delete();

        return redirect()
            ->route('kategori-risiko.index')
            ->with('success', 'Kategori risiko beserta seluruh data terkait berhasil dihapus.');
    }
}
