<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;

        $users = User::orderBy('id')
            ->when($search, function ($q, $search) {
                return $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            })
            ->paginate();

        if($search) $users->appends(['search'=>$search]);
        return view('user.index', [
            'users' => $users
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('user.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Tentukan pesan validasi kustom
        $messages = [
            'nama.regex' => 'Kolom nama hanya dapat diisi dengan huruf.',
        ];
    
        // Validasi input dengan aturan yang sudah ditetapkan dan pesan kustom
        $validator = Validator::make($request->all(), [
            'nama' => ['required', 'max:100', 'regex:/^[a-zA-Z\s]+$/'],
            'username' => ['required', 'max:100', 'unique:users'],
            'role' => ['required', 'in:admin,petugas'],
            'password' => ['required', 'max:100', 'confirmed'],
        ], $messages);
    
        // Jika validasi gagal, kembali ke halaman sebelumnya dengan pesan kesalahan
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    
        // Jika validasi berhasil, simpan data

        $request->validate([
            'nama'=>['required','max:100',],
            'username'=>['required','max:100','unique:users'],
            'role'=>['required','in:admin,petugas'],
            'password'=>['required','max:100','confirmed']
        ]);

        $request->merge([
            'password'=>bcrypt($request->password)
        ]);

        User::create($request->all());

        return redirect()->route('user.index')->with('store','success');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('user.edit',[
            'user'=>$user
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'nama'=>['required','max:100','regex:/^[a-zA-Z\s]+$/'],
            'username'=>['required','max:100','unique:users,username,'.$user->id],
            'role'=>['required','in:admin,petugas'],
            'password_baru'=>['nullable','max:100','confirmed']
        ]);

        if($request->password_baru){
            $request->merge([
                'password'=>bcrypt($request->password_baru)
            ]);
            $user->update($request->all());
        } else {
            $user->update($request->only('nama','username','role'));
        }

        return redirect()->route('user.index')->with('update','success');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return back()->with('destroy','success');

    }
}
