@extends('layouts.main', ['title' => 'Pelanggan'])
@section('title-content')
    <i class="fas fa-users mr-2"></i>
    Pelanggan
@endsection

@section('content')
<div class="row">
    <div class="col-xl-4 col-lg-6">
        <form method="POST" action="{{ route('pelanggan.store') }}" class="card card-orange card-outline">
            <div class="card-header">
                <h3 class="card-title">Buat Pelanggan Baru</h3>
            </div>

            <div class="card-body">
                @csrf
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <x-input name="nama" type="text" />
                </div>
                <div class="form-group">
                    <label>Alamat</label>
                    <x-textarea name="alamat" />
                </div>
                <div class="form-group">
                    <label>Nomor Telpon/HP</label>
                    <x-input name="nomor_tlp" type="text" />
                </div>
                <div class="form-group">
                    <label>Member</label>
                    <select name="member" class="form-control">
                        <option value="0">Tidak</option>
                        <option value="1">Ya</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Nomor Member</label>
                    <x-input name="nomor_member" type="text" />
                </div>
                <div class="form-group">
                    <label>Tanggal Bergabung</label>
                    <x-input name="tanggal_bergabung" type="date" />
                </div>
            </div>
            <div class="card-footer form-inline">
                <button type="submit" class="btn btn-primary">
                    Simpan Pelanggan
                </button>
                <a href="{{ route('pelanggan.index') }}" class="btn btn-secondary ml-auto">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection