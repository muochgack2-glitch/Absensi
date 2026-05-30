# Modern Card & Button Components - Inspired by eRapor8

Koleksi komponen card dan button modern yang reusable untuk aplikasi SPMB.

## 📦 Komponen yang Tersedia

### CARD COMPONENTS

### 1. Card (Base Component)
Komponen card dasar yang fleksibel.

```blade
<x-card>
    Content here
</x-card>

<x-card hover="true" shadow="lg" padding="lg">
    Card with hover effect
</x-card>

<x-card border="true" borderColor="#3b82f6">
    Card with colored border
</x-card>
```

**Props:**
- `hover` (boolean): Enable hover effect
- `shadow` (string): sm, md, lg, xl
- `padding` (string): none, sm, md, lg, xl
- `border` (boolean): Enable left border
- `borderColor` (string): Border color hex code
- `class` (string): Additional CSS classes

---

### 2. Stat Card
Card untuk menampilkan statistik dengan icon dan trend.

```blade
<x-stat-card 
    icon="fas fa-users" 
    label="Total Users" 
    value="1,234" 
    color="blue"
    description="All time"
    trend="+12%"
    :trendUp="true"
/>
```

**Props:**
- `icon` (string): Font Awesome icon class
- `label` (string): Label text
- `value` (string): Main value to display
- `color` (string): blue, green, yellow, red, purple, indigo
- `description` (string): Optional description
- `trend` (string): Trend indicator text
- `trendUp` (boolean|null): true (up), false (down), null (neutral)
- `sparkline` (slot): Optional sparkline chart

**Colors Available:**
- `blue` - Default, for general stats
- `green` - Success, positive metrics
- `yellow` - Warning, pending items
- `red` - Danger, critical items
- `purple` - Special metrics
- `indigo` - Alternative color

---

### 3. Info Card
Card untuk menampilkan informasi, peringatan, atau notifikasi.

```blade
<x-info-card type="info" title="Information" icon="fas fa-info-circle">
    This is an informational message.
</x-info-card>

<x-info-card type="success" title="Success!" dismissible="true">
    Operation completed successfully.
</x-info-card>

<x-info-card type="warning" title="Warning">
    Please review this information carefully.
</x-info-card>

<x-info-card type="danger" title="Error">
    An error occurred. Please try again.
</x-info-card>
```

**Props:**
- `icon` (string): Custom icon (optional, auto-detected by type)
- `title` (string): Card title
- `type` (string): default, info, success, warning, danger
- `dismissible` (boolean): Show close button

---

### 4. Section Card
Card dengan header section untuk mengorganisir konten.

```blade
<x-section-card title="Recent Activity" icon="fas fa-list">
    <x-slot:actions>
        <button class="btn btn-sm btn-primary">View All</button>
    </x-slot:actions>
    
    <p>Your content here</p>
</x-section-card>

<x-section-card title="Live Data" icon="fas fa-chart-line" badge="LIVE">
    Real-time content
</x-section-card>
```

**Props:**
- `title` (string): Section title
- `icon` (string): Font Awesome icon class
- `badge` (string): Badge text (e.g., "LIVE", "NEW")
- `actions` (slot): Action buttons in header
- `padding` (string): sm, md, lg

---

### 5. Empty State
Komponen untuk menampilkan state kosong.

```blade
<x-empty-state 
    icon="fas fa-inbox" 
    message="No data available" 
/>

<x-empty-state 
    icon="fas fa-search" 
    message="No results found"
    description="Try adjusting your search criteria"
>
    <x-slot:action>
        <button class="btn btn-primary">Clear Filters</button>
    </x-slot:action>
</x-empty-state>
```

**Props:**
- `icon` (string): Font Awesome icon class
- `message` (string): Main message
- `description` (string): Optional description
- `action` (slot): Optional action button
- `size` (string): sm, md, lg

---

### 6. Action Card
Card interaktif dengan hover effect untuk navigasi.

```blade
<x-action-card 
    icon="fas fa-plus" 
    title="Add New Student" 
    description="Register a new student"
    href="{{ route('pendaftar.create') }}"
    color="blue"
/>

<x-action-card 
    icon="fas fa-file-export" 
    title="Export Data" 
    description="Download reports"
    href="{{ route('report.export') }}"
    color="green"
/>
```

**Props:**
- `icon` (string): Font Awesome icon class
- `title` (string): Card title
- `description` (string): Card description
- `href` (string): Link URL
- `color` (string): blue, green, purple, orange, red

---

### BUTTON COMPONENTS

### 7. Button
Komponen button modern dengan berbagai variant dan state.

```blade
<x-button>Click Me</x-button>

<x-button variant="primary" size="lg">Large Button</x-button>

<x-button variant="success" icon="fas fa-check">Save</x-button>

<x-button variant="danger" outline="true">Delete</x-button>

<x-button variant="primary" loading="true">Loading...</x-button>

<x-button variant="info" disabled="true">Disabled</x-button>

<x-button variant="primary" block="true">Full Width</x-button>

<x-button variant="success" rounded="true">Rounded</x-button>

<x-button variant="primary" href="/link">Link Button</x-button>
```

**Props:**
- `variant` (string): primary, secondary, success, danger, warning, info, dark, light
- `size` (string): sm, md, lg
- `outline` (boolean): Outline style
- `icon` (string): Font Awesome icon class (left side)
- `iconRight` (string): Font Awesome icon class (right side)
- `loading` (boolean): Show loading spinner
- `disabled` (boolean): Disable button
- `block` (boolean): Full width button
- `rounded` (boolean): Fully rounded corners
- `type` (string): button, submit, reset
- `href` (string): URL for link button
- `class` (string): Additional CSS classes

**Variants Available:**
- `primary` - Main action button (gradient blue/purple)
- `secondary` - Secondary actions (gray)
- `success` - Positive actions (green gradient)
- `danger` - Destructive actions (red gradient)
- `warning` - Warning actions (yellow gradient)
- `info` - Informational actions (blue gradient)
- `dark` - Dark theme button
- `light` - Light theme button

---

### 8. Icon Button
Button dengan hanya icon, tanpa text.

```blade
<x-icon-button icon="fas fa-edit" variant="primary" />

<x-icon-button icon="fas fa-trash" variant="danger" size="sm" />

<x-icon-button icon="fas fa-heart" variant="danger" rounded="true" />

<x-icon-button icon="fas fa-cog" variant="secondary" outline="true" tooltip="Settings" />

<x-icon-button icon="fas fa-sync" variant="info" loading="true" />
```

**Props:**
- `icon` (string): Font Awesome icon class
- `variant` (string): primary, secondary, success, danger, warning, info, dark, light
- `size` (string): sm, md, lg
- `outline` (boolean): Outline style
- `rounded` (boolean): Circular button
- `loading` (boolean): Show loading spinner
- `disabled` (boolean): Disable button
- `type` (string): button, submit, reset
- `href` (string): URL for link button
- `tooltip` (string): Tooltip text (requires Bootstrap tooltip)

---

### 9. Button Group
Kelompokkan multiple buttons.

```blade
{{-- Horizontal Group --}}
<x-button-group>
    <x-button variant="primary">Left</x-button>
    <x-button variant="primary">Middle</x-button>
    <x-button variant="primary">Right</x-button>
</x-button-group>

{{-- Vertical Group --}}
<x-button-group vertical="true">
    <x-button variant="info">Top</x-button>
    <x-button variant="info">Bottom</x-button>
</x-button-group>

{{-- Icon Button Group --}}
<x-button-group>
    <x-icon-button icon="fas fa-align-left" variant="secondary" />
    <x-icon-button icon="fas fa-align-center" variant="secondary" />
    <x-icon-button icon="fas fa-align-right" variant="secondary" />
</x-button-group>
```

**Props:**
- `vertical` (boolean): Vertical orientation
- `size` (string): sm, md, lg
- `class` (string): Additional CSS classes

---

### TABLE COMPONENTS

### 10. Table
Komponen table modern dengan styling eRapor8.

```blade
<x-table>
    <x-slot:header>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Status</th>
        </tr>
    </x-slot:header>
    
    <tr>
        <td>John Doe</td>
        <td>john@example.com</td>
        <td><span class="badge-modern badge-success">Active</span></td>
    </tr>
</x-table>
```

**Props:**
- `striped` (boolean): Striped rows (default: true)
- `hover` (boolean): Hover effect (default: true)
- `bordered` (boolean): Add borders (default: false)
- `responsive` (boolean): Responsive wrapper (default: true)
- `size` (string): sm, md, lg
- `class` (string): Additional CSS classes

---

### 11. Table Actions
Action buttons untuk table rows.

```blade
<x-table-actions align="center">
    <x-icon-button icon="fas fa-eye" variant="info" size="sm" tooltip="View" />
    <x-icon-button icon="fas fa-edit" variant="primary" size="sm" tooltip="Edit" />
    <x-icon-button icon="fas fa-trash" variant="danger" size="sm" tooltip="Delete" />
</x-table-actions>
```

**Props:**
- `align` (string): start, center, end
- `gap` (string): sm, md, lg
- `class` (string): Additional CSS classes

---

### 12. Sortable Header
Sortable column headers.

```blade
<x-sortable-header 
    column="name" 
    :current="$sortColumn" 
    :direction="$sortDirection"
    route="users.index"
>
    Name
</x-sortable-header>
```

**Props:**
- `column` (string): Column name
- `current` (string): Current sort column
- `direction` (string): asc or desc
- `route` (string): Route name for sorting
- `class` (string): Additional CSS classes

---

### 13. Table Search
Search input untuk table.

```blade
<x-table-search 
    placeholder="Search users..." 
    route="users.index"
    :value="request('search')"
/>
```

**Props:**
- `placeholder` (string): Placeholder text
- `route` (string): Route name
- `value` (string): Current search value
- `name` (string): Input name (default: 'search')
- `class` (string): Additional CSS classes

---

### 14. Table Filter
Filter form untuk table.

```blade
<x-table-filter route="users.index">
    <select name="status" class="form-select">
        <option value="">All Status</option>
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
    </select>
    
    <select name="role" class="form-select">
        <option value="">All Roles</option>
        <option value="admin">Admin</option>
        <option value="user">User</option>
    </select>
</x-table-filter>
```

**Props:**
- `route` (string): Route name
- `class` (string): Additional CSS classes

---

### 15. Pagination
Modern pagination component.

```blade
<x-pagination :paginator="$users" />

{{-- With custom options --}}
<x-pagination :paginator="$users" showInfo="true" size="lg" />
```

**Props:**
- `paginator` (object): Laravel paginator object
- `showInfo` (boolean): Show info text (default: true)
- `size` (string): sm, md, lg
- `class` (string): Additional CSS classes

---

### FORM COMPONENTS

### 16. Form Group
Container untuk form input dengan label, error, dan help text.

```blade
<x-form-group label="Email" name="email" required="true" help="We'll never share your email">
    <x-input name="email" type="email" placeholder="Enter email" />
</x-form-group>
```

**Props:**
- `label` (string): Label text
- `name` (string): Input name (for error detection)
- `required` (boolean): Show required asterisk
- `error` (string): Custom error message
- `help` (string): Help text
- `class` (string): Additional CSS classes

---

### 17. Input
Modern text input dengan icon support.

```blade
<x-input name="email" type="email" placeholder="Enter email" />
<x-input name="search" icon="fas fa-search" placeholder="Search..." />
<x-input name="password" type="password" iconRight="fas fa-eye" />
```

**Props:**
- `type` (string): Input type (text, email, password, etc.)
- `name` (string): Input name
- `value` (string): Default value
- `placeholder` (string): Placeholder text
- `icon` (string): Left icon (Font Awesome)
- `iconRight` (string): Right icon
- `disabled` (boolean): Disable input
- `readonly` (boolean): Readonly input
- `size` (string): sm, md, lg

---

### 18. Textarea
Modern textarea component.

```blade
<x-textarea name="description" rows="4" placeholder="Enter description" />
```

**Props:**
- `name` (string): Input name
- `value` (string): Default value
- `placeholder` (string): Placeholder text
- `rows` (number): Number of rows
- `disabled` (boolean): Disable textarea
- `readonly` (boolean): Readonly textarea
- `size` (string): sm, md, lg

---

### 19. Select
Custom select dropdown dengan icon.

```blade
<x-select name="country" placeholder="Select country">
    <option value="">Select country</option>
    <option value="id">Indonesia</option>
    <option value="my">Malaysia</option>
</x-select>

{{-- With options array --}}
<x-select name="status" :options="['active' => 'Active', 'inactive' => 'Inactive']" />
```

**Props:**
- `name` (string): Input name
- `value` (string): Selected value
- `options` (array): Options array (optional)
- `placeholder` (string): Placeholder option
- `disabled` (boolean): Disable select
- `size` (string): sm, md, lg

---

### 20. Checkbox
Modern checkbox dengan custom styling.

```blade
<x-checkbox name="agree" label="I agree to terms" />
<x-checkbox name="subscribe" label="Subscribe" checked="true" />
```

**Props:**
- `name` (string): Input name
- `label` (string): Label text
- `value` (string): Checkbox value (default: '1')
- `checked` (boolean): Checked state
- `disabled` (boolean): Disable checkbox

---

### 21. Radio
Modern radio button dengan custom styling.

```blade
<x-radio name="gender" value="male" label="Male" />
<x-radio name="gender" value="female" label="Female" checked="true" />
```

**Props:**
- `name` (string): Input name
- `label` (string): Label text
- `value` (string): Radio value
- `checked` (boolean): Checked state
- `disabled` (boolean): Disable radio

---

### 22. Switch
Modern toggle switch.

```blade
<x-switch name="notifications" label="Enable Notifications" />
<x-switch name="dark_mode" label="Dark Mode" checked="true" size="lg" />
```

**Props:**
- `name` (string): Input name
- `label` (string): Label text
- `value` (string): Switch value (default: '1')
- `checked` (boolean): Checked state
- `disabled` (boolean): Disable switch
- `size` (string): sm, md, lg

---

### 23. File Upload
Modern file upload dengan preview.

```blade
<x-file-upload name="avatar" accept="image/*" />
<x-file-upload name="documents" multiple="true" accept=".pdf,.doc" />
```

**Props:**
- `name` (string): Input name
- `accept` (string): Accepted file types
- `multiple` (boolean): Allow multiple files
- `disabled` (boolean): Disable upload
- `preview` (boolean): Show file preview (default: true)

---

### NOTIFICATION COMPONENTS

### 24. Alert
Static alert boxes dengan dismissible option.

```blade
<x-alert type="info">This is an info alert</x-alert>
<x-alert type="success" dismissible="true">Success message</x-alert>
<x-alert type="warning">Warning message</x-alert>
<x-alert type="danger">Error message</x-alert>
```

**Props:**
- `type` (string): info, success, warning, danger
- `dismissible` (boolean): Show close button
- `class` (string): Additional CSS classes

---

### 25. Toast Container
Dynamic toast notification system dengan JavaScript API.

```blade
{{-- Add to layout --}}
<x-toast-container position="top-right" />

{{-- JavaScript API --}}
<script>
Toast.success('Operation successful!');
Toast.error('An error occurred');
Toast.warning('Please be careful');
Toast.info('Information message');
</script>
```

**Props:**
- `position` (string): top-right, top-left, bottom-right, bottom-left, top-center, bottom-center
- `duration` (number): Auto-dismiss duration in ms (default: 5000)

**JavaScript API:**
- `Toast.success(message, duration)` - Success toast
- `Toast.error(message, duration)` - Error toast
- `Toast.warning(message, duration)` - Warning toast
- `Toast.info(message, duration)` - Info toast

---

### 26. Notification Badge
Count and dot badges dengan pulse animation.

```blade
{{-- Count badge --}}
<x-notification-badge count="5" />
<x-notification-badge count="99+" color="danger" />

{{-- Dot badge --}}
<x-notification-badge dot="true" color="success" />
<x-notification-badge dot="true" pulse="true" />
```

**Props:**
- `count` (string|number): Badge count
- `dot` (boolean): Show as dot instead of count
- `color` (string): primary, success, warning, danger, info
- `pulse` (boolean): Pulse animation
- `class` (string): Additional CSS classes

---

### MODAL COMPONENTS

### 27. Modal System
Modern modal dengan backdrop blur dan smooth animations.

```blade
{{-- Trigger Button --}}
<x-button variant="primary" data-modal-trigger="myModal">
    Open Modal
</x-button>

{{-- Modal Definition --}}
<div id="myModal" class="modal-modern">
    <div class="modal-backdrop"></div>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modal Title</h5>
                <button class="modal-close" data-modal-close>
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <p>Modal content goes here</p>
            </div>
            <div class="modal-footer">
                <x-button variant="secondary" outline="true" data-modal-close>
                    Close
                </x-button>
                <x-button variant="primary">
                    Save Changes
                </x-button>
            </div>
        </div>
    </div>
</div>
```

**Modal Sizes:**
```blade
{{-- Small (400px) --}}
<div class="modal-dialog modal-sm">

{{-- Medium (600px) - Default --}}
<div class="modal-dialog modal-md">

{{-- Large (800px) --}}
<div class="modal-dialog modal-lg">

{{-- Extra Large (1140px) --}}
<div class="modal-dialog modal-xl">

{{-- Fullscreen --}}
<div class="modal-dialog modal-fullscreen">

{{-- Centered --}}
<div class="modal-dialog modal-dialog-centered">
```

**JavaScript API:**
```javascript
// Show modal
Modal.show('myModal');

// Hide modal
Modal.hide('myModal');

// Confirmation modal
Modal.confirm('Are you sure?', function() {
    // Callback on confirm
    console.log('Confirmed!');
}, {
    title: 'Confirm Action',
    confirmText: 'Yes',
    cancelText: 'No',
    type: 'warning' // warning, danger, info, success
});

// Alert modal
Modal.alert('This is an alert message', 'Alert Title', 'info');
```

**Features:**
- **5 Size Options**: sm, md, lg, xl, fullscreen
- **Backdrop Blur**: Modern blur effect
- **Smooth Animations**: Slide-up and fade-in
- **ESC Key Support**: Close with ESC key
- **Click Outside**: Close by clicking backdrop
- **Focus Trap**: Accessibility support
- **Auto-trigger**: Use `data-modal-trigger` attribute
- **Confirmation API**: Built-in confirm/alert dialogs

**Modal Structure:**
- `.modal-modern` - Main modal container
- `.modal-backdrop` - Backdrop overlay
- `.modal-dialog` - Dialog wrapper
- `.modal-content` - Content container
- `.modal-header` - Header section
- `.modal-body` - Body section
- `.modal-footer` - Footer section
- `.modal-close` - Close button
- `[data-modal-close]` - Close trigger
- `[data-modal-trigger]` - Open trigger

---

## 🎨 Design System

Semua komponen menggunakan CSS variables dari `theme-vars.blade.php`:

- **Colors**: `--primary`, `--secondary`, `--success`, `--warning`, `--danger`, `--info`
- **Spacing**: `--space-1` to `--space-16` (8px base)
- **Border Radius**: `--radius-sm` to `--radius-2xl`
- **Shadows**: `--shadow-sm` to `--shadow-xl`
- **Typography**: `--text-xs` to `--text-4xl`
- **Transitions**: `--transition-fast`, `--transition-base`, `--transition-slow`

---

## 📋 Contoh Penggunaan

### Dashboard Stats Grid
```blade
<div class="row g-4">
    <div class="col-md-6 col-lg-3">
        <x-stat-card 
            icon="fas fa-users" 
            label="Total Pendaftar" 
            value="1,234" 
            color="blue"
            description="Semua angkatan"
            trend="+12"
            :trendUp="true"
        />
    </div>
    <div class="col-md-6 col-lg-3">
        <x-stat-card 
            icon="fas fa-user-plus" 
            label="Pendaftar Baru" 
            value="45" 
            color="yellow"
            description="Hari ini"
        />
    </div>
</div>
```

### Info Alerts
```blade
<x-info-card type="warning" title="Maintenance Notice" dismissible="true">
    System maintenance scheduled for tonight at 10 PM.
</x-info-card>
```

### Content Section
```blade
<x-section-card title="Recent Registrations" icon="fas fa-list" badge="LIVE">
    <x-slot:actions>
        <a href="{{ route('pendaftar.index') }}" class="btn btn-sm btn-outline-primary">
            View All
        </a>
    </x-slot:actions>
    
    <table class="table">
        <!-- Table content -->
    </table>
</x-section-card>
```

### Quick Actions
```blade
<div class="row g-4">
    <div class="col-md-6">
        <x-action-card 
            icon="fas fa-user-plus" 
            title="Tambah Pendaftar" 
            description="Daftarkan siswa baru"
            href="{{ route('pendaftar.create') }}"
            color="blue"
        />
    </div>
    <div class="col-md-6">
        <x-action-card 
            icon="fas fa-file-export" 
            title="Export Laporan" 
            description="Download data pendaftar"
            href="{{ route('report.export') }}"
            color="green"
        />
    </div>
</div>
```

### Form Actions
```blade
<div class="btn-group-spaced">
    <x-button variant="primary" icon="fas fa-save">Save</x-button>
    <x-button variant="secondary" outline="true">Cancel</x-button>
    <x-button variant="danger" outline="true" icon="fas fa-trash">Delete</x-button>
</div>
```

### Table Row Actions
```blade
<div class="btn-group-spaced gap-sm">
    <x-icon-button icon="fas fa-eye" variant="info" size="sm" tooltip="View" />
    <x-icon-button icon="fas fa-edit" variant="primary" size="sm" tooltip="Edit" />
    <x-icon-button icon="fas fa-trash" variant="danger" size="sm" tooltip="Delete" />
</div>
```

### Modal Footer
```blade
<div class="d-flex justify-content-end gap-2">
    <x-button variant="secondary" outline="true">Close</x-button>
    <x-button variant="primary" icon="fas fa-check">Confirm</x-button>
</div>
```

### Pagination
```blade
<x-button-group>
    <x-button variant="secondary" icon="fas fa-chevron-left">Previous</x-button>
    <x-button variant="primary">1</x-button>
    <x-button variant="secondary">2</x-button>
    <x-button variant="secondary">3</x-button>
    <x-button variant="secondary" iconRight="fas fa-chevron-right">Next</x-button>
</x-button-group>
```

### Floating Action Button
```blade
<a href="{{ route('item.create') }}" class="btn-fab" 
   style="background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white;">
    <i class="fas fa-plus"></i>
</a>
```

### Button Toolbar
```blade
<div class="btn-toolbar">
    <x-button-group>
        <x-button variant="primary" icon="fas fa-plus">New</x-button>
        <x-button variant="primary" icon="fas fa-edit">Edit</x-button>
    </x-button-group>
    
    <x-button-group>
        <x-icon-button icon="fas fa-bold" variant="secondary" />
        <x-icon-button icon="fas fa-italic" variant="secondary" />
    </x-button-group>
    
    <x-button variant="success" icon="fas fa-save">Save</x-button>
</div>
```

### Complete Data Table
```blade
<x-section-card title="Users" icon="fas fa-users">
    <x-slot:actions>
        <x-button variant="success" size="sm" icon="fas fa-plus">Add New</x-button>
        <x-button variant="secondary" size="sm" outline="true" icon="fas fa-download">Export</x-button>
    </x-slot:actions>
    
    <x-table-search placeholder="Search users..." route="users.index" />
    
    <x-table-filter route="users.index">
        <select name="status" class="form-select">
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
    </x-table-filter>
    
    <x-table>
        <x-slot:header>
            <tr>
                <x-sortable-header column="name" :current="$sort" :direction="$direction" route="users.index">
                    Name
                </x-sortable-header>
                <th>Email</th>
                <th>Status</th>
                <th class="text-center">Actions</th>
            </tr>
        </x-slot:header>
        
        @forelse($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td><span class="badge-modern badge-success">Active</span></td>
                <td>
                    <x-table-actions align="center">
                        <x-icon-button icon="fas fa-eye" variant="info" size="sm" tooltip="View" />
                        <x-icon-button icon="fas fa-edit" variant="primary" size="sm" tooltip="Edit" />
                        <x-icon-button icon="fas fa-trash" variant="danger" size="sm" tooltip="Delete" />
                    </x-table-actions>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4">
                    <x-empty-state icon="fas fa-users" message="No users found" />
                </td>
            </tr>
        @endforelse
    </x-table>
    
    <x-pagination :paginator="$users" />
</x-section-card>
```

---

## 🚀 Best Practices

1. **Konsistensi Warna**: Gunakan color scheme yang konsisten
   - Blue: General/Info
   - Green: Success/Positive
   - Yellow: Warning/Pending
   - Red: Danger/Critical

2. **Spacing**: Gunakan Bootstrap grid system dengan gap utilities
   ```blade
   <div class="row g-4">  <!-- g-4 = gap 1.5rem -->
   ```

3. **Responsive**: Semua komponen sudah responsive
   ```blade
   <div class="col-md-6 col-lg-3">  <!-- 1 col mobile, 2 tablet, 4 desktop -->
   ```

4. **Accessibility**: Gunakan semantic HTML dan ARIA labels
   ```blade
   <x-action-card aria-label="Add new student" ... />
   ```

5. **Performance**: Lazy load data untuk stat cards
   ```javascript
   // Load stats via AJAX
   fetch('/api/stats').then(data => updateCards(data));
   ```

---

## 📚 Referensi

- Design inspired by: [eRapor8](https://github.com/eraporsmk/erapor8)
- Icons: [Font Awesome 6](https://fontawesome.com/)
- Framework: Laravel Blade Components
- CSS: Custom CSS Variables + Bootstrap 5

---

**Created**: 2026-05-30
**Version**: 1.0.0
**Status**: Production Ready ✅
