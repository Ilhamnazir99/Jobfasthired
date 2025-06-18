@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto mt-10 bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-semibold mb-6">📩 Your Notifications</h2>

    {{-- Flash message --}}
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if($notifications->isEmpty())
        <p class="text-gray-600">You have no notifications at the moment.</p>
    @else
        <ul class="space-y-4">
            @foreach($notifications as $notification)
                @php
                    $isUnread = is_null($notification->read_at);
                    $type = $notification->data['type'] ?? 'general';
                    $message = $notification->data['message'] ?? 'You have a new notification.';
                    $icon = match($type) {
                        'accepted' => '✅',
                        'rejected' => '❌',
                        default => '🔔',
                    };
                    $bgClass = $isUnread ? 'bg-blue-50' : 'bg-white';
                @endphp

                <li class="border rounded p-4 {{ $bgClass }}">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-800">
                                <span class="text-xl mr-2">{{ $icon }}</span>{{ $message }}
                            </p>
                            <small class="text-gray-500">{{ $notification->created_at->diffForHumans() }}</small>
                        </div>
                        @if($isUnread)
                            <form method="POST" action="{{ route('student.notifications.markAsRead', $notification->id) }}">
                                @csrf
                                <button class="text-blue-600 hover:underline text-sm" type="submit">Mark as read</button>
                            </form>
                        @else
                            <span class="text-green-600 text-sm font-semibold">✓ Read</span>
                        @endif
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</div>
@endsection
