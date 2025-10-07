# Bootstrap 5 Quick Reference for Laravel

## Common Bootstrap Classes Used in Your Application

### Layout & Grid
```html
<!-- Container -->
<div class="container">...</div>
<div class="container-fluid">...</div>

<!-- Row & Columns -->
<div class="row">
    <div class="col-12 col-md-6 col-lg-4">...</div>
    <div class="col-12 col-md-6 col-lg-8">...</div>
</div>
```

### Components

#### Buttons
```html
<button class="btn btn-primary">Primary</button>
<button class="btn btn-secondary">Secondary</button>
<button class="btn btn-success">Success</button>
<button class="btn btn-danger">Danger</button>
<button class="btn btn-warning">Warning</button>
<button class="btn btn-info">Info</button>
<button class="btn btn-light">Light</button>
<button class="btn btn-dark">Dark</button>

<!-- Outline buttons -->
<button class="btn btn-outline-primary">Outline</button>

<!-- Sizes -->
<button class="btn btn-lg btn-primary">Large</button>
<button class="btn btn-sm btn-primary">Small</button>
```

#### Cards
```html
<div class="card">
    <div class="card-header">Card Header</div>
    <div class="card-body">
        <h5 class="card-title">Card Title</h5>
        <p class="card-text">Card content goes here.</p>
        <a href="#" class="btn btn-primary">Button</a>
    </div>
    <div class="card-footer">Card Footer</div>
</div>
```

#### Navbar
```html
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="#">Brand</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">About</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
```

#### Forms
```html
<form>
    <div class="mb-3">
        <label for="email" class="form-label">Email address</label>
        <input type="email" class="form-control" id="email" placeholder="name@example.com">
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password">
    </div>
    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="remember">
        <label class="form-check-label" for="remember">Remember me</label>
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>
```

#### Modals
```html
<!-- Button trigger -->
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
    Launch Modal
</button>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modal title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Modal body content...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>
```

#### Alerts
```html
<div class="alert alert-primary" role="alert">Primary alert</div>
<div class="alert alert-success" role="alert">Success alert</div>
<div class="alert alert-danger" role="alert">Danger alert</div>
<div class="alert alert-warning" role="alert">Warning alert</div>

<!-- Dismissible -->
<div class="alert alert-success alert-dismissible fade show" role="alert">
    Success message!
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
```

### Utilities

#### Spacing (Margin & Padding)
```html
<!-- Margin: m-{side}-{size} -->
<div class="m-3">Margin all sides</div>
<div class="mt-2">Margin top</div>
<div class="mb-4">Margin bottom</div>
<div class="ms-auto">Margin start (left)</div>
<div class="me-3">Margin end (right)</div>
<div class="mx-5">Margin horizontal</div>
<div class="my-2">Margin vertical</div>

<!-- Padding: p-{side}-{size} -->
<div class="p-3">Padding all sides</div>
<div class="pt-2">Padding top</div>
<div class="pb-4">Padding bottom</div>
<div class="px-5">Padding horizontal</div>
<div class="py-2">Padding vertical</div>

<!-- Sizes: 0, 1, 2, 3, 4, 5, auto -->
```

#### Display
```html
<div class="d-none">Hidden</div>
<div class="d-block">Block</div>
<div class="d-inline">Inline</div>
<div class="d-inline-block">Inline block</div>
<div class="d-flex">Flex</div>
<div class="d-grid">Grid</div>

<!-- Responsive -->
<div class="d-none d-md-block">Hidden on mobile, visible on tablet+</div>
```

#### Flexbox
```html
<div class="d-flex justify-content-center">Centered</div>
<div class="d-flex justify-content-between">Space between</div>
<div class="d-flex align-items-center">Vertically centered</div>
<div class="d-flex flex-column">Column direction</div>
<div class="d-flex flex-row">Row direction</div>
```

#### Text
```html
<!-- Alignment -->
<p class="text-start">Left aligned</p>
<p class="text-center">Center aligned</p>
<p class="text-end">Right aligned</p>

<!-- Colors -->
<p class="text-primary">Primary text</p>
<p class="text-success">Success text</p>
<p class="text-danger">Danger text</p>
<p class="text-muted">Muted text</p>
<p class="text-white">White text</p>

<!-- Font Weight -->
<p class="fw-bold">Bold text</p>
<p class="fw-normal">Normal weight</p>
<p class="fw-light">Light weight</p>

<!-- Font Size -->
<p class="fs-1">Font size 1 (largest)</p>
<p class="fs-6">Font size 6 (smallest)</p>
```

#### Background Colors
```html
<div class="bg-primary text-white">Primary background</div>
<div class="bg-success text-white">Success background</div>
<div class="bg-danger text-white">Danger background</div>
<div class="bg-light">Light background</div>
<div class="bg-dark text-white">Dark background</div>
```

#### Sizing
```html
<!-- Width -->
<div class="w-25">Width 25%</div>
<div class="w-50">Width 50%</div>
<div class="w-75">Width 75%</div>
<div class="w-100">Width 100%</div>

<!-- Height -->
<div class="h-25">Height 25%</div>
<div class="h-50">Height 50%</div>
<div class="h-75">Height 75%</div>
<div class="h-100">Height 100%</div>

<!-- Min/Max -->
<div class="min-vh-100">Min viewport height 100%</div>
<div class="min-vw-100">Min viewport width 100%</div>
```

#### Borders
```html
<div class="border">Border all sides</div>
<div class="border-top">Border top only</div>
<div class="border-0">No border</div>
<div class="border border-primary">Primary colored border</div>
<div class="rounded">Rounded corners</div>
<div class="rounded-circle">Circle</div>
```

#### Shadows
```html
<div class="shadow-sm">Small shadow</div>
<div class="shadow">Regular shadow</div>
<div class="shadow-lg">Large shadow</div>
```

### Responsive Breakpoints
```
- xs: < 576px
- sm: ≥ 576px
- md: ≥ 768px
- lg: ≥ 992px
- xl: ≥ 1200px
- xxl: ≥ 1400px

Usage: .{property}-{breakpoint}-{value}
Example: .d-none .d-md-block (hidden on mobile, visible on tablet+)
```

## Laravel Blade Examples with Bootstrap

### Layout Template
```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="/">{{ config('app.name') }}</a>
            <!-- Navigation items -->
        </div>
    </nav>

    <main class="py-4">
        <div class="container">
            @yield('content')
        </div>
    </main>

    <footer class="bg-light py-3 mt-5">
        <div class="container text-center">
            <p class="mb-0">&copy; {{ date('Y') }} {{ config('app.name') }}</p>
        </div>
    </footer>
</body>
</html>
```

### Using the Layout
```blade
@extends('layouts.app')

@section('title', 'Page Title')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h1>Welcome to Bootstrap!</h1>
            <p class="lead">Your content here...</p>
        </div>
    </div>
@endsection
```

## JavaScript Component Usage

```javascript
// In your Blade file or JS
// Modal
const myModal = new bootstrap.Modal(document.getElementById('myModal'));
myModal.show();

// Toast
const toastEl = document.getElementById('myToast');
const toast = new bootstrap.Toast(toastEl);
toast.show();

// Tooltip
const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
});
```

## Customizing Bootstrap

Edit `resources/css/app.scss`:

```scss
// Override Bootstrap variables BEFORE importing
$primary: #ff6b6b;
$success: #51cf66;
$danger: #ff6b6b;

// Import Bootstrap
@import 'bootstrap/scss/bootstrap';

// Your custom styles
body {
    font-family: 'Instrument Sans', sans-serif;
}

.custom-class {
    // Your custom styles
}
```

## Resources

- **Bootstrap Documentation**: https://getbootstrap.com/docs/5.3/
- **Bootstrap Icons**: https://icons.getbootstrap.com/
- **Bootstrap Themes**: https://themes.getbootstrap.com/
- **Bootstrap Examples**: https://getbootstrap.com/docs/5.3/examples/

---

**Note**: All these Bootstrap features are now available in your Laravel application!
