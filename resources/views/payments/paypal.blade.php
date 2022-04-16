<div id="paypal-button"></div>
<form action="/payment-requests/{{$pay->uuid}}" method="POST" id="paypalform">
  {{ csrf_field() }}
  <input type="hidden" name="paymentID" id="paymentID">
  <input type="hidden" name="payerID" id="payerID">
</form>
<script src="https://www.paypalobjects.com/api/checkout.js"></script>
<script>
  paypal.Button.render({
    // Configure environment
    env: '{{$config['paypal_mode']}}',
    client: {
      @if($config['paypal_mode'] =='sandbox')
      sandbox: '{{$config['sandbox_client_id']}}',
      @elseif($config['paypal_mode'] =='production')
      production: '{{$config['production_client_id']}}'
      @endif
    },
    // Customize button (optional)
    locale: '{{$config['paypal_locale']}}',
    style: {
      size: '{{$config['paypal_size']}}',
      color: '{{$config['paypal_color']}}',
      shape: '{{$config['paypal_shape']}}',
    },
    // Set up a payment
    payment: function(data, actions) {
      return actions.payment.create({
        payment: {transactions: [{
          amount: {
            total: '{{$pay->invoice->balance}}',
            currency: '{{$config['currency']}}'
          }
        }]},
        experience: {
          input_fields: {
            no_shipping: 1
          }
        }
      });
    },
    // Execute the payment
    onAuthorize: function(data, actions) {
      return actions.payment.execute()
      .then(function () {
        document.getElementById('paymentID').value = data.paymentID;
        document.getElementById('payerID').value = data.payerID;
        document.getElementById('paypalform').submit();
      });
    }
  }, '#paypal-button');

</script>