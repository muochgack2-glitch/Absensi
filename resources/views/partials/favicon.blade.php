@php
    $settingFavicon = \App\Models\SettingSystem::first()?->favicon;
    $faviconUrl = !empty($settingFavicon)
        ? asset('storage/' . $settingFavicon) . '?v=' . md5($settingFavicon)
        : asset('favicon.ico');
@endphp
<link rel="icon" href="{{ $faviconUrl }}">
<link rel="shortcut icon" href="{{ $faviconUrl }}">
<link rel="apple-touch-icon" href="{{ $faviconUrl }}">
