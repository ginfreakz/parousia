@extends('admin.layout')

@section('content')

<div class="container">
    <h1>View SDM</h1>
    <form>
        <!-- Personal Section -->
        <h3>Personal</h3>
        <div class="form-row">
            <div class="form-group col-md-6">
            <label for="lembaga_id">Lembaga</label>
            <select name="lembaga_id" id="lembaga_id" class="form-control" disabled>
                <option value="" disabled selected>Pilih</option>
                @foreach($lembagas as $lembaga)
                <option value="{{ $lembaga->id }}" {{ $sdm->lembaga_id == $lembaga->id ? 'selected' : '' }}>{{ $lembaga->name }}</option>
                @endforeach
            </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
            <label for="nama_lengkap">Nama Lengkap</label>
            <input type="text" name="nama_lengkap" id="nama_lengkap" class="form-control" value="{{ $sdm->nama_lengkap }}" disabled>
            </div>
            <div class="form-group col-md-6">
            <label for="nama_panggilan">Nama Panggilan</label>
            <input type="text" name="nama_panggilan" id="nama_panggilan" class="form-control" value="{{ $sdm->nama_panggilan }}" disabled>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
            <label for="peran">Peran</label>
            <select name="peran" id="peran" class="form-control" disabled>
                <option value="" disabled {{ $sdm->jenis_identitas == '' ? 'selected' : '' }}>Pilih</option>
                <option value="Anak" {{ $sdm->peran == 'Anak' ? 'selected' : '' }}>Anak</option>
                <option value="Kepala keluarga" {{ $sdm->peran == 'Kepala keluarga' ? 'selected' : '' }}>Kepala keluarga</option>
                <option value="Ibu rumah tangga" {{ $sdm->peran == 'Ibu rumah tangga' ? 'selected' : '' }}>Ibu rumah tangga</option>
            </select>
            </div>
            <div class="form-group col-md-6">
            <label for="jenis_identitas">Jenis Identitas</label>
            <select name="jenis_identitas" id="jenis_identitas" class="form-control" disabled>
                <option value="" disabled {{ $sdm->jenis_identitas == '' ? 'selected' : '' }}>Pilih</option>
                <option value="KTP" {{ $sdm->jenis_identitas == 'KTP' ? 'selected' : '' }}>KTP</option>
                <option value="SIM" {{ $sdm->jenis_identitas == 'SIM' ? 'selected' : '' }}>SIM</option>
                <option value="Paspor" {{ $sdm->jenis_identitas == 'Paspor' ? 'selected' : '' }}>Paspor</option>
                <option value="Kartu Pelajar" {{ $sdm->jenis_identitas == 'Kartu Pelajar' ? 'selected' : '' }}>Kartu Pelajar</option>
            </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
            <label for="nomor_identitas">Nomor Identitas</label>
            <input type="text" name="nomor_identitas" id="nomor_identitas" class="form-control" value="{{ $sdm->nomor_identitas }}" disabled>
            </div>
            <div class="form-group col-md-6">
            <label for="tempat_lahir">Tempat Lahir</label>
            <input type="text" name="tempat_lahir" id="tempat_lahir" class="form-control" value="{{ $sdm->tempat_lahir }}" disabled>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
            <label for="tanggal_lahir">Tanggal Lahir</label>
            <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control" value="{{ $sdm->tanggal_lahir }}" disabled>
            </div>
            <div class="form-group col-md-6">
            <label for="jenis_kelamin">Jenis Kelamin</label>
            <select name="jenis_kelamin" id="jenis_kelamin" class="form-control" disabled>
                <option value="" disabled {{ $sdm->jenis_kelamin == '' ? 'selected' : '' }}>Pilih</option>
                <option value="Laki-laki" {{ $sdm->jenis_kelamin == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                <option value="Perempuan" {{ $sdm->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
            </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="golongan_darah">Golongan Darah</label>
                <select name="golongan_darah" id="golongan_darah" class="form-control" disabled>
                    <option value="" disabled {{ $sdm->golongan_darah == '' ? 'selected' : '' }}>Pilih</option>
                    <option value="O+" {{ $sdm->golongan_darah == 'O+' ? 'selected' : '' }}>O+</option>
                    <option value="O-" {{ $sdm->golongan_darah == 'O-' ? 'selected' : '' }}>O-</option>
                    <option value="A+" {{ $sdm->golongan_darah == 'A+' ? 'selected' : '' }}>A+</option>
                    <option value="A-" {{ $sdm->golongan_darah == 'A-' ? 'selected' : '' }}>A-</option>
                    <option value="B+" {{ $sdm->golongan_darah == 'B+' ? 'selected' : '' }}>B+</option>
                    <option value="B-" {{ $sdm->golongan_darah == 'B-' ? 'selected' : '' }}>B-</option>
                    <option value="AB+" {{ $sdm->golongan_darah == 'AB+' ? 'selected' : '' }}>AB+</option>
                    <option value="AB-" {{ $sdm->golongan_darah == 'AB-' ? 'selected' : '' }}>AB-</option>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="suku">Suku</label>
                <input type="text" name="suku" id="suku" class="form-control" value="{{ $sdm->suku }}" required disabled>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="status_pernikahan">Status Pernikahan</label>
                <select name="status_pernikahan" id="status_pernikahan" class="form-control" disabled>
                    <option value="" disabled {{ $sdm->status_pernikahan == '' ? 'selected' : '' }}>Pilih</option>
                    <option value="Belum Menikah" {{ $sdm->status_pernikahan == 'Belum Menikah' ? 'selected' : '' }}>Belum Menikah</option>
                    <option value="Menikah" {{ $sdm->status_pernikahan == 'Menikah' ? 'selected' : '' }}>Menikah</option>
                    <option value="Cerai" {{ $sdm->status_pernikahan == 'Cerai' ? 'selected' : '' }}>Cerai</option>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="status_sdm">Status SDM</label>
                <select name="status_sdm" id="status_sdm" class="form-control" disabled>
                    <option value="" disabled {{ $sdm->status_sdm == '' ? 'selected' : '' }}>Pilih</option>
                    <option value="Aktif" {{ $sdm->status_sdm == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Tidak Aktif" {{ $sdm->status_sdm == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
            </div>
        </div>


        <!-- Add other fields for Personal -->

        <!-- Rekening Pribadi Section -->
        <h3>Data Rekening Pribadi</h3>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="rekening_pribadi[nama_bank]">Nama Bank</label>
                <input type="text" name="rekening_pribadi[nama_bank]" id="rekening_pribadi[nama_bank]" class="form-control" value="{{ $sdm->rekening_pribadi['nama_bank'] ?? '' }}" disabled>
            </div>
            <div class="form-group col-md-6">
                <label for="rekening_pribadi[no_rekening]">No Rekening</label>
                <input type="text" name="rekening_pribadi[no_rekening]" id="rekening_pribadi[no_rekening]" class="form-control" value="{{ $sdm->rekening_pribadi['no_rekening'] ?? '' }}"disabled>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="rekening_pribadi[atas_nama]">Atas Nama</label>
                <input type="text" name="rekening_pribadi[atas_nama]" id="rekening_pribadi[atas_nama]" class="form-control" value="{{ $sdm->rekening_pribadi['atas_nama'] ?? '' }}"disabled>
            </div>
        </div>

        <!-- Tempat Tinggal Section -->
        <h3>Tempat Tinggal - Sesuai KTP</h3>
        <div class="form-row">
            <div class="form-group col-md-6">
            <label for="tempat_tinggal_ktp[alamat]">Alamat</label>
            <input type="text" name="tempat_tinggal_ktp[alamat]" id="tempat_tinggal_ktp[alamat]" class="form-control" value="{{ $sdm->tempat_tinggal_ktp['alamat'] ?? '' }}"disabled>
            </div>
            <div class="form-group col-md-6">
            <label for="tempat_tinggal_ktp[negara]">Negara</label>
            <input type="text" name="tempat_tinggal_ktp[negara]" id="tempat_tinggal_ktp[negara]" class="form-control" value="{{ $sdm->tempat_tinggal_ktp['negara'] ?? '' }}"disabled>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
            <label for="tempat_tinggal_ktp[provinsi]">Provinsi</label>
            <input type="text" name="tempat_tinggal_ktp[provinsi]" id="tempat_tinggal_ktp[provinsi]" class="form-control" value="{{ $sdm->tempat_tinggal_ktp['provinsi'] ?? '' }}"disabled>
            </div>
            <div class="form-group col-md-6">
            <label for="tempat_tinggal_ktp[kota]">Kota</label>
            <input type="text" name="tempat_tinggal_ktp[kota]" id="tempat_tinggal_ktp[kota]" class="form-control" value="{{ $sdm->tempat_tinggal_ktp['kota'] ?? '' }}"disabled>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
            <label for="tempat_tinggal_ktp[kecamatan]">Kecamatan</label>
            <input type="text" name="tempat_tinggal_ktp[kecamatan]" id="tempat_tinggal_ktp[kecamatan]" class="form-control" value="{{ $sdm->tempat_tinggal_ktp['kecamatan'] ?? '' }}"disabled>
            </div>
            <div class="form-group col-md-6">
            <label for="tempat_tinggal_ktp[kelurahan]">Kelurahan</label>
            <input type="text" name="tempat_tinggal_ktp[kelurahan]" id="tempat_tinggal_ktp[kelurahan]" class="form-control" value="{{ $sdm->tempat_tinggal_ktp['kelurahan'] ?? '' }}"disabled>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
            <label for="tempat_tinggal_ktp[rt_rw]">RT/RW</label>
            <input type="text" name="tempat_tinggal_ktp[rt_rw]" id="tempat_tinggal_ktp[rt_rw]" class="form-control" value="{{ $sdm->tempat_tinggal_ktp['rt_rw'] ?? '' }}"disabled>
            </div>
        </div>

        <!-- Add other fields for Tempat Tinggal -->

        <h3>Tempat Tinggal - Sekarang</h3>
        <div class="form-row">
            <div class="form-group col-md-6">
            <label for="tempat_tinggal_sekarang[alamat]">Alamat</label>
            <input type="text" name="tempat_tinggal_sekarang[alamat]" id="tempat_tinggal_sekarang[alamat]" class="form-control" value="{{ $sdm->tempat_tinggal_sekarang['alamat'] ?? '' }}"disabled>
            </div>
            <div class="form-group col-md-6">
            <label for="tempat_tinggal_sekarang[negara]">Negara</label>
            <input type="text" name="tempat_tinggal_sekarang[negara]" id="tempat_tinggal_sekarang[negara]" class="form-control" value="{{ $sdm->tempat_tinggal_sekarang['negara'] ?? '' }}"disabled>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
            <label for="tempat_tinggal_sekarang[provinsi]">Provinsi</label>
            <input type="text" name="tempat_tinggal_sekarang[provinsi]" id="tempat_tinggal_sekarang[provinsi]" class="form-control" value="{{ $sdm->tempat_tinggal_sekarang['provinsi'] ?? '' }}"disabled>
            </div>
            <div class="form-group col-md-6">
            <label for="tempat_tinggal_sekarang[kota]">Kota</label>
            <input type="text" name="tempat_tinggal_sekarang[kota]" id="tempat_tinggal_sekarang[kota]" class="form-control" value="{{ $sdm->tempat_tinggal_sekarang['kota'] ?? '' }}"disabled>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
            <label for="tempat_tinggal_sekarang[kecamatan]">Kecamatan</label>
            <input type="text" name="tempat_tinggal_sekarang[kecamatan]" id="tempat_tinggal_sekarang[kecamatan]" class="form-control" value="{{ $sdm->tempat_tinggal_sekarang['kecamatan'] ?? '' }}"disabled>
            </div>
            <div class="form-group col-md-6">
            <label for="tempat_tinggal_sekarang[kelurahan]">Kelurahan</label>
            <input type="text" name="tempat_tinggal_sekarang[kelurahan]" id="tempat_tinggal_sekarang[kelurahan]" class="form-control" value="{{ $sdm->tempat_tinggal_sekarang['kelurahan'] ?? '' }}"disabled>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
            <label for="tempat_tinggal_sekarang[rt_rw]">RT/RW</label>
            <input type="text" name="tempat_tinggal_sekarang[rt_rw]" id="tempat_tinggal_sekarang[rt_rw]" class="form-control" value="{{ $sdm->tempat_tinggal_sekarang['rt_rw'] ?? '' }}"disabled>
            </div>
        </div>
        <h3>Lokasi Pelayanan</h3>
        <div class="form-group">
            <label for="lokasi_pelayanan">Lokasi Pelayanan</label>
            <select name="lokasi_pelayanan[]" id="lokasi_pelayanan" class="form-control" multiple disabled>
                <option value="" disabled>Pilih</option>
                @foreach($regions as $region)
                    @php
                        $cityName = $region->kota; // Since you only store city names
                    @endphp
                    <option value="{{ $cityName }}" 
                        {{ in_array($cityName, $selectedRegions) ? 'selected' : '' }}>
                        {{ $cityName }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Kontak & Medsos Section -->
        <h3>Kontak & Medsos</h3>
        <div class="form-row">
            <div class="form-group col-md-6">
            <label for="kontak_medsos[no_hp1]">No HP 1</label>
            <input type="text" name="kontak_medsos[no_hp1]" id="kontak_medsos[no_hp1]" class="form-control" value="{{ $sdm->kontak_medsos['no_hp1'] ?? '' }}"disabled>
            </div>
            <div class="form-group col-md-6">
            <label for="kontak_medsos[no_hp2]">No HP 2</label>
            <input type="text" name="kontak_medsos[no_hp2]" id="kontak_medsos[no_hp2]" class="form-control" value="{{ $sdm->kontak_medsos['no_hp2'] ?? '' }}"disabled>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
            <label for="kontak_medsos[email_1]">Email 1</label>
            <input type="text" name="kontak_medsos[email_1]" id="kontak_medsos[email_1]" class="form-control" value="{{ $sdm->kontak_medsos['email_1'] ?? '' }}"disabled>
            </div>
            <div class="form-group col-md-6">
            <label for="kontak_medsos[email_2]">Email 2</label>
            <input type="text" name="kontak_medsos[email_2]" id="kontak_medsos[email_2]" class="form-control" value="{{ $sdm->kontak_medsos['email_2'] ?? '' }}"disabled>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
            <label for="kontak_medsos[instagram]">Instagram</label>
            <input type="text" name="kontak_medsos[instagram]" id="kontak_medsos[instagram]" class="form-control" value="{{ $sdm->kontak_medsos['instagram'] ?? '' }}"disabled>
            </div>
            <div class="form-group col-md-6">
            <label for="kontak_medsos[tiktok]">Tiktok</label>
            <input type="text" name="kontak_medsos[tiktok]" id="kontak_medsos[tiktok]" class="form-control" value="{{ $sdm->kontak_medsos['tiktok'] ?? '' }}"disabled>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
            <label for="kontak_medsos[facebook]">Facebook</label>
            <input type="text" name="kontak_medsos[facebook]" id="kontak_medsos[facebook]" class="form-control" value="{{ $sdm->kontak_medsos['facebook'] ?? '' }}"disabled>
            </div>
            <div class="form-group col-md-6">
            <label for="kontak_medsos[lain_lain]">Lain-lain</label>
            <input type="text" name="kontak_medsos[lain_lain]" id="kontak_medsos[lain_lain]" class="form-control" value="{{ $sdm->kontak_medsos['lain_lain'] ?? '' }}"disabled>
            </div>
        </div>

        <!-- Add other fields for Kontak & Medsos -->

        <!-- Pendidikan Section -->
<h3>Pendidikan</h3>

<!-- TK / PAUD Section -->
<h4>TK / PAUD</h4>
<div class="form-row">
    <div class="form-group col-md-4">
        <label for="pendidikan[tk_paud][nama]">Nama Sekolah</label>
        <input type="text" name="pendidikan[tk_paud][nama]" id="pendidikan[tk_paud][nama]" class="form-control" value="{{ $sdm->pendidikan['tk_paud']['nama'] ?? '' }}"disabled>
    </div>
    <div class="form-group col-md-4">
        <label for="pendidikan[tk_paud][tahun_masuk]">Tahun Masuk</label>
        <input type="text" name="pendidikan[tk_paud][tahun_masuk]" id="pendidikan[tk_paud][tahun_masuk]" class="form-control" value="{{ $sdm->pendidikan['tk_paud']['tahun_masuk'] ?? '' }}"disabled>
    </div>
    <div class="form-group col-md-4">
        <label for="pendidikan[tk_paud][tahun_lulus]">Tahun Lulus</label>
        <input type="text" name="pendidikan[tk_paud][tahun_lulus]" id="pendidikan[tk_paud][tahun_lulus]" class="form-control" value="{{ $sdm->pendidikan['tk_paud']['tahun_lulus'] ?? '' }}"disabled>
    </div>
</div>

<!-- SD Section -->
<h4>SD</h4>
<div class="form-row">
    <div class="form-group col-md-4">
        <label for="pendidikan[sd][nama]">Nama Sekolah</label>
        <input type="text" name="pendidikan[sd][nama]" id="pendidikan[sd][nama]" class="form-control" value="{{ $sdm->pendidikan['sd']['nama'] ?? '' }}"disabled>
    </div>
    <div class="form-group col-md-4">
        <label for="pendidikan[sd][tahun_masuk]">Tahun Masuk</label>
        <input type="text" name="pendidikan[sd][tahun_masuk]" id="pendidikan[sd][tahun_masuk]" class="form-control" value="{{ $sdm->pendidikan['sd']['tahun_masuk'] ?? '' }}"disabled>
    </div>
    <div class="form-group col-md-4">
        <label for="pendidikan[sd][tahun_lulus]">Tahun Lulus</label>
        <input type="text" name="pendidikan[sd][tahun_lulus]" id="pendidikan[sd][tahun_lulus]" class="form-control" value="{{ $sdm->pendidikan['sd']['tahun_lulus'] ?? '' }}"disabled>
    </div>
</div>

<!-- SMP Section -->
<h4>SMP</h4>
<div class="form-row">
    <div class="form-group col-md-4">
        <label for="pendidikan[smp][nama]">Nama Sekolah</label>
        <input type="text" name="pendidikan[smp][nama]" id="pendidikan[smp][nama]" class="form-control" value="{{ $sdm->pendidikan['smp']['nama'] ?? '' }}"disabled>
    </div>
    <div class="form-group col-md-4">
        <label for="pendidikan[smp][tahun_masuk]">Tahun Masuk</label>
        <input type="text" name="pendidikan[smp][tahun_masuk]" id="pendidikan[smp][tahun_masuk]" class="form-control" value="{{ $sdm->pendidikan['smp']['tahun_masuk'] ?? '' }}"disabled>
    </div>
    <div class="form-group col-md-4">
        <label for="pendidikan[smp][tahun_lulus]">Tahun Lulus</label>
        <input type="text" name="pendidikan[smp][tahun_lulus]" id="pendidikan[smp][tahun_lulus]" class="form-control" value="{{ $sdm->pendidikan['smp']['tahun_lulus'] ?? '' }}"disabled>
    </div>
</div>

<!-- SMA Section -->
<h4>SMA</h4>
<div class="form-row">
    <div class="form-group col-md-4">
        <label for="pendidikan[sma][nama]">Nama Sekolah</label>
        <input type="text" name="pendidikan[sma][nama]" id="pendidikan[sma][nama]" class="form-control" value="{{ $sdm->pendidikan['sma']['nama'] ?? '' }}"disabled>
    </div>
    <div class="form-group col-md-4">
        <label for="pendidikan[sma][tahun_masuk]">Tahun Masuk</label>
        <input type="text" name="pendidikan[sma][tahun_masuk]" id="pendidikan[sma][tahun_masuk]" class="form-control" value="{{ $sdm->pendidikan['sma']['tahun_masuk'] ?? '' }}"disabled>
    </div>
    <div class="form-group col-md-4">
        <label for="pendidikan[sma][tahun_lulus]">Tahun Lulus</label>
        <input type="text" name="pendidikan[sma][tahun_lulus]" id="pendidikan[sma][tahun_lulus]" class="form-control" value="{{ $sdm->pendidikan['sma']['tahun_lulus'] ?? '' }}"disabled>
    </div>
</div>

<!-- Pendidikan Tinggi Section -->
<h4>Pendidikan Tinggi</h4>
@for ($i = 1; $i <= 3; $i++)
    <h5>Pendidikan Tinggi {{ $i }}</h5>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="pendidikan[tinggi_{{ $i }}][universitas]">Universitas / STT</label>
            <input type="text" name="pendidikan[tinggi_{{ $i }}][universitas]" id="pendidikan[tinggi_{{ $i }}][universitas]" class="form-control" value="{{ $sdm->pendidikan['tinggi_' . $i]['universitas'] ?? '' }}"disabled>
        </div>
        <div class="form-group col-md-6">
            <label for="pendidikan[tinggi_{{ $i }}][program]">Program</label>
            <select name="pendidikan[tinggi_{{ $i }}][program]" id="pendidikan[tinggi_{{ $i }}][program]" class="form-control"disabled>
                <option value="" disabled {{ !isset($sdm->pendidikan['tinggi_' . $i]['program']) ? 'selected' : '' }}>Pilih</option>
                <option value="S1" {{ (isset($sdm->pendidikan['tinggi_' . $i]['program']) && $sdm->pendidikan['tinggi_' . $i]['program'] == 'S1') ? 'selected' : '' }}>S1</option>
                <option value="S2" {{ (isset($sdm->pendidikan['tinggi_' . $i]['program']) && $sdm->pendidikan['tinggi_' . $i]['program'] == 'S2') ? 'selected' : '' }}>S2</option>
                <option value="S3" {{ (isset($sdm->pendidikan['tinggi_' . $i]['program']) && $sdm->pendidikan['tinggi_' . $i]['program'] == 'S3') ? 'selected' : '' }}>S3</option>
            </select>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="pendidikan[tinggi_{{ $i }}][fakultas]">Fakultas</label>
            <input type="text" name="pendidikan[tinggi_{{ $i }}][fakultas]" id="pendidikan[tinggi_{{ $i }}][fakultas]" class="form-control" value="{{ $sdm->pendidikan['tinggi_' . $i]['fakultas'] ?? '' }}"disabled>
        </div>
        <div class="form-group col-md-6">
            <label for="pendidikan[tinggi_{{ $i }}][jurusan]">Jurusan</label>
            <input type="text" name="pendidikan[tinggi_{{ $i }}][jurusan]" id="pendidikan[tinggi_{{ $i }}][jurusan]" class="form-control" value="{{ $sdm->pendidikan['tinggi_' . $i]['jurusan'] ?? '' }}"disabled>
        </div>
    </div>
@endfor

<!-- Beasiswa Section -->
<h4>Beasiswa Parousia</h4>
<div class="form-row">
    <div class="form-group col-md-6">
        <label for="pendidikan[beasiswa_parousia][status]">Beasiswa Parousia</label>
        <select name="pendidikan[beasiswa_parousia][status]" id="pendidikan[beasiswa_parousia][status]" class="form-control" onchange="toggleBeasiswaFields()"disabled>
            <option value="" disabled {{ !isset($sdm->pendidikan['beasiswa_parousia']['status']) ? 'selected' : '' }}>Pilih</option>
            <option value="Ya" {{ (isset($sdm->pendidikan['beasiswa_parousia']['status']) && $sdm->pendidikan['beasiswa_parousia']['status'] == 'Ya') ? 'selected' : '' }}>Ya</option>
            <option value="Tidak" {{ (isset($sdm->pendidikan['beasiswa_parousia']['status']) && $sdm->pendidikan['beasiswa_parousia']['status'] == 'Tidak') ? 'selected' : '' }}>Tidak</option>
        </select>
    </div>
</div>
<div id="beasiswa_fields" style="display: {{ (isset($sdm->pendidikan['beasiswa_parousia']['status']) && $sdm->pendidikan['beasiswa_parousia']['status'] == 'Ya') ? 'block' : 'none' }};">
    <div class="form-group">
        <label for="pendidikan[beasiswa_parousia][periode_waktu]">Periode Waktu</label>
        <input type="text" name="pendidikan[beasiswa_parousia][periode_waktu]" id="pendidikan[beasiswa_parousia][periode_waktu]" class="form-control" value="{{ $sdm->pendidikan['beasiswa_parousia']['periode_waktu'] ?? '' }}"disabled>
    </div>
    <div class="form-group">
        <label for="pendidikan[beasiswa_parousia][ikatan_dinas]">Masa Ikatan Dinas</label>
        <input type="text" name="pendidikan[beasiswa_parousia][ikatan_dinas]" id="pendidikan[beasiswa_parousia][ikatan_dinas]" class="form-control" value="{{ $sdm->pendidikan['beasiswa_parousia']['ikatan_dinas'] ?? '' }}"disabled>
    </div>
</div>
    </form>
</div>

@endsection
