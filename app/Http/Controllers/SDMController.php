<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User; // Ensure User model is imported
use App\Models\Region; // Ensure Region model is imported
use Illuminate\Support\Facades\Auth;


class SDMController extends Controller
{
    public function index()
    {
        // Fetch roles using raw SQL
        $SQL = "SELECT * FROM roles";
        $roles = DB::select($SQL);
    
        // Fetch all lembaga records
        $lembagas = DB::table('lembaga')->get();
    
        // Fetch unique categories & peran for dropdown
        $kategori_sdm = DB::table('sdm')->distinct()->pluck('kategori_sdm')->filter();
        $peran_sdm = DB::table('sdm')->distinct()->pluck('peran')->filter();

        $regions = Region::all();
    
        // Return view with data
        return view('admin.create-sdm', [
            'roles' => $roles,
            'lembagas' => $lembagas,
            'kategori_sdm' => $kategori_sdm,
            'peran_sdm' => $peran_sdm,
            'regions' => $regions,
        ]);
    }

    public function store(Request $request)
{

    $kategori_sdm = $request->input('kategori_sdm');
    $kategori_sdm_parts = explode('-', $kategori_sdm);
    $kategori_sdm_id = $kategori_sdm_parts[0];
    $kategori_sdm_nama = $kategori_sdm_parts[1];

    $regions = $request->input('lokasi_pelayanan'); // Get selected regions (array)

    // Extract region IDs and names separately
    $region_ids = [];
    $region_namas = [];

    foreach ($regions as $region) {
        $region_parts = explode('-', $region);
        $region_ids[] = $region_parts[0];   // Store only the ID
        $region_namas[] = $region_parts[1]; // Store only the name
    }

    
    $validatedData = $request->validate([
        'username' => 'required|string|unique:users',
        'password' => 'required|string|min:8',
        'kategori_sdm' => 'required|string',
        'nama_lengkap' => 'required|string|max:255|unique:sdm,nama_lengkap',
        'nama_panggilan' => 'nullable|string',
        'peran' => 'nullable|string',
        'jenis_identitas' => 'nullable|string',
        'nomor_identitas' => 'nullable|string',
        'tempat_lahir' => 'nullable|string',
        'tanggal_lahir' => 'nullable|date',
        'jenis_kelamin' => 'nullable|string',
        'golongan_darah' => 'nullable|string',
        'suku' => 'nullable|string',
        'status_pernikahan' => 'nullable|string',
        'status_sdm' => 'nullable|string',
        'rekening_pribadi.nama_bank' => 'nullable|string',
        'rekening_pribadi.no_rekening' => 'nullable|string',
        'rekening_pribadi.atas_nama' => 'nullable|string',
        'tempat_tinggal_ktp.alamat' => 'nullable|string',
        'tempat_tinggal_ktp.negara' => 'nullable|string',
        'tempat_tinggal_ktp.provinsi' => 'nullable|string',
        'tempat_tinggal_ktp.kota' => 'nullable|string',
        'tempat_tinggal_ktp.kecamatan' => 'nullable|string',
        'tempat_tinggal_ktp.kelurahan' => 'nullable|string',
        'tempat_tinggal_ktp.rt_rw' => 'nullable|string',
        'tempat_tinggal_sekarang.alamat' => 'nullable|string',
        'tempat_tinggal_sekarang.negara' => 'nullable|string',
        'tempat_tinggal_sekarang.provinsi' => 'nullable|string',
        'tempat_tinggal_sekarang.kota' => 'nullable|string',
        'tempat_tinggal_sekarang.kecamatan' => 'nullable|string',
        'tempat_tinggal_sekarang.kelurahan' => 'nullable|string',
        'tempat_tinggal_sekarang.rt_rw' => 'nullable|string',
        'kontak_medsos.no_hp1' => 'nullable|string',
        'kontak_medsos.no_hp2' => 'nullable|string',
        'kontak_medsos.email_1' => 'nullable|string',
        'kontak_medsos.email_2' => 'nullable|string',
        'kontak_medsos.instagram' => 'nullable|string',
        'kontak_medsos.tiktok' => 'nullable|string',
        'kontak_medsos.facebook' => 'nullable|string',
        'kontak_medsos.lain_lain' => 'nullable|string',
        'pendidikan.tk_paud.nama' => 'nullable|string',
        'pendidikan.tk_paud.tahun_masuk' => 'nullable|string',
        'pendidikan.tk_paud.tahun_lulus' => 'nullable|string',
        'pendidikan.sd.nama' => 'nullable|string',
        'pendidikan.sd.tahun_masuk' => 'nullable|string',
        'pendidikan.sd.tahun_lulus' => 'nullable|string',
        'pendidikan.smp.nama' => 'nullable|string',
        'pendidikan.smp.tahun_masuk' => 'nullable|string',
        'pendidikan.smp.tahun_lulus' => 'nullable|string',
        'pendidikan.sma.nama' => 'nullable|string',
        'pendidikan.sma.tahun_masuk' => 'nullable|string',
        'pendidikan.sma.tahun_lulus' => 'nullable|string',
        'pendidikan.tinggi_1.universitas' => 'nullable|string',
        'pendidikan.tinggi_1.program' => 'nullable|in:S1,S2,S3',
        'pendidikan.tinggi_1.fakultas' => 'nullable|string',
        'pendidikan.tinggi_1.jurusan' => 'nullable|string',
        'pendidikan.tinggi_2.universitas' => 'nullable|string',
        'pendidikan.tinggi_2.program' => 'nullable|in:S1,S2,S3',
        'pendidikan.tinggi_2.fakultas' => 'nullable|string',
        'pendidikan.tinggi_2.jurusan' => 'nullable|string',
        'pendidikan.tinggi_3.universitas' => 'nullable|string',
        'pendidikan.tinggi_3.program' => 'nullable|in:S1,S2,S3',
        'pendidikan.tinggi_3.fakultas' => 'nullable|string',
        'pendidikan.tinggi_3.jurusan' => 'nullable|string',
        'pendidikan.beasiswa_parousia.status' => 'nullable|in:Ya,Tidak',
        'pendidikan.beasiswa_parousia.periode_waktu' => 'nullable|string',
        'pendidikan.beasiswa_parousia.ikatan_dinas' => 'nullable|string',
    ]);
    
    DB::transaction(function () use ($validatedData, $kategori_sdm_id, $request, $kategori_sdm_nama , $region_ids , $region_namas) {
        // Create or update the user
        $user = User::create([
            'username' => $validatedData['username'],
            'password' => Hash::make($validatedData['password']),
            'name' => $validatedData['nama_lengkap'],
            'role_id' => $kategori_sdm_id,
            'lembaga_id' => $request->input('lembaga_id'),
            'region_id' => json_encode($region_ids)

        ]);
        $data = [
            'user_id' => $user->id,
            'kategori_sdm' => $kategori_sdm_nama,
            'nama_lengkap' => $validatedData['nama_lengkap'],
            'nama_panggilan' => $validatedData['nama_panggilan'] ?? null,
            'peran' => $validatedData['peran'] ?? null,
            'jenis_identitas' => $validatedData['jenis_identitas'] ?? null,
            'nomor_identitas' => $validatedData['nomor_identitas'] ?? null,
            'golongan_darah' => $validatedData['golongan_darah'] ?? null,
            'suku' => $validatedData['suku'] ?? null,
            'status_pernikahan' => $validatedData['status_pernikahan'] ?? null,
            'status_sdm' => $validatedData['status_sdm'] ?? 'Tidak Aktif',
            'tempat_lahir' => $validatedData['tempat_lahir'] ?? null,
            'tanggal_lahir' => $validatedData['tanggal_lahir'] ?? null,
            'jenis_kelamin' => $validatedData['jenis_kelamin'] ?? null,
            'rekening_pribadi' => json_encode($validatedData['rekening_pribadi'] ?? []),
            'tempat_tinggal_ktp' => json_encode($validatedData['tempat_tinggal_ktp'] ?? []),
            'tempat_tinggal_sekarang' => json_encode($validatedData['tempat_tinggal_sekarang'] ?? []),
            'lokasi_pelayanan' => json_encode($region_namas),
            'kontak_medsos' => json_encode($validatedData['kontak_medsos'] ?? []),
            'pendidikan' => json_encode($validatedData['pendidikan'] ?? []),
            'created_at' => now(),
            'created_by' => auth()->id(),
            'picture_path' => 'default.jpg',
            'lembaga_id' => $request->input('lembaga_id'),
            #'updated_at' => now(),
        ];
    
        // Insert data into the database
        DB::table('sdm')->insert($data);
    });

   

    return redirect()->route('show-sdm.index')->with('success', 'Form submitted successfully!');
}

    public function viewCatatanSDM(){
        return view('sdm.catatan-sdm');
    }

    public function storeCatatanSDM(Request $request){
        $validatedData = $request->validate([
            'sdm_id' => 'required|integer',
            'catatan' => 'required|string',
        ]);

        $data = [
            'sdm_id' => $validatedData['sdm_id'],
            'catatan' => $validatedData['catatan'],
            'created_at' => now(),
        ];

        // Insert data into the database
        DB::table('catatan_sdm')->insert($data);

        return redirect()->route('catatan-sdm.index')->with('success', 'Catatan SDM submitted successfully!');
    }
    public function show()
    {
        $user = Auth::user();
        $lembagaId = $user->lembaga_id;
        $roleId = $user->role_id;
        $userRegionIds = json_decode($user->region_id, true);

        $query = DB::table('sdm')
        ->join('lembaga', 'sdm.lembaga_id', '=', 'lembaga.id')
        ->leftJoin('regions', function ($join) {
            $join->on('regions.kota', '=', DB::raw("ANY(string_to_array(sdm.lokasi_pelayanan::TEXT, ','))"));
        })        
        ->select('sdm.*', 'lembaga.name as lembaga_name', 'regions.id as region_id', 'regions.kota as region_name');

        // Apply filters based on user role and organization
        if (in_array($roleId, [5, 7])) {
            if ($lembagaId != 1) {
                $query->where('sdm.lembaga_id', $lembagaId);
            }
        }
        else if ($roleId == 3) {
            $query->whereRaw('?::jsonb @> to_jsonb(regions.id)', [json_encode($userRegionIds)]);
        }
        else {
            $query->where('sdm.user_id', $user->id);
        }

        $sdm = $query->get();

        foreach ($sdm as $item) {
            $item->pendidikan = json_decode($item->pendidikan, true);
        }

        return view('show-sdm', ['sdm' => $sdm])->with('roleId', $roleId);
    }
    public function view($id)
    {
        $sdm = DB::table('sdm')->where('id', $id)->first();
        
        if ($sdm) {
            $jsonFields = ['rekening_pribadi', 'kontak_medsos', 'pendidikan', 'tempat_tinggal_ktp', 'tempat_tinggal_sekarang'];
            foreach ($jsonFields as $field) {
                $sdm->{$field} = json_decode($sdm->{$field}, true); // Convert to array
            }
        }

        $SQL = "SELECT * FROM roles";
        $roles = DB::select($SQL);
        $lembagas = DB::table('lembaga')->get();

        $regions = Region::all();

        $selectedRegions = json_decode($sdm->lokasi_pelayanan, true);

        return view('admin.view-sdm', compact('sdm', 'regions', 'selectedRegions'))->with(['roles' => $roles, 'lembagas' => $lembagas]);
    }
    public function edit($id)
    {
        $sdm = DB::table('sdm')->where('id', $id)->first();
        
        if ($sdm) {
            $jsonFields = ['rekening_pribadi', 'kontak_medsos', 'pendidikan', 'tempat_tinggal_ktp', 'tempat_tinggal_sekarang'];
            foreach ($jsonFields as $field) {
                $sdm->{$field} = json_decode($sdm->{$field}, true); // Convert to array
            }
        }
        $SQL = "SELECT * FROM roles";
        $roles = DB::select($SQL);
        $lembagas = DB::table('lembaga')->get();

        $regions = Region::all();

        $selectedRegions = json_decode($sdm->lokasi_pelayanan, true); // Decode the JSON string into an array

        return view('edit-sdm', compact('sdm', 'regions', 'selectedRegions'))->with(['roles' => $roles, 'lembagas' => $lembagas]); // View for the form
 
    }
    public function update($id, Request $request)
    {


        $region_ids = [];
        $region_namas = [];

        if ($request->has('lokasi_pelayanan')) {
            foreach ($request->input('lokasi_pelayanan') as $region) {
                $region_parts = explode('-', $region);
                if (count($region_parts) === 2) {
                    $region_ids[] = $region_parts[0];   // Store only the ID
                    $region_namas[] = $region_parts[1]; // Store only the name
                }
            }
        }

        // Store in JSON format
        
        //$validatedData['region_id'] = json_encode($region_ids);        

        $role = $request->input('kategori_sdm');
        $role_parts = explode('-', $role);

        if (count($role_parts) < 2) {
            $role_id = DB::table('users')
            ->join('sdm', 'sdm.user_id', '=', 'users.id')
            ->where('sdm.id', $id)
            ->value('users.role_id');
            $role_nama = DB::table('roles')->where('id', $role_id)->value('name');
        }
        else {
            $role_id = $role_parts[0];
            $role_nama = $role_parts[1];
        }


        $validatedData = $request->validate([
            'nama_lengkap' => 'required|string|max:255|unique:sdm,nama_lengkap,' . $id,
            'nama_panggilan' => 'nullable|string',
            'peran' => 'nullable|string',
            'jenis_identitas' => 'nullable|string',
            'nomor_identitas' => 'nullable|string',
            'tempat_lahir' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|string',
            'golongan_darah' => 'nullable|string',
            'suku' => 'nullable|string',
            'status_sdm' => 'nullable|string',
            'rekening_pribadi.nama_bank' => 'nullable|string',
            'rekening_pribadi.no_rekening' => 'nullable|string',
            'rekening_pribadi.atas_nama' => 'nullable|string',
            'tempat_tinggal_ktp.alamat' => 'nullable|string',
            'tempat_tinggal_ktp.negara' => 'nullable|string',
            'tempat_tinggal_ktp.provinsi' => 'nullable|string',
            'tempat_tinggal_ktp.kota' => 'nullable|string',
            'tempat_tinggal_ktp.kecamatan' => 'nullable|string',
            'tempat_tinggal_ktp.kelurahan' => 'nullable|string',
            'tempat_tinggal_ktp.rt_rw' => 'nullable|string',
            'tempat_tinggal_sekarang.alamat' => 'nullable|string',
            'tempat_tinggal_sekarang.negara' => 'nullable|string',
            'tempat_tinggal_sekarang.provinsi' => 'nullable|string',
            'tempat_tinggal_sekarang.kota' => 'nullable|string',
            'tempat_tinggal_sekarang.kecamatan' => 'nullable|string',
            'tempat_tinggal_sekarang.kelurahan' => 'nullable|string',
            'tempat_tinggal_sekarang.rt_rw' => 'nullable|string',
            'lokasi_pelayanan' => 'nullable',
            'kontak_medsos.no_hp1' => 'nullable|string',
            'kontak_medsos.no_hp2' => 'nullable|string',
            'kontak_medsos.email_1' => 'nullable|string',
            'kontak_medsos.email_2' => 'nullable|string',
            'kontak_medsos.instagram' => 'nullable|string',
            'kontak_medsos.tiktok' => 'nullable|string',
            'kontak_medsos.facebook' => 'nullable|string',
            'kontak_medsos.lain_lain' => 'nullable|string',
            'pendidikan.tk_paud.nama' => 'nullable|string',
            'pendidikan.tk_paud.tahun_masuk' => 'nullable|string',
            'pendidikan.tk_paud.tahun_lulus' => 'nullable|string',
            'pendidikan.sd.nama' => 'nullable|string',
            'pendidikan.sd.tahun_masuk' => 'nullable|string',
            'pendidikan.sd.tahun_lulus' => 'nullable|string',
            'pendidikan.smp.nama' => 'nullable|string',
            'pendidikan.smp.tahun_masuk' => 'nullable|string',
            'pendidikan.smp.tahun_lulus' => 'nullable|string',
            'pendidikan.sma.nama' => 'nullable|string',
            'pendidikan.sma.tahun_masuk' => 'nullable|string',
            'pendidikan.sma.tahun_lulus' => 'nullable|string',
            'pendidikan.tinggi_1.universitas' => 'nullable|string',
            'pendidikan.tinggi_1.program' => 'nullable|in:S1,S2,S3',
            'pendidikan.tinggi_1.fakultas' => 'nullable|string',
            'pendidikan.tinggi_1.jurusan' => 'nullable|string',
            'pendidikan.tinggi_2.universitas' => 'nullable|string',
            'pendidikan.tinggi_2.program' => 'nullable|in:S1,S2,S3',
            'pendidikan.tinggi_2.fakultas' => 'nullable|string',
            'pendidikan.tinggi_2.jurusan' => 'nullable|string',
            'pendidikan.tinggi_3.universitas' => 'nullable|string',
            'pendidikan.tinggi_3.program' => 'nullable|in:S1,S2,S3',
            'pendidikan.tinggi_3.fakultas' => 'nullable|string',
            'pendidikan.tinggi_3.jurusan' => 'nullable|string',
            'pendidikan.beasiswa_parousia.status' => 'nullable|in:Ya,Tidak',
            'pendidikan.beasiswa_parousia.periode_waktu' => 'nullable|string',
            'pendidikan.beasiswa_parousia.ikatan_dinas' => 'nullable|string',
            'lembaga_id' => 'nullable|integer',
        ]);
        $validatedData['lokasi_pelayanan'] = json_encode($region_namas);

        // Update user data in the `sdm` table
        $validatedData['kategori_sdm'] = $role_nama;
        DB::table('sdm')->where('id', $id)->update($validatedData);

        $user_id = DB::table('sdm')->where('id', $id)->value('user_id');
        DB::table('users')->where('id', $user_id)->update([
            'region_id' => json_encode($region_ids),
            'role_id' => $role_id
        ]);
        
        // Redirect back with a success message
        return redirect()->route('show-sdm.index', $id)->with('success', 'Data updated successfully!');


    }
    public function destroy($id){

        // Retrieve the user_id associated with the sdm entry
        $userId = DB::table('sdm')->where('id', $id)->value('user_id');

        // Delete the sdm entry
        DB::table('sdm')->where('id', $id)->delete();

        // Delete the user entry
        if ($userId) {
            DB::table('users')->where('id', $userId)->delete();
        }
        return redirect()->route('show-sdm.index')->with('success', 'Data deleted successfully!');
    }

    public function filter(Request $request)
    {
        $query = DB::table('sdm')
            ->join('lembaga', 'sdm.lembaga_id', '=', 'lembaga.id')
            ->select('sdm.*', 'lembaga.name as lembaga_name');
            

        // Apply filters dynamically
        if ($request->filled('name')) {
            $query->where('nama_panggilan', 'like', '%' . $request->name . '%');
        }
        if ($request->filled('kategori')) {
            $query->where('kategori_sdm', $request->kategori);
        }
        if ($request->filled('lembaga')) {
            $query->where('lembaga.name', $request->lembaga);
        }
        if ($request->filled('peran')) {
            $query->where('peran', 'like', '%' . $request->peran . '%');
        }
        if ($request->filled('status')) {
            $query->where('status_sdm', $request->status);
        }
        if ($request->filled('lokasi')) {
            $query->where('lokasi_pelayanan', 'like', '%' . $request->lokasi . '%');
        }

        // Get filtered results
        $sdm = $query->get();
        foreach ($sdm as $s) {
            $s->pendidikan = json_decode($s->pendidikan, true);
        }

        if ($request->has('beasiswa') && $request->beasiswa != '') {
            $sdm = $sdm->filter(function ($s) use ($request) {
                return isset($s->pendidikan['beasiswa_parousia']['status']) &&
                       $s->pendidikan['beasiswa_parousia']['status'] === $request->beasiswa;
            })->values();
        }

        return response()->json(['sdm' => $sdm]);
    }

}
