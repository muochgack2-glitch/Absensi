# Fix: Nama Jaringan Case-Insensitive Grouping

## Problem
Nama jaringan yang sama tetapi berbeda huruf besar/kecil dihitung sebagai data terpisah di statistik.

**Contoh:**
- "SMP Negeri 1" 
- "smp negeri 1"
- "SMP NEGERI 1"

Ketiga nama di atas seharusnya dihitung sebagai 1 jaringan yang sama, tetapi sistem menghitungnya sebagai 3 jaringan berbeda.

## Root Cause
Grouping menggunakan nilai asli `nama_jaringan` tanpa normalisasi case:
```php
->groupBy(fn($p) => $p->nama_jaringan ?: '(Langsung)')
```

## Solution
Normalisasi semua nama jaringan ke UPPERCASE dan trim whitespace sebelum grouping:
```php
->groupBy(fn($p) => strtoupper(trim($p->nama_jaringan ?: '(Langsung)')))
```

## Files Modified

### 1. routes/web.php (Line 45-46)
**Dashboard statistics query** - menggunakan SQL `UPPER(TRIM())` dengan `groupByRaw()`

```php
$perJaringanDashboard = \App\Models\Pendaftar::query()
    ->selectRaw("COALESCE(NULLIF(TRIM(UPPER(nama_jaringan)), ''), '(Langsung)') as nama_jaringan, COUNT(*) as total")
    ->groupByRaw("UPPER(TRIM(COALESCE(nama_jaringan, '')))")
    ->orderByDesc('total')
    ->take(8)
    ->get();
```

### 2. app/Http/Controllers/ReportController.php

#### Location 1: Line 45 - `index()` method
**Laporan statistik per jaringan**

```php
$perJaringan = $pendaftars
    ->groupBy(fn($p) => strtoupper(trim($p->nama_jaringan ?: '(Langsung)')))
    ->map(function ($group, $nama) use ($jurusanAktif) {
        // ... mapping logic
    })
    ->sortByDesc('total')
    ->values();
```

#### Location 2: Line 217 - `exportJaringanExcel()` method
**Export Excel rekap per jaringan**

```php
$perJaringan = $pendaftars
    ->groupBy(fn($p) => strtoupper(trim($p->nama_jaringan ?: '(Langsung)')))
    ->map(function ($group, $nama) use ($jurusanAktif) {
        // ... mapping logic for CSV export
    })
    ->sortByDesc(fn($r) => $r[1])
    ->values();
```

#### Location 3: Line 284 - `exportPdf()` method
**Export PDF laporan per jaringan**

```php
$perJaringan = $pendaftars
    ->groupBy(fn($p) => strtoupper(trim($p->nama_jaringan ?: '(Langsung)')))
    ->map(function ($group, $nama) use ($jurusanAktif) {
        // ... mapping logic for PDF
    })
    ->sortByDesc('total')
    ->values();
```

## Impact

### Before Fix:
```
Dashboard Statistics:
- SMP Negeri 1: 5 pendaftar
- smp negeri 1: 3 pendaftar
- SMP NEGERI 1: 2 pendaftar
Total: 3 jaringan berbeda
```

### After Fix:
```
Dashboard Statistics:
- SMP NEGERI 1: 10 pendaftar
Total: 1 jaringan (merged)
```

## Testing Checklist

- [x] Dashboard statistics - perJaringan grouping
- [x] Laporan page - perJaringan table
- [x] Export Excel - rekap jaringan
- [x] Export PDF - laporan per jaringan

## Deployment Steps

1. **Push to GitHub:**
   ```bash
   git add .
   git commit -m "Fix: Case-insensitive grouping for nama_jaringan in statistics"
   git push origin main
   ```

2. **Pull on aaPanel server:**
   ```bash
   cd /www/wwwroot/spmb
   git pull origin main
   ```

3. **Clear Laravel cache:**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan view:clear
   ```

4. **Verify on browser:**
   - Check dashboard statistics
   - Check laporan page
   - Test Excel export
   - Test PDF export

## Notes

- **SQL queries**: Use `UPPER(TRIM())` with `groupByRaw()`
- **PHP collections**: Use `strtoupper(trim())` in groupBy closure
- **Empty values**: Handled with `?: '(Langsung)'` fallback
- **Display**: Nama ditampilkan dalam UPPERCASE (normalized form)
- **No database changes**: Nilai asli di database tetap tidak berubah

## Date
May 31, 2026
