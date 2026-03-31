<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error {{ $exception?->getStatusCode() ?? 500 }}</title>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="https://use.fontawesome.com/releases/v5.7.0/css/all.css"
        integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous"
        rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>

<body>
    <div class="d-flex align-items-center justify-content-center min-vh-100 p-3">
        <div class="text-center">
            <div class="mb-3">
                <i class="fas fa-exclamation-circle text-white" style="font-size: 3rem; opacity: 0.7;"></i>
            </div>
            <h1 class="text-white mb-2 font-weight-bold">
                {{ $exception?->getStatusCode() ?? 500 }}
            </h1>
            <h2 class="text-white mb-3">
                @switch($exception?->getStatusCode() ?? 500)
                    @case(400)
                        Invalid Request
                    @break

                    @case(401)
                        Unauthorized Access
                    @break

                    @case(403)
                        Access Denied
                    @break

                    @case(404)
                        Page Not Found
                    @break

                    @case(419)
                        Session Expired
                    @break

                    @case(429)
                        Too Many Requests
                    @break

                    @case(503)
                        Service Unavailable
                    @break

                    @case(500)

                    @default
                        Server Error
                @endswitch
            </h2>
            <p class="text-white mb-4">
                @switch($exception?->getStatusCode() ?? 500)
                    @case(404)
                        It seems this page does not exist or the link is broken. <br> You can return to the homepage or go back to the previous page.
                    @break

                    @default
                        An unexpected error occurred. Please try again later.
                @endswitch
            </p>
            <a href="{{ url('/') }}" class="btn btn-sm btn-danger">
                <i class="fas fa-arrow-left mr-2"></i>Back to Home
            </a>
        </div>
    </div>
</body>

</html>
