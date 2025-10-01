# Excel Export Templates

This folder contains templates and documentation for Excel/CSV export functionality.

## Files

### CSV Export Templates
- `students-export.blade.php` - Template for exporting student data to CSV
- `grades-export.blade.php` - Template for exporting grades data to CSV  
- `student-data-export.blade.php` - Template for exporting detailed student information to CSV

### Excel Export Documentation
- `attendance-export.blade.php` - Documentation for attendance Excel export structure

## Usage

### CSV Exports
CSV exports use Blade templates to generate properly formatted CSV content:

```php
$csv = view('excel.students-export', compact('students'))->render();
return response()->streamDownload(function() use ($csv) {
    echo $csv;
}, 'filename.csv');
```

### Excel Exports
Excel exports use PhpSpreadsheet library and are handled in the controllers:

```php
use App\Helpers\ExcelHelper;

$spreadsheet = ExcelHelper::generateAttendanceExcel($classroom, $subjects, $students, $attendanceMap);
$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
```

## Dependencies

- `phpoffice/phpspreadsheet` - For Excel file generation
- Laravel Blade - For CSV template rendering

## Export Functions

1. **Students Export** - Exports student list with basic information
2. **Grades Export** - Exports grades data for all students
3. **Student Data Export** - Exports detailed student information including guardian details
4. **Attendance Export** - Exports attendance data as Excel with multiple worksheets per subject
