<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;
use App\Models\Kategori;



class ProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;

        $produks = Produk::join('kategoris', 'kategoris.id', 'produks.kategori_id')
            ->orderBy('produks.id')
            ->select('produks.*', 'kategoris.nama_kategori as nama_kategori') // Mengambil nama kategori dari tabel kategoris
            ->when($search, function ($q, $search) {
                return $q->where('kode_produk', 'like', "%{$search}%")
                    ->orWhere('nama_produk', 'like', "%{$search}%");
            })
            ->paginate();

        if($search) $produks->appends(['search'=>$search]);

        // Check if no products found after search
        $isEmpty = $produks->isEmpty();

        return view('produk.index', [
            'produks' => $produks,
            'isEmpty' => $isEmpty, // Pass the flag to the view
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $dataKategori = Kategori::orderBy('nama_kategori')->get();

        $kategoris = [
            ['', 'Pilih Kategori:']
        ];

        foreach ($dataKategori as $kategori) {
            $kategoris[] = [$kategori->id, $kategori->nama_kategori];
        }

        return view('produk.create', [
            'kategoris' => $kategoris
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_produk'=>['required','max:250','unique:produks'],
            'nama_produk'=>['required','max:150','regex:/^[a-zA-Z\s]+$/'],
            'harga'=>['required','numeric'],
            'kategori_id'=>['required','exists:kategoris,id'],
        ]);

        Produk::create($request->all());

        return redirect()->route('produk.index')->with('store','success');
    }

    /**
     * Display the specified resource.
     */
    public function show(Produk $produk)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Produk $produk)
    {
        $dataKategori = Kategori::orderBy('nama_kategori')->get();

        $kategoris = [
            ['', 'Pilih Kategori:']
        ];

        foreach ($dataKategori as $kategori) {
            $kategoris[] = [$kategori->id, $kategori->nama_kategori];
        }

        return view('produk.edit',[
            'produk'=>$produk,
            'kategoris'=>$kategoris,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Produk $produk)
    {
        $request->validate([
            'kode_produk'=>['required','max:250','unique:produks,kode_produk,' . $produk->id],
            'nama_produk'=>['required','max:150','regex:/^[a-zA-Z\s]+$/'],
            'harga'=>['required','numeric'],
            'kategori_id'=>['required','exists:kategoris,id'],
        ]);

        $produk->update($request->all());

        return redirect()->route('produk.index')->with('update','success');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Produk $produk)
    {
        $produk->delete();

        return back()->with('destroy','success');
    }
}
