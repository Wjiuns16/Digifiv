<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name') }}</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    
    @vite(['resources/css/app.css'])

</head>

<body>
    <div id="swagger-api"></div>

    <script src="{{ asset('vendor/swagger/swagger-ui-bundle.js') }}" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function (){
            window.onload = () => {
                window.ui = SwaggerUIBundle({
                    url: "{{ env('APP_URL') }}" + '/docs/openapi.json',
                    dom_id: '#swagger-api',
                });
            };
        });
    </script>
</body>

</html>