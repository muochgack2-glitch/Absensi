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
        /* Theme Colors (Dynamic) */
        --primary: {{ $themePrimary }} !important;
        --secondary: {{ $themeSecondary }} !important;
        --theme-primary: {{ $themePrimary }} !important;
        --theme-secondary: {{ $themeSecondary }} !important;

        /* Modern Color Palette - Inspired by eRapor8 & Tailwind */
        
        /* Gray Scale */
        --gray-50: #f8fafc;
        --gray-100: #f1f5f9;
        --gray-200: #e2e8f0;
        --gray-300: #cbd5e1;
        --gray-400: #94a3b8;
        --gray-500: #64748b;
        --gray-600: #475569;
        --gray-700: #334155;
        --gray-800: #1e293b;
        --gray-900: #0f172a;

        /* Blue */
        --blue-50: #eff6ff;
        --blue-100: #dbeafe;
        --blue-200: #bfdbfe;
        --blue-500: #3b82f6;
        --blue-600: #2563eb;
        --blue-700: #1d4ed8;

        /* Green */
        --green-50: #f0fdf4;
        --green-100: #dcfce7;
        --green-500: #10b981;
        --green-600: #059669;
        --green-700: #047857;

        /* Yellow */
        --yellow-50: #fefce8;
        --yellow-100: #fef3c7;
        --yellow-500: #f59e0b;
        --yellow-600: #d97706;
        --yellow-700: #b45309;

        /* Red */
        --red-50: #fef2f2;
        --red-100: #fee2e2;
        --red-500: #ef4444;
        --red-600: #dc2626;
        --red-700: #b91c1c;

        /* Semantic Colors */
        --success: var(--green-500);
        --success-light: var(--green-100);
        --success-dark: var(--green-700);
        
        --warning: var(--yellow-500);
        --warning-light: var(--yellow-100);
        --warning-dark: var(--yellow-700);
        
        --danger: var(--red-500);
        --danger-light: var(--red-100);
        --danger-dark: var(--red-700);
        
        --info: var(--blue-500);
        --info-light: var(--blue-100);
        --info-dark: var(--blue-700);

        /* Background Colors */
        --bg-primary: #ffffff;
        --bg-secondary: var(--gray-50);
        --bg-tertiary: var(--gray-100);

        /* Text Colors */
        --text-primary: var(--gray-900);
        --text-secondary: var(--gray-600);
        --text-tertiary: var(--gray-500);
        --text-muted: var(--gray-400);

        /* Border Colors */
        --border-light: var(--gray-200);
        --border-medium: var(--gray-300);
        --border-dark: var(--gray-400);

        /* Spacing Scale (8px base) */
        --space-1: 0.25rem;  /* 4px */
        --space-2: 0.5rem;   /* 8px */
        --space-3: 0.75rem;  /* 12px */
        --space-4: 1rem;     /* 16px */
        --space-5: 1.25rem;  /* 20px */
        --space-6: 1.5rem;   /* 24px */
        --space-8: 2rem;     /* 32px */
        --space-10: 2.5rem;  /* 40px */
        --space-12: 3rem;    /* 48px */
        --space-16: 4rem;    /* 64px */

        /* Border Radius */
        --radius-sm: 0.375rem;   /* 6px */
        --radius-md: 0.5rem;     /* 8px */
        --radius-lg: 0.75rem;    /* 12px */
        --radius-xl: 1rem;       /* 16px */
        --radius-2xl: 1.5rem;    /* 24px */
        --radius-full: 9999px;

        /* Shadows */
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);

        /* Typography */
        --font-sans: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        --font-mono: 'Courier New', monospace;

        /* Font Sizes */
        --text-xs: 0.75rem;      /* 12px */
        --text-sm: 0.875rem;     /* 14px */
        --text-base: 1rem;       /* 16px */
        --text-lg: 1.125rem;     /* 18px */
        --text-xl: 1.25rem;      /* 20px */
        --text-2xl: 1.5rem;      /* 24px */
        --text-3xl: 1.875rem;    /* 30px */
        --text-4xl: 2.25rem;     /* 36px */

        /* Font Weights */
        --font-normal: 400;
        --font-medium: 500;
        --font-semibold: 600;
        --font-bold: 700;
        --font-extrabold: 800;

        /* Transitions */
        --transition-fast: 150ms cubic-bezier(0.4, 0, 0.2, 1);
        --transition-base: 300ms cubic-bezier(0.4, 0, 0.2, 1);
        --transition-slow: 500ms cubic-bezier(0.4, 0, 0.2, 1);

        /* Z-Index Scale */
        --z-dropdown: 1000;
        --z-sticky: 1020;
        --z-fixed: 1030;
        --z-modal-backdrop: 1040;
        --z-modal: 1050;
        --z-popover: 1060;
        --z-tooltip: 1070;
    }

    /* Dark Mode Support (Optional) */
    .admin-dark,
    [data-theme="dark"] {
        --bg-primary: var(--gray-900);
        --bg-secondary: var(--gray-800);
        --bg-tertiary: var(--gray-700);
        
        --text-primary: var(--gray-50);
        --text-secondary: var(--gray-300);
        --text-tertiary: var(--gray-400);
        --text-muted: var(--gray-500);
        
        --border-light: var(--gray-700);
        --border-medium: var(--gray-600);
        --border-dark: var(--gray-500);
    }
</style>
