{{-- UI Components and Style Guide for Farm Mart --}}

## New UI Components Created

### 1. Toast Notification System
**Location:** `resources/views/layouts/toast.blade.php`
**Usage:** Included in the main layout automatically
**Features:**
- Success, Error, Info, and Warning toasts
- Auto-dismiss after 5 seconds
- Stacked notifications
- Smooth animations

**How to use in your code:**
```javascript
// In any JavaScript context after the page loads
window.toast.success('Product added successfully!');
window.toast.error('An error occurred');
window.toast.info('Information message');
window.toast.warning('Warning message');

// With custom duration (in ms, 0 for no auto-dismiss)
window.toast.success('Message', 5000);
```

### 2. Form Components
All form components are stored in `resources/views/layouts/` and include proper error handling and styling:

#### Form Input
**File:** `form-input.blade.php`
```blade
@include('layouts.form-input', [
    'name' => 'email',
    'label' => 'Email Address',
    'type' => 'email',
    'placeholder' => 'Enter your email',
    'icon' => '✉️',
    'required' => true,
    'hint' => 'We\'ll never share your email'
])
```

#### Form Textarea
**File:** `form-textarea.blade.php`
```blade
@include('layouts.form-textarea', [
    'name' => 'description',
    'label' => 'Description',
    'rows' => 4,
    'placeholder' => 'Enter product description',
    'required' => true
])
```

#### Form Select
**File:** `form-select.blade.php`
```blade
@include('layouts.form-select', [
    'name' => 'category',
    'label' => 'Category',
    'options' => ['vegetables' => 'Vegetables', 'fruits' => 'Fruits'],
    'placeholder' => 'Select a category',
    'required' => true
])
```

#### Form Button
**File:** `form-button.blade.php`
```blade
@include('layouts.form-button', [
    'type' => 'submit',
    'variant' => 'primary', // primary, secondary, danger, success, warning, outline
    'size' => 'md', // sm, md, lg
    'icon' => '✓',
    'loading' => false,
    'slot' => 'Save Changes'
])
```

#### Form Checkbox
**File:** `form-checkbox.blade.php`
```blade
@include('layouts.form-checkbox', [
    'name' => 'agree',
    'label' => 'I agree to the terms',
    'value' => 1,
    'required' => true
])
```

### 3. Loading Spinner
**File:** `loading-spinner.blade.php`
**Sizes:** sm, md, lg
**Usage:**
```blade
@include('layouts.loading-spinner', [
    'size' => 'md',
    'text' => 'Loading...',
    'dark' => false
])
```

**For skeleton loaders:**
```javascript
showSkeletonLoader('#data-table', 5); // Show 5 skeleton rows
// After loading
hideSkeletonLoader('#data-table');
```

## Agricultural Theme Colors

The application now uses agricultural theme colors:
- **Primary Green:** #22c55e (Agricultural) - used for main actions
- **Secondary Brown:** #92400e (Earth) - used for secondary elements
- **Accent Gold:** #f59e0b (Harvest) - used for highlights

These are available as CSS variables in `resources/css/app.css`:
```css
--color-ag-primary: #22c55e;
--color-ag-secondary: #92400e;
--color-ag-accent: #f59e0b;
```

## Enhanced Dashboard Features

### Farmer Dashboard Improvements
- Stats cards with hover effects and transitions
- Quick action buttons for adding products and viewing orders
- Enhanced table with better visual hierarchy
- Gradient header styling
- Improved empty state with call-to-action

### Consumer Dashboard Improvements
- Enhanced cart and orders overview
- Product recommendations with better styling
- Card hover animations
- Improved visual feedback
- Empty state handling

## Flash Message Integration

Flash messages are automatically converted to toast notifications:
- Success messages appear as green toasts
- Error messages appear as red toasts
- Validation errors appear as individual error toasts

## Chart.js Integration

To use Chart.js in your views, include it in your blade templates:
```blade
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
<canvas id="myChart"></canvas>

<script>
    const ctx = document.getElementById('myChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr'],
            datasets: [{
                label: 'Sales',
                data: [12, 19, 3, 5],
                borderColor: '#22c55e',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
            }]
        }
    });
</script>
```

## Responsive Design Improvements

The application now includes:
- Enhanced mobile menu readiness
- Better card layouts on mobile (stacked to grid)
- Improved form spacing on mobile devices
- Responsive tables that convert to card view on small screens

## Installation Notes

### Dependencies to Install
```bash
npm install chart.js @chartjs/adapter-date-fns
```

### Alpine.js
Alpine.js is already included via CDN in the main layout for the toast system and other interactive components.

## Best Practices

1. **Always use the form components** - They include validation error handling automatically
2. **Use toast notifications** - They're better UX than page-level alerts
3. **Respect dark mode** - All components have dark mode support via `dark:` Tailwind classes
4. **Use icons consistently** - The emoji approach is simple and cross-platform friendly
5. **Test on mobile** - The responsive design requires testing on actual devices

## File Organization

```
resources/views/
├── layouts/
│   ├── app.blade.php              (Main layout with toast)
│   ├── toast.blade.php            (Toast notification system)
│   ├── form-input.blade.php       (Input component)
│   ├── form-textarea.blade.php    (Textarea component)
│   ├── form-select.blade.php      (Select component)
│   ├── form-button.blade.php      (Button component)
│   ├── form-checkbox.blade.php    (Checkbox component)
│   └── loading-spinner.blade.php  (Spinner component)
├── dashboard/
│   ├── farmer.blade.php           (Enhanced farmer dashboard)
│   └── consumer.blade.php         (Enhanced consumer dashboard)
└── ... (other views)
```

## Future Enhancements

Recommended additions:
1. Form validation in JavaScript using Alpine.js
2. Product image upload preview
3. Analytics dashboard with Chart.js
4. Real-time notifications using WebSockets
5. Mobile-specific components
6. Form wizard for multi-step processes
