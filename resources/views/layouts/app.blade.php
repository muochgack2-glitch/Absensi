<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'SPMB - Sistem Penerimaan Murid Baru' }}</title>
    @include('partials.meta')
    @include('partials.theme-vars')
    <link href="{{ asset('css/landing.css') }}" rel="stylesheet">
    <link href="{{ asset('css/landing-modern.css') }}?v={{ time() }}" rel="stylesheet">
</head>
<body>
    @yield('content')
</body>
</html>
