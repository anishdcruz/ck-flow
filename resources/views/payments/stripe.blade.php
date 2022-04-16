<form action="/payment-requests/{{$pay->uuid}}" method="POST">
	{{ csrf_field() }}
  <script
    src="https://checkout.stripe.com/checkout.js" class="stripe-button"
    data-key="{{$config['key']}}"
    data-amount="{{getStripeAmount($pay->invoice->balance)}}"
    data-name="{{$config['title']}}"
    data-description="{{$config['description']}}"
    data-image="{{$config['logo_url']}}"
    data-locale="auto"
    data-currency="{{$config['currency']}}">
  </script>
</form>