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
                                    <div class="payment-receipt-icon payment-receipt-success">
                                        <span class="icon icon-checkmark-circled"></span>
                                    </div>
                                    <h1>Payment Received</h1>
                                    <p>Thank You for the Payment!</p>
                                    <p>Your payment has been processed successfully .</p>
                                    <p>The details of this payment will be sent to your email address.</p>
                                    <small>If you have any questions about payment, please contact us.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
