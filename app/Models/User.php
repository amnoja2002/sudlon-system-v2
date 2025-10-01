<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use App\Traits\HasRole;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRole;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'photo',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Get the user's profile photo URL
     */
    public function profile_photo_url(): string
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }

        // Generate a default avatar with initials
        return $this->default_avatar_url();
    }

    /**
     * Accessor to allow $user->profile_photo_url usage without calling as a method.
     */
    public function getProfilePhotoUrlAttribute(): string
    {
        return $this->profile_photo_url();
    }

    /**
     * Get a default avatar URL with user initials
     */
    public function default_avatar_url(): string
    {
        $initials = $this->initials();
        $colors = ['blue', 'green', 'purple', 'red', 'yellow', 'indigo', 'pink'];
        $color = $colors[array_rand($colors)];
        
        // For now, return a placeholder URL. In a real app, you might use a service like Gravatar or generate SVG
        return "https://ui-avatars.com/api/?name=" . urlencode($initials) . "&background=" . $color . "&color=fff&size=128";
    }

    public function classrooms()
    {
        return $this->hasMany(Classroom::class, 'teacher_id');
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class, 'teacher_id');
    }

    public function reportCards()
    {
        return $this->hasMany(ReportCard::class, 'generated_by');
    }

    // Removed: accessRequests and subjectAccesses relationships (feature deprecated)
}
