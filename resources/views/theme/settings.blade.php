@extends('layouts.app')

@section('title', 'Cài đặt giao diện')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Cài đặt giao diện</h4>
                </div>
                <div class="card-body">
                    <form id="themeSettingsForm">
                        @csrf
                        
                        <!-- Theme Selection -->
                        <div class="mb-4">
                            <label class="form-label">Giao diện</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="theme" id="themeLight" value="light" 
                                           {{ $themeSettings->theme === 'light' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="themeLight">
                                        <i class="fas fa-sun"></i> Sáng
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="theme" id="themeDark" value="dark"
                                           {{ $themeSettings->theme === 'dark' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="themeDark">
                                        <i class="fas fa-moon"></i> Tối
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="theme" id="themeCustom" value="custom"
                                           {{ $themeSettings->theme === 'custom' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="themeCustom">
                                        <i class="fas fa-paint-brush"></i> Tùy chỉnh
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Font Settings -->
                        <div class="mb-4">
                            <label class="form-label">Font chữ</label>
                            <select class="form-select" name="font_family">
                                <option value="Arial" {{ $themeSettings->font_family === 'Arial' ? 'selected' : '' }}>Arial</option>
                                <option value="Roboto" {{ $themeSettings->font_family === 'Roboto' ? 'selected' : '' }}>Roboto</option>
                                <option value="Open Sans" {{ $themeSettings->font_family === 'Open Sans' ? 'selected' : '' }}>Open Sans</option>
                                <option value="Montserrat" {{ $themeSettings->font_family === 'Montserrat' ? 'selected' : '' }}>Montserrat</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Cỡ chữ</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="font_size" id="fontSmall" value="small"
                                           {{ $themeSettings->font_size === 'small' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="fontSmall">Nhỏ</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="font_size" id="fontMedium" value="medium"
                                           {{ $themeSettings->font_size === 'medium' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="fontMedium">Vừa</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="font_size" id="fontLarge" value="large"
                                           {{ $themeSettings->font_size === 'large' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="fontLarge">Lớn</label>
                                </div>
                            </div>
                        </div>

                        <!-- Color Settings -->
                        <div class="mb-4">
                            <label class="form-label">Màu chủ đạo</label>
                            <input type="color" class="form-control form-control-color" name="primary_color" 
                                   value="{{ $themeSettings->primary_color }}">
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Màu phụ</label>
                            <input type="color" class="form-control form-control-color" name="secondary_color"
                                   value="{{ $themeSettings->secondary_color }}">
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Màu nền</label>
                            <input type="color" class="form-control form-control-color" name="background_color"
                                   value="{{ $themeSettings->background_color }}">
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Màu chữ</label>
                            <input type="color" class="form-control form-control-color" name="text_color"
                                   value="{{ $themeSettings->text_color }}">
                        </div>

                        <!-- Compact Mode -->
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="compact_mode" id="compactMode"
                                       {{ $themeSettings->compact_mode ? 'checked' : '' }}>
                                <label class="form-check-label" for="compactMode">Chế độ gọn</label>
                            </div>
                        </div>

                        <!-- Custom CSS -->
                        <div class="mb-4">
                            <label class="form-label">CSS tùy chỉnh</label>
                            <textarea class="form-control" name="custom_css" rows="4" 
                                      placeholder="Nhập CSS tùy chỉnh của bạn...">{{ $themeSettings->custom_css }}</textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary" id="resetSettings">
                                <i class="fas fa-undo"></i> Đặt lại
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Lưu cài đặt
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('themeSettingsForm');
    const resetButton = document.getElementById('resetSettings');

    // Preview theme changes
    function updatePreview() {
        const formData = new FormData(form);
        const settings = Object.fromEntries(formData.entries());
        
        // Apply theme changes
        document.documentElement.style.setProperty('--primary-color', settings.primary_color);
        document.documentElement.style.setProperty('--secondary-color', settings.secondary_color);
        document.documentElement.style.setProperty('--background-color', settings.background_color);
        document.documentElement.style.setProperty('--text-color', settings.text_color);
        document.documentElement.style.setProperty('--font-family', settings.font_family);
        
        // Apply font size
        const fontSizeMap = {
            'small': '14px',
            'medium': '16px',
            'large': '18px'
        };
        document.documentElement.style.setProperty('--font-size', fontSizeMap[settings.font_size]);
        
        // Apply compact mode
        document.body.classList.toggle('compact-mode', settings.compact_mode === 'on');
        
        // Apply theme
        document.body.classList.remove('theme-light', 'theme-dark', 'theme-custom');
        document.body.classList.add(`theme-${settings.theme}`);
    }

    // Add event listeners for real-time preview
    form.querySelectorAll('input, select').forEach(element => {
        element.addEventListener('change', updatePreview);
    });

    // Handle form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('{{ route("theme.update") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi lưu cài đặt');
        });
    });

    // Handle reset
    resetButton.addEventListener('click', function() {
        if (confirm('Bạn có chắc chắn muốn đặt lại tất cả cài đặt?')) {
            fetch('{{ route("theme.reset") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi đặt lại cài đặt');
            });
        }
    });

    // Initial preview
    updatePreview();
});
</script>
@endpush

@push('styles')
<style>
:root {
    --primary-color: {{ $themeSettings->primary_color }};
    --secondary-color: {{ $themeSettings->secondary_color }};
    --background-color: {{ $themeSettings->background_color }};
    --text-color: {{ $themeSettings->text_color }};
    --font-family: {{ $themeSettings->font_family }};
    --font-size: {{ $themeSettings->font_size === 'small' ? '14px' : ($themeSettings->font_size === 'large' ? '18px' : '16px') }};
}

body {
    font-family: var(--font-family);
    font-size: var(--font-size);
    background-color: var(--background-color);
    color: var(--text-color);
}

.theme-dark {
    --background-color: #1a1a1a;
    --text-color: #ffffff;
}

.compact-mode .card {
    margin-bottom: 0.5rem;
}

.compact-mode .card-body {
    padding: 0.75rem;
}

.form-control-color {
    width: 100%;
    height: 38px;
}
</style>
@endpush 