@extends('admin.layout')

@section('content')

<h1 class="h3 mb-4 text-gray-800">Update Contact</h1>
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Update Contact</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('contact.update', $contact->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="form-group">
                        <label for="status">Status</label>
                        <div class="custom-control custom-switch custom-switch-lg">
                            <input type="checkbox" class="custom-control-input" id="status" name="status" value="1"
                                {{ $contact->status == 1 ? 'checked' : '' }}>
                            <label class="custom-control-label" for="status"></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ $contact->name }}"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Utusan</label>
                        <select class="form-control" id="utusan" name="utusan" required>
                            <option value="" disabled selected>Pilih Utusan</option>
                            @foreach($listUtusan as $utusan)
                                <option value="{{ $utusan->utusan }}">
                                    {{ $utusan->utusan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="suku">Suku</label>
                        <input type="text" class="form-control" id="suku" name="suku" value="{{ $contact->suku }}">
                    </div>
                    <div class="form-group">
                        <label for="usia">Usia</label>
                        <input type="text" class="form-control" id="usia" name="usia" value="{{ $contact->usia }}">
                    </div>
                    <div class="form-group">
                        <label for="kelamin">Kelamin</label>
                        <select class="form-control" id="kelamin" name="kelamin" required>
                            <option value="Laki-laki"
                                {{ $contact->kelamin == 'Laki-laki' ? 'selected' : '' }}>
                                Laki-laki</option>
                            <option value="Perempuan"
                                {{ $contact->kelamin == 'Perempuan' ? 'selected' : '' }}>
                                Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="{{ $contact->phone }}">
                    </div>
                    <div class="form-group">
                        <label for="region">Region</label>
                        <select class="form-control" id="region" name="region_id" required>
                            @foreach($regions as $region)
                                <option value="{{ $region->id }}-{{ $region->kota }}">{{ $region->kota }},
                                    {{ $region->provinsi }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="pekerjaan">Pekerjaan</label>
                        <input type="text" class="form-control" id="pekerjaan" name="pekerjaan"
                            value="{{ $contact->pekerjaan }}">
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"
                            maxlength="80">{{ old('description', $contact->description) }}</textarea>
                        <blade
                            error|(%26%2339%3Bdescription%26%2339%3B)%20%3Csmall%20class%3D%26%2334%3Btext-danger%26%2334%3B%3E%7B%7B%2524message%7D%7D%3C%2Fsmall%3E%20%40enderror%0D>
                            <small id="desc-error" class="text-danger"></small>
                    </div>

                    <button type="submit" class="btn btn-primary">Edit</button>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        var statusCheckbox = document.getElementById('status');
        statusCheckbox.addEventListener('change', function () {
            if (statusCheckbox.checked) {
                statusCheckbox.value = 1; // Active
            } else {
                statusCheckbox.value = 0; // Inactive
            }
        });
    });

</script>

@endsection
