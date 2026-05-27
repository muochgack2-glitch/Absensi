@php
    $themeSettings = \App\Models\SettingSystem::first();
    $presets = [
        'purple' => ['#667eea', '#764ba2'],
        'blue' => ['#0ea5e9', '#0369a1'],
        'green' => ['#10b981', '#047857'],
        'orange' => ['#f97316', '#c2410c'],
        'red' => ['#ef4444', '#991b1b'],
        'slate' => ['#475569', '#0f172a'],
    ];

    $preset = $themeSettings?->theme_preset ?: 'purple';
    [$presetPrimary, $presetSecondary] = $presets[$preset] ?? $presets['purple'];
    $themePrimary = $themeSettings?->theme_primary ?: $presetPrimary;
    $themeSecondary = $themeSettings?->theme_secondary ?: $presetSecondary;
@endphp
<style>
    :root {
        --primary: {{ $themePrimary }} !important;
        --secondary: {{ $themeSecondary }} !important;
        --theme-primary: {{ $themePrimary }} !important;
        --theme-secondary: {{ $themeSecondary }} !important;
    }
</style>
