@extends('admin.master')

@section('title', Auth::guard()->user()->name . ' -'.  __('admin.Edit Password'))
@section('sub-title', __('admin.Edit Password'))
@section('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">

@stop
@section('content')

    <div class="box-all   ">
        <form action="{{ route('update-password' ) }}" class="update_form" method="POST">
            @csrf
        <div class="row ">

            <div class="   col-md-12 ">
                <div class="p-3 bg-white rounded shadow  mb-5">

                    <div class="row mt-3">
                        <div class="alert  d-none" >
                            <ul>
                                <li>

                                </li>

                            </ul>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="labels">{{ __('admin.Current Password') }}</label>
                            <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" placeholder="{{ __('admin.Current Password') }}" value="{{ old('current_password') }}">
                                @error('current_password')
                                        <small class="invalid-feedback"> {{ $message }}</small>
                                @enderror
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="labels">{{ __('admin.New Password') }}</label>
                            <input type="password" name="new_password" class="form-control @error('new_password') is-invalid @enderror" placeholder="{{ __('admin.New Password') }}" value="{{ old('new_password') }}">
                                @error('new_password')
                                        <small class="invalid-feedback"> {{ $message }}</small>
                                @enderror
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="labels">{{ __('admin.New Password Confirmation') }}</label>
                            <input type="password" name="new_password_confirmation" class="form-control @error('new_password_confirmation') is-invalid @enderror" placeholder="{{ __('admin.New Password Confirmation') }}" value="{{ old('new_password_confirmation') }}">
                                @error('new_password_confirmation')
                                        <small class="invalid-feedback"> {{ $message }}</small>
                                @enderror
                        </div>

                    </div>


                    <div class="mt-3 d-flex justify-content-end">
                        <button class="btn btn-primary profile-button btn-flat"  type="submit"> {{ __('admin.Save Edit') }} </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    </div>

@stop

@section('scripts')
    <script>
        let form = $(".update_form");
        let btn = $(".profile-button");

        form.onsubmit = (e) => {
            e.preventDefault();
        }



        btn.on("click", function() {
            btn.attr('disabled', true);
            let url = form.attr('action');
            let data = form.serialize();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });


            $.ajax({
                type: "POST",
                url: url,
                data: data,
                beforeSend: function(data) {
                    btn.addClass("btn-circle");
                    btn.html('<i class="fa fa-spin fa-spinner "></i>');
                } ,
                success: function(data) {
                    $('.invalid-feedback').remove();
                    $('input').removeClass('is-invalid');
                    setTimeout(() => {
                        btn.html('<i class="fas fa-check"></i>');
                        $('input').val('');
                        $('.alert ul li').html('');
                        $('.alert').removeClass('d-none').removeClass('alert-danger').addClass('alert-success');
                        $('.alert ul li').append('{{ __('admin.Password changed successfully') }}')
                    }, 1000);

                    setTimeout(() => {
                        btn.removeClass("btn-circle");
                        btn.removeAttr("disabled");
                        btn.html('{{ __('admin.Save Edit') }}');

                    }, 2000);

                    setTimeout(() => {
                        $('.alert').addClass('d-none');
                    }, 10000);


                },
                error: function(data) {
                    btn.attr("disabled", false)
                    btn.removeClass('btn-circle')
                    btn.html('{{ __('admin.Save Edit') }}');

                    $('.invalid-feedback').remove();

                    if(data.responseJSON.title){
                        $('.alert ul li').val('')
                        $('input').val('')
                        $('.alert ul li').html('');
                        $('.alert').removeClass('d-none').removeClass('alert-success').addClass('alert-danger')
                        $('.alert ul li').append(data.responseJSON.title)
                    }else{
                        $.each(data.responseJSON.errors, function(field, error) {
                            $("input[name='" + field + "']").addClass('is-invalid').after(
                                '<small class="invalid-feedback">' + error + '</small>');
                        });
                    }

                },
            })
        })
    </script>
@stop