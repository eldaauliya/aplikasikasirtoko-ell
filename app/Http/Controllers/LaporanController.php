<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penjualan;
use DB;

class LaporanController extends Controller
{
    public function index()
    {
        return view('laporan.form');
    }

    public function harian(Request $request)
    {
        $tanggal = $request->tanggal;
        $role = $request->role;

        $penjualan = Penjualan::join('users', 'users.id', 'penjualans.user_id')
            ->leftJoin('pelanggans', 'pelanggans.id', '=', 'penjualans.pelanggan_id')
            ->whereDate('tanggal', $request->tanggal)
            ->where('status', '!=', 'batal');

        // Jika peran (role) dipilih, tambahkan kondisi untuk memfilter berdasarkan peran (role)
        if ($role) {
            $penjualan->where('users.role', $role);
        }

        $penjualan = $penjualan->select('penjualans.*', 'pelanggans.nama as nama_pelanggan', 'users.nama as nama_kasir')
            ->orderBy('id')
            ->get();

        return view('laporan.harian', [
            'penjualan' => $penjualan,
            'tanggal' => $tanggal,
            'role' => $role,
        ]);
    }

    public function bulanan(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $role = $request->role;


        $penjualan = Penjualan::select(
            DB::raw('COUNT(penjualans.id) as jumlah_transaksi'),
            DB::raw('SUM(penjualans.total) as jumlah_total'),
            DB::raw("DATE_FORMAT(penjualans.tanggal, '%d/%m/%Y') tgl"),
        )
        ->where('penjualans.status', '!=', 'batal') // tambahkan 'penjualans' untuk menghindari ambiguitas kolom 'id'
        ->whereMonth('penjualans.tanggal', $bulan) // tambahkan 'penjualans' untuk menghindari ambiguitas kolom 'tanggal'
        ->whereYear('penjualans.tanggal', $tahun); // tambahkan 'penjualans' untuk menghindari ambiguitas kolom 'tanggal'

        // Jika peran (role) dipilih, tambahkan kondisi untuk memfilter berdasarkan peran (role)
        if ($role) {
            $penjualan->leftJoin('users', 'users.id', '=', 'penjualans.user_id')
                ->where('users.role', $role);
        }

        $penjualan = $penjualan->groupBy('tgl')->get();

        $nama_bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $bulan_nama = isset($nama_bulan[$bulan]) ? $nama_bulan[$bulan] : null;

        return view('laporan.bulanan', [
            'penjualan' => $penjualan,
            'bulan' => $bulan_nama,
            'role' => $role
        ]);
    }
}
