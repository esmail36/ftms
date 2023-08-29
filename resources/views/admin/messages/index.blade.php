@extends('admin.master')

@section('title', 'Chat Messages')

@section('content')

    <div class="chat-wrapper">
        <div class="chat-list">
            <div class="chats-label">
                <h5>Chats</h5>
            </div>

            <div class="chat-boxes">

            </div>



        </div>
        <div class="chat-area">
            <header>

                <img src="{{ asset($student->image) }}" alt="">
                <div class="details">
                    <span id="student_name">{{ $student->name }}</span>
                    <p>Active now</p>
                </div>
            </header>
            <div class="chat_box">

            </div>
            <form method="POST" class="typing_area" action="{{ route('admin.send.message') }}">
                @csrf
                <input type="text" value="{{ $student->slug }}" name="reciver_id" hidden>
                <input type="text" name="message" class="form-control input-field" placeholder="Type a message here..."
                    autocomplete="off">
                <button type="submit" class="message_btn"><i class="fab fa-telegram-plane"></i></button>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script src="{{ asset('adminAssets/dist/js/chat.js') }}"></script>
    <script>
        const userId = "{{ Auth::user()->id }}";
        const pusherKey = "{{ env('PUSHER_APP_KEY') }}";

        var pusher = new Pusher(pusherKey, {
            cluster: 'ap2',
            authEndpoint: '/broadcasting/auth',
        });

        var channel = pusher.subscribe(`private-Messages.${userId}`);
        channel.bind('new-message', function(data) {
            appendMessage(data.message.message, data.image);
            scrollToBottom();
        });


    </script>
@endsection