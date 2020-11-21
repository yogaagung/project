<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found</title>
    <!-- CSS -->
    <link rel="stylesheet" href="/css/all.css">
    <script>
        (function() {
            window.Laravel = {
                csrfToken: '{{ csrf_token() }}'
            };
        })();
    </script>

</head>

<body>
    <div>
        <div class="container">
            <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
                <h1 class="_text_center">You Don't Have Enough Permission To Access</h1>
            </div>
        </div>
    </div>
</body>

</html>