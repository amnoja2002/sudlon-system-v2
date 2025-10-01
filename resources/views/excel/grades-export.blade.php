{{-- Grades CSV Export Template --}}
Student,Subject,Term,Score
@foreach($grades as $grade)
{{ $grade->student->name }},{{ $grade->subject }},{{ $grade->term }},{{ $grade->score }}
@endforeach
