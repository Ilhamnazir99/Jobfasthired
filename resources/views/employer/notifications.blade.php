@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto mt-10 bg-white p-6 rounded-lg shadow-md">

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800 flex items-center gap-2">
            <svg data-lucide="bell" class="w-6 h-6 text-blue-600"></svg>
            Notifications
        </h2>

        <div class="flex items-center gap-4">
            @if($notifications->isNotEmpty())
                <form method="POST" action="{{ route('employer.notifications.markAllAsRead') }}">
                    @csrf
                    <button class="text-sm text-blue-600 hover:text-blue-800 hover:underline transition">
                        Mark all as read
                    </button>
                </form>

                <form method="POST" action="{{ route('employer.notifications.clearAll') }}" onsubmit="return confirm('Are you sure you want to clear all notifications?');">
                    @csrf
                    @method('DELETE')
                    <button class="text-sm text-red-500 hover:text-red-700 hover:underline transition">
                        Clear all
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Filter Tabs --}}
    <div class="flex gap-6 mb-6 border-b border-gray-200 pb-2">
        <a href="{{ route('employer.notifications', ['filter' => 'all']) }}"
           class="text-sm {{ $filter === 'all' ? 'text-blue-600 font-semibold border-b-2 border-blue-600' : 'text-gray-600 hover:text-blue-600' }}">
            All
        </a>
        <a href="{{ route('employer.notifications', ['filter' => 'unread']) }}"
           class="text-sm {{ $filter === 'unread' ? 'text-blue-600 font-semibold border-b-2 border-blue-600' : 'text-gray-600 hover:text-blue-600' }}">
            Unread
        </a>
    </div>

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-md">
            {{ session('success') }}
        </div>
    @endif

    @if($notifications->isEmpty())
        <div class="text-gray-500 text-center py-8">
            <svg data-lucide="inbox" class="w-12 h-12 mx-auto text-gray-400"></svg>
            <p class="mt-2">You have no {{ $filter }} notifications at the moment.</p>
        </div>
    @else
        <ul class="space-y-4">
            @foreach($notifications as $notification)
                @php
                    $isUnread = is_null($notification->read_at);
                    $message = $notification->data['student_name'] . ' applied for ' . $notification->data['job_title'];
                @endphp

                <li class="p-4 border rounded-md flex items-start justify-between gap-4 transition hover:shadow-sm {{ $isUnread ? 'bg-blue-50 border-blue-200' : 'bg-white' }}">
                    <div class="flex items-start gap-3">
                        <div class="relative">
                            <svg data-lucide="user-check" class="w-6 h-6 {{ $isUnread ? 'text-blue-600' : 'text-gray-400' }}"></svg>
                            @if($isUnread)
                                <span class="absolute -top-1 -right-1 w-2 h-2 bg-red-500 rounded-full animate-ping"></span>
                            @endif
                        </div>
                        <div>
                            <p class="text-gray-800">{{ $message }}</p>
                            <small class="text-gray-500">{{ $notification->created_at->diffForHumans() }}</small>
                        </div>
                    </div>

                    <div class="text-sm flex items-center gap-2">
                        @if($isUnread)
                            <form method="POST" action="{{ route('employer.notifications.mark', $notification->id) }}">
                                @csrf
                                <button class="text-blue-600 hover:underline" type="submit">
                                    Mark as read
                                </button>
                            </form>
                        @else
                            <span class="text-green-500 font-medium flex items-center gap-1">
                                <svg data-lucide="check" class="w-4 h-4"></svg> Read
                            </span>
                        @endif
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</div>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
    lucide.createIcons();
</script>
@endsection
