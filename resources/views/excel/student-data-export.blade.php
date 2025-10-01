{{-- Student Data CSV Export Template --}}
Student Name,First Name,Last Name,Grade Level,Section,Classroom,Guardian Email,Guardian Contact,Status
@foreach($students as $student)
{{ $student->name }},{{ $student->first_name }},{{ $student->last_name }},{{ $student->grade_level }},{{ $student->section }},{{ $student->classroom ? $student->classroom->display_name : 'N/A' }},{{ $student->guardian_email ?? 'N/A' }},{{ $student->guardian_contact ?? 'N/A' }},{{ $student->is_active ? 'Active' : 'Inactive' }}
@endforeach
