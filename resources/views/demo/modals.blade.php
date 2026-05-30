@extends('layouts.admin')

@section('title', 'Modal Components Demo')

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
    
    .demo-button-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: var(--space-3);
    }
</style>
@endpush

@section('content')
<div class="dashboard-content">
    <!-- Page Header -->
    <div class="mb-5">
        <h2 class="mb-2">Modal Components Demo</h2>
        <p class="text-muted">Koleksi komponen modal modern inspired by eRapor8</p>
    </div>

    <!-- 1. Basic Modals -->
    <div class="demo-section">
        <h3 class="demo-section-title">1. Basic Modals</h3>
        <p class="demo-section-description">Modal dasar dengan berbagai ukuran.</p>
        
        <x-section-card title="Modal Sizes" icon="fas fa-window-maximize">
            <div class="demo-button-grid">
                <x-button variant="primary" data-modal-trigger="modalSmall">
                    Small Modal
                </x-button>
                
                <x-button variant="primary" data-modal-trigger="modalMedium">
                    Medium Modal (Default)
                </x-button>
                
                <x-button variant="primary" data-modal-trigger="modalLarge">
                    Large Modal
                </x-button>
                
                <x-button variant="primary" data-modal-trigger="modalXL">
                    Extra Large Modal
                </x-button>
            </div>
        </x-section-card>
    </div>

    <!-- 2. Confirmation Modals -->
    <div class="demo-section">
        <h3 class="demo-section-title">2. Confirmation Modals</h3>
        <p class="demo-section-description">Modal konfirmasi dengan JavaScript API.</p>
        
        <x-section-card title="Confirmation Examples" icon="fas fa-question-circle">
            <div class="demo-button-grid">
                <x-button variant="warning" icon="fas fa-exclamation-triangle"
                          onclick="Modal.confirm('Apakah Anda yakin ingin melanjutkan?', () => Toast.success('Confirmed!'))">
                    Warning Confirm
                </x-button>
                
                <x-button variant="danger" icon="fas fa-trash"
                          onclick="Modal.confirm('Data akan dihapus permanen. Lanjutkan?', () => Toast.success('Deleted!'), { type: 'danger', title: 'Hapus Data', confirmText: 'Hapus', cancelText: 'Batal' })">
                    Delete Confirm
                </x-button>
                
                <x-button variant="info" icon="fas fa-info-circle"
                          onclick="Modal.alert('Ini adalah pesan informasi penting.', 'Informasi', 'info')">
                    Info Alert
                </x-button>
                
                <x-button variant="success" icon="fas fa-check-circle"
                          onclick="Modal.alert('Operasi berhasil diselesaikan!', 'Berhasil!', 'success')">
                    Success Alert
                </x-button>
            </div>
        </x-section-card>
    </div>

    <!-- 3. Form Modals -->
    <div class="demo-section">
        <h3 class="demo-section-title">3. Form Modals</h3>
        <p class="demo-section-description">Modal dengan form input.</p>
        
        <x-section-card title="Form Modal Example" icon="fas fa-edit">
            <x-button variant="primary" icon="fas fa-plus" data-modal-trigger="modalForm">
                Add New User
            </x-button>
        </x-section-card>
    </div>

    <!-- 4. Content Modals -->
    <div class="demo-section">
        <h3 class="demo-section-title">4. Content Modals</h3>
        <p class="demo-section-description">Modal dengan berbagai jenis konten.</p>
        
        <x-section-card title="Content Examples" icon="fas fa-file-alt">
            <div class="demo-button-grid">
                <x-button variant="secondary" icon="fas fa-list" data-modal-trigger="modalList">
                    List Modal
                </x-button>
                
                <x-button variant="secondary" icon="fas fa-table" data-modal-trigger="modalTable">
                    Table Modal
                </x-button>
                
                <x-button variant="secondary" icon="fas fa-image" data-modal-trigger="modalImage">
                    Image Modal
                </x-button>
            </div>
        </x-section-card>
    </div>

    <!-- Documentation -->
    <div class="demo-section">
        <x-info-card type="info" title="📚 How to Use">
            <p class="mb-3"><strong>HTML Modal:</strong></p>
            <pre class="bg-dark text-light p-3 rounded mb-3"><code>&lt;!-- Trigger Button --&gt;
&lt;button data-modal-trigger="myModal"&gt;Open Modal&lt;/button&gt;

&lt;!-- Modal --&gt;
&lt;div id="myModal" class="modal-modern"&gt;
    &lt;div class="modal-backdrop"&gt;&lt;/div&gt;
    &lt;div class="modal-dialog"&gt;
        &lt;div class="modal-content"&gt;
            &lt;div class="modal-header"&gt;
                &lt;h5 class="modal-title"&gt;Title&lt;/h5&gt;
                &lt;button class="modal-close" data-modal-close&gt;×&lt;/button&gt;
            &lt;/div&gt;
            &lt;div class="modal-body"&gt;Content&lt;/div&gt;
            &lt;div class="modal-footer"&gt;
                &lt;button data-modal-close&gt;Close&lt;/button&gt;
            &lt;/div&gt;
        &lt;/div&gt;
    &lt;/div&gt;
&lt;/div&gt;</code></pre>
            
            <p class="mb-3"><strong>JavaScript API:</strong></p>
            <pre class="bg-dark text-light p-3 rounded mb-0"><code>// Show modal
Modal.show('myModal');

// Hide modal
Modal.hide('myModal');

// Confirmation
Modal.confirm('Message', callback, options);

// Alert
Modal.alert('Message', 'Title', 'type');</code></pre>
        </x-info-card>
    </div>
</div>

<!-- Modal Definitions -->

<!-- Small Modal -->
<div id="modalSmall" class="modal-modern">
    <div class="modal-backdrop"></div>
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Small Modal</h5>
                <button class="modal-close" data-modal-close>
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <p>This is a small modal with max-width of 400px.</p>
            </div>
            <div class="modal-footer">
                <x-button variant="secondary" outline="true" data-modal-close>Close</x-button>
                <x-button variant="primary">Save</x-button>
            </div>
        </div>
    </div>
</div>

<!-- Medium Modal -->
<div id="modalMedium" class="modal-modern">
    <div class="modal-backdrop"></div>
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Medium Modal (Default)</h5>
                <button class="modal-close" data-modal-close>
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <p>This is a medium modal with max-width of 600px. This is the default size.</p>
                <p class="mb-0">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
            </div>
            <div class="modal-footer">
                <x-button variant="secondary" outline="true" data-modal-close>Cancel</x-button>
                <x-button variant="primary">Confirm</x-button>
            </div>
        </div>
    </div>
</div>

<!-- Large Modal -->
<div id="modalLarge" class="modal-modern">
    <div class="modal-backdrop"></div>
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Large Modal</h5>
                <button class="modal-close" data-modal-close>
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <p>This is a large modal with max-width of 800px.</p>
                <div class="row">
                    <div class="col-md-6">
                        <p>Perfect for displaying more content or complex layouts.</p>
                    </div>
                    <div class="col-md-6">
                        <p>You can use grid system inside modals.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <x-button variant="secondary" outline="true" data-modal-close>Close</x-button>
            </div>
        </div>
    </div>
</div>

<!-- Extra Large Modal -->
<div id="modalXL" class="modal-modern">
    <div class="modal-backdrop"></div>
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Extra Large Modal</h5>
                <button class="modal-close" data-modal-close>
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <p>This is an extra large modal with max-width of 1140px.</p>
                <p class="mb-0">Ideal for displaying tables, charts, or complex data visualizations.</p>
            </div>
            <div class="modal-footer">
                <x-button variant="secondary" outline="true" data-modal-close>Close</x-button>
            </div>
        </div>
    </div>
</div>

<!-- Form Modal -->
<div id="modalForm" class="modal-modern">
    <div class="modal-backdrop"></div>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New User</h5>
                <button class="modal-close" data-modal-close>
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="userForm">
                    <x-form-group label="Full Name" name="name" required="true">
                        <x-input name="name" placeholder="Enter full name" icon="fas fa-user" />
                    </x-form-group>
                    
                    <x-form-group label="Email Address" name="email" required="true">
                        <x-input name="email" type="email" placeholder="Enter email" icon="fas fa-envelope" />
                    </x-form-group>
                    
                    <x-form-group label="Role" name="role" required="true">
                        <x-select name="role">
                            <option value="">Select role</option>
                            <option value="admin">Admin</option>
                            <option value="user">User</option>
                            <option value="guest">Guest</option>
                        </x-select>
                    </x-form-group>
                    
                    <x-form-group name="active">
                        <x-checkbox name="active" label="Active user" checked="true" />
                    </x-form-group>
                </form>
            </div>
            <div class="modal-footer">
                <x-button variant="secondary" outline="true" data-modal-close>Cancel</x-button>
                <x-button variant="primary" icon="fas fa-save" 
                          onclick="event.preventDefault(); Toast.success('User saved!'); Modal.hide('modalForm');">
                    Save User
                </x-button>
            </div>
        </div>
    </div>
</div>

<!-- List Modal -->
<div id="modalList" class="modal-modern">
    <div class="modal-backdrop"></div>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select Item</h5>
                <button class="modal-close" data-modal-close>
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body" style="padding: 0;">
                <div class="list-group list-group-flush">
                    <a href="#" class="list-group-item list-group-item-action" onclick="event.preventDefault(); Toast.success('Item 1 selected'); Modal.hide('modalList');">
                        <i class="fas fa-check-circle text-success me-2"></i> Item 1
                    </a>
                    <a href="#" class="list-group-item list-group-item-action" onclick="event.preventDefault(); Toast.success('Item 2 selected'); Modal.hide('modalList');">
                        <i class="fas fa-check-circle text-success me-2"></i> Item 2
                    </a>
                    <a href="#" class="list-group-item list-group-item-action" onclick="event.preventDefault(); Toast.success('Item 3 selected'); Modal.hide('modalList');">
                        <i class="fas fa-check-circle text-success me-2"></i> Item 3
                    </a>
                    <a href="#" class="list-group-item list-group-item-action" onclick="event.preventDefault(); Toast.success('Item 4 selected'); Modal.hide('modalList');">
                        <i class="fas fa-check-circle text-success me-2"></i> Item 4
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Table Modal -->
<div id="modalTable" class="modal-modern">
    <div class="modal-backdrop"></div>
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Data Table</h5>
                <button class="modal-close" data-modal-close>
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <x-table size="sm">
                    <x-slot:header>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                        </tr>
                    </x-slot:header>
                    <tr>
                        <td>1</td>
                        <td>John Doe</td>
                        <td>john@example.com</td>
                        <td><span class="badge-modern badge-success">Active</span></td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Jane Smith</td>
                        <td>jane@example.com</td>
                        <td><span class="badge-modern badge-success">Active</span></td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Bob Johnson</td>
                        <td>bob@example.com</td>
                        <td><span class="badge-modern badge-danger">Inactive</span></td>
                    </tr>
                </x-table>
            </div>
            <div class="modal-footer">
                <x-button variant="secondary" outline="true" data-modal-close>Close</x-button>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="modalImage" class="modal-modern">
    <div class="modal-backdrop"></div>
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Image Preview</h5>
                <button class="modal-close" data-modal-close>
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body text-center">
                <img src="https://via.placeholder.com/800x400/667eea/ffffff?text=Image+Preview" 
                     alt="Preview" 
                     class="img-fluid rounded">
                <p class="mt-3 text-muted">This is an example of image modal</p>
            </div>
            <div class="modal-footer">
                <x-button variant="secondary" outline="true" data-modal-close>Close</x-button>
                <x-button variant="primary" icon="fas fa-download">Download</x-button>
            </div>
        </div>
    </div>
</div>

@endsection
