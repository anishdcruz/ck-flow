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
                <div class="payment-request">
                    <div class="payment-request-inner">
                        <div class="panel">
                            <div class="panel-heading">
                            	<div class="row">
                            		<div class="col-8">
                            			<div class="login__brand">
                            			    <img src="{{asset('/images/flow.png')}}" class="login__logo">
                            			</div>
                            		</div>
                            		<div class="col-8 col-offset-8">
                            			<div class="pay-now-wrap">
                            				@include($config['gateway'], ['pay' => $pay, 'config' => $config])
                            			</div>
                            		</div>
                            	</div>

                            </div>
                            <div class="panel-body">
                            	<div class="template-preview">
                            		<object data="/payment-requests/{{$pay->uuid}}/invoice" type="application/pdf" width="100%" height="100%">
                            		</object>
                            	</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
