@extends('layouts.admin')

@section('title', 'Table Components Demo')

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
</style>
@endpush

@section('content')
<div class="dashboard-content">
    <!-- Page Header -->
    <div class="mb-5">
        <h2 class="mb-2">Table Components Demo</h2>
        <p class="text-muted">Koleksi komponen table modern inspired by eRapor8</p>
    </div>

    <!-- 1. Basic Table -->
    <div class="demo-section">
        <h3 class="demo-section-title">1. Basic Table</h3>
        <p class="demo-section-description">Table dasar dengan styling modern.</p>
        
        <x-section-card title="User List" icon="fas fa-users">
            <x-table>
                <x-slot:header>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                    </tr>
                </x-slot:header>
                
                <tr>
                    <td>1</td>
                    <td><strong>John Doe</strong></td>
                    <td>john@example.com</td>
                    <td><span class="badge-modern badge-primary">Admin</span></td>
                    <td><span class="badge-modern badge-success">Active</span></td>
                </tr>
                <tr>
                    <td>2</td>
                    <td><strong>Jane Smith</strong></td>
                    <td>jane@example.com</td>
                    <td><span class="badge-modern badge-info">User</span></td>
                    <td><span class="badge-modern badge-success">Active</span></td>
                </tr>
                <tr>
                    <td>3</td>
                    <td><strong>Bob Johnson</strong></td>
                    <td>bob@example.com</td>
                    <td><span class="badge-modern badge-info">User</span></td>
                    <td><span class="badge-modern badge-danger">Inactive</span></td>
                </tr>
            </x-table>
        </x-section-card>
    </div>

    <!-- 2. Table with Actions -->
    <div class="demo-section">
        <h3 class="demo-section-title">2. Table with Actions</h3>
        <p class="demo-section-description">Table dengan action buttons per row.</p>
        
        <x-section-card title="Students" icon="fas fa-graduation-cap">
            <x-table>
                <x-slot:header>
                    <tr>
                        <th>No. Reg</th>
                        <th>Name</th>
                        <th>Major</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </x-slot:header>
                
                <tr>
                    <td><strong>REG001</strong></td>
                    <td>Ahmad Fauzi</td>
                    <td>Teknik Informatika</td>
                    <td><span class="badge-modern badge-success">Verified</span></td>
                    <td>
                        <x-table-actions align="center">
                            <x-icon-button icon="fas fa-eye" variant="info" size="sm" tooltip="View" />
                            <x-icon-button icon="fas fa-edit" variant="primary" size="sm" tooltip="Edit" />
                            <x-icon-button icon="fas fa-trash" variant="danger" size="sm" tooltip="Delete" />
                        </x-table-actions>
                    </td>
                </tr>
                <tr>
                    <td><strong>REG002</strong></td>
                    <td>Siti Nurhaliza</td>
                    <td>Akuntansi</td>
                    <td><span class="badge-modern badge-warning">Pending</span></td>
                    <td>
                        <x-table-actions align="center">
                            <x-icon-button icon="fas fa-eye" variant="info" size="sm" tooltip="View" />
                            <x-icon-button icon="fas fa-edit" variant="primary" size="sm" tooltip="Edit" />
                            <x-icon-button icon="fas fa-trash" variant="danger" size="sm" tooltip="Delete" />
                        </x-table-actions>
                    </td>
                </tr>
                <tr>
                    <td><strong>REG003</strong></td>
                    <td>Budi Santoso</td>
                    <td>Manajemen</td>
                    <td><span class="badge-modern badge-success">Verified</span></td>
                    <td>
                        <x-table-actions align="center">
                            <x-icon-button icon="fas fa-eye" variant="info" size="sm" tooltip="View" />
                            <x-icon-button icon="fas fa-edit" variant="primary" size="sm" tooltip="Edit" />
                            <x-icon-button icon="fas fa-trash" variant="danger" size="sm" tooltip="Delete" />
                        </x-table-actions>
                    </td>
                </tr>
            </x-table>
        </x-section-card>
    </div>

    <!-- 3. Table Sizes -->
    <div class="demo-section">
        <h3 class="demo-section-title">3. Table Sizes</h3>
        <p class="demo-section-description">3 ukuran table: small, medium, large.</p>
        
        <div class="row g-4">
            <div class="col-lg-4">
                <x-card>
                    <h6 class="mb-3">Small Table</h6>
                    <x-table size="sm" :responsive="false">
                        <x-slot:header>
                            <tr>
                                <th>Name</th>
                                <th>Value</th>
                            </tr>
                        </x-slot:header>
                        <tr>
                            <td>Item 1</td>
                            <td>100</td>
                        </tr>
                        <tr>
                            <td>Item 2</td>
                            <td>200</td>
                        </tr>
                    </x-table>
                </x-card>
            </div>
            
            <div class="col-lg-4">
                <x-card>
                    <h6 class="mb-3">Medium Table (Default)</h6>
                    <x-table size="md" :responsive="false">
                        <x-slot:header>
                            <tr>
                                <th>Name</th>
                                <th>Value</th>
                            </tr>
                        </x-slot:header>
                        <tr>
                            <td>Item 1</td>
                            <td>100</td>
                        </tr>
                        <tr>
                            <td>Item 2</td>
                            <td>200</td>
                        </tr>
                    </x-table>
                </x-card>
            </div>
            
            <div class="col-lg-4">
                <x-card>
                    <h6 class="mb-3">Large Table</h6>
                    <x-table size="lg" :responsive="false">
                        <x-slot:header>
                            <tr>
                                <th>Name</th>
                                <th>Value</th>
                            </tr>
                        </x-slot:header>
                        <tr>
                            <td>Item 1</td>
                            <td>100</td>
                        </tr>
                        <tr>
                            <td>Item 2</td>
                            <td>200</td>
                        </tr>
                    </x-table>
                </x-card>
            </div>
        </div>
    </div>

    <!-- 4. Table Variants -->
    <div class="demo-section">
        <h3 class="demo-section-title">4. Table Variants</h3>
        <p class="demo-section-description">Striped, hover, dan bordered variants.</p>
        
        <div class="row g-4">
            <div class="col-lg-6">
                <x-card>
                    <h6 class="mb-3">Striped Table</h6>
                    <x-table striped="true" hover="false">
                        <x-slot:header>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Stock</th>
                            </tr>
                        </x-slot:header>
                        <tr><td>Product A</td><td>$100</td><td>50</td></tr>
                        <tr><td>Product B</td><td>$200</td><td>30</td></tr>
                        <tr><td>Product C</td><td>$150</td><td>40</td></tr>
                        <tr><td>Product D</td><td>$300</td><td>20</td></tr>
                    </x-table>
                </x-card>
            </div>
            
            <div class="col-lg-6">
                <x-card>
                    <h6 class="mb-3">Bordered Table</h6>
                    <x-table bordered="true" striped="false">
                        <x-slot:header>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Stock</th>
                            </tr>
                        </x-slot:header>
                        <tr><td>Product A</td><td>$100</td><td>50</td></tr>
                        <tr><td>Product B</td><td>$200</td><td>30</td></tr>
                        <tr><td>Product C</td><td>$150</td><td>40</td></tr>
                        <tr><td>Product D</td><td>$300</td><td>20</td></tr>
                    </x-table>
                </x-card>
            </div>
        </div>
    </div>

    <!-- 5. Table with Search -->
    <div class="demo-section">
        <h3 class="demo-section-title">5. Table with Search</h3>
        <p class="demo-section-description">Table dengan search functionality.</p>
        
        <x-section-card title="Searchable Data" icon="fas fa-search">
            <x-table-search placeholder="Search by name or email..." />
            
            <x-table>
                <x-slot:header>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Status</th>
                    </tr>
                </x-slot:header>
                
                <tr>
                    <td>Alice Johnson</td>
                    <td>alice@company.com</td>
                    <td>Engineering</td>
                    <td><span class="badge-modern badge-success">Active</span></td>
                </tr>
                <tr>
                    <td>Bob Williams</td>
                    <td>bob@company.com</td>
                    <td>Marketing</td>
                    <td><span class="badge-modern badge-success">Active</span></td>
                </tr>
                <tr>
                    <td>Carol Davis</td>
                    <td>carol@company.com</td>
                    <td>Sales</td>
                    <td><span class="badge-modern badge-warning">Pending</span></td>
                </tr>
            </x-table>
        </x-section-card>
    </div>

    <!-- 6. Table with Filters -->
    <div class="demo-section">
        <h3 class="demo-section-title">6. Table with Filters</h3>
        <p class="demo-section-description">Table dengan filter options.</p>
        
        <x-section-card title="Filtered Data" icon="fas fa-filter">
            <x-table-filter>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="pending">Pending</option>
                </select>
                
                <select name="department" class="form-select">
                    <option value="">All Departments</option>
                    <option value="engineering">Engineering</option>
                    <option value="marketing">Marketing</option>
                    <option value="sales">Sales</option>
                </select>
            </x-table-filter>
            
            <x-table>
                <x-slot:header>
                    <tr>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Position</th>
                        <th>Status</th>
                    </tr>
                </x-slot:header>
                
                <tr>
                    <td>David Brown</td>
                    <td>Engineering</td>
                    <td>Senior Developer</td>
                    <td><span class="badge-modern badge-success">Active</span></td>
                </tr>
                <tr>
                    <td>Emma Wilson</td>
                    <td>Marketing</td>
                    <td>Marketing Manager</td>
                    <td><span class="badge-modern badge-success">Active</span></td>
                </tr>
                <tr>
                    <td>Frank Miller</td>
                    <td>Sales</td>
                    <td>Sales Representative</td>
                    <td><span class="badge-modern badge-warning">Pending</span></td>
                </tr>
            </x-table>
        </x-section-card>
    </div>

    <!-- 7. Table with Empty State -->
    <div class="demo-section">
        <h3 class="demo-section-title">7. Table with Empty State</h3>
        <p class="demo-section-description">Table dengan empty state ketika tidak ada data.</p>
        
        <x-section-card title="No Data Example" icon="fas fa-inbox">
            <x-table>
                <x-slot:header>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Status</th>
                    </tr>
                </x-slot:header>
                
                <tr>
                    <td colspan="3">
                        <x-empty-state 
                            icon="fas fa-inbox" 
                            message="No data available"
                            description="There are no records to display at this time."
                        >
                            <x-slot:action>
                                <x-button variant="primary" icon="fas fa-plus">Add New Record</x-button>
                            </x-slot:action>
                        </x-empty-state>
                    </td>
                </tr>
            </x-table>
        </x-section-card>
    </div>

    <!-- 8. Complete Example -->
    <div class="demo-section">
        <h3 class="demo-section-title">8. Complete Example</h3>
        <p class="demo-section-description">Table lengkap dengan search, filter, actions, dan pagination.</p>
        
        <x-section-card title="Complete Data Table" icon="fas fa-table" badge="LIVE">
            <x-slot:actions>
                <x-button variant="success" size="sm" icon="fas fa-plus">Add New</x-button>
                <x-button variant="secondary" size="sm" outline="true" icon="fas fa-download">Export</x-button>
            </x-slot:actions>
            
            <x-table-search placeholder="Search orders..." />
            
            <x-table-filter>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="completed">Completed</option>
                    <option value="pending">Pending</option>
                    <option value="cancelled">Cancelled</option>
                </select>
                
                <input type="date" name="date_from" class="form-control" placeholder="From Date">
                <input type="date" name="date_to" class="form-control" placeholder="To Date">
            </x-table-filter>
            
            <x-table>
                <x-slot:header>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </x-slot:header>
                
                <tr>
                    <td><strong>#ORD-001</strong></td>
                    <td>John Doe</td>
                    <td>$1,250.00</td>
                    <td>2026-05-30</td>
                    <td><span class="badge-modern badge-success">Completed</span></td>
                    <td>
                        <x-table-actions align="center">
                            <x-icon-button icon="fas fa-eye" variant="info" size="sm" tooltip="View" />
                            <x-icon-button icon="fas fa-print" variant="secondary" size="sm" tooltip="Print" />
                            <x-icon-button icon="fas fa-download" variant="primary" size="sm" tooltip="Download" />
                        </x-table-actions>
                    </td>
                </tr>
                <tr>
                    <td><strong>#ORD-002</strong></td>
                    <td>Jane Smith</td>
                    <td>$850.00</td>
                    <td>2026-05-29</td>
                    <td><span class="badge-modern badge-warning">Pending</span></td>
                    <td>
                        <x-table-actions align="center">
                            <x-icon-button icon="fas fa-eye" variant="info" size="sm" tooltip="View" />
                            <x-icon-button icon="fas fa-print" variant="secondary" size="sm" tooltip="Print" />
                            <x-icon-button icon="fas fa-download" variant="primary" size="sm" tooltip="Download" />
                        </x-table-actions>
                    </td>
                </tr>
                <tr>
                    <td><strong>#ORD-003</strong></td>
                    <td>Bob Johnson</td>
                    <td>$2,100.00</td>
                    <td>2026-05-28</td>
                    <td><span class="badge-modern badge-success">Completed</span></td>
                    <td>
                        <x-table-actions align="center">
                            <x-icon-button icon="fas fa-eye" variant="info" size="sm" tooltip="View" />
                            <x-icon-button icon="fas fa-print" variant="secondary" size="sm" tooltip="Print" />
                            <x-icon-button icon="fas fa-download" variant="primary" size="sm" tooltip="Download" />
                        </x-table-actions>
                    </td>
                </tr>
            </x-table>
            
            {{-- Pagination example (static) --}}
            <div class="pagination-modern">
                <div class="pagination-info">
                    Menampilkan 1 - 3 dari 50 data
                </div>
                <nav class="pagination-nav">
                    <x-button-group>
                        <x-button variant="secondary" disabled="true" icon="fas fa-chevron-left">Previous</x-button>
                        <x-button variant="primary">1</x-button>
                        <x-button variant="secondary">2</x-button>
                        <x-button variant="secondary">3</x-button>
                        <x-button variant="secondary">4</x-button>
                        <x-button variant="secondary" iconRight="fas fa-chevron-right">Next</x-button>
                    </x-button-group>
                </nav>
            </div>
        </x-section-card>
    </div>

    <!-- Documentation Link -->
    <div class="demo-section">
        <x-info-card type="info" title="📚 Documentation">
            <p class="mb-2">Untuk dokumentasi lengkap dan contoh kode, lihat file:</p>
            <code>resources/views/components/README.md</code>
            <p class="mt-3 mb-0"><strong>Note:</strong> Table components ini TIDAK akan mempengaruhi halaman print. Semua efek modern akan di-disable saat print dengan <code>@media print</code> CSS rules.</p>
        </x-info-card>
    </div>
</div>
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
