<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Student;
use App\Models\Evaluation;
use Illuminate\Http\Request;
use App\Models\AppliedEvaluation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use PDF;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('all_students');


        if (Auth::guard('company')->check()) {

            if (request()->has('keyword')) {
                $students = Student::with('specialization', 'university')
                    ->where('company_id', Auth::user()->id)
                    ->where('name', 'like', '%' . request()->keyword . '%')
                    ->latest('id')
                    ->paginate(env('PAGINATION_COUNT'));
            } else {
                $students = Student::with('specialization', 'university')
                    ->where('company_id', Auth::user()->id)
                    ->latest('id')
                    ->paginate(env('PAGINATION_COUNT'));
            }
        } elseif (Auth::guard('teacher')->check()) {

            if (request()->has('keyword')) {
                $students = Student::with('specialization', 'university')
                    ->where('teacher_id', Auth::user()->id)
                    ->where('name', 'like', '%' . request()->keyword . '%')
                    ->latest('id')
                    ->paginate(env('PAGINATION_COUNT'));
            } else {
                $students = Student::with('specialization', 'university')
                    ->where('teacher_id', Auth::user()->id)
                    ->latest('id')
                    ->paginate(env('PAGINATION_COUNT'));
            }
        } elseif (Auth::guard('trainer')->check()) {

            if (request()->has('keyword')) {
                $students = Student::with('specialization', 'university')
                    ->where('trainer_id', Auth::user()->id)
                    ->where('name', 'like', '%' . request()->keyword . '%')
                    ->latest('id')
                    ->paginate(env('PAGINATION_COUNT'));
            } else {
                $students = Student::with('specialization', 'university')
                    ->where('trainer_id', Auth::user()->id)
                    ->latest('id')
                    ->paginate(env('PAGINATION_COUNT'));
            }
        } else {
            if (request()->has('keyword')) {
                $students = Student::with('specialization', 'university')
                    ->where('name', 'like', '%' . request()->keyword . '%')
                    ->latest('id')
                    ->paginate(env('PAGINATION_COUNT'));
            } else {
                $students = Student::with('specialization', 'university')
                    ->latest('id')
                    ->paginate(env('PAGINATION_COUNT'));
            }
        }

        $applied_evaluations = AppliedEvaluation::where('evaluation_type', 'student')->get();

        return view('admin.students.index', compact('students', 'applied_evaluations'));
    }

    // search and return students names
    public function search(Request $request)
    {
        $search = $request->search;

        if ($search != null) {
            if (Auth::guard('company')->check()) {
                $students = Student::where('company_id', Auth::user()->id)
                    ->where(function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%')
                            ->orWhere('student_id', 'like', '%' . $search . '%');
                    })
                    ->pluck('name');
            } elseif (Auth::guard('trainer')->check()) {
                $students = Student::where('trainer_id', Auth::user()->id)
                    ->where(function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%')
                            ->orWhere('student_id', 'like', '%' . $search . '%');
                    })
                    ->pluck('name');
            } elseif (Auth::guard('teacher')->check()) {
                $students = Student::where('teacher_id', Auth::user()->id)
                    ->where(function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%')
                            ->orWhere('student_id', 'like', '%' . $search . '%');
                    })
                    ->pluck('name');
            } else {
                $students = Student::where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('student_id', 'like', '%' . $search . '%');
                })
                    ->pluck('name');
            }

            if ($students) {
                return response()->json(["students" => $students]);
            } else {
                return response()->json(["message" => "meg"]);
            }
        }
    }

    public function delete_company_student($slug)
    {
        $student = Student::whereSlug($slug)->first();


        $applyNotifications = DB::table('notifications')
            ->where('type', 'App\Notifications\AppliedNotification')
            ->where('notifiable_type', 'App\Models\Company')
            ->where('notifiable_id', $student->company_id)
            ->get();

        if ($applyNotifications) {
            foreach ($applyNotifications as $notification) {

                $data = json_decode($notification->data, true);
                if (($data['student_id'] == $student->id) &&
                    ($data['category_id'] == $student->category_id) &&
                    ($data['company_id'] == $student->company_id)
                ) {
                    DB::table('notifications')
                        ->where('id', $notification->id)
                        ->delete();
                }
            }
        }

        $acceptApplyNotifications = DB::table('notifications')
            ->where('type', 'App\Notifications\AcceptApplyNotification')
            ->where('notifiable_type', 'App\Models\Student')
            ->where('notifiable_id', $student->id)
            ->get();

        if ($acceptApplyNotifications) {
            foreach ($acceptApplyNotifications as $notification) {

                $data = json_decode($notification->data, true);
                if (($data['studentId'] == $student->id) &&
                    ($data['company_id'] == $student->company_id)
                ) {
                    DB::table('notifications')
                        ->where('id', $notification->id)
                        ->delete();
                }
            }
        }



        $student->update([
            'company_id' => null,
            'category_id' => null,
            'trainer_id' => null,
        ]);

        return $slug;
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        Gate::authorize('evaluate_student');

        $student = Student::whereSlug($slug)->first();

        $evaluation = Evaluation::where('evaluation_type', 'student')->first();

        if ($evaluation) {
            return view('admin.students.evaluate', compact('evaluation', 'student'));
        } else {
            return redirect()->back()
                ->with('msg', 'Please Add Evaluation First')
                ->with('type', 'info');
        }
    }

    /**
     * Show more informations about the student.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show_more_informations($slug)
    {
        Gate::authorize('more_about_student');
        $student = Student::whereSlug($slug)->first();
        $applied_evaluation = AppliedEvaluation::where('student_id', $student->id)->first();
        return view('admin.students.informations', compact('student', 'applied_evaluation'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($slug)
    {
        Gate::authorize('delete_student');

        $student = Student::whereSlug($slug)->first();

        Student::destroy($student->id);
        return $slug;
    }


    /**
     * Display a trashed listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trash()
    {
        Gate::authorize('recycle_students');

        if (request()->has('keyword')) {
            $students = Student::onlyTrashed()->where('name', 'like', '%' . request()->keyword . '%')
                ->paginate(env('PAGINATION_COUNT'));
        } else {
            $students = Student::onlyTrashed()->latest('id')->paginate(env('PAGINATION_COUNT'));
        }
        return view('admin.students.trash', compact('students'));
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($slug)
    {
        Gate::authorize('restore_student');

        $students = Student::onlyTrashed()->whereSlug($slug)->first();

        $students->restore();
        return $slug;
    }


    /**
     * Restore the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function forcedelete($slug)
    {
        Gate::authorize('forceDelete_student');

        $students = Student::onlyTrashed()->whereSlug($slug)->first();


        if (public_path($students->image)) {
            try {
                File::delete(public_path($students->image));
            } catch (Exception $e) {
                Log::error($e->getMessage());
            }
        }
        $students->forcedelete();
        return $slug;
    }


    /**
     * Display the student evaluation.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show_evaluation($slug)
    {
        Gate::authorize('evaluation_student');

        $student = Student::whereHas('applied_evaluation')->whereSlug($slug)->first();
        $evaluation = AppliedEvaluation::where('student_id', $student->id)
            ->where('evaluation_type', 'student')
            ->first();
        $data = json_decode($evaluation->data, true);
        $scores = [
            'bad' => 20,
            'acceptable' => 40,
            'good' => 60,
            'very good' => 80,
            'excellent' => 100,
        ];

        $total_score = 0;
        $count = count($data);
        foreach ($data as $response) {
            $total_score += $scores[$response];
        }

        $average_score = $total_score / $count;
        $average_score = floor($average_score);

        return view('admin.students.evaluation_page', compact('student', 'data', 'average_score'));
    }



    /**
     * Export student evaluation as PDF.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function export_pdf($id)
    {
        $student = Student::findOrFail($id);
        $name = $student->name;
        $applied_evaluation = AppliedEvaluation::with('evaluation')->where('student_id', $id)->first();
        $questions = json_decode($applied_evaluation->data, true);
        $scores = [
            'bad' => 20,
            'acceptable' => 40,
            'good' => 60,
            'very good' => 80,
            'excellent' => 100,
        ];

        $total_score = 0;
        $count = count($questions);
        foreach ($questions as $response) {
            $total_score += $scores[$response];
        }

        $average_score = $total_score / $count;
        $average_score = floor($average_score);

        $data = [
            'student' => $student,
            'questions' => $questions,
            'applied_evaluation' => $applied_evaluation,
            'total_rate' => $average_score,
        ];

        $name_of_pdf = str_replace(' ', '-', $student->name) . '-' . $student->student_id;

        $pdf = PDF::make();
        if(app()->getLocale() == 'en') {
            $pdf->loadView('admin.students.pdf', $data);
        }else {
            $pdf->loadView('admin.students.pdf-ar', $data);
        }
        return $pdf->download($name_of_pdf . '-evaluation.pdf');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show_attendece_calender($slug)
    {
        Gate::authorize('student_attendence');

       $student = Student::whereSlug($slug)->first();
       return view('admin.students.attendace_page', compact('student'));
    }


     /**
     * Export student attendance as PDF.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function export_attendance_pdf($slug)
    {
        $student = Student::whereSlug($slug)->first();

        $name_of_pdf = str_replace(' ', '-', $student->name) . '-' . $student->student_id;
        $data = ['student' => $student];
        $pdf = PDF::make();
        if(app()->getLocale() == 'en') {
            $pdf->loadView('admin.students.attendance-pdf', $data);
        }else {
            $pdf->loadView('admin.students.attendance-pdf-ar', $data);
        }
        return $pdf->download($name_of_pdf . '-attendance.pdf');
    }


    // public function indexPdf($slug)
    // {
    //     $student = Student::whereSlug($slug)->first();
    //     return view('admin.students.attendance-pdf', compact('student'));
    // }
}
