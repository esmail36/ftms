@extends('student.master')

@section('title', $student->name)
@section('sub-title', 'Profile')

@section('content')
    <section class="bg-light" id="reviews">
        <div class="container">

        </div>
    </section>
    <div class="bg-light">
        <div class="container ">
            <div class="box-all  ">
                @if(Auth::user()->slug == $student->slug)
                <form action="{{ route('student.profile_edit', $student->slug) }}" method="POST" enctype="multipart/form-data"
                    class="update_form">
                    @csrf
                    @method('PUT')
                    @endif
                    <div class="row  ">
                        <div class="col-md-4 mt-5 ">
                            <div class="info bg-white shadow  rounded mr-3  alig-content-center">
                                <div class="d-flex flex-column align-items-center text-center p-2 py-2">


                                    @php

                                        if ($student->image) {
                                            $img = $student->image;
                                            $src = asset($img);
                                        } else {
                                            $src = asset('adminAssets/dist/img/no-image.png');
                                        }

                                    @endphp


                                    <div class="kt-avatar kt-avatar--outline kt-avatar--circle" id="kt_user_avatar_3">
                                        <div class="kt-avatar__holder" style="background-image: url({{ $src }})">
                                        </div>
                                        @if(Auth::user()->slug == $student->slug)
                                        <label class="kt-avatar__upload" data-toggle="kt-tooltip" title="Change avatar">
                                            <i class='bx bxs-pencil'></i>
                                            <input type="file" class="img" name="image" id="image">
                                        </label>
                                        @endif
                                    </div>



                                    <span class="font-weight-bold mt-3 mb-3" id="primary_name">{{ $student->name }}</span>
                                    @if(Auth::user()->slug == $student->slug)
                                    <span class="text-black-50 mb-3" id="primary_email"
                                        >{{ $student->email }}</span>
                                        @endif
                                    <span>
                                    </span>
                                    <div class="alert  alert-success d-none">
                                        <ul>
                                            <li>

                                            </li>

                                        </ul>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class=" col-md-8 mt-5 ">
                            <div class="p-3 bg-white shadow rounded mb-5">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                </div>
                                <div class="row mt-3">

                                    <div class="col-md-6 mb-3">
                                        <label class="labels">{{ __('admin.Name') }}</label>
                                        <input type="text" disabled id="name" class="form-control "
                                            placeholder="{{ __('admin.Name') }}" value="{{ $student->name }}">

                                    </div>

                                    @if(Auth::user()->slug == $student->slug)
                                    <div class="col-md-6 mb-3">
                                        <label class="labels">{{ __('admin.Student ID') }}</label>
                                        <input type="text" name="" class="form-control " disabled
                                            value="{{ $student->student_id }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="labels">{{ __('admin.Email') }}</label>
                                        <input type="text" name="email" id="email"
                                            class="form-control @error('name') is-invalid @enderror"
                                            value="{{ $student->email }}" placeholder="{{ __('admin.Email') }}"
                                            >
                                        @error('name')
                                            <small class="invalid-feedback"> {{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="labels">{{ __('admin.Phone') }}</label>
                                        <input type="text" name="phone" id="phone"
                                            class="form-control @error('name') is-invalid @enderror"
                                            placeholder="{{ __('admin.Phone') }}" value="{{ $student->phone }}">
                                        @error('name')
                                            <small class="invalid-feedback"> {{ $message }}</small>
                                        @enderror
                                    </div>


                                    @endif


                                    <div class="col-md-6 mb-3">
                                        <label class="labels">{{ __('admin.University') }}</label>
                                        <input type="text" name="" class="form-control " disabled
                                            value="{{ $student->university->name }}"
                                            >
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="labels">{{ __('admin.Specialization') }}</label>
                                        <input type="text" name="" class="form-control " disabled
                                            value="{{ $student->specialization->name }}"
                                            >
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="labels">{{ __('admin.Your Teacher') }}</label>
                                        <input type="text" name="" class="form-control " disabled
                                            value="{{ $student->teacher->name ? $student->teacher->name : 'No teacher yet' }}"
                                            >
                                    </div>
                                    @if ($student->company_id)
                                        <div class="col-md-6 mb-3">
                                            <label class="labels">{{ __('admin.Your Company') }}</label>
                                            <input type="text" name="" class="form-control " disabled
                                                value="{{ $student->company->name }}"
                                                >
                                        </div>
                                    @endif


                                </div>

                                @if(Auth::user()->slug == $student->slug)
                                <div class="mt-2 wrapper-btn text-end">
                                    <button class="btn btn-brand profile-button" type="submit">
                                        {{ __('admin.Save Edit') }}</button>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @if(Auth::user()->slug == $student->slug)
                </form>
                @endif
            </div>






        </div>
    </div>
@stop

@if(Auth::user()->slug == $student->slug)

@section('scripts')
<script src="{{ asset('studentAssets/js/profile.js') }}"></script>

<script>
    let authEmail = '{{ Auth::user()->email }}';
    let form2 = $(".update_form")[0];
    let btn = $(".profile-button");
    let image;

    form2.onsubmit = (e) => {
        e.preventDefault();
    }

    $(".img").on("change", function(e) {
        image = e.target.files[0];
    })

    btn.on("click", function() {
        btn.attr('disabled', true);
        let formData = new FormData(form2);
        formData.append('image', image);
        let url = form2.getAttribute("action");
        $.ajax({
            type: "POST",
            url: url,
            data: formData,
            dataType: "json",
            processData: false,
            contentType: false,
            beforeSend: function(data) {
                btn.html('<i class="fa fa-spin fa-spinner "></i>');
                $('.invalid-feedback').remove();
                $('input').removeClass('is-invalid');

            },
            success: function(data) {

                if (data.email != authEmail) {

                    btn.html('<i class="fas fa-check"></i>');
                    toastr.warning('{{ __('admin.Email must be confirmed') }}');


                    setTimeout(() => {

                        Swal.fire({
                            text: '{{ __('admin.We have sent you an activation code to your email, please check your email.') }}',
                            icon: 'warning',
                            confirmButtonText: '{{ __('admin.OK') }}'
                        });
                    }, 2000);

                    setTimeout(() => {
                        btn.removeAttr("disabled");
                        btn.html('{{ __('admin.Save Edit') }}');
                    }, 2000);

                } else {



                    setTimeout(() => {
                        btn.html('<i class="fas fa-check"></i>');
                        toastr.success('{{ __('admin.Profile Updated successfully') }}');

                    }, 2000);

                    setTimeout(() => {
                        btn.removeAttr("disabled");
                        btn.html('{{ __('admin.Save Edit') }}');
                    }, 3500);

                    $("#primary_email").empty();
                    $("#primary_email").append(data.email);
                    if (data.image) {
                        $("#student_img").attr("src", host + "/" + data.image);
                        $("#dropdown_img").attr("src", host + "/" + data.image);
                    }
                }
            },
            error: function(data) {
                btn.attr("disabled", false)
                btn.html('{{ __('admin.Save Edit') }}');
                $('.invalid-feedback').remove();

                $.each(data.responseJSON, function(field, error) {
                    $("input[name='" + field + "']").addClass('is-invalid').after(
                        '<small class="invalid-feedback">' + error + '</small>');
                });


            },
        })
    })
</script>


@stop
@endif
