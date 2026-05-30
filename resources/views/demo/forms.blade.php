@extends('layouts.admin')

@section('title', 'Form Components Demo')

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
        <h2 class="mb-2">Form Components Demo</h2>
        <p class="text-muted">Koleksi komponen form modern inspired by eRapor8</p>
    </div>

    <!-- 1. Form Groups -->
    <div class="demo-section">
        <h3 class="demo-section-title">1. Form Groups</h3>
        <p class="demo-section-description">Form group dengan label, error message, dan help text.</p>
        
        <x-section-card title="Form Group Examples" icon="fas fa-wpforms">
            <div class="row">
                <div class="col-md-6">
                    <x-form-group label="Email Address" name="email" required="true" help="We'll never share your email">
                        <x-input name="email" type="email" placeholder="Enter email" />
                    </x-form-group>
                </div>
                
                <div class="col-md-6">
                    <x-form-group label="Password" name="password" required="true">
                        <x-input name="password" type="password" placeholder="Enter password" />
                    </x-form-group>
                </div>
                
                <div class="col-md-12">
                    <x-form-group label="Description" name="description">
                        <x-textarea name="description" rows="3" placeholder="Enter description" />
                    </x-form-group>
                </div>
            </div>
        </x-section-card>
    </div>

    <!-- 2. Input Variants -->
    <div class="demo-section">
        <h3 class="demo-section-title">2. Input Variants</h3>
        <p class="demo-section-description">Input dengan berbagai ukuran dan icon.</p>
        
        <x-section-card title="Input Examples" icon="fas fa-keyboard">
            <div class="row g-4">
                <div class="col-md-4">
                    <label class="form-label-modern">Small Input</label>
                    <x-input name="input_sm" size="sm" placeholder="Small input" />
                </div>
                
                <div class="col-md-4">
                    <label class="form-label-modern">Medium Input (Default)</label>
                    <x-input name="input_md" size="md" placeholder="Medium input" />
                </div>
                
                <div class="col-md-4">
                    <label class="form-label-modern">Large Input</label>
                    <x-input name="input_lg" size="lg" placeholder="Large input" />
                </div>
                
                <div class="col-md-6">
                    <label class="form-label-modern">Input with Left Icon</label>
                    <x-input name="search" icon="fas fa-search" placeholder="Search..." />
                </div>
                
                <div class="col-md-6">
                    <label class="form-label-modern">Input with Right Icon</label>
                    <x-input name="email_icon" type="email" iconRight="fas fa-envelope" placeholder="Email" />
                </div>
                
                <div class="col-md-6">
                    <label class="form-label-modern">Disabled Input</label>
                    <x-input name="disabled" value="Disabled value" disabled="true" />
                </div>
                
                <div class="col-md-6">
                    <label class="form-label-modern">Readonly Input</label>
                    <x-input name="readonly" value="Readonly value" readonly="true" />
                </div>
            </div>
        </x-section-card>
    </div>

    <!-- 3. Textarea -->
    <div class="demo-section">
        <h3 class="demo-section-title">3. Textarea</h3>
        <p class="demo-section-description">Textarea dengan berbagai ukuran.</p>
        
        <x-section-card title="Textarea Examples" icon="fas fa-align-left">
            <div class="row g-4">
                <div class="col-md-4">
                    <label class="form-label-modern">Small Textarea</label>
                    <x-textarea name="textarea_sm" size="sm" rows="3" placeholder="Small textarea" />
                </div>
                
                <div class="col-md-4">
                    <label class="form-label-modern">Medium Textarea</label>
                    <x-textarea name="textarea_md" size="md" rows="3" placeholder="Medium textarea" />
                </div>
                
                <div class="col-md-4">
                    <label class="form-label-modern">Large Textarea</label>
                    <x-textarea name="textarea_lg" size="lg" rows="3" placeholder="Large textarea" />
                </div>
            </div>
        </x-section-card>
    </div>

    <!-- 4. Select Dropdown -->
    <div class="demo-section">
        <h3 class="demo-section-title">4. Select Dropdown</h3>
        <p class="demo-section-description">Custom select dropdown dengan icon.</p>
        
        <x-section-card title="Select Examples" icon="fas fa-list">
            <div class="row g-4">
                <div class="col-md-4">
                    <label class="form-label-modern">Small Select</label>
                    <x-select name="select_sm" size="sm">
                        <option value="">Select option</option>
                        <option value="1">Option 1</option>
                        <option value="2">Option 2</option>
                        <option value="3">Option 3</option>
                    </x-select>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label-modern">Medium Select</label>
                    <x-select name="select_md" size="md">
                        <option value="">Select option</option>
                        <option value="1">Option 1</option>
                        <option value="2">Option 2</option>
                        <option value="3">Option 3</option>
                    </x-select>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label-modern">Large Select</label>
                    <x-select name="select_lg" size="lg">
                        <option value="">Select option</option>
                        <option value="1">Option 1</option>
                        <option value="2">Option 2</option>
                        <option value="3">Option 3</option>
                    </x-select>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label-modern">Country</label>
                    <x-select name="country" placeholder="Select country">
                        <option value="">Select country</option>
                        <option value="id">Indonesia</option>
                        <option value="my">Malaysia</option>
                        <option value="sg">Singapore</option>
                        <option value="th">Thailand</option>
                    </x-select>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label-modern">Disabled Select</label>
                    <x-select name="disabled_select" disabled="true">
                        <option value="">Disabled select</option>
                    </x-select>
                </div>
            </div>
        </x-section-card>
    </div>

    <!-- 5. Checkbox & Radio -->
    <div class="demo-section">
        <h3 class="demo-section-title">5. Checkbox & Radio</h3>
        <p class="demo-section-description">Modern checkbox dan radio buttons.</p>
        
        <div class="row g-4">
            <div class="col-md-6">
                <x-section-card title="Checkbox Examples" icon="fas fa-check-square">
                    <div class="d-flex flex-column gap-3">
                        <x-checkbox name="agree" label="I agree to terms and conditions" />
                        <x-checkbox name="subscribe" label="Subscribe to newsletter" checked="true" />
                        <x-checkbox name="remember" label="Remember me" />
                        <x-checkbox name="disabled_check" label="Disabled checkbox" disabled="true" />
                    </div>
                </x-section-card>
            </div>
            
            <div class="col-md-6">
                <x-section-card title="Radio Examples" icon="fas fa-dot-circle">
                    <div class="d-flex flex-column gap-3">
                        <x-radio name="gender" value="male" label="Male" />
                        <x-radio name="gender" value="female" label="Female" checked="true" />
                        <x-radio name="gender" value="other" label="Other" />
                        <x-radio name="gender" value="disabled" label="Disabled radio" disabled="true" />
                    </div>
                </x-section-card>
            </div>
        </div>
    </div>

    <!-- 6. Switch Toggle -->
    <div class="demo-section">
        <h3 class="demo-section-title">6. Switch Toggle</h3>
        <p class="demo-section-description">Modern switch toggle dengan berbagai ukuran.</p>
        
        <x-section-card title="Switch Examples" icon="fas fa-toggle-on">
            <div class="row g-4">
                <div class="col-md-4">
                    <label class="form-label-modern mb-3">Small Switch</label>
                    <div class="d-flex flex-column gap-3">
                        <x-switch name="switch_sm_1" label="Enable feature" size="sm" />
                        <x-switch name="switch_sm_2" label="Dark mode" size="sm" checked="true" />
                    </div>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label-modern mb-3">Medium Switch</label>
                    <div class="d-flex flex-column gap-3">
                        <x-switch name="notifications" label="Notifications" size="md" />
                        <x-switch name="auto_save" label="Auto save" size="md" checked="true" />
                    </div>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label-modern mb-3">Large Switch</label>
                    <div class="d-flex flex-column gap-3">
                        <x-switch name="switch_lg_1" label="Public profile" size="lg" />
                        <x-switch name="switch_lg_2" label="Email alerts" size="lg" checked="true" />
                    </div>
                </div>
            </div>
        </x-section-card>
    </div>

    <!-- 7. File Upload -->
    <div class="demo-section">
        <h3 class="demo-section-title">7. File Upload</h3>
        <p class="demo-section-description">Modern file upload dengan preview.</p>
        
        <x-section-card title="File Upload Examples" icon="fas fa-upload">
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label-modern">Upload Image</label>
                    <x-file-upload name="avatar" accept="image/*" />
                </div>
                
                <div class="col-md-6">
                    <label class="form-label-modern">Upload Document</label>
                    <x-file-upload name="document" accept=".pdf,.doc,.docx" />
                </div>
                
                <div class="col-md-12">
                    <label class="form-label-modern">Upload Multiple Files</label>
                    <x-file-upload name="attachments" multiple="true" />
                </div>
            </div>
        </x-section-card>
    </div>

    <!-- 8. Complete Form Example -->
    <div class="demo-section">
        <h3 class="demo-section-title">8. Complete Form Example</h3>
        <p class="demo-section-description">Form lengkap dengan validasi dan berbagai input types.</p>
        
        <x-section-card title="User Registration Form" icon="fas fa-user-plus">
            <form action="#" method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <x-form-group label="Full Name" name="name" required="true">
                            <x-input name="name" placeholder="Enter full name" icon="fas fa-user" />
                        </x-form-group>
                    </div>
                    
                    <div class="col-md-6">
                        <x-form-group label="Email Address" name="email" required="true">
                            <x-input name="email" type="email" placeholder="Enter email" icon="fas fa-envelope" />
                        </x-form-group>
                    </div>
                    
                    <div class="col-md-6">
                        <x-form-group label="Password" name="password" required="true" help="Minimum 8 characters">
                            <x-input name="password" type="password" placeholder="Enter password" icon="fas fa-lock" />
                        </x-form-group>
                    </div>
                    
                    <div class="col-md-6">
                        <x-form-group label="Confirm Password" name="password_confirmation" required="true">
                            <x-input name="password_confirmation" type="password" placeholder="Confirm password" icon="fas fa-lock" />
                        </x-form-group>
                    </div>
                    
                    <div class="col-md-6">
                        <x-form-group label="Phone Number" name="phone">
                            <x-input name="phone" type="tel" placeholder="Enter phone number" icon="fas fa-phone" />
                        </x-form-group>
                    </div>
                    
                    <div class="col-md-6">
                        <x-form-group label="Country" name="country" required="true">
                            <x-select name="country">
                                <option value="">Select country</option>
                                <option value="id">Indonesia</option>
                                <option value="my">Malaysia</option>
                                <option value="sg">Singapore</option>
                            </x-select>
                        </x-form-group>
                    </div>
                    
                    <div class="col-md-12">
                        <x-form-group label="Bio" name="bio">
                            <x-textarea name="bio" rows="4" placeholder="Tell us about yourself" />
                        </x-form-group>
                    </div>
                    
                    <div class="col-md-12">
                        <x-form-group label="Gender" name="gender" required="true">
                            <div class="d-flex gap-4">
                                <x-radio name="gender" value="male" label="Male" />
                                <x-radio name="gender" value="female" label="Female" />
                                <x-radio name="gender" value="other" label="Other" />
                            </div>
                        </x-form-group>
                    </div>
                    
                    <div class="col-md-12">
                        <x-form-group label="Profile Picture" name="avatar">
                            <x-file-upload name="avatar" accept="image/*" />
                        </x-form-group>
                    </div>
                    
                    <div class="col-md-12">
                        <x-form-group name="preferences">
                            <div class="d-flex flex-column gap-3">
                                <x-checkbox name="newsletter" label="Subscribe to newsletter" />
                                <x-checkbox name="terms" label="I agree to terms and conditions" />
                                <x-switch name="notifications" label="Enable email notifications" checked="true" />
                            </div>
                        </x-form-group>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="d-flex gap-3 justify-content-end">
                            <x-button variant="secondary" outline="true" type="button">Cancel</x-button>
                            <x-button variant="primary" type="submit" icon="fas fa-check">Register</x-button>
                        </div>
                    </div>
                </div>
            </form>
        </x-section-card>
    </div>

    <!-- Documentation Link -->
    <div class="demo-section">
        <x-info-card type="info" title="📚 Documentation">
            <p class="mb-2">Untuk dokumentasi lengkap dan contoh kode, lihat file:</p>
            <code>resources/views/components/README.md</code>
        </x-info-card>
    </div>
</div>
@endsection
