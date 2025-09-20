<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .title { font-size: 18px; font-weight: bold; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px; }
        th { background: #f2f2f2; text-align: left; }
    </style>
    <title>Classroom Students</title>
 </head>
<body>
    <div class="title">{{ $classroom->name }} - Students</div>
    <table>
        <thead>
            <tr>
                <th style="width: 80px;">#</th>
                <th>Name</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $index => $student)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $student->name }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

