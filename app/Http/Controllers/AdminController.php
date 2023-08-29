<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Role;
use App\Models\Admin;
use App\Rules\TwoSyllables;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('all_admins');
        $admins = Admin::latest('id')->paginate(env('PGINATION_COUNT'));
        return view('admin.admins.index', compact('admins'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Gate::authorize('add_admin');
        $roles =Role::get();

        return view('admin.admins.create',compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', new TwoSyllables()],
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|min:8',//|regex:/[0-9]/
            'phone' => 'required|unique:admins,phone',
            'image' => ['required', 'mimes:png,jpg,jpeg,webp,jfif,svg', 'max:2048'],
            'role_id' => 'required',
        ]);

        $slug = Str::slug($request->name);
        $slugCount = Admin::where('slug' , 'like' , $slug. '%')->count();
        $count =  $slugCount + 1;

        if($slugCount > 1){
            $slug = $slug . '-' . $count;
        }

        $path = $request->file('image')->store('/uploads/admins', 'custom');

        Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role_id' => $request->role_id,
            'password' => Hash::make($request->password),
            'image' => $path,
            'slug' => $slug,
        ]);

        return redirect()
        ->route('admin.admins.index')
        ->with('msg',__('admin.Admin has been added successfully'))
        ->with('type', 'success');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function show(Admin $admin)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function edit(Admin $admin)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Admin $admin)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function destroy($slug)
    {
        Gate::authorize('delete_admin');

        $admin = Admin::whereSlug($slug)->first();

        $path = public_path($admin->image);

        if($path) {
            try {
                File::delete($path);
            } catch(Exception $e) {
                Log::error($e->getMessage());
            }
        }
        $admin->forceDelete();
        return $slug;
    }
}