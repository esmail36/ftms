@extends('student.master')

@section('title', $task->main_title)

@section('styles')
    <style>
        .task {
            background: #f8f9fa;
        }

        .divider {
            border-bottom: 1px solid #ccc;
        }

        th {
            width: 220px
        }

        .invalid-feedback {
            position: absolute;
            bottom: -29px;
            font-size: 15px;
        }

        .validaition {
            position: absolute;
            bottom: 0;
        }

        .task_img {
            display: inline-block;
            filter: invert(56%) sepia(52%) saturate(282%) hue-rotate(169deg) brightness(83%) contrast(100%);
            width: 44px;
            margin-right: 8px;
        }

        .hidden_form {
            display: none;
        }

        #form_wrapper {
            position: relative;

        }
    </style>
@stop


@section('content')

    <section id="reviews">
        <div class="container">
            <h2 class="text-white"><img class="task_img" src="{{ asset('studentAssets/img/task.svg') }}"
                    alt="">{{ $task->main_title }}</h2>
        </div>
    </section>

    <!-- ABOUT -->
    <section style="padding-top:30px ">
        <div class="container">
            <div class="row ">
                <div class="col-lg-12 ">
                    <div class="task rounded p-3">
                        <p><b>{{ __('admin.Opend') }} :
                            </b>{{ Carbon::parse($task->start_date)->format('l, j F Y, g:i A') }}</p>
                        <p><b>{{ __('admin.Due') }} : </b>{{ Carbon::parse($task->end_date)->format('l, j F Y, g:i A') }}
                        </p>
                        <div class="divider"></div>
                        <p class="my-3">{{ $task->sub_title }}</p>
                        <div class="desc mb-5 mt-3">
                            {!! $task->description !!}


                        </div>

                        <a target="_blank" href="{{ asset('uploads/tasks-files/' . $task->file) }}"
                            download>{{ $task->file }}</a>
                    </div>
                    @if (session('msg'))
                        <div class="alert alert-{{ session('type') }} alert-dismissible fade show mt-3" id="alert">
                            {{ session('msg') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    <h3 class="my-4">{{ __('admin.Submission status') }}</h3>

                    <table class="table table-striped table-bordered table-hover">

                        <tbody>
                            <tr>
                                <th>{{ __('admin.Submission status') }}</th>
                                @if ($applied_task)
                                    <td style="background: #d1e7dd ; color: #0f5132 ;font-weight: 500;">
                                        {{ __('admin.Submitted') }}
                                    </td>
                                @else
                                    <td id="submitted_text" class="text-danger">
                                        {{ __('admin.Not Submitted yet') }}
                                    </td>
                                @endif


                            </tr>
                            <tr>
                                <th>{{ __('admin.Time remaining') }}</th>
                                <td id="time_remaining">

                                    @php
                                        $end_date = Carbon::parse($task->end_date);
                                        $time_passed_days = now()->diffInDays($end_date);
                                        $time_passed_hours = now()->diffInHours($end_date);
                                        $time_passed_minutes = now()->diffInMinutes($end_date);
                                    @endphp

                                    @if (!$applied_task)
                                        @if ($end_date->gt(now()))
                                            @if ($remaining_days > 0)
                                                {{ $remaining_days . ' ' . __('admin.Days and') . ' ' . $remaining_hours . ' ' . __('admin.hours remaining') }}
                                            @elseif($remaining_hours)
                                                {{ $remaining_hours . ' ' . __('admin.hours remaining') }}
                                            @else
                                                {{ $remaining_minutes . ' ' . __('admin.minutes remaining') }}
                                            @endif
                                        @else
                                            <span class="text-danger">
                                                @if ($time_passed_days && $time_passed_hours)
                                                    {{ $time_passed_days == 1 ? $time_passed_days . ' ' . __('admin.Day ago') : $time_passed_days . ' ' . __('admin.Days ago') }}
                                                @elseif ($time_passed_hours)
                                                    @if (app()->getLocale() == 'en')
                                                        {{ $time_passed_hours . ' ' . __('admin.hours ago') }}
                                                    @else
                                                        {{ __('admin.Since') . ' ' . $time_passed_hours . ' ' . __('admin.Hour') }}
                                                    @endif
                                                @else
                                                    @if (app()->getLocale() == 'en')
                                                        {{ $time_passed_minutes . ' ' . __('admin.minutes ago') }}
                                                    @else
                                                        {{ __('admin.Since') . ' ' . $time_passed_minutes . ' ' . __('admin.Minute') }}
                                                    @endif
                                                @endif
                                            </span>
                                        @endif
                                    @else
                                        {{ $applied_task->updated_at->gt($applied_task->created_at) ? __('admin.Updated') . ' ' . $applied_task->updated_at->diffForHumans() : __('admin.Submitted') . ' ' . $applied_task->created_at->diffForHumans() }}
                                        <span class="float-right text-success"><i class="fas fa-check"></i></span>
                                    @endif
                                </td>

                            </tr>
                            <tr>
                                <th>{{ __('admin.File submission') }}</th>
                                @if ($applied_task)
                                    <td id="file_submitted">
                                        <a href="{{ asset('uploads/applied-tasks/' . $applied_task->file) }}"
                                            target="_blank" download>{{ $applied_task->file }}</a>
                                    </td>
                                @else
                                    <td id="file_submitted">{{ __('admin.There is no file yet') }}</td>
                                @endif

                            </tr>
                        </tbody>
                    </table>


                </div>
                <div class="col-lg-12 text-end" id="form_wrapper">
                    @if (!now()->gt($end_date))
                        @if ($applied_task)
                            <button type="button" id="show_edit_form" value="edit-btn"
                                class="btn btn-brand">{{ __('admin.Edit submission') }}</button>

                            <form action="{{ route('student.edit.applied.task', $applied_task->id) }}" class="hidden_form"
                                id="edit_form" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="task_id" value="{{ $task->id }}">
                                <div class="file-drop-area">
                                    <span class="fake-btn">{{ __('admin.Choose file') }}</span>
                                    <span class="file-msg">{{ __('admin.or drag and drop file here') }}</span>
                                    <input class="file-input" name="file" id="file_input" type="file" required>
                                    <span
                                        class="validaition">{{ __('admin.Supported file types: doc, docs, pdf, pptx, zip, rar') }}
                                        <b>|</b>
                                        {{ __('admin.Max file size: 5 MB') }}</span>
                                    <span class="text-danger">{{ $errors->first('file') }}</span>
                                </div>

                                <button type="button" id="hide_edit_form"
                                    class="mt-5 btn btn-seconed-brand btn-sm">{{ __('admin.Cancel') }}</button>
                                <button type="submit" class="btn btn-brand btn-sm mt-5" id="edit_btn"><i
                                        class="fas fa-edit"></i>
                                    {{ __('admin.Edit') }}</button>

                            </form>
                        @else
                            <button type="button" id="show_form"
                                class="btn btn-brand">{{ __('admin.Add Submission') }}</button>


                            <form action="{{ route('student.submit.task') }}" class="mt-4 hidden_form" method="POST"
                                id="task_form" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="task_id" value="{{ $task->id }}">
                                <div class="file-drop-area">
                                    <span class="fake-btn">{{ __('admin.Choose file') }}</span>
                                    <span class="file-msg">{{ __('admin.or drag and drop file here') }}</span>
                                    <input class="file-input" name="file" id="file_input" type="file" required>
                                    <span
                                        class="validaition">{{ __('admin.Supported file types: doc, docs, pdf, pptx, zip, rar') }}<b>|</b>
                                        {{ __('admin.Max file size: 5 MB') }}</span>
                                    <span class="text-danger">{{ $errors->first('file') }}</span>
                                </div>

                                <button type="button" id="hide_form"
                                    class="mt-5 btn btn-seconed-brand">{{ __('admin.Cancel') }}</button>
                                <button type="submit" id="submit_btn"
                                    class="mt-5 btn btn-brand">{{ __('admin.Submit') }}</button>

                            </form>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </section>






    {{-- @endif --}}
@stop

@section('scripts')

    {{-- Ajax --}}
    <script>
        $(document).ready(function() {

            // submit form
            $("#form_wrapper").on("click", '#show_form', function() {
                $('#task_form').removeClass("hidden_form");
                taskScrollToBottom();
                $(this).hide();
            });

            $("#form_wrapper").on("click", '#hide_form', function() {
                $('#task_form').addClass("hidden_form");
                $("#show_form").show();
            });

            // edit form
            $("#form_wrapper").on("click", '#show_edit_form', function() {
                $('#edit_form').removeClass("hidden_form");
                taskScrollToBottom();
                $(this).hide();
            });
            $("#form_wrapper").on("click", '#hide_edit_form', function() {
                $('#edit_form').addClass("hidden_form");
                $("#show_edit_form").show();
            });

            // task scroll behaviour
            function taskScrollToBottom() {
                window.scrollTo(0, document.body.scrollHeight);
            }

        })
    </script>

    {{-- Drop and drag input --}}
    <script>
        var $fileInput = $('.file-input');
        var $droparea = $('.file-drop-area');

        // highlight drag area
        $fileInput.on('dragenter focus click', function() {
            $droparea.addClass('is-active');
        });

        // back to normal state
        $fileInput.on('dragleave blur drop', function() {
            $droparea.removeClass('is-active');
        });

        // change inner text
        $fileInput.on('change', function() {
            var filesCount = $(this)[0].files.length;
            var $textContainer = $(this).prev();

            if (filesCount === 1) {
                // if single file is selected, show file name
                var fileName = $(this).val().split('\\').pop();
                $textContainer.text(fileName);
            } else {
                // otherwise show number of files
                $textContainer.text(filesCount + ' files selected');
            }
        });
    </script>

    {{-- @if (session('msg'))
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-right',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: false,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        })

        @if (session('type') == 'success')
            Toast.fire({
                icon: 'success',
                title: '{{ session('msg') }}'
            })
        @elseif (session('type') == 'danger')
            Toast.fire({
                icon: 'error',
                title: '{{ session('msg') }}'
            })
        @else
            Toast.fire({
                icon: 'info',
                title: '{{ session('msg') }}'
            })
        @endif
    </script>
    @endif --}}

@stop