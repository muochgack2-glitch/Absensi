@extends('layouts.admin')

@section('title', 'Button Components Demo')

@push('styles')
<style>
    .demo-section {
        margin-bottom: 48px;
    }
    
    .demo-section-title {
        font-size: 24px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 8px;
    }
    
    .demo-section-description {
        font-size: 14px;
        color: var(--text-secondary);
        margin-bottom: 24px;
    }
    
    .demo-row {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        align-items: center;
        margin-bottom: 16px;
    }
    
    .demo-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 12px;
        display: block;
    }
</style>
@endpush

@section('content')
<div class="dashboard-content">
    <!-- Page Header -->
    <div class="mb-5">
        <h2 class="mb-2">Button Components Demo</h2>
        <p class="text-muted">Koleksi komponen button modern inspired by eRapor8</p>
    </div>

    <!-- 1. Button Variants -->
    <div class="demo-section">
        <h3 class="demo-section-title">1. Button Variants</h3>
        <p class="demo-section-description">8 varian warna button dengan gradient modern.</p>
        
        <x-card padding="lg">
            <span class="demo-label">Solid Buttons</span>
            <div class="demo-row">
                <x-button variant="primary">Primary</x-button>
                <x-button variant="secondary">Secondary</x-button>
                <x-button variant="success">Success</x-button>
                <x-button variant="danger">Danger</x-button>
                <x-button variant="warning">Warning</x-button>
                <x-button variant="info">Info</x-button>
                <x-button variant="dark">Dark</x-button>
                <x-button variant="light">Light</x-button>
            </div>
            
            <span class="demo-label mt-4">Outline Buttons</span>
            <div class="demo-row">
                <x-button variant="primary" outline="true">Primary</x-button>
                <x-button variant="secondary" outline="true">Secondary</x-button>
                <x-button variant="success" outline="true">Success</x-button>
                <x-button variant="danger" outline="true">Danger</x-button>
                <x-button variant="warning" outline="true">Warning</x-button>
                <x-button variant="info" outline="true">Info</x-button>
                <x-button variant="dark" outline="true">Dark</x-button>
                <x-button variant="light" outline="true">Light</x-button>
            </div>
        </x-card>
    </div>

    <!-- 2. Button Sizes -->
    <div class="demo-section">
        <h3 class="demo-section-title">2. Button Sizes</h3>
        <p class="demo-section-description">3 ukuran button: small, medium, large.</p>
        
        <x-card padding="lg">
            <div class="demo-row">
                <x-button variant="primary" size="sm">Small Button</x-button>
                <x-button variant="primary" size="md">Medium Button</x-button>
                <x-button variant="primary" size="lg">Large Button</x-button>
            </div>
            
            <span class="demo-label mt-4">Outline Sizes</span>
            <div class="demo-row">
                <x-button variant="success" outline="true" size="sm">Small</x-button>
                <x-button variant="success" outline="true" size="md">Medium</x-button>
                <x-button variant="success" outline="true" size="lg">Large</x-button>
            </div>
        </x-card>
    </div>

    <!-- 3. Buttons with Icons -->
    <div class="demo-section">
        <h3 class="demo-section-title">3. Buttons with Icons</h3>
        <p class="demo-section-description">Button dengan icon di kiri, kanan, atau keduanya.</p>
        
        <x-card padding="lg">
            <span class="demo-label">Icon Left</span>
            <div class="demo-row">
                <x-button variant="primary" icon="fas fa-plus">Add New</x-button>
                <x-button variant="success" icon="fas fa-check">Save</x-button>
                <x-button variant="danger" icon="fas fa-trash">Delete</x-button>
                <x-button variant="info" icon="fas fa-download">Download</x-button>
            </div>
            
            <span class="demo-label mt-4">Icon Right</span>
            <div class="demo-row">
                <x-button variant="primary" iconRight="fas fa-arrow-right">Next</x-button>
                <x-button variant="secondary" iconRight="fas fa-external-link-alt">Open</x-button>
                <x-button variant="info" iconRight="fas fa-chevron-down">More</x-button>
            </div>
            
            <span class="demo-label mt-4">Both Icons</span>
            <div class="demo-row">
                <x-button variant="warning" icon="fas fa-exclamation-triangle" iconRight="fas fa-arrow-right">
                    Warning Action
                </x-button>
            </div>
        </x-card>
    </div>

    <!-- 4. Icon Buttons -->
    <div class="demo-section">
        <h3 class="demo-section-title">4. Icon Buttons</h3>
        <p class="demo-section-description">Button dengan hanya icon, tanpa text.</p>
        
        <x-card padding="lg">
            <span class="demo-label">Solid Icon Buttons</span>
            <div class="demo-row">
                <x-icon-button icon="fas fa-edit" variant="primary" tooltip="Edit" />
                <x-icon-button icon="fas fa-trash" variant="danger" tooltip="Delete" />
                <x-icon-button icon="fas fa-eye" variant="info" tooltip="View" />
                <x-icon-button icon="fas fa-download" variant="success" tooltip="Download" />
                <x-icon-button icon="fas fa-cog" variant="secondary" tooltip="Settings" />
            </div>
            
            <span class="demo-label mt-4">Outline Icon Buttons</span>
            <div class="demo-row">
                <x-icon-button icon="fas fa-edit" variant="primary" outline="true" tooltip="Edit" />
                <x-icon-button icon="fas fa-trash" variant="danger" outline="true" tooltip="Delete" />
                <x-icon-button icon="fas fa-eye" variant="info" outline="true" tooltip="View" />
                <x-icon-button icon="fas fa-download" variant="success" outline="true" tooltip="Download" />
            </div>
            
            <span class="demo-label mt-4">Rounded Icon Buttons</span>
            <div class="demo-row">
                <x-icon-button icon="fas fa-heart" variant="danger" rounded="true" />
                <x-icon-button icon="fas fa-star" variant="warning" rounded="true" />
                <x-icon-button icon="fas fa-share" variant="info" rounded="true" />
                <x-icon-button icon="fas fa-bookmark" variant="primary" rounded="true" />
            </div>
            
            <span class="demo-label mt-4">Icon Button Sizes</span>
            <div class="demo-row">
                <x-icon-button icon="fas fa-plus" variant="primary" size="sm" />
                <x-icon-button icon="fas fa-plus" variant="primary" size="md" />
                <x-icon-button icon="fas fa-plus" variant="primary" size="lg" />
            </div>
        </x-card>
    </div>

    <!-- 5. Button States -->
    <div class="demo-section">
        <h3 class="demo-section-title">5. Button States</h3>
        <p class="demo-section-description">Loading, disabled, dan state lainnya.</p>
        
        <x-card padding="lg">
            <span class="demo-label">Loading State</span>
            <div class="demo-row">
                <x-button variant="primary" loading="true">Loading...</x-button>
                <x-button variant="success" loading="true" icon="fas fa-check">Saving...</x-button>
                <x-icon-button icon="fas fa-sync" variant="info" loading="true" />
            </div>
            
            <span class="demo-label mt-4">Disabled State</span>
            <div class="demo-row">
                <x-button variant="primary" disabled="true">Disabled</x-button>
                <x-button variant="success" disabled="true" icon="fas fa-check">Disabled</x-button>
                <x-icon-button icon="fas fa-edit" variant="danger" disabled="true" />
            </div>
        </x-card>
    </div>

    <!-- 6. Button Groups -->
    <div class="demo-section">
        <h3 class="demo-section-title">6. Button Groups</h3>
        <p class="demo-section-description">Kelompokkan button secara horizontal atau vertical.</p>
        
        <x-card padding="lg">
            <span class="demo-label">Horizontal Group</span>
            <div class="demo-row">
                <x-button-group>
                    <x-button variant="primary">Left</x-button>
                    <x-button variant="primary">Middle</x-button>
                    <x-button variant="primary">Right</x-button>
                </x-button-group>
                
                <x-button-group>
                    <x-icon-button icon="fas fa-align-left" variant="secondary" />
                    <x-icon-button icon="fas fa-align-center" variant="secondary" />
                    <x-icon-button icon="fas fa-align-right" variant="secondary" />
                </x-button-group>
            </div>
            
            <span class="demo-label mt-4">Vertical Group</span>
            <div class="demo-row">
                <x-button-group vertical="true">
                    <x-button variant="info">Top</x-button>
                    <x-button variant="info">Middle</x-button>
                    <x-button variant="info">Bottom</x-button>
                </x-button-group>
            </div>
        </x-card>
    </div>

    <!-- 7. Special Buttons -->
    <div class="demo-section">
        <h3 class="demo-section-title">7. Special Buttons</h3>
        <p class="demo-section-description">Block buttons, rounded buttons, dan variasi lainnya.</p>
        
        <x-card padding="lg">
            <span class="demo-label">Block Button (Full Width)</span>
            <x-button variant="primary" block="true" icon="fas fa-save">Save Changes</x-button>
            
            <span class="demo-label mt-4">Rounded Buttons</span>
            <div class="demo-row">
                <x-button variant="primary" rounded="true">Rounded Primary</x-button>
                <x-button variant="success" rounded="true" icon="fas fa-check">Rounded Success</x-button>
                <x-button variant="danger" rounded="true" outline="true">Rounded Outline</x-button>
            </div>
            
            <span class="demo-label mt-4">Button as Link</span>
            <div class="demo-row">
                <x-button variant="primary" href="#" icon="fas fa-link">Link Button</x-button>
                <x-button variant="info" href="#" iconRight="fas fa-external-link-alt">External Link</x-button>
            </div>
        </x-card>
    </div>

    <!-- 8. Button Toolbar -->
    <div class="demo-section">
        <h3 class="demo-section-title">8. Button Toolbar</h3>
        <p class="demo-section-description">Kombinasi button groups dan spaced buttons.</p>
        
        <x-card padding="lg">
            <div class="btn-toolbar">
                <x-button-group>
                    <x-button variant="primary" icon="fas fa-plus">New</x-button>
                    <x-button variant="primary" icon="fas fa-edit">Edit</x-button>
                    <x-button variant="primary" icon="fas fa-trash">Delete</x-button>
                </x-button-group>
                
                <x-button-group>
                    <x-icon-button icon="fas fa-bold" variant="secondary" />
                    <x-icon-button icon="fas fa-italic" variant="secondary" />
                    <x-icon-button icon="fas fa-underline" variant="secondary" />
                </x-button-group>
                
                <x-button variant="success" icon="fas fa-save">Save</x-button>
            </div>
        </x-card>
    </div>

    <!-- 9. Real World Examples -->
    <div class="demo-section">
        <h3 class="demo-section-title">9. Real World Examples</h3>
        <p class="demo-section-description">Contoh penggunaan dalam aplikasi nyata.</p>
        
        <div class="row g-4">
            <div class="col-lg-6">
                <x-section-card title="Form Actions" icon="fas fa-file-alt">
                    <div class="btn-group-spaced">
                        <x-button variant="primary" icon="fas fa-save">Save</x-button>
                        <x-button variant="secondary" outline="true">Cancel</x-button>
                        <x-button variant="danger" outline="true" icon="fas fa-trash">Delete</x-button>
                    </div>
                </x-section-card>
            </div>
            
            <div class="col-lg-6">
                <x-section-card title="Table Actions" icon="fas fa-table">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="btn-group-spaced">
                            <x-icon-button icon="fas fa-eye" variant="info" size="sm" tooltip="View" />
                            <x-icon-button icon="fas fa-edit" variant="primary" size="sm" tooltip="Edit" />
                            <x-icon-button icon="fas fa-trash" variant="danger" size="sm" tooltip="Delete" />
                        </div>
                        <x-button variant="success" size="sm" icon="fas fa-download">Export</x-button>
                    </div>
                </x-section-card>
            </div>
            
            <div class="col-lg-6">
                <x-section-card title="Pagination" icon="fas fa-list">
                    <div class="d-flex justify-content-center">
                        <x-button-group>
                            <x-button variant="secondary" icon="fas fa-chevron-left">Previous</x-button>
                            <x-button variant="primary">1</x-button>
                            <x-button variant="secondary">2</x-button>
                            <x-button variant="secondary">3</x-button>
                            <x-button variant="secondary" iconRight="fas fa-chevron-right">Next</x-button>
                        </x-button-group>
                    </div>
                </x-section-card>
            </div>
            
            <div class="col-lg-6">
                <x-section-card title="Modal Actions" icon="fas fa-window-maximize">
                    <div class="d-flex justify-content-end gap-2">
                        <x-button variant="secondary" outline="true">Close</x-button>
                        <x-button variant="primary" icon="fas fa-check">Confirm</x-button>
                    </div>
                </x-section-card>
            </div>
        </div>
    </div>

    <!-- Documentation Link -->
    <div class="demo-section">
        <x-info-card type="info" title="📚 Documentation">
            <p class="mb-2">Untuk dokumentasi lengkap dan contoh kode, lihat file:</p>
            <code>resources/views/components/README.md</code>
        </x-info-card>
    </div>
</div>

<!-- Floating Action Button Example -->
<a href="#" class="btn-fab" style="background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white;" title="Add New">
    <i class="fas fa-plus"></i>
</a>
@endsection

@push('scripts')
<script>
    // Initialize Bootstrap tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush
