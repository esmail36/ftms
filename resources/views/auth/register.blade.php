<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} | {{ __('admin.Create a new account') }}</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('adminAssets/dist/img/selection/favicon.ico') }}">

    <link rel="stylesheet" href="{{ asset('adminAssets/loginAssets/assets/css/bootstrap-grid.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminAssets/loginAssets/assets/css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
    @if(app()->getLocale() == 'ar')
    <link rel="stylesheet" href="{{ asset('adminAssets/loginAssets/assets/css/style-ar.css') }}">


    @endif
    <style>
        .bg{
            position: absolute;
            height: 100%;
            width: 100%;
            background: url('{{ asset('adminAssets/dist/img/selection/bg.png') }}') no-repeat center center;
            background-size: cover !important;

        }
        .p{
            font-size: 12px;
            font-weight: 600
        }
        .p span{
            color: #ff0000;
            font-size: 14px
        }

      </style>



</head>

<body>
    @php
    $data = json_decode(File::get(storage_path('app/settings.json')), true);
    @endphp
    <div class="bg">
        <div class="overlay">
            <div class="signin">
                <div class="logo"><a href="{{ route('website.home') }}"><img src="{{ asset($data['darkLogo']) }}"  style="width: 170px" alt=""></a></div>
                </div>
                <div class="signin-form register">
                    <div class="row">
                        <form method="POST" action="{{ route('student.register', $subsicribe->student_id ) }}">
                            @csrf
                            <h3 >{{ __('admin.Create a new account') }}</h3>
                            <div class="row">

                                        {{-- name  --}}
                                <div class="col-md-6">
                                    <div class="mb-3 form-group">
                                        <input type="name" class="form-control" value="{{ $subsicribe->name }}" disabled placeholder="{{ __('admin.Name') }}">
                                    </div>
                                </div>

                                {{-- Student ID  --}}
                                <div class="col-md-6">

                                    <div class="mb-3 form-group">
                                        <input type="text" class="form-control" value="{{ $subsicribe->student_id }}" placeholder="{{ __('admin.Student ID') }}" disabled>
                                    </div>
                                </div>
                                {{-- Student ID  --}}
                                <div class="col-md-6">
                                    <div class="mb-3 form-group">
                                        <input type="text" class="form-control" value="{{ $university->name }}" placeholder="{{ __('admin.Student ID') }}" disabled>
                                    </div>
                                </div>

                                 {{-- Student ID  --}}
                                 <div class="col-md-6">
                                    <div class="mb-3 form-group">
                                        <input type="text" class="form-control" value="{{ $specialization->name }}" placeholder="{{ __('admin.Student ID') }}" disabled>
                                    </div>
                                </div>

                                {{-- email  --}}
                                <div class="col-md-6">
                                    <div class="mb-3 form-group">
                                        <input type="email" class="form-control @error('email') error @enderror"
                                            name="email" value="{{ old('email') }}" placeholder="{{ __('admin.Email') }}">
                                        @error('email')
                                            <small>{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                {{-- phone  --}}
                                <div class="col-md-6">
                                    <div class="mb-3 form-group">
                                        <input type="text" class="form-control @error('phone') error @enderror"
                                            name="phone" value="{{ old('phone') }}" placeholder="{{ __('admin.Phone') }}">
                                        @error('phone')
                                            <small>{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>




                                {{-- password --}}
                                <div class="col-md-6">
                                    <div class="mb-3 form-group pass">
                                        <input type="password" id="password"
                                            class="form-control @error('password') error @enderror" name="password"
                                            placeholder="{{ __('admin.Password') }}">
                                        @error('password')
                                            <small>{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Confirm Password  --}}
                                <div class="col-md-6">
                                    <div class="mb-3 form-group">
                                        <input  type="password" id="confirm_password" class="form-control @error('password') error @enderror" name="password_confirmation" placeholder="{{ __('admin.Password Confirmation') }}">
                                        @error('password_confirmation')
                                            <small>{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <div class="btn-web">
                                    <button type="submit" class="btn btn-primary bold ">{{ __('admin.Register') }}</button>

                                </div>

                        </form>
                        <div class="account">

                            <p> <a href="{{ route('student.login.show') }}"> {{ __('admin.Do you have account ?') }} </a></p>
                        </div>
                    </div>



                </div>
            </div>
            <div class="bottom-bg">

            </div>
        </div>
    </div>



    <!--Header ends-->
    <script src="https://code.jquery.com/jquery-3.6.3.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>



    {{-- Ajax Request --}}
    <script>

        // Confirm Password input Check

        var password = document.querySelector("#password");
        var confirm_password = document.querySelector("#confirm_password");
        // confirm_password.addClass("");
        password.onkeyup = () => {
            if (confirm_password.value !== password.value) {
                confirm_password.classList.add("error");
                confirm_password.classList.remove("is-valid");
                confirm_password.classList.remove("border-success");
            } else {
                confirm_password.classList.remove("error");
                confirm_password.classList.add("is-valid");
                confirm_password.classList.add("border-success");
            }
        }
        confirm_password.onkeyup = () => {
            if (confirm_password.value !== password.value) {
                confirm_password.classList.add("error");
                confirm_password.classList.remove("is-valid");
                confirm_password.classList.remove("border-success");
            } else {
                confirm_password.classList.remove("error");
                confirm_password.classList.add("is-valid");
                confirm_password.classList.add("border-success");
            }
        }


    </script>


</body>

</html>
