<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Data Export</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #2563eb;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .filters {
            background-color: #f8f9fa;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .filters h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #333;
        }
        .filter-item {
            display: inline-block;
            margin-right: 20px;
            margin-bottom: 5px;
        }
        .filter-label {
            font-weight: bold;
            color: #555;
        }
        .filter-value {
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .grade-item {
            display: inline-block;
            background-color: #e3f2fd;
            padding: 2px 6px;
            margin: 1px;
            border-radius: 3px;
            font-size: 10px;
        }
        .summary {
            margin-top: 30px;
            padding: 15px;
            background-color: #f0f9ff;
            border-radius: 5px;
        }
        .summary h3 {
            margin: 0 0 10px 0;
            color: #1e40af;
        }
        .summary-item {
            margin-bottom: 5px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Student Data Export</h1>
        <p>Generated on: {{ date('F d, Y \a\t h:i A') }}</p>
        <p>Total Students: {{ $students->count() }}</p>
    </div>

    @if($filters['search'] || $filters['grade_level'] || $filters['section'] || $filters['classroom'])
    <div class="filters">
        <h3>Applied Filters:</h3>
        @if($filters['search'])
            <div class="filter-item">
                <span class="filter-label">Search:</span>
                <span class="filter-value">{{ $filters['search'] }}</span>
            </div>
        @endif
        @if($filters['grade_level'])
            <div class="filter-item">
                <span class="filter-label">Grade Level:</span>
                <span class="filter-value">Grade {{ $filters['grade_level'] }}</span>
            </div>
        @endif
        @if($filters['section'])
            <div class="filter-item">
                <span class="filter-label">Section:</span>
                <span class="filter-value">{{ $filters['section'] }}</span>
            </div>
        @endif
        @if($filters['classroom'])
            <div class="filter-item">
                <span class="filter-label">Classroom:</span>
                <span class="filter-value">{{ $filters['classroom'] }}</span>
            </div>
        @endif
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Email</th>
                <th>Grade Level</th>
                <th>Section</th>
                <th>Classroom</th>
                <th>Grades</th>
                <th>Average</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $student)
            <tr>
                <td>{{ $student->name }}</td>
                <td>{{ $student->email }}</td>
                <td>Grade {{ $student->grade_level }}</td>
                <td>{{ $student->section }}</td>
                <td>{{ $student->classroom?->name ?? 'No Classroom' }}</td>
                <td>
                    @foreach($student->grades as $grade)
                        <span class="grade-item">{{ $grade->subject }}: {{ $grade->score }}</span>
                    @endforeach
                </td>
                <td>
                    @if($student->grades->count() > 0)
                        {{ number_format($student->grades->avg('score'), 2) }}
                    @else
                        N/A
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <h3>Summary Statistics</h3>
        <div class="summary-item">
            <strong>Total Students:</strong> {{ $students->count() }}
        </div>
        <div class="summary-item">
            <strong>Students with Grades:</strong> {{ $students->filter(fn($s) => $s->grades->count() > 0)->count() }}
        </div>
        <div class="summary-item">
            <strong>Average Grade (All Students):</strong> 
            @php
                $allGrades = $students->flatMap->grades;
                $avgGrade = $allGrades->count() > 0 ? $allGrades->avg('score') : 0;
            @endphp
            {{ number_format($avgGrade, 2) }}
        </div>
        <div class="summary-item">
            <strong>Grade Level Distribution:</strong>
            @foreach($students->groupBy('grade_level') as $grade => $gradeStudents)
                Grade {{ $grade }}: {{ $gradeStudents->count() }} students
                @if(!$loop->last), @endif
            @endforeach
        </div>
    </div>

    <div class="footer">
        <p>This report was generated by the Sudlon System on {{ date('F d, Y \a\t h:i A') }}</p>
        <p>For questions or concerns, please contact the school administration.</p>
    </div>
</body>
</html>
