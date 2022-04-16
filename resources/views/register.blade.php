<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{settings()->get('application_name')}}</title>
        <link rel="stylesheet" type="text/css" href="{{ mix('css/app.css') }}">
    </head>
    <body>
        <div class="container">
            <div class="container-main">
                <div class="login__wrap">
                    <div class="login__content">
                        <div class="panel">
                            <div class="panel-heading">
                                <div class="login__brand">
                                    <img src="{{asset('/images/flow.png')}}" class="login__logo">
                                </div>
                            </div>
                            <div class="panel-body">
                                <form class="login__form" action="{{url('/invite/'.$item->token)}}" method="POST">
                                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                                    <div class="form-group">
                                        <label class="form-label">{{__('lang.name')}}</label>
                                        <input type="text" name="name" value="{{old('name', $item->name)}}" class="form-input">
                                        @if($errors->has('name'))
                                            <small class="error-control">{{$errors->first('name')}}</small>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">{{__('lang.email')}}</label>
                                        <input type="text" name="email" value="{{old('email', $item->email)}}" class="form-input" disabled>
                                        @if($errors->has('email'))
                                            <small class="error-control">{{$errors->first('email')}}</small>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">{{__('lang.password')}}</label>
                                        <input type="password" name="password" class="form-input">
                                        @if($errors->has('password'))
                                            <small class="error-control">{{$errors->first('password')}}</small>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">{{__('lang.password_confirmation')}}</label>
                                        <input type="password" name="password_confirmation" class="form-input">
                                        @if($errors->has('password_confirmation'))
                                            <small class="error-control">{{$errors->first('password_confirmation')}}</small>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <input type="submit" value="{{__('lang.register')}}" class="btn btn-primary">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
