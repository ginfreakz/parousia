@extends('admin.layout')

@section('content')
<h1 class="h3 mb-4 text-gray-800">Welcome, {{$user->name}}</h1>

@if($user->role_id == 2)
    @php
    $sectionClass = '';
        $motivationalTexts = [];

        if ($jml_contact >= $target->target_max) {
            $sectionClass = 'bg-success text-white'; // Green
            $motivationalTexts = [
                "Luar biasa! Kerja kerasmu telah membuahkan hasil. Langit bersukacita atas setiap jiwa yang diselamatkan. Teruslah setia dalam pelayanan!",
                "Tuhan telah memakai hidupmu dengan luar biasa. Jangan berhenti di sini—ada lebih banyak lagi yang bisa kamu capai untuk kemuliaan-Nya.",
                "Selamat! Setiap jiwa yang kamu jangkau adalah sukacita besar di surga. Teruslah melangkah dengan iman dan semangat yang sama!"
            ];
        } elseif ($jml_contact >= $target->target_normal && $jml_contact <= $target->target_max) {
            $sectionClass = 'bg-warning text-dark'; // Yellow
            $motivationalTexts = [
                "Kamu sudah berada di jalan yang benar! Jangan berhenti sekarang. Teruslah menjangkau dan menjadi terang bagi dunia.",
                "Perjalanan ini penuh tantangan, tetapi kamu telah membuat kemajuan besar. Tuhan memberkati setiap langkah kecil yang kamu ambil!",
                "Setiap jiwa yang kamu dekati adalah langkah menuju kemenangan rohani. Tetaplah setia dan penuh kasih dalam pelayananmu."
            ];
        } else {
            $sectionClass = 'bg-danger text-white'; // Red
            $motivationalTexts = [
                "Tetaplah setia dalam pelayanan! Tuhan melihat setiap usaha kecilmu. Jangan lelah menabur, karena pada waktunya kamu akan menuai.",
                "Ingatlah bahwa panggilanmu adalah untuk taat, bukan hanya pada hasil. Terus lakukan bagianmu, dan biarkan Tuhan yang menumbuhkan.",
                "Jangan patah semangat! Setiap jiwa yang kamu sentuh adalah kemenangan bagi Kerajaan Allah. Maju terus dalam iman dan kasih!"
            ];
        }

        $randomMotivationalText = $motivationalTexts[array_rand($motivationalTexts)];
    @endphp

    <div class="card {{ $sectionClass }} mb-4">
        <div class="card-body">
        <h5 class="card-title">Penjangkauan Jiwa Bulan Ini</h5>
        <p class="card-text">Jumlah jiwa yang telah Anda jangkau bulan ini adalah <strong>{{$jml_contact}}</strong>.</p>
            <p class="card-text"><i>{{ $randomMotivationalText }}</i></p>
        </div>
    </div>
@endif

@if(in_array($user->role_id, [3, 4, 5]))
    <div class="card bg-info text-white mb-4">
        <div class="card-body">
            <h5 class="card-title">Ulang Tahun Hari Ini</h5>
            @if($birthdayContacts->isEmpty())
                <p class="card-text">Tidak ada yang berulang tahun hari ini.</p>
            @else
                <ul class="list-group">
                    @foreach($birthdayContacts as $contact)
                        <li class="list-group-item bg-info text-white">{{ $contact->name }} - {{ $contact->ulang_tahun }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
@endif
@endsection
