<?php

namespace App\Livewire\Roles\Principal;

use App\Models\User;
use App\Models\Role;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    use WithPagination;

    public $roles;
    public $showingUserModal = false;
    public $showingDeactivateModal = false;
    public $selectedUser = null;
    public $selectedRole = null;
    public $deactivateUserId = null;
    public $userData = [
        'name' => '',
        'email' => '',
        'password' => '',
        'role_id' => null,
    ];
    public $filterRoleSlug = null; // 'teacher' | 'parent' | null
    
    public function mount()
    {
        $this->roles = Role::all();
    }

    public function showUserModal($userId = null)
    {
        $this->selectedUser = $userId ? User::find($userId) : null;
        $this->showingUserModal = true;
        if ($this->selectedUser) {
            $this->selectedRole = $this->selectedUser->role_id;
            // preload editable fields
            $this->userData['name'] = $this->selectedUser->name;
            $this->userData['email'] = $this->selectedUser->email;
        } else {
            $this->reset('userData');
            $this->dispatch('refresh');
        }
    }

    public function updateUserRole()
    {
        $this->validate([
            'userData.name' => 'required|min:3',
            'userData.email' => 'required|email|unique:users,email,' . ($this->selectedUser?->id ?? 'null'),
            'selectedRole' => 'required|exists:roles,id',
        ]);

        $updates = [
            'name' => $this->userData['name'],
            'email' => $this->userData['email'],
            'role_id' => $this->selectedRole,
        ];
        $this->selectedUser->update($updates);

        $this->showingUserModal = false;
        session()->flash('message', 'User role updated successfully.');
    }

    public function createUser()
    {
        $this->validate([
            'userData.name' => 'required|min:3',
            'userData.email' => 'required|email|unique:users,email',
            'userData.password' => 'required|min:6',
            'userData.role_id' => 'required|exists:roles,id',
        ]);

        $data = $this->userData;
        // Do not double-hash since User casts already hash password
        $data['is_active'] = true;

        User::create($data);
        $this->showingUserModal = false;
        session()->flash('message', 'User created successfully.');
    }

    public function deactivateUser($userId)
    {
        $user = User::find($userId);
        if ($user) {
            $user->update(['is_active' => false]);
            session()->flash('message', 'User deactivated.');
        }
    }

    public function confirmDeactivate($userId)
    {
        $this->deactivateUserId = $userId;
        $this->showingDeactivateModal = true;
    }

    public function deactivateUserConfirmed()
    {
        if ($this->deactivateUserId) {
            $this->deactivateUser($this->deactivateUserId);
        }
        $this->showingDeactivateModal = false;
        $this->deactivateUserId = null;
        // refresh list so UI reflects change immediately
        $this->resetPage();
    }

    public function getUsersProperty()
    {
        return User::with('role')
            ->when(true, fn($q) => $q->where('is_active', true))
            ->when($this->filterRoleSlug, function($q) {
                $slug = $this->filterRoleSlug;
                $q->whereHas('role', fn($qr) => $qr->where('slug', $slug));
            })
            ->paginate(10);
    }

    public function showTeachers()
    {
        $this->filterRoleSlug = 'teacher';
    }

    public function showParents()
    {
        $this->filterRoleSlug = 'parent';
    }

    public function showAllUsers()
    {
        $this->filterRoleSlug = null;
    }

    public function getStudentCountProperty()
    {
        return \App\Models\Student::where('is_active', true)->count();
    }

    public function getTeacherCountProperty()
    {
        return User::whereHas('role', fn($q) => $q->where('slug', 'teacher'))->count();
    }

    public function getParentCountProperty()
    {
        return User::whereHas('role', fn($q) => $q->where('slug', 'parent'))->count();
    }

    public function render()
    {
        return view('livewire.roles.principal.dashboard');
    }
}