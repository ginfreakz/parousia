@extends('admin.layout')

@section('content')

<div class="container">
    <h1>Catatan SDM</h1>
    <form action="{{ route('catatan-sdm.store') }}" method="POST" id="catatanSdmForm">
        @csrf
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="name">Nama SDM</label>
                <select name="name" id="name" class="form-control">
                @foreach($sdm as $s)
                    <option value="{{$s->id}}">{{$s->nama_panggilan}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="kategori_sdm">Pilih kategori</label>
                <select name="kategori_sdm" id="kategori_sdm" class="form-control" onchange="renderInputs()">
                    <option value="" disabled selected>Pilih</option>
                    <option value="1">Pelayanan</option>
                    <option value="2">Kesehatan</option>
                    <option value="3">Lain - lain</option>
                </select>
            </div>
        </div>

        <div id="dynamicInputs">
            <!-- Dynamic inputs will be injected here -->
        </div>

        <div class="form-group mt-3">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </form>
</div>

<script>
    function renderInputs() {
        const category = document.getElementById('kategori_sdm').value;
        const dynamicInputs = document.getElementById('dynamicInputs');
        dynamicInputs.innerHTML = ''; // Clear previous inputs

        if (category === '1') {
            // Pelayanan: from time, to time, and text input
            dynamicInputs.innerHTML = `
                <div class="form-group">
                    <label for="tanggal">Tanggal</label>
                    <input type="date" name="tanggal" id="tanggal" class="form-control">
                </div>
                <div class="form-group">
                    <label for="catatan">Uraian Catatan</label>
                    <textarea name="catatan" id="catatan" rows="4" class="form-control"></textarea>
                </div>
            `;
        } else if (category === '2') {
            // Kesehatan: text input and date
            dynamicInputs.innerHTML = `
                <div class="form-group">
                    <label for="tanggal">Tanggal sakit</label>
                    <input type="date" name="tanggal" id="tanggal" class="form-control">
                </div>
                <div class="form-group">
                    <label for="catatan">Uraian Catatan</label>
                    <textarea name="catatan" id="catatan" rows="4" class="form-control"></textarea>
                </div>
            `;
        } else if (category === '3') {
            // Lain - lain: date, text input, and text area
            dynamicInputs.innerHTML = `
                <div class="form-group">
                    <label for="tanggal">Tanggal</label>
                    <input type="date" name="tanggal" id="tanggal" class="form-control">
                </div>
                <div class="form-group">
                    <label for="catatan">Uraian Catatan</label>
                    <textarea name="catatan" id="catatan" rows="4" class="form-control"></textarea>
                </div>
            `;
        }
    }
</script>

@endsection
