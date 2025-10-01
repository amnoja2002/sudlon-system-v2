# Role-Based File Organization

This directory contains all role-specific Livewire components organized by user roles.

## Directory Structure

```
app/Livewire/Roles/
├── Parent/
│   ├── Dashboard.php      # Parent dashboard functionality
│   └── Portal.php         # Parent portal component
├── Teacher/
│   ├── Dashboard.php      # Main teacher dashboard
│   ├── AdvancedDashboard.php  # Advanced teacher dashboard with more features
│   └── Portal.php         # Teacher portal component
└── Principal/
    └── Dashboard.php      # Principal dashboard functionality
```

## View Organization

Corresponding views are organized in:
```
resources/views/livewire/roles/
├── parent/
│   └── dashboard.blade.php
├── teacher/
│   ├── dashboard.blade.php
│   └── components/
│       ├── attendance.blade.php
│       ├── classrooms.blade.php
│       ├── reports.blade.php
│       ├── students.blade.php
│       └── subjects.blade.php
└── principal/
    └── dashboard.blade.php
```

## Role-Specific Features

### Parent Role
- View student information
- Access grades and attendance
- Browse classrooms and teachers
- Student shortcuts management

### Teacher Role
- Classroom management
- Student management
- Subject management
- Grade management
- Attendance tracking
- Report generation
- Excel/CSV exports

### Principal Role
- User management
- Role management
- System overview
- Student management
- Report access

## Route Configuration

Routes are configured in `routes/web.php` to point to the appropriate role-specific classes:

```php
// Principal Dashboard
Route::get('/principal/dashboard', \App\Livewire\Roles\Principal\Dashboard::class)
    ->middleware('role:principal')
    ->name('principal.dashboard');

// Teacher Dashboard
Route::get('/teacher/dashboard', \App\Livewire\Roles\Teacher\Dashboard::class)
    ->middleware('role:teacher')
    ->name('teacher.dashboard');

// Parent Dashboard
Route::get('/parent/dashboard', \App\Livewire\Roles\Parent\Dashboard::class)
    ->middleware('role:parent')
    ->name('parent.dashboard');
```

## Benefits of This Organization

1. **Clear Separation**: Each role has its own dedicated namespace and directory
2. **Maintainability**: Easy to find and modify role-specific functionality
3. **Scalability**: Easy to add new roles or extend existing ones
4. **Code Reusability**: Common functionality can be shared through traits or base classes
5. **Security**: Role-based access control is clearly defined
