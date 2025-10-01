{{-- Students CSV Export Template --}}
Name,Email,Grade Level,Section,Classroom
@foreach($students as $student)
{{ $student->name }},{{ $student->email }},{{ $student->grade_level }},{{ $student->section }},{{ $student->classroom ? $student->classroom->display_name : 'N/A' }}
@endforeach
