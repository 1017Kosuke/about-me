<footer class="bg-gray-900 text-white text-center py-4 mt-8">
    <p>&copy; {{ date('Y') }} My App. All rights reserved.</p>

    <h3 class="mt-4 font-bold">最近登録したユーザー</h3>
    <ul>
        @foreach ($users as $user)
            <li>{{ $user->username }} ({{ $user->created_at->format('Y-m-d') }})</li>
        @endforeach
    </ul>
</footer>
