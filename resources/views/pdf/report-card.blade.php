<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Report Card - {{ $reportCard->student->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .school-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .report-title {
            font-size: 18px;
            margin-bottom: 5px;
        }
        .student-info {
            margin-bottom: 30px;
        }
        .info-row {
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 150px;
        }
        .grades-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .grades-table th,
        .grades-table td {
            border: 1px solid #333;
            padding: 8px;
            text-align: center;
        }
        .grades-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .summary {
            margin-top: 30px;
            padding: 20px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .summary-label {
            font-weight: bold;
        }
        .summary-value {
            font-size: 18px;
            font-weight: bold;
        }
        .passed {
            color: #28a745;
        }
        .failed {
            color: #dc3545;
        }
        .comments {
            margin-top: 30px;
            padding: 15px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="school-name">SUDLON ELEMENTARY SCHOOL</div>
        <div class="report-title">REPORT CARD</div>
        <div>School Year: {{ $reportCard->school_year }}</div>
    </div>

    <div class="student-info">
        <div class="info-row">
            <span class="info-label">Student Name:</span>
            <span>{{ $reportCard->student->name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Classroom:</span>
            <span>{{ $reportCard->classroom->name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Grade Level:</span>
            <span>{{ $reportCard->classroom->grade_level }} - {{ $reportCard->classroom->section }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">School Year:</span>
            <span>{{ $reportCard->school_year }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Quarter:</span>
            <span>{{ $reportCard->semester }}</span>
        </div>
    </div>

    <table class="grades-table">
        <thead>
            <tr>
                <th>Subject</th>
                <th>Grade</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @if(is_array($reportCard->grades))
                @foreach($reportCard->grades as $subject => $grade)
                <tr>
                    <td>{{ $subject }}</td>
                    <td>{{ number_format($grade, 1) }}</td>
                    <td>{{ $grade >= 75 ? 'Passed' : 'Failed' }}</td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="3" style="text-align: center;">No grades available</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="summary">
        <div class="summary-row">
            <span class="summary-label">Overall Average:</span>
            <span class="summary-value {{ $reportCard->average >= 75 ? 'passed' : 'failed' }}">
                {{ number_format($reportCard->average, 2) }}
            </span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Status:</span>
            <span class="summary-value {{ $reportCard->remarks === 'Passed' ? 'passed' : 'failed' }}">
                {{ $reportCard->remarks }}
            </span>
        </div>
    </div>

    @if($reportCard->teacher_comments)
    <div class="comments">
        <h4>Teacher's Comments:</h4>
        <p>{{ $reportCard->teacher_comments }}</p>
    </div>
    @endif

    <div style="margin-top: 50px;">
        <div style="display: flex; justify-content: space-between;">
            <div style="text-align: center;">
                <div style="margin-top: 50px;">Teacher's Signature</div>
            </div>
            <div style="text-align: center;">
                <div style="margin-top: 50px;">Principal's Signature</div>
            </div>
        </div>
    </div>

    <div style="margin-top: 30px; text-align: center; font-size: 12px; color: #666;">
        Generated on {{ $reportCard->generated_at ? $reportCard->generated_at->format('F d, Y \a\t g:i A') : now()->format('F d, Y \a\t g:i A') }}
    </div>
</body>
</html>