@extends('admin.layout')

@section('content')

<h1 class="h3 mb-4 text-gray-800">Update Catatan SDM</h1>
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Update Catatan SDM</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('catatan-sdm.update', $catatan->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="form-group">
                        <label for="sdm_id">SDM Name</label>
                        <select class="form-control" id="sdm_id" name="sdm_id" required>
                            @foreach($sdm as $s)
                                <option value="{{ $s->id }}" {{ $catatan->sdm_id == $s->id ? 'selected' : '' }}>{{ $s->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="kategori_sdm">Kategori</label>
                        <select class="form-control" id="kategori_sdm" name="kategori_sdm" required>
                            <option value="1" {{ $catatan->kategori_sdm == 1 ? 'selected' : '' }}>Pelayanan</option>
                            <option value="2" {{ $catatan->kategori_sdm == 2 ? 'selected' : '' }}>Kesehatan</option>
                            <option value="3" {{ $catatan->kategori_sdm == 3 ? 'selected' : '' }}>Lain - Lain</option>
                        </select>
                    </div>
                    <div id="dynamicInputs">
                        <!-- Dynamic inputs will be populated here based on the selected category -->
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const kategoriSelect = document.getElementById('kategori_sdm');
        const dynamicInputs = document.getElementById('dynamicInputs');

        function updateDynamicInputs() {
            const category = kategoriSelect.value;
            if (category === '1') {
                dynamicInputs.innerHTML = `
                    <div class="form-group">
                        <label for="tanggal">Tanggal</label>
                        <input type="date" name="tanggal" id="tanggal" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="catatan">Uraian Catatan</label>
                        <textarea name="catatan" id="catatan" rows="4" class="form-control">{{ $catatan->catatan }}</textarea>
                    </div>
                `;
            } else if (category === '2') {
                dynamicInputs.innerHTML = `
                    <div class="form-group">
                        <label for="tanggal">Tanggal sakit</label>
                        <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ $catatan->tanggal }}">
                    </div>
                    <div class="form-group">
                        <label for="catatan">Uraian Catatan</label>
                        <textarea name="catatan" id="catatan" rows="4" class="form-control"></textarea>
                    </div>
                `;
            } else if (category === '3') {
                dynamicInputs.innerHTML = `
                    <div class="form-group">
                        <label for="tanggal">Tanggal</label>
                        <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ $catatan->tanggal }}">
                    </div>
                    <div class="form-group">
                        <label for="catatan">Uraian Catatan</label>
                        <textarea name="catatan" id="catatan" rows="4" class="form-control">{{ $catatan->catatan }}</textarea>
                    </div>
                `;
            }
        }

        kategoriSelect.addEventListener('change', updateDynamicInputs);
        updateDynamicInputs();
    });
</script>

@endsection