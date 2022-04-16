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
                            <div class="panel-body">
                                <div class="payment-receipt">
                                    <div class="payment-receipt-icon payment-receipt-error">
                                        <span class="icon icon-close-circled"></span>
                                    </div>
                                    <h1>Payment Error</h1>
                                    <p>Your transaction could not be processed.</p>
                                    <p>
                                        <strong>Please try again.</strong>
                                     If the problem persists, please contact us.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
