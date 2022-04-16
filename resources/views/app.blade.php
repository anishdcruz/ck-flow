<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{settings()->get('application_name')}}</title>
        <link rel="stylesheet" type="text/css" href="{{mix('css/app.css')}}">
    </head>
    <body>
        <div id="root"></div>
    </body>
    <script type="text/javascript" src="{{lang()}}"></script>
    <script>
        window.FLOW = {
            application: {
                date_format: "{{settings()->get('application_date_format')}}"
            },
            currency: {
                code: "{{settings()->get('currency_code')}}",
                precision: "{{settings()->get('currency_precision')}}",
                decimal_separator: "{{settings()->get('decimal_separator')}}",
                thousands_separator: "{{settings()->get('thousands_separator')}}",
                placement: "{{settings()->get('placement')}}"
            },
            links: @json(auth()->user()->topLinks()),
            user: {
                id: {{auth()->id()}},
                name: "{{auth()->user()->name}}"
            }
        };
    </script>
    <script type="text/javascript" src="{{mix('js/app.js')}}"></script>
</html>
