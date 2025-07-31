@extends('admin.layout')

@section('content')

<div class="container">
    <h1>SDM</h1>
    <form action="{{ route('create-sdm.store') }}" method="POST" id="sdmForm">
        @csrf
        <!-- Personal Section -->
        <h3>Personal</h3>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="kategori_sdm">Kategori SDM <span class="text-danger">*</label>
                <select name="kategori_sdm" id="kategori_sdm" class="form-control">
                    <option value="" disabled selected>Pilih</option> 
                    @foreach($roles as $role)
                        <option value="{{ $role->id}}-{{ $role->name }}">{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="nama_lengkap">Nama Lengkap <span class="text-danger">*</label>
                <input type="text" name="nama_lengkap" id="nama_lengkap" class="form-control" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="nama_panggilan">Nama Panggilan <span class="text-danger">*</label>
                <input type="text" name="nama_panggilan" id="nama_panggilan" class="form-control" required>
            </div>
            <div class="form-group col-md-6">
                <label for="peran">Peran <span class="text-danger">*</label>
                <select name="peran" id="peran" class="form-control">
                    <option value="" disabled selected>Pilih</option>
                    <option value="Anak">Anak</option>
                    <option value="Kepala keluarga">Kepala keluarga</option>
                    <option value="Ibu rumah tangga">Ibu rumah tangga</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="jenis_identitas">Jenis Identitas <span class="text-danger">*</label>
                <select name="jenis_identitas" id="jenis_identitas" class="form-control">
                    <option value="" disabled selected>Pilih</option>
                    <option value="KTP">KTP</option>
                    <option value="SIM">SIM</option>
                    <option value="Paspor">Paspor</option>
                    <option value="Kartu Pelajar">Kartu Pelajar</option>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="nomor_identitas">Nomor Identitas <span class="text-danger">*</label>
                <input type="text" name="nomor_identitas" id="nomor_identitas" class="form-control" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="tempat_lahir">Tempat Lahir <span class="text-danger">*</label>
                <input type="text" name="tempat_lahir" id="tempat_lahir" class="form-control" required>
            </div>
            <div class="form-group col-md-6">
                <label for="tanggal_lahir">Tanggal Lahir <span class="text-danger">*</label>
                <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control" required>
            </div>
            <div class="form-group col-md-6">
                <label for="jenis_kelamin">Jenis Kelamin <span class="text-danger">*</label>
                <select name="jenis_kelamin" id="jenis_kelamin" class="form-control">
                    <option value="" disabled selected>Pilih</option>
                    <option value="Laki-laki">Laki-laki</option>
                    <option value="Perempuan">Perempuan</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="golongan_darah">Golongan Darah</label>
                <select name="golongan_darah" id="golongan_darah" class="form-control">
                    <option value="" disabled selected>Pilih</option>
                    <option value="O+">O+</option>
                    <option value="O-">O-</option>
                    <option value="A+">A+</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B-">B-</option>
                    <option value="AB+">AB+</option>
                    <option value="AB-">AB-</option>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="suku">Suku <span class="text-danger">*</label>
                <input type="text" name="suku" id="suku" class="form-control" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="status_pernikahan">Status Pernikahan</label>
                <select name="status_pernikahan" id="status_pernikahan" class="form-control">
                    <option value="" disabled selected>Pilih</option>
                    <option value="Belum Menikah">Belum Menikah</option>
                    <option value="Menikah">Menikah</option>
                    <option value="Cerai">Cerai</option>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="status_sdm">Status SDM</label>
                <select name="status_sdm" id="status_sdm" class="form-control">
                    <option value="" disabled selected>Pilih</option>
                    <option value="Aktif">Aktif</option>
                    <option value="Tidak Aktif">Tidak Aktif</option>
                </select>
            </div>
        </div>


        <!-- Add other fields for Personal -->

        <!-- Rekening Pribadi Section -->
        <h3>Data Rekening Pribadi</h3>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="rekening_pribadi[nama_bank]">Nama Bank</label>
                <input type="text" name="rekening_pribadi[nama_bank]" id="rekening_pribadi[nama_bank]" class="form-control">
            </div>
            <div class="form-group col-md-6">
                <label for="rekening_pribadi[no_rekening]">No Rekening</label>
                <input type="text" name="rekening_pribadi[no_rekening]" id="rekening_pribadi[no_rekening]" class="form-control">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="rekening_pribadi[atas_nama]">Atas Nama</label>
                <input type="text" name="rekening_pribadi[atas_nama]" id="rekening_pribadi[atas_nama]" class="form-control">
            </div>
        </div>

        <!-- Tempat Tinggal Section -->
        <h3>Tempat Tinggal - Sesuai KTP</h3>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="tempat_tinggal_ktp[alamat]">Alamat</label>
                <input type="text" name="tempat_tinggal_ktp[alamat]" id="tempat_tinggal_ktp[alamat]" class="form-control">
            </div>
            <div class="form-group col-md-6">
                <label for="tempat_tinggal_ktp[negara]">Negara</label>
                <input type="text" name="tempat_tinggal_ktp[negara]" id="tempat_tinggal_ktp[negara]" class="form-control">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="tempat_tinggal_ktp[provinsi]">Provinsi</label>
                <input type="text" name="tempat_tinggal_ktp[provinsi]" id="tempat_tinggal_ktp[provinsi]" class="form-control">
            </div>
            <div class="form-group col-md-6">
                <label for="tempat_tinggal_ktp[kota]">Kota</label>
                <input type="text" name="tempat_tinggal_ktp[kota]" id="tempat_tinggal_ktp[kota]" class="form-control">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="tempat_tinggal_ktp[kecamatan]">Kecamatan</label>
                <input type="text" name="tempat_tinggal_ktp[kecamatan]" id="tempat_tinggal_ktp[kecamatan]" class="form-control">
            </div>
            <div class="form-group col-md-6">
                <label for="tempat_tinggal_ktp[kelurahan]">Kelurahan</label>
                <input type="text" name="tempat_tinggal_ktp[kelurahan]" id="tempat_tinggal_ktp[kelurahan]" class="form-control">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="tempat_tinggal_ktp[rt_rw]">RT/RW</label>
                <input type="text" name="tempat_tinggal_ktp[rt_rw]" id="tempat_tinggal_ktp[rt_rw]" class="form-control">
            </div>
        </div>

        <!-- Add other fields for Tempat Tinggal -->

        <h3>Tempat Tinggal - Sekarang</h3>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="tempat_tinggal_sekarang[alamat]">Alamat</label>
                <input type="text" name="tempat_tinggal_sekarang[alamat]" id="tempat_tinggal_sekarang[alamat]" class="form-control">
            </div>
            <div class="form-group col-md-6">
                <label for="tempat_tinggal_sekarang[negara]">Negara</label>
                <input type="text" name="tempat_tinggal_sekarang[negara]" id="tempat_tinggal_sekarang[negara]" class="form-control">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="tempat_tinggal_sekarang[provinsi]">Provinsi</label>
                <input type="text" name="tempat_tinggal_sekarang[provinsi]" id="tempat_tinggal_sekarang[provinsi]" class="form-control">
            </div>
            <div class="form-group col-md-6">
                <label for="tempat_tinggal_sekarang[kota]">Kota</label>
                <input type="text" name="tempat_tinggal_sekarang[kota]" id="tempat_tinggal_sekarang[kota]" class="form-control">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="tempat_tinggal_sekarang[kecamatan]">Kecamatan</label>
                <input type="text" name="tempat_tinggal_sekarang[kecamatan]" id="tempat_tinggal_sekarang[kecamatan]" class="form-control">
            </div>
            <div class="form-group col-md-6">
                <label for="tempat_tinggal_sekarang[kelurahan]">Kelurahan</label>
                <input type="text" name="tempat_tinggal_sekarang[kelurahan]" id="tempat_tinggal_sekarang[kelurahan]" class="form-control">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="tempat_tinggal_sekarang[rt_rw]">RT/RW</label>
                <input type="text" name="tempat_tinggal_sekarang[rt_rw]" id="tempat_tinggal_sekarang[rt_rw]" class="form-control">
            </div>
        </div>

        <h3>Lokasi Pelayanan</h3>
        <div class="form-group">
            <label for="lokasi_pelayanan">Lokasi Pelayanan <span class="text-danger">*</label>
            <select name="lokasi_pelayanan[]" id="lokasi_pelayanan" class="form-control" multiple>
                @foreach($regions as $region)
                    <option value="{{ $region->id }}-{{ $region->kota }}">{{ $region->kota }}</option>
                @endforeach
            </select>
        </div>

        <!-- Kontak & Medsos Section -->
        <h3>Kontak & Medsos</h3>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="kontak_medsos[no_hp1]">No HP 1</label>
                <input type="text" name="kontak_medsos[no_hp1]" id="kontak_medsos[no_hp1]" class="form-control">
            </div>
            <div class="form-group col-md-6">
                <label for="kontak_medsos[no_hp2]">No HP 2</label>
                <input type="text" name="kontak_medsos[no_hp2]" id="kontak_medsos[no_hp2]" class="form-control">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="kontak_medsos[email_1]">Email 1</label>
                <input type="text" name="kontak_medsos[email_1]" id="kontak_medsos[email_1]" class="form-control">
            </div>
            <div class="form-group col-md-6">
                <label for="kontak_medsos[email_2]">Email 2</label>
                <input type="text" name="kontak_medsos[email_2]" id="kontak_medsos[email_2]" class="form-control">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="kontak_medsos[instagram]">Instagram</label>
                <input type="text" name="kontak_medsos[instagram]" id="kontak_medsos[instagram]" class="form-control">
            </div>
            <div class="form-group col-md-6">
                <label for="kontak_medsos[tiktok]">Tiktok</label>
                <input type="text" name="kontak_medsos[tiktok]" id="kontak_medsos[tiktok]" class="form-control">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="kontak_medsos[facebook]">Facebook</label>
                <input type="text" name="kontak_medsos[facebook]" id="kontak_medsos[facebook]" class="form-control">
            </div>
            <div class="form-group col-md-6">
                <label for="kontak_medsos[lain_lain]">Lain-lain</label>
                <input type="text" name="kontak_medsos[lain_lain]" id="kontak_medsos[lain_lain]" class="form-control">
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
        <input type="text" name="pendidikan[tk_paud][nama]" id="pendidikan[tk_paud][nama]" class="form-control">
    </div>
    <div class="form-group col-md-4">
        <label for="pendidikan[tk_paud][tahun_masuk]">Tahun Masuk</label>
        <input type="text" name="pendidikan[tk_paud][tahun_masuk]" id="pendidikan[tk_paud][tahun_masuk]" class="form-control">
    </div>
    <div class="form-group col-md-4">
        <label for="pendidikan[tk_paud][tahun_lulus]">Tahun Lulus</label>
        <input type="text" name="pendidikan[tk_paud][tahun_lulus]" id="pendidikan[tk_paud][tahun_lulus]" class="form-control">
    </div>
</div>

<!-- SD Section -->
<h4>SD</h4>
<div class="form-row">
    <div class="form-group col-md-4">
        <label for="pendidikan[sd][nama]">Nama Sekolah</label>
        <input type="text" name="pendidikan[sd][nama]" id="pendidikan[sd][nama]" class="form-control">
    </div>
    <div class="form-group col-md-4">
        <label for="pendidikan[sd][tahun_masuk]">Tahun Masuk</label>
        <input type="text" name="pendidikan[sd][tahun_masuk]" id="pendidikan[sd][tahun_masuk]" class="form-control">
    </div>
    <div class="form-group col-md-4">
        <label for="pendidikan[sd][tahun_lulus]">Tahun Lulus</label>
        <input type="text" name="pendidikan[sd][tahun_lulus]" id="pendidikan[sd][tahun_lulus]" class="form-control">
    </div>
</div>

<!-- SMP Section -->
<h4>SMP</h4>
<div class="form-row">
    <div class="form-group col-md-4">
        <label for="pendidikan[smp][nama]">Nama Sekolah</label>
        <input type="text" name="pendidikan[smp][nama]" id="pendidikan[smp][nama]" class="form-control">
    </div>
    <div class="form-group col-md-4">
        <label for="pendidikan[smp][tahun_masuk]">Tahun Masuk</label>
        <input type="text" name="pendidikan[smp][tahun_masuk]" id="pendidikan[smp][tahun_masuk]" class="form-control">
    </div>
    <div class="form-group col-md-4">
        <label for="pendidikan[smp][tahun_lulus]">Tahun Lulus</label>
        <input type="text" name="pendidikan[smp][tahun_lulus]" id="pendidikan[smp][tahun_lulus]" class="form-control">
    </div>
</div>

<!-- SMA Section -->
<h4>SMA</h4>
<div class="form-row">
    <div class="form-group col-md-4">
        <label for="pendidikan[sma][nama]">Nama Sekolah</label>
        <input type="text" name="pendidikan[sma][nama]" id="pendidikan[sma][nama]" class="form-control">
    </div>
    <div class="form-group col-md-4">
        <label for="pendidikan[sma][tahun_masuk]">Tahun Masuk</label>
        <input type="text" name="pendidikan[sma][tahun_masuk]" id="pendidikan[sma][tahun_masuk]" class="form-control">
    </div>
    <div class="form-group col-md-4">
        <label for="pendidikan[sma][tahun_lulus]">Tahun Lulus</label>
        <input type="text" name="pendidikan[sma][tahun_lulus]" id="pendidikan[sma][tahun_lulus]" class="form-control">
    </div>
</div>

<!-- Pendidikan Tinggi Section -->
<h4>Pendidikan Tinggi</h4>
@for ($i = 1; $i <= 3; $i++)
    <h5>Pendidikan Tinggi {{ $i }}</h5>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="pendidikan[tinggi_{{ $i }}][universitas]">Universitas / STT</label>
            <input type="text" name="pendidikan[tinggi_{{ $i }}][universitas]" id="pendidikan[tinggi_{{ $i }}][universitas]" class="form-control">
        </div>
        <div class="form-group col-md-6">
            <label for="pendidikan[tinggi_{{ $i }}][program]">Program</label>
            <select name="pendidikan[tinggi_{{ $i }}][program]" id="pendidikan[tinggi_{{ $i }}][program]" class="form-control">
                <option value="" disabled selected>Pilih</option>
                <option value="S1">S1</option>
                <option value="S2">S2</option>
                <option value="S3">S3</option>
            </select>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="pendidikan[tinggi_{{ $i }}][fakultas]">Fakultas</label>
            <input type="text" name="pendidikan[tinggi_{{ $i }}][fakultas]" id="pendidikan[tinggi_{{ $i }}][fakultas]" class="form-control">
        </div>
        <div class="form-group col-md-6">
            <label for="pendidikan[tinggi_{{ $i }}][jurusan]">Jurusan</label>
            <input type="text" name="pendidikan[tinggi_{{ $i }}][jurusan]" id="pendidikan[tinggi_{{ $i }}][jurusan]" class="form-control">
        </div>
    </div>
@endfor

<!-- Beasiswa Section -->
<h4>Beasiswa Parousia</h4>
<div class="form-row">
    <div class="form-group col-md-6">
        <label for="pendidikan[beasiswa_parousia][status]">Beasiswa Parousia</label>
        <select name="pendidikan[beasiswa_parousia][status]" id="pendidikan[beasiswa_parousia][status]" class="form-control" onchange="toggleBeasiswaFields()">
            <option value="" disabled selected>Pilih</option>
            <option value="Ya">Ya</option>
            <option value="Tidak">Tidak</option>
        </select>
    </div>
</div>
<div id="beasiswa_fields" style="display: none;">
    <div class="form-group">
        <label for="pendidikan[beasiswa_parousia][periode_waktu]">Periode Waktu</label>
        <input type="text" name="pendidikan[beasiswa_parousia][periode_waktu]" id="pendidikan[beasiswa_parousia][periode_waktu]" class="form-control">
    </div>
    <div class="form-group">
        <label for="pendidikan[beasiswa_parousia][ikatan_dinas]">Masa Ikatan Dinas</label>
        <input type="text" name="pendidikan[beasiswa_parousia][ikatan_dinas]" id="pendidikan[beasiswa_parousia][ikatan_dinas]" class="form-control">
    </div>
</div>

<script>
    function toggleBeasiswaFields() {
        const status = document.getElementById('pendidikan[beasiswa_parousia][status]').value;
        document.getElementById('beasiswa_fields').style.display = (status === 'Ya') ? 'block' : 'none';
    }
</script>


<h4>Register User</h4>
<div class="form-row">
    <div class="form-group col-md-6">
        <label for="username">Username <span class="text-danger">*</label>
        <input type="username" name="username" id="username" class="form-control" required>
    </div>
    <div class="form-group col-md-6">
        <label for="password">Password <span class="text-danger">*</label>
        <input type="password" name="password" id="password" class="form-control" required>
    </div>
</div>
<div class="form-row">
    <div class="form-group col-md-6">
        <label for="password_confirmation">Confirm Password <span class="text-danger">*</label>
        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
    </div>
    <div class="form-group col-md-6">
        <label for="lembaga_id">Lembaga <span class="text-danger">*</label>
        <select name="lembaga_id" id="lembaga_id" class="form-control">
            <option value="" disabled selected>Pilih</option>
            @foreach($lembagas as $lembaga)
                <option value="{{ $lembaga->id }}">{{ $lembaga->name }}</option>
            @endforeach
        </select>
    </div>
</div>

        <!-- Add other fields for Pendidikan -->

        <div class="form-group mt-3">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
        </form>
    </div>
    <script>
    document.getElementById('sdmForm').addEventListener('submit', function(e) {
        // Array of dropdown fields to validate
        const dropdownFields = ['kategori_sdm', 'peran', 'jenis_identitas', 'lokasi_pelayanan', 'jenis_kelamin'];

        // Flag to track validation
        let isValid = true;

        // Loop through each dropdown field
        dropdownFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field.value === "") {
                // Highlight invalid fields
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                // Remove validation highlights if valid
                field.classList.remove('is-invalid');
            }
        });

        if (!isValid) {
            // Prevent form submission if validation fails
            e.preventDefault();
            alert('Please make a selection for all required dropdown fields.');
        }
    });

    // Remove validation error on user interaction
    document.querySelectorAll('select').forEach(select => {
        select.addEventListener('change', function() {
            this.classList.remove('is-invalid');
        });
    });
</script>

<style>
    .is-invalid {
        border-color: red;
    }
</style>
@endsection
