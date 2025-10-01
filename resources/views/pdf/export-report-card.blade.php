<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Card - {{ $student->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
        }
        .school-name {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 5px;
        }
        .report-title {
            font-size: 18px;
            color: #333;
            margin-bottom: 10px;
        }
        .school-year {
            font-size: 14px;
            color: #666;
        }
        .student-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }
        .student-details {
            flex: 1;
        }
        .student-details h3 {
            margin: 0 0 10px 0;
            color: #333;
            font-size: 16px;
        }
        .info-row {
            display: flex;
            margin-bottom: 5px;
        }
        .info-label {
            font-weight: bold;
            width: 120px;
            color: #555;
        }
        .info-value {
            color: #333;
        }
        .grades-section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 15px;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f3f4f6;
            font-weight: bold;
            color: #333;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .grade-cell {
            text-align: center;
            font-weight: bold;
        }
        .excellent { color: #059669; }
        .very-good { color: #0891b2; }
        .good { color: #ca8a04; }
        .fair { color: #dc2626; }
        .needs-improvement { color: #dc2626; }
        .summary-section {
            background-color: #f0f9ff;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 30px;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .summary-item {
            margin-bottom: 10px;
        }
        .summary-label {
            font-weight: bold;
            color: #1e40af;
        }
        .summary-value {
            color: #333;
            font-size: 14px;
        }
        .comments-section {
            margin-bottom: 30px;
        }
        .comments-box {
            background-color: #f8f9fa;
            border: 1px solid #e5e7eb;
            border-radius: 5px;
            padding: 15px;
            min-height: 100px;
        }
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
        }
        .signature-box {
            text-align: center;
            width: 200px;
        }
        .signature-line {
            border-bottom: 1px solid #333;
            height: 40px;
            margin-bottom: 5px;
        }
        .signature-label {
            font-size: 12px;
            color: #666;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .grade-scale {
            background-color: #fef3c7;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .grade-scale h4 {
            margin: 0 0 10px 0;
            color: #92400e;
        }
        .scale-item {
            display: inline-block;
            margin-right: 15px;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="school-name">SUDLON ELEMENTARY SCHOOL</div>
        <div class="report-title">REPORT CARD</div>
        <div class="school-year">School Year {{ $reportCard->school_year }} - {{ $reportCard->semester }} Semester</div>
    </div>

    <div class="student-info">
        <div class="student-details">
            <h3>Student Information</h3>
            <div class="info-row">
                <span class="info-label">Name:</span>
                <span class="info-value">{{ $student->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Grade Level:</span>
                <span class="info-value">Grade {{ $student->grade_level }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Section:</span>
                <span class="info-value">{{ $student->section }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Classroom:</span>
                <span class="info-value">{{ $student->classroom?->name ?? 'N/A' }}</span>
            </div>
        </div>
        <div class="student-details">
            <h3>Academic Information</h3>
            <div class="info-row">
                <span class="info-label">School Year:</span>
                <span class="info-value">{{ $reportCard->school_year }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Semester:</span>
                <span class="info-value">{{ $reportCard->semester }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Generated:</span>
                <span class="info-value">{{ $reportCard->generated_at?->format('M d, Y') ?? 'N/A' }}</span>
            </div>
        </div>
    </div>

    <div class="grade-scale">
        <h4>Grading Scale</h4>
        <div class="scale-item"><strong>96-100:</strong> Excellent</div>
        <div class="scale-item"><strong>90-95:</strong> Very Good</div>
        <div class="scale-item"><strong>85-89:</strong> Good</div>
        <div class="scale-item"><strong>80-84:</strong> Fair</div>
        <div class="scale-item"><strong>Below 80:</strong> Needs Improvement</div>
    </div>

    <div class="grades-section">
        <div class="section-title">Subject Grades</div>
        <table>
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>1st Quarter</th>
                    <th>2nd Quarter</th>
                    <th>3rd Quarter</th>
                    <th>4th Quarter</th>
                    <th>Final Grade</th>
                </tr>
            </thead>
            <tbody>
                @if(is_array($reportCard->grades) && count($reportCard->grades) > 0)
                    @foreach($reportCard->grades as $subject => $grades)
                    <tr>
                        <td><strong>{{ $subject }}</strong></td>
                        <td class="grade-cell">{{ $grades['1st'] ?? 'N/A' }}</td>
                        <td class="grade-cell">{{ $grades['2nd'] ?? 'N/A' }}</td>
                        <td class="grade-cell">{{ $grades['3rd'] ?? 'N/A' }}</td>
                        <td class="grade-cell">{{ $grades['4th'] ?? 'N/A' }}</td>
                        <td class="grade-cell">
                            @php
                                $quarterGrades = array_filter([$grades['1st'] ?? null, $grades['2nd'] ?? null, $grades['3rd'] ?? null, $grades['4th'] ?? null]);
                                $finalGrade = count($quarterGrades) > 0 ? array_sum($quarterGrades) / count($quarterGrades) : 'N/A';
                            @endphp
                            {{ is_numeric($finalGrade) ? number_format($finalGrade, 2) : $finalGrade }}
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="6" style="text-align: center; color: #666;">No grades available</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="summary-section">
        <div class="section-title">Academic Summary</div>
        <div class="summary-grid">
            <div>
                <div class="summary-item">
                    <span class="summary-label">General Average:</span>
                    <span class="summary-value">{{ number_format($reportCard->average, 2) }}</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Remarks:</span>
                    <span class="summary-value {{ strtolower(str_replace(' ', '-', $reportCard->remarks)) }}">{{ $reportCard->remarks }}</span>
                </div>
            </div>
            <div>
                <div class="summary-item">
                    <span class="summary-label">Total Subjects:</span>
                    <span class="summary-value">{{ is_array($reportCard->grades) ? count($reportCard->grades) : 0 }}</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Status:</span>
                    <span class="summary-value">{{ $reportCard->average >= 75 ? 'PASSED' : 'FAILED' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="comments-section">
        <div class="section-title">Teacher's Comments</div>
        <div class="comments-box">
            {{ $reportCard->teacher_comments ?: 'No comments available.' }}
        </div>
    </div>

    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line"></div>
            <div class="signature-label">Classroom Teacher</div>
        </div>
        <div class="signature-box">
            <div class="signature-line"></div>
            <div class="signature-label">Principal</div>
        </div>
    </div>

    <div class="footer">
        <p>This report card was generated by the Sudlon System on {{ date('F d, Y \a\t h:i A') }}</p>
        <p>For questions or concerns, please contact the school administration.</p>
    </div>
</body>
</html>
