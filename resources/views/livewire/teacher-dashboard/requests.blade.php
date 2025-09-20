@if($currentView === 'requests')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Parent Access Requests</h3>
        </div>

        @php $requests = $this->accessRequestsForTeacher; @endphp
        @if($requests->count() === 0)
            <p class="text-sm text-gray-500">No requests at the moment.</p>
        @else
            <div class="space-y-3">
                @foreach($requests as $req)
                    <div class="p-4 border rounded-lg flex items-center justify-between">
                        <div>
                            <div class="font-medium text-gray-900">
                                {{ $req->parent?->name }}
                                <span class="text-gray-500 font-normal">requests access to</span>
                                @if($req->subject)
                                    {{ $req->subject->name }}
                                @else
                                    this classroom's subjects
                                @endif
                            </div>
                            <div class="text-xs text-gray-500 mt-1">{{ ucfirst($req->status) }} • {{ $req->created_at->diffForHumans() }}</div>
                            @if($req->reason)
                                <div class="text-xs text-gray-600 mt-1">Reason: {{ $req->reason }}</div>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            @if($req->status === 'pending')
                                <button wire:click="approveAccessRequest({{ $req->id }})" class="px-3 py-1.5 text-sm rounded-md bg-green-600 text-white hover:bg-green-700">Approve</button>
                                <button wire:click="openRejectModal({{ $req->id }})" class="px-3 py-1.5 text-sm rounded-md bg-red-600 text-white hover:bg-red-700">Reject</button>
                            @else
                                <span class="text-sm px-2 py-1 rounded @if($req->status==='approved') bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">{{ ucfirst($req->status) }}</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $requests->links() }}
            </div>
        @endif
    </div>
</div>
@endif

