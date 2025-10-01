<?php

namespace App\Helpers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class ExcelHelper
{
    /**
     * Generate attendance Excel file for a classroom
     */
    public static function generateAttendanceExcel($classroom, $subjects, $students, $attendanceMap)
    {
        $spreadsheet = new Spreadsheet();
        $sheetIndex = 0;

        foreach ($subjects as $subject) {
            if ($sheetIndex === 0) {
                $sheet = $spreadsheet->getActiveSheet();
            } else {
                $sheet = $spreadsheet->createSheet();
            }
            
            $sheet->setTitle(substr($subject->name, 0, 31));

            // Get distinct attendance dates for this subject
            $dates = collect($attendanceMap)
                ->filter(function ($attendance, $key) use ($subject) {
                    return strpos($key, $subject->id . '|') === 0;
                })
                ->keys()
                ->map(function ($key) {
                    return explode('|', $key)[1];
                })
                ->unique()
                ->sort()
                ->values();

            // Header
            $sheet->setCellValue('A1', 'Subject: ' . $subject->name);
            $headerRow = 2;
            $colIdx = 1;
            $colLetter = Coordinate::stringFromColumnIndex($colIdx);
            $sheet->setCellValue($colLetter . $headerRow, 'Student Name');
            $colIdx++;
            
            foreach ($dates as $date) {
                $colLetter = Coordinate::stringFromColumnIndex($colIdx);
                $sheet->setCellValue($colLetter . $headerRow, (string)$date);
                $colIdx++;
            }
            
            $lastHeaderCol = Coordinate::stringFromColumnIndex(max(1, $dates->count() + 1));
            $sheet->getStyle('A' . $headerRow . ':' . $lastHeaderCol . $headerRow)->getFont()->setBold(true);

            // Student rows
            $row = $headerRow + 1;
            foreach ($students as $student) {
                $colIdx = 1;
                $colLetter = Coordinate::stringFromColumnIndex($colIdx);
                $sheet->setCellValue($colLetter . $row, $student->name);
                $colIdx++;
                
                foreach ($dates as $date) {
                    $key = $subject->id . '|' . $student->id . '|' . $date;
                    $value = '';
                    if (isset($attendanceMap[$key])) {
                        $att = $attendanceMap[$key];
                        if ($att && strtolower((string)$att->status) === 'present') {
                            $value = 'Present';
                        }
                    }
                    $colLetter = Coordinate::stringFromColumnIndex($colIdx);
                    $sheet->setCellValue($colLetter . $row, $value);
                    $colIdx++;
                }
                $row++;
            }

            // Auto-size columns
            for ($i = 1; $i <= max(1, $dates->count() + 1); $i++) {
                $letter = Coordinate::stringFromColumnIndex($i);
                $sheet->getColumnDimension($letter)->setAutoSize(true);
            }

            $sheetIndex++;
        }

        return $spreadsheet;
    }

    /**
     * Generate CSV content from data
     */
    public static function generateCsv($data, $headers = [])
    {
        $csv = '';
        
        if (!empty($headers)) {
            $csv .= implode(',', $headers) . "\n";
        }
        
        foreach ($data as $row) {
            $csv .= implode(',', $row) . "\n";
        }
        
        return $csv;
    }
}
