@section('meta')
@php
    $resolvedTitle = $metaTitle ?? $title ?? 'SPMB - Sistem Penerimaan Murid Baru';
    $resolvedDescription = $metaDescription ?? 'Platform online untuk penerimaan murid baru SPMB yang cepat, transparan, dan real-time.';
    $resolvedImage = $metaImage ?? asset('images/og-image.svg');
    $resolvedUrl = url()->current();
    $resolvedKeywords = $metaKeywords ?? 'SPMB, Sistem Penerimaan Murid Baru, pendaftaran murid baru, cek status pendaftaran, penerimaan murid';
@endphp
<meta name="description" content="{{ $resolvedDescription }}">
<meta name="keywords" content="{{ $resolvedKeywords }}">
<meta name="robots" content="index, follow, max-image-preview:large">
<meta name="author" content="{{ $metaAuthor ?? config('app.name', 'SPMB - Sistem Penerimaan Murid Baru') }}">
<meta name="theme-color" content="#111827">
@include('partials.favicon')
<link rel="canonical" href="{{ $resolvedUrl }}">
<meta property="og:locale" content="id_ID">
<meta property="og:site_name" content="{{ $metaSiteName ?? config('app.name', 'SPMB - Sistem Penerimaan Murid Baru') }}">
<meta property="og:title" content="{{ $resolvedTitle }}">
<meta property="og:description" content="{{ $resolvedDescription }}">
<meta property="og:image" content="{{ $resolvedImage }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:url" content="{{ $resolvedUrl }}">
<meta property="og:type" content="website">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $resolvedTitle }}">
<meta name="twitter:description" content="{{ $resolvedDescription }}">
<meta name="twitter:image" content="{{ $resolvedImage }}">
@show
