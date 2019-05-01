<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }

            .button {
                background-color: #4CAF50; /* Green */
                border: none;
                color: white;
                padding: 10px 44px;
                text-align: center;
                text-decoration: none;
                display: inline-block;
                font-size: 32px;
                border-radius: 12px;
            }

            #pwform {
                display: flex;
                flex-direction: column;
                margin-bottom: 20px;
            }

            #pwform > div {
                width: 50%;
                margin: 0 auto;
            }

            #pwform > div > div {
                text-align: right;
            }

            #pwform > div > div:first-child {
                margin-bottom: 10px;
            }

            #pwform > div > div > span {
                font-size: 32px;
            }

            input {
                width: 160px;
                height: 40px;
                font-size: 32px;
                border-radius: 12px;
                border: 1px solid #4CAF50;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                    @auth
                        <a href="{{ url('/home') }}">Home</a>
                    @else
                        <a href="{{ route('login') }}">Login</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}">Register</a>
                        @endif
                    @endauth
                </div>
            @endif

            <div class="content">
                <div class="title m-b-md">
                    Set your password
                </div>

                <div class="links">
                    <form method="POST" id="pwform" action="/user/{{ session('user_id') }}">
                        @csrf()
                        @method('PATCH')
                        <div>
                            <div>
                                <span>密碼：</span>
                                <input name="pw" type="password" maxlength="4">
                            </div>
                            <div>
                                <span>確認密碼：</span>
                                <input name="pw_confirmation" type="password" maxlength="4">
                            </div>
                        </div>
                    </form>
                    <button class="button" type="submit" form="pwform" value="Submit">Set</button>
                </div>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        {{ $errors->first() }}
                    </div>
                @endif
            </div>
        </div>
    </body>
</html>
