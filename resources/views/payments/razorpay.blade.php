<form action="/payment-requests/{{$pay->uuid}}" method="POST">
    {{ csrf_field() }}
<script
    src="https://checkout.razorpay.com/v1/checkout.js"
    data-key="rzp_test_oDZY1sdmq77Qc7"
    data-amount="{{getStripeAmount($pay->invoice->balance)}}"
    data-buttontext="{{__('lang.pay_now')}}"
    data-name="{{$config['title']}}"
    data-description="{{$config['description']}}"
    data-image="{{$config['logo_url']}}"
></script>
<input type="hidden" value="Hidden Element" name="hidden">
</form>