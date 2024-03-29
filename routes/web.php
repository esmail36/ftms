<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdvertController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\NotifyController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TrainerController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\UniversityController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\SpecializationsController;
use App\Http\Controllers\WebSite\websiteController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\Website\MessagesController;
use App\Http\Controllers\guestWebsite\GuestWebsiteController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SubsicribeController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['prefix' => LaravelLocalization::setLocale()], function()
{
    Route::prefix('/')->middleware('guest')->name('website.')->group(function(){

    Route::get('/', [GuestWebsiteController::class, 'index'])->name('home');
    Route::post('/contact-us', [GuestWebsiteController::class, 'contact_us'])->name('contact_us');

    });

// login to control panle
Route::group(['namespace' => 'Auth'] ,function() {
    Route::get('/selection-type', [HomeController::class, 'index'])->name('selection')->middleware('guest');
    Route::get('/login/{type}', [LoginController::class, 'loginForm'])->middleware('guest')->name('login.show');
    Route::post('/login', [LoginController::class, 'login'])->name('login');
    Route::get('/logout/{type}', [LoginController::class, 'logout'])->name('logout');
});

// student register
Route::group(['namespace' => 'Student'] ,function() {
    Route::get('/student/select-id',[SubsicribeController::class,'selectUniversity_id'])->name('student.select-id');
    Route::post('/student/select-id',[SubsicribeController::class,'subsicribeId'])->name('student.subsicribeId');
    Route::get('/student/register/{student_id}',[RegisterController::class,'showStudentRegisterForm'])->name('student.register-view');
    Route::post('/student/submit/{student_id}',[RegisterController::class,'createStudent'])->name('student.register');
    Route::get('/student/get/specialization/{id}', [RegisterController::class, 'get_specialization']);

    // verify email
    Route::get('account/verify/{token}', [RegisterController::class, 'verifyAccount'])->name('student.verify');
});


// login to website
Route::group(['namespace' => 'AuthStudent'] ,function() {
    Route::get('/students/login', [LoginController::class, 'loginForm_student'])->middleware('guest')->name('student.login.show');
    Route::post('/login/student', [LoginController::class, 'login_studens'])->name('login_studens');

});
// update Password
Route::group(['namespace' => 'updatePassword'] ,function() {
        //password
        Route::get('edit/password/{type}', [HomeController::class , 'editPassword'])->name('edit-password')->middleware('auth:student,trainer,teacher,company,admin');
        Route::post('update/password', [HomeController::class , 'updatePassword'])->name('update-password')->middleware('auth:student,trainer,teacher,company,admin');
});

// reset Password
Route::group(['namespace' => 'resetPassword'] ,function() {
    Route::get('forget-password/{type}', [ResetPasswordController::class, 'showForgetPasswordForm'])->name('forget.password.get')->middleware('guest');
Route::post('forget-password', [ResetPasswordController::class, 'submitForgetPasswordForm'])->name('forget.password.post')->middleware('guest');
Route::get('reset-password/{type}/{token}', [ResetPasswordController::class, 'showResetPasswordForm'])->name('reset.password.get')->middleware('guest');
Route::post('reset-password', [ResetPasswordController::class, 'submitResetPasswordForm'])->name('reset.password.post')->middleware('guest');
});




// route of website
Route::prefix('/')->middleware('auth:student','is_verify_email')->name('student.')->group(function(){
    // home page
    Route::get('/home',[websiteController::class,'index'])->name('home');

    // company page
    Route::get('/company/{slug}/{program}',[websiteController::class,'showCompany'])->name('company');
    Route::get('/company/{slug}',[websiteController::class,'company_apply'])->name('company_apply');
    Route::get('/company/cancel/{id}/request', [websiteController::class, 'company_cancel'])->name('company_cancel');
    Route::post('/company/comment', [websiteController::class, 'comment'])->name('comment');
    Route::get('evaluate/{slug}', [websiteController::class, 'evaluate_company'])->name('evaluate.company');
    Route::post('student/apply_evaluation/{id}', [websiteController::class, 'apply_evaluation'])->name('apply_evaluation');


    //all company
    Route::get('/companies',[websiteController::class,'allCompanies'])->name('allCompanies');
    Route::get('load/more/categories', [websiteController::class, 'load_more_categories'])->name('load_more_categories');
    Route::get('search/companies', [websiteController::class, 'ajax_search']);
    Route::get('get/companies/names', [websiteController::class, 'get_companies_names']);

    //profile
    Route::get('/profile/{slug}',[websiteController::class,'profile'])->name('profile');
    Route::put('/profile/{slug}', [websiteController::class, 'editProfile'])->name('profile_edit');


    //notifiacation
    Route::get('all/notifications', [NotifyController::class, 'read_student_notify'])->name('read_notify');
    Route::get('/mark-student-read/{id}', [NotifyController::class, 'mark_student_read'])->name('mark_read');

    //task
    Route::get('/task/{slug}',[websiteController::class,'task'])->name('task');
    Route::post('/task/submit', [websiteController::class, 'submit_task'])->name('submit.task');
    Route::post('/edit/task/{id}', [websiteController::class, 'edit_applied_task'])->name('edit.applied.task');

    // messages
    Route::get('chats', [MessagesController::class, 'all_chats'])->name('all.chats');
    Route::post('student/send/message', [MessagesController::class, 'send_message'])->name('send.message');
    Route::get('get/messages', [MessagesController::class, 'get_messages'])->name('get.messages');
    Route::get('chats/read/message', [MessagesController::class, 'read_message'])->name('read.message');
    Route::get('load/more/messages', [MessagesController::class, 'load_more_messages'])->name('load.more.messages');

});


// routes of control panel
Route::prefix('admin')->middleware('auth:admin,teacher,trainer,company')->name('admin.')->group(function() {

    //home page
    Route::get('/home', [HomeController::class, 'home'])->name('home');

    //profile
    Route::get('/profile', [HomeController::class, 'profile'])->name('profile');
    Route::put('/profile/{id}', [HomeController::class, 'profile_edit'])->name('profile_edit');

    //notifiacation
    Route::get('/read-notify', [NotifyController::class, 'read_notify'])->name('read_notify');
    Route::get('/mark-read/{id}', [NotifyController::class, 'mark_read'])->name('mark_read');

    // accept and reject apply
    Route::get('/accept',[NotifyController::class,'accept_apply'])->name('accept_apply');
    Route::delete('/reject/{id}',[NotifyController::class,'reject_apply'])->name('reject_apply');


    // Category
    Route::get('categories/trash', [CategoryController::class, 'trash'])->name('categories.trash');
    Route::delete('categories/{id}/forcedelete', [CategoryController::class, 'forcedelete'])->name('categories.forcedelete');
    Route::post('categories/{id}/restore', [CategoryController::class, 'restore'])->name('categories.restore');
    Route::resource('categories', CategoryController::class);

    // Company
    Route::get('companies/trash', [CompanyController::class, 'trash'])->name('companies.trash');
    Route::delete('companies/{company}/forcedelete', [CompanyController::class, 'forceDelete'])->name('companies.forcedelete');
    Route::post('companies/{company}/restore', [CompanyController::class, 'restore'])->name('companies.restore');
    Route::resource('companies', CompanyController::class);

    //university
    Route::get('get/specialization/{id}', [UniversityController::class, 'get_specialization']);
    Route::resource('universities',UniversityController::class);

    // specialization
    Route::resource('specializations', SpecializationsController::class);

    // trainer
    Route::get('/get/category/{id}', [TrainerController::class, 'get_category']);

    Route::get('get/category/{id}', [TrainerController::class, 'get_category']);
    Route::resource('trainers', TrainerController::class);

    // teacher
    Route::resource('teachers', TeacherController::class);
    Route::get('/get/specialization/{id}', [TeacherController::class, 'get_specialization']);


    // admin
    Route::resource('admins', AdminController::class);

    //student
    Route::get('students/trash', [StudentController::class, 'trash'])->name('students.trash');
    Route::delete('students/{id}/forcedelete', [StudentController::class, 'forceDelete'])->name('students.forcedelete');
    Route::post('students/{id}/restore', [StudentController::class, 'restore'])->name('students.restore');
    Route::delete('students/{slug}/delete/company', [StudentController::class, 'delete_company_student'])->name('students.delete.from.company');
    Route::resource('students', StudentController::class);
    Route::get('{slug}/informations', [StudentController::class, 'show_more_informations'])->name('student.informations');
    Route::get('{slug}/attendence', [StudentController::class, 'show_attendece_calender'])->name('student.attendence');
    Route::get('search/students', [StudentController::class, 'search']);


    // show evaluation
    Route::get('show/evaluation/{id}', [StudentController::class, 'show_evaluation'])->name('show_evaluation');
    // export evaluation as pdf
    Route::get('export/evaluation/pdf/{id}', [StudentController::class, 'export_pdf'])->name('export_pdf');
    // export attendance as pdf
    Route::get('export/attendance/pdf/{slug}', [StudentController::class, 'export_attendance_pdf'])->name('export_attendance_pdf');

    // evaluations
    Route::post('apply_evaluation/{id}', [EvaluationController::class, 'apply_evaluation'])->name('apply_evaluation');
    Route::resource('evaluations', EvaluationController::class);

    //settings
    Route::get('settings', [HomeController::class, 'settings'])->name('settings');
    Route::post('settings/team', [HomeController::class, 'settings_store'])->name('settings_store');
    Route::put('settings/edit/member/{id}', [HomeController::class, 'editMember'])->name('editMember');
    Route::delete('settings/delete/member/{id}', [HomeController::class, 'deleteMember'])->name('deleteMember');

    Route::post('settings', [HomeController::class, 'settings_website'])->name('settings_website');

    // tasks
    Route::get('tasks/{slug}/edit', [TaskController::class, 'edit'])->name('task.edit');
    Route::resource('tasks', TaskController::class);

    // messages
    Route::get('get/students/messages', [MessageController::class, 'get_students_messages'])->name('students.messages');
    Route::get('student/messages', [MessageController::class, 'student_messages'])->name('messages');
    Route::post('send/message', [MessageController::class, 'send_message'])->name('send.message');
    Route::get('message/read/at', [MessageController::class, 'readAt'])->name('message.read.at');


    //import university id
    Route::get('subscribes/import/', [SubsicribeController::class, 'import'])->name('subscribes.import_view');
    Route::post('subscribes/import/Excel', [SubsicribeController::class, 'importExcel'])->name('subscribes.importExcel');
    // subsicribes
    Route::resource('subscribes', SubsicribeController::class);
    Route::get('search/subsicribers', [SubsicribeController::class, 'search_subsicribers'])->name('search.subsicribers');

    // adverts
    Route::resource('adverts', AdvertController::class);

    // All messages page
    Route::get('all/messages', [HomeController::class, 'all_messages_page'])->name('all.messages.page');
    Route::get('request/all/messages', [MessageController::class, 'all_messages_request'])->name('all.messages.request');
    Route::get('request/all/admins/messages', [MessageController::class, 'all_admins_messages'])->name('all.admins.messages');
    Route::get('search/students/messages', [MessageController::class, 'search_students_messages'])->name('search.students.messages');
    Route::get('request/all/companies/messages', [MessageController::class, 'all_companies_messages'])->name('all.companeis.messages');
    Route::get('request/all/teachers/messages', [MessageController::class, 'all_teachers_messages'])->name('all.teachers.messages');
    Route::get('load/more/compnaies', [MessageController::class, 'load_more_companies'])->name('load.more.companies');
    Route::get('load/more/teachers', [MessageController::class, 'load_more_teachers'])->name('load.more.teachers');

      // roles
      Route::resource('roles', RoleController::class);

      //Attendances
      Route::resource('attendances',AttendanceController::class);


    // verify email
    Route::get('account/verify/{slug}/{actor}', [HomeController::class, 'verifyAccount'])->name('Eamilverify');
});


});



