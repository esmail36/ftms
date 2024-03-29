<?php

namespace App\Http\Controllers\Website;

use App\Events\CreateMessage;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Message;
use App\Models\Teacher;
use App\Models\Trainer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


class MessagesController extends Controller
{


    // chats
    public function all_chats()
    {
        $auth = Auth::user();
        $output = '';
        $activeMessage = [];
        $notifyNum = 0;
        $roles = [];

        if($auth->trainer_id && $auth->teacher_id && $auth->company_id) {
            $roles = ['trainer', 'teacher', 'company'];
        } elseif($auth->trainer_id && $auth->company_id) {
            $roles = ['trainer', 'company'];
        } elseif($auth->teacher_id && $auth->company_id) {
            $roles = ['teacher', 'company'];
        } elseif($auth->teacher_id && $auth->trainer_id) {
            $roles = ['teacher', 'trainer'];
        } elseif($auth->trainer_id) {
            $roles = ['trainer'];
        } elseif($auth->teacher_id) {
            $roles = ['teacher'];
        } elseif($auth->company_id) {
            $roles = ['company'];
        } else {
            $roles = [];
        }

        $allmessages = [];


       if($roles != []) {
        foreach($roles as $role) {
            $roleActive = '';
            $roleObj = $auth->{$role};

            $lastMessage = Message::where([
                    ['sender_type', 'student'],
                    ['receiver_type', $role],
                    ['sender_id', $auth->id],
                    ['receiver_id', $roleObj->id],
                ])
                ->orWhere([
                    ['sender_type', $role],
                    ['receiver_type', 'student'],
                    ['sender_id', $roleObj->id],
                    ['receiver_id', $auth->id],
                ])
                ->latest('created_at')
                ->first();


            $activeMessage = Message::where([
                    ['sender_id', $roleObj->id],
                    ['receiver_id', $auth->id],
                    ['sender_type', $role],
                    ['receiver_type', 'student'],
                ])
                ->orderBy('created_at', 'desc')
                ->first();

            $allmessages[] = $lastMessage;
            $allmessages[] = $activeMessage;

            // dd($lastMessage);


            if($lastMessage) {
                $lastMessageText = Str::words($lastMessage->message, 4, '...');
                $time = $lastMessage->created_at->diffForHumans();

                if($activeMessage && $activeMessage->read_at == null) {
                    $roleActive = 'active';
                    $activeMessage[$role] = $activeMessage;
                    $notifyNum++;
                }

                if($roleObj->image) {
                    $src = asset($roleObj->image);
                } else {
                    $src = 'https://ui-avatars.com/api/?background=random&name=' . $roleObj->name;
                }

                $output .= '<div class="media">
                                <a href="#" data-type="' . $role . '"
                                    data-slug="' . $roleObj->slug . '"
                                    data-name="' . $roleObj->name . '"
                                    data-id="' . $lastMessage->id . '"
                                    class="list-group-item list-group-item-action chat-circle ' . $roleActive . '">
                                    <div class="d-flex" style="gap: 10px">
                                        <div class="msg-img">
                                            <img src="'. $src . '">
                                        </div>
                                        <div class="main-info">

                                            <div class="msg-body" style="width: 100%;">
                                                <h3 class="dropdown-item-title text-start">' . $roleObj->name . '</h3>
                                                <p class="text-sm message m-0 mt-1">' . $lastMessageText . '
                                                    <i class="fas fa-circle active-dot"
                                                        style="color: #003e83ad !important; font-size: 8px; "></i>
                                                </p>

                                                <div>
                                                            <p class="d-flex justify-content-start align-items-center "
                                                                style="gap:4px; font-size: 12px; margin:0 ">
                                                                <i class="far fa-clock "
                                                                    style="line-height: 1; font-size: 12px; color: #464a4c !important"></i>
                                                                    ' . $time . '
                                                            </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </a>
                            </div>';
            } else {

                if($roleObj->image) {
                    $src = asset($roleObj->image);
                } else {
                    $src = 'https://ui-avatars.com/api/?background=random&name=' . $roleObj->name;
                }

                $output .= '<div class="media">
                                     <a href="#" data-type="' . $role . '"
                                         data-slug="'.$roleObj->slug.'"
                                         data-name="'.$roleObj->name.'"
                                         class="list-group-item list-group-item-action chat-circle">
                                         <div class="d-flex" style="gap: 10px">
                                            <div class="msg-img">
                                                    <img src="'. $src . '">
                                                </div>
                                         <div class="main-info">

                                            <div class="msg-body" style="width: 100%;">
                                                <h3 class="dropdown-item-title text-start">
                                                    '.$roleObj->name.'
                                                </h3>
                                                <p class="text-sm message  m-0 mt-1">
                                                    '.__('admin.No messages yet!').'

                                                </p>

                                            </div>


                                        </div>
                                        </div>
                                    </a>
                                </div>';
            }
        }
       } else {
        $output .= '<div class="media">
                                     <a href="#"
                                         class="list-group-item list-group-item-action ">

                                            <div class="msg-body" style="width: 100%;">

                                                <p class="text-sm message">
                                                    ' . __('admin.There is no one to chat with yet!') . '

                                                </p>

                                            </div>


                                        </div>

                                    </a>
                                </div>';
       }

    //    $allmessages = collect($allmessages)->sortByDesc('created_at');
    //    if($allmessages) {

    //    }

    //    dd($allmessages);

        $data = [
            "output" => $output,
            "number" => $notifyNum,
        ];
        return $data;

    }

    // send message
    public function send_message(Request $request)
    {
        $user = Auth::user();
        $trainer = Trainer::whereSlug($request->slug)->first();
        $teacher = Teacher::whereSlug($request->slug)->first();
        $company = Company::whereSlug($request->slug)->first();

        if($trainer) {
            $role = 'trainer';
            $roleObj = $trainer;
        } elseif($company) {
            $role = 'company';
            $roleObj = $company;
        } else {
            $role = 'teacher';
            $roleObj = $teacher;
        }

        $message = Message::create([
            'message' => $request->message,
            'sender_id' => $user->id,
            'sender_type' => 'student',
            'receiver_id' => $roleObj->id,
            'receiver_type' => $role,
        ]);

        broadcast(new CreateMessage($message));

        $output = '<div class="chat outgoing message" data-id="' . $message->id . '">
                                <div class="details">
                                    <p>' . $message->message . '</p>
                                </div>
                                </div>';
        return $output;
    }


    // get messages
    public function get_messages(Request $request)
    {
        $slug = $request->slug;
        $type = $request->type;
        $auth = Auth::user();

        if($type == 'trainer') {
            $role = Trainer::whereSlug($slug)->first();
        } elseif($type == 'teacher') {
            $role = Teacher::whereSlug($slug)->first();
        } else {
            $role = Company::whereSlug($slug)->first();
        }

        $messages = Message::where([
                ['sender_id', $auth->id],
                ['sender_type', 'student'],
                ['receiver_id', $role->id],
                ['receiver_type', $type],
            ])
            ->orWhere([
                ['sender_id', $role->id],
                ['sender_type', $type],
                ['receiver_id', $auth->id],
                ['receiver_type', 'student'],
            ])
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get()
            ->reverse();

        // if ($type == 'trainer') {
        //     $trainer = Trainer::whereSlug($slug)->first();
        //     $messages = Message::where('student_id', $auth->id)
        //         ->where('trainer_id', $trainer->id)
        //         ->orderBy('id', 'desc')
        //         ->limit(10)
        //         ->get()
        //         ->reverse();
        // } elseif($type == 'teacher') {
        //     $teacher = Teacher::whereSlug($slug)->first();
        //     $messages = Message::where('student_id', $auth->id)
        //         ->where('teacher_id', $teacher->id)
        //         ->orderBy('id', 'desc')
        //         ->limit(10)
        //         ->get()
        //         ->reverse();
        // } else {
        //     $company = Company::whereSlug($slug)->first();
        //     $messages = Message::where('student_id', $auth->id)
        //         ->where('company_id', $company->id)
        //         ->orderBy('id', 'desc')
        //         ->limit(10)
        //         ->get()
        //         ->reverse();
        // }


        $output = '';

        if ($messages) {
            foreach ($messages as $message) {

                if ($message->sender_id == $auth->id && $message->sender_type == 'student') {
                    $output .= '<div class="chat outgoing message" data-id="' . $message->id . '">
                                <div class="details">
                                    <p>' . $message->message . '</p>
                                </div>
                                </div>';
                } else {
                    $output .= '<div class="chat incoming message" data-id="' . $message->id . '">
                                    <div class="details">
                                        <p>' . $message->message . '</p>
                                    </div>
                                </div>';
                }
            }
        }



        return $output;
    }


    // mark message as read
    public function read_message(Request $request)
    {
        $auth = Auth::user();
        if($request->type == 'trainer') {
            $message = Message::where([
                    ['sender_type', 'student'],
                    ['receiver_type', 'trainer'],
                    ['sender_id', $auth->id],
                    ['receiver_id', $auth->trainer_id],
                ])
                ->orWhere([
                    ['sender_type', 'trainer'],
                    ['receiver_type', 'student'],
                    ['sender_id', $auth->trainer_id],
                    ['receiver_id', $auth->id],
                ])
                ->latest('id')
                ->first();
        } elseif($request->type == 'teacher') {
            $message = Message::where([
                    ['sender_type', 'student'],
                    ['receiver_type', 'teacher'],
                    ['sender_id', $auth->id],
                    ['receiver_id', $auth->teacher_id],
                ])
                ->orWhere([
                    ['sender_type', 'teacher'],
                    ['receiver_type', 'student'],
                    ['sender_id', $auth->teacher_id],
                    ['receiver_id', $auth->id],
                ])
                ->latest('id')
                ->first();
        } else {
            $message = Message::where([
                    ['sender_type', 'student'],
                    ['receiver_type', 'company'],
                    ['sender_id', $auth->id],
                    ['receiver_id', $auth->company_id],
                ])
                ->orWhere([
                    ['sender_type', 'company'],
                    ['receiver_type', 'student'],
                    ['sender_id', $auth->company_id],
                    ['receiver_id', $auth->id],
                ])
                ->latest('id')
                ->first();
        }


        $message->read_at = now();
        $message->save();



        // return $output;
    }
}
