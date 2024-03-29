@extends('admin.master')

@section('title', __('admin.Students'))
@section('sub-title', __('admin.Students'))
@section('students-menu-open', 'menu-open')
@section('students-active', 'active')

@section('styles')

    <style>
        a {
            color: #000;
            text-decoration: none !important;
        }

        a:hover {
            color: #000;
            text-decoration: none !important;
        }

        .search-wrapper {
            position: relative;
        }

        .search-wrapper input:focus {
            /* border-right: none; */
            /* box-shadow:none */

        }

        .search-wrapper input:focus {
            border: 1px solid #86b7fe !important;
            /* outline: 0 !important; */
            box-shadow: 0 0 0 0.25rem rgb(13 110 253 / 25%) !important;
        }



        .search-result {
            background: #e2e2e2;
            position: absolute;
            width: 100%;
            /* height: auto; */
            top: 38px;
            left: 0;
            border-radius: .375rem;
            border-top-right-radius: 0;
            border-top-left-radius: 0;
            /* border: 1px solid #ced4da; */
            border-top: none;
            display: none;

        }

        .search-result ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }


        .search-result ul li a {
            width: 100%;
            padding: 8px 15px;
            display: block;
            text-decoration: none;
            color: #000
        }

        .search-result ul li a u {
            text-decoration: none;
            /* color: #b40000 */
            font-weight: 600
        }

        .search-result ul li a:hover {
            background: #dbdbdb
        }

        #search_input {
            position: relative;
        }

        #students_names {
            position: absolute;
            left: 0;
            top: 38px;
            width: 100%;
            padding: 8px 0;
            padding-bottom: 2px;
            background-color: #fff;
            list-style-type: none;
            box-shadow: 0 19px 25px 3px rgba(0, 0, 0, 0.2);
            border-radius: 5px;
        }

        #students_names li {
            height: 30px;
            transition: all 0.1s ease-in-out;
            display: flex;
            align-items: center;
        }

        #students_names li:hover {
            background-color: #eeeeeeb1;
        }

        #students_names a {
            display: inline-block;
            width: 100%;
            padding: 0 10px;
        }
    </style>
@stop

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex  justify-content-between">




                        <div class="card-tools col-6" style="display: flex; align-items: center; gap: 10px">
                            <form action="" id="search_form">
                                <div class="input-group input-group" style="width: 280px;">
                                    <input type="text" name="keyword" id="search_input" value="{{ request()->keyword }}"
                                        class="form-control " placeholder="{{ __('admin.Search by Student Name') }}"
                                        autocomplete="off">
                                    <ul id="students_names">

                                    </ul>
                                </div>
                            </form>


                        </div>


                        @can('recycle_students')
                            <div class="btn-website">

                                <a href="{{ route('admin.students.trash') }}" class="btn btn-outline-secondary btn-flat"><i
                                        class="fas fa-trash"></i> {{ __('admin.Recycle Bin') }}</a>
                            </div>
                        @endcan

                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body table-responsive p-0">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr style="background-color: #1e272f; color: #fff;">
                                <th>#</th>
                                <th>{{ __('admin.Student Name') }}</th>
                                {{-- <th>{{ __('admin.Student phone') }}</th> --}}
                                <th>{{ __('admin.University Name') }}</th>
                                @if (!(Auth::guard('company')->check() || Auth::guard('trainer')->check()))
                                    <th>{{ __('admin.Student ID') }}</th>
                                @endif
                                {{-- <th>{{ __('admin.Specialization') }}</th> --}}
                                {{-- <th>{{ __('admin.Evaluation Status') }}</th> --}}
                                @canAny(['more_about_student','delete_student'])
                                    <th>{{ __('admin.Actions') }}</th>
                                @endcanAny
                            </tr>
                        </thead>

                        <tbody>
                            @php
                                $count = $students->count();
                            @endphp
                            @forelse ($students as $student)
                                <tr id="row_{{ $student->slug }}">
                                    <td>
                                        {{ $count }}

                                        @php
                                            $count--;
                                        @endphp

                                    </td>
                                    <td>{{ $student->name }}</td>
                                    {{-- <td>{{ $student->phone }}</td> --}}
                                    <td>{{ $student->university->name }}</td>
                                    @if (!(Auth::guard('company')->check() || Auth::guard('trainer')->check()))
                                        <td>{{ $student->student_id }}</td>
                                    @endif
                                    {{-- <td>{{ $student->specialization->name }}</td> --}}
                                    {{-- <td>
                                        @php
                                            $isEvaluated = false;
                                        @endphp
                                        @foreach ($applied_evaluations as $applied_evaluation)
                                            @if ($student->id == $applied_evaluation->student_id)
                                                @php
                                                    $isEvaluated = true;
                                                @endphp
                                            @endif
                                        @endforeach
                                        @if ($isEvaluated)
                                            <span class="text-success og-evaluation">{{ __('admin.Evaluated') }}</span>
                                            <span class="text-success evaluation-check"><i class="fas fa-check"></i></span>
                                        @else
                                            <span class="text-danger og-evaluation">{{ __('admin.Not Evaluated yet') }}</span>
                                            <span class="text-danger evaluation-check"><i class="fas fa-times"></i></span>

                                        @endif
                                    </td> --}}
                                    {{-- @canAny(['evaluate_student', 'evaluation_student', 'delete_student']) --}}
                                    {{-- <td>
                                        <div>
                                           @canAny(['evaluate_student', 'evaluation_student'])
                                           @if ($isEvaluated)
                                           @can('evaluation_student')

                                           <a title="{{ __('admin.Evaluation') }}" href="{{ route('admin.show_evaluation', $student->slug) }}"
                                            class="btn btn-info btn-sm btn-flat" data-disabled="true"
                                            title="show evaluation">{{ __('admin.Evaluation') }}</a>
                                            @endcan
                                            @else
                                            @can('evaluate_student')
                                           <a title="{{ __('admin.Evaluate') }}" href="{{ route('admin.students.show', $student->slug) }}"
                                               class="btn btn-sm btn-flat btn-outline-secondary btn-flat" data-disabled="true"
                                               title="evaluate">{{ __('admin.Evaluate') }}</a>
                                               @endcan
                                               @endif
                                           @endcanAny
                                           @can('delete_student')
                                           @if (Auth::guard('company')->check())
                                           <form class="d-inline delete_form"
                                               action="{{ route('admin.students.delete.from.company', $student->slug) }}"
                                               method="POST">
                                               @csrf
                                               @method('delete')
                                               <button title="{{ __('admin.Delete') }}" class="btn btn-danger btn-sm btn-delete btn-flat"> <i
                                                       class="fas fa-trash"></i> </button>
                                           </form>
                                       @else
                                           <form class="d-inline delete_form"
                                               action="{{ route('admin.students.destroy', $student->slug) }}"method="POST">
                                               @csrf
                                               @method('delete')
                                               <button title="{{ __('admin.Move to recycle bin') }}" class="btn btn-danger btn-sm btn-delete btn-flat"> <i
                                                       class="fas fa-trash"></i> </button>
                                           </form>
                                       @endif
                                           @endcan
                                        </div>
                                    </td> --}}
                                {{-- @endcanAny --}}
                                @canAny(['more_about_student','delete_student'])

                                <td>
                                    @can('delete_student')
                                        @if (Auth::guard('company')->check())
                                            <form class="d-inline delete_form"
                                                action="{{ route('admin.students.delete.from.company', $student->slug) }}"
                                                method="POST">
                                                @csrf
                                                @method('delete')
                                                <button title="{{ __('admin.Delete') }}"
                                                    class="btn btn-danger btn-sm btn-delete btn-flat"> <i
                                                        class="fas fa-trash"></i> </button>
                                            </form>
                                        @else
                                            <form class="d-inline delete_form"
                                                action="{{ route('admin.students.destroy', $student->slug) }}"method="POST">
                                                @csrf
                                                @method('delete')
                                                <button title="{{ __('admin.Move to recycle bin') }}"
                                                    class="btn btn-danger btn-sm btn-delete btn-flat"> <i
                                                        class="fas fa-trash"></i> </button>
                                            </form>
                                        @endif
                                    @endcan
                                    @can('more_about_student')
                                    <a href="{{ route('admin.student.informations', $student->slug) }}" title="{{ __('admin.more about') }} {{ $student->name }}" class="btn btn-sm btn-primary btn-flat"><i class="fas fa-info"></i></a>
                                    @endcan
                                </td>
                                @endcanAny
                            </tr>
                            @empty
                                <td colspan="12" style="text-align: center">
                                    <img src="{{ asset('adminAssets/dist/img/folder.png') }}" alt=""
                                        width="300">
                                    <br>
                                    <h4>{{ __('admin.NO Data Selected') }}</h4>
                                </td>
                        </tbody>
                        @endforelse


                    </table>
                </div>
                <!-- /.card-body -->

            </div>
            <!-- /.card -->
            <div class="mb-3 myPaginate">
                {{ $students->appends($_GET)->links() }}
            </div>
        </div>
    </div>




@stop

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>


    <script>
        $(document).ready(function() {
            let input = $("#search_input");
            $("#students_names").hide();
            input.on("keyup", function() {
                let search = input.val();

                $.ajax({
                    type: "get",
                    url: 'search/students',
                    data: {
                        search: search
                    },
                    success: function(response) {
                        if (response.message) {
                            $("#students_names").show();
                            $("#students_names").empty();
                            let msg =
                                `<p style="padding: 10px;">there is no result for<b><i>${search}</i></b></p>`;
                            $("#students_names").append(msg);
                        } else {
                            $("#students_names").empty();
                            $.each(response.students, function(key, student) {
                                let row =
                                    `<li><a href="#" id="dropdown_item" data-name="${student}">${student}</a></li>`
                                $("#students_names").show();
                                $("#students_names").append(row);
                            });

                        }
                    }
                })
            });
        });

        $(document).on("click", "#dropdown_item", function(event) {
            event.preventDefault()
            let name = $(this).data("name");
            $("#search_input").val(name);
            $("#students_names").hide();
            $("#search_form").submit();


        })
    </script>
@stop
