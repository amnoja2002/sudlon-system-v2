<?php

namespace App\Livewire;

use Livewire\Component;

class Home extends Component
{
    public function render()
    {
        $news = collect([
            [
                'title' => 'Early Registration for School Year 2025-2026',
                'category' => 'Announcement',
                'category_color' => 'red',
                'text_color' => 'red',
                'date' => now()->subDays(2),
                'excerpt' => 'Early registration for incoming Kindergarten, Grades 1-6 students is now open.',
            ],
            [
                'title' => 'Brigada Eskwela Success',
                'category' => 'Event',
                'category_color' => 'deped',
                'text_color' => 'deped-600',
                'date' => now()->subDays(5),
                'excerpt' => 'Thank you to all volunteers and partners who helped prepare our school for the new academic year.',
            ],
            [
                'title' => 'Academic Excellence Awards',
                'category' => 'Achievement',
                'category_color' => 'gold',
                'text_color' => 'gold-600',
                'date' => now()->subDays(8),
                'excerpt' => 'Congratulations to our students who demonstrated outstanding academic performance this quarter.',
            ],
        ]);

        $quickLinks = collect([
            [
                'title' => 'Enrollment',
                'icon' => 'clipboard-list',
                'description' => 'Registration procedures and requirements',
                'link' => '#enrollment'
            ],
            [
                'title' => 'Calendar',
                'icon' => 'calendar',
                'description' => 'School events and activities',
                'link' => '#calendar'
            ],
            [
                'title' => 'K-6 Program',
                'icon' => 'academic-cap',
                'description' => 'Curriculum and learning areas',
                'link' => '#curriculum'
            ],
            [
                'title' => 'Student Services',
                'icon' => 'users',
                'description' => 'Support and resources',
                'link' => '#services'
            ],
        ]);

        return view('livewire.home', [
            'news' => $news,
            'quickLinks' => $quickLinks,
        ])->layout('layouts.app');
    }
}