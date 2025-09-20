<?php

namespace App\Livewire;

use Livewire\Component;

class News extends Component
{
    public function render()
    {
        // This would typically come from your database
        $news = collect([
            [
                'title' => 'Back-to-School Night',
                'category' => 'Announcement',
                'category_color' => 'berry',
                'text_color' => 'berry',
                'date' => now()->subDays(7),
                'excerpt' => 'Families are invited this Thursday at 6 PM to meet teachers and visit classrooms.',
            ],
            [
                'title' => 'New Learning Resources',
                'category' => 'Update',
                'category_color' => 'grass',
                'text_color' => 'emerald-700',
                'date' => now()->subDays(14),
                'excerpt' => 'We\'ve added new educational materials to support our STEM curriculum.',
            ],
            [
                'title' => 'Sports Day Success',
                'category' => 'Event',
                'category_color' => 'sun',
                'text_color' => 'amber-700',
                'date' => now()->subDays(21),
                'excerpt' => 'Thank you to all participants and volunteers who made our annual Sports Day a success!',
            ],
        ]);

        return view('livewire.news', [
            'news' => $news
        ])->layout('layouts.app');
    }
}