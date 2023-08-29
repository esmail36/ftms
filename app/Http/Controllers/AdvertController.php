<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Advert;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use App\Notifications\NewAdvertNotification;


class AdvertController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('all_adverts');
        if(Auth::guard('company')->check()){
            $adverts = Advert::latest('id')->where('company_id',Auth::user()->id)->paginate(env('PAGINATION_COUNT'));
        }elseif(Auth::guard('teacher')->check()){
            $adverts = Advert::latest('id')->where('teacher_id',Auth::user()->id)->paginate(env('PAGINATION_COUNT'));
        }elseif(Auth::guard('trainer')->check()){
            $adverts = Advert::latest('id')->where('trainer_id',Auth::user()->id)->paginate(env('PAGINATION_COUNT'));
        }else{
            $adverts = Advert::latest('id')->paginate(env('PAGINATION_COUNT'));

        }
        return view('admin.adverts.index',compact('adverts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Gate::authorize('add_advert');

        return view('admin.adverts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $auth = Auth::user();

        $request->validate([
            'main_title'=> 'required',
            'sub_title'=> 'required',
            'image'=> 'image',
        ]);

        if($request->image){
            $path = $request->file('image')->store('/uploads/advert', 'custom');
        }else{
            $random= rand(0,9);
            $path = 'uploads/advert/default'.$random.'.png';
        }
        if(Auth::guard('trainer')->check()){
            Advert::create([
                'main_title' => $request->main_title,
                'sub_title' => $request->sub_title,
                'image' => $path,
                'trainer_id' => $auth->id,
            ]);
            $students = Student::where('trainer_id', Auth::user()->id)->get();
            $trainer_id = Auth::user()->id;
            $teacher_id = '';
            $company_id = '';
            $from = 'TrainerAdvert';

        }elseif(Auth::guard('teacher')->check()){
            Advert::create([
                'main_title' => $request->main_title,
                'sub_title' => $request->sub_title,
                'image' => $path,
                'teacher_id' => $auth->id,
            ]);
            $students = Student::where('teacher_id', Auth::user()->id)->get();
            $trainer_id = '';
            $teacher_id = Auth::user()->id;
            $company_id = '';
            $from = 'TeacherAdvert';
        }elseif(Auth::guard('company')->check()){
            Advert::create([
                'main_title' => $request->main_title,
                'sub_title' => $request->sub_title,
                'image' => $path,
                'company_id' => $auth->id,
            ]);
            $students = Student::where('company_id', Auth::user()->id)->get();
            $trainer_id = '';
            $teacher_id = '';
            $company_id = Auth::user()->id;
            $from = 'CompanyAdvert';
        }else{
            Advert::create([
                'main_title' => $request->main_title,
                'sub_title' => $request->sub_title,
                'image' => $path,
            ]);

        }

        foreach ($students as $student) {
            $student->notify((new NewAdvertNotification(Auth::user()->name,$trainer_id,$teacher_id,$company_id,$from,Auth::user()->image)));
        }

        return redirect()->route('admin.adverts.index')->with('msg',__('admin.Advert has been added successfully'))->with('type', 'success');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        Gate::authorize('edit_advert');

        $advert = Advert::where('id',$id)->first();
        return view('admin.adverts.edit',compact('advert'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Advert $advert)
    {
        $auth = Auth::user();

        $request->validate([
            'main_title'=> 'required',
            'sub_title'=> 'required',
        ]);

        $path = $advert->image;

        if($request->image) {
            File::delete(public_path($advert->image));
            $path = $request->file('image')->store('/uploads/advert', 'custom');
        }
        if(Auth::guard('trainer')->check()){
            $advert->update([
                'main_title' => $request->main_title,
                'sub_title' => $request->sub_title,
                'image' => $path,
                'trainer_id' => $auth->id,
            ]);

        }elseif(Auth::guard('teacher')->check()){
            $advert->update([
                'main_title' => $request->main_title,
                'sub_title' => $request->sub_title,
                'image' => $path,
                'teacher_id' => $auth->id,
            ]);

        }elseif(Auth::guard('company')->check()){
            $advert->update([
                'main_title' => $request->main_title,
                'sub_title' => $request->sub_title,
                'image' => $path,
                'company_id' => $auth->id,
            ]);

        }else{
            $advert->update([
                'main_title' => $request->main_title,
                'sub_title' => $request->sub_title,
                'image' => $path,
            ]);

        }

        return redirect()->route('admin.adverts.index')->with('msg',__('admin.Advert has been updated successfully'))->with('type', 'success');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Gate::authorize('delete_advert');


        $advert = Advert::where('id',$id)->first();


        if(Auth::guard('teacher')->check()){
            $students = Student::where('teacher_id', Auth::user()->id)
            ->get();
        }elseif(Auth::guard('company')->check()){
            $students = Student::where('company_id', Auth::user()->id)
            ->get();
        }elseif(Auth::guard('trainer')->check()){
            $students = Student::where('trainer_id', Auth::user()->id)
            ->get();
        }

        foreach ($students as $student) {
            $notifications = DB::table('notifications')
                ->where('type', 'App\Notifications\NewAdvertNotification')
                ->where('notifiable_type', 'App\Models\Student')
                ->where('notifiable_id', $student->id)
                ->get();

            foreach ($notifications as $notification) {
                $data = json_decode($notification->data, true);

                if(Auth::guard('teacher')->check()){
                    if (($data['teacher_id'] == $advert->teacher_id && $data['from'] == 'TeacherAdvert')) {
                        DB::table('notifications')
                            ->where('id', $notification->id)
                            ->delete();
                    }
                }elseif(Auth::guard('company')->check() & $data['from'] == 'CompanyAdvert' ){
                    if (($data['company_id'] == $advert->company_id)) {
                        DB::table('notifications')
                            ->where('id', $notification->id)
                            ->delete();
                    }
                }elseif(Auth::guard('trainer')->check() & $data['from'] == 'TrainerAdvert'){
                    if (($data['trainer_id'] == $advert->trainer_id)) {
                        DB::table('notifications')
                            ->where('id', $notification->id)
                            ->delete();
                    }
                }



            }
        }

        if(!strpos($advert->image, 'default')){
        $path = public_path($advert->image);
            if($path) {
                        try {
                            File::delete($path);
                        } catch(Exception $e) {
                            Log::error($e->getMessage());
                        }
                    }
                    $advert->delete();
                    return $id;
        }else{
                $advert->delete();
                return $id;
        }
    }
}