<?php
// filepath: /d:/Parousia Ministry/parousia_ministry/app/Http/Controllers/Auth/RegisterController.php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB; // Import the DB facade

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm()
    {
        $lembagas = DB::select('SELECT * FROM lembaga'); // Fetch all lembagas using raw SQL query
        return view('auth.register', ['lembaga' => $lembagas]);
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'lembaga_id' => ['required'],
        ]);
    }

    protected function create(array $data)
{
    // Create the user first
    $newUser = User::create([
        'name' => $data['name'],
        'username' => $data['username'],
        'password' => Hash::make($data['password']),
        'lembaga_id' => $data['lembaga_id'],
    ]);

    // Then create training progress
    DB::table('trainings')->insert([
        'user_id' => $newUser->id,
        'current_page' => 1,
        'completed_pages' => json_encode([])
    ]);

    return $newUser; // Return AFTER both operations
}
}