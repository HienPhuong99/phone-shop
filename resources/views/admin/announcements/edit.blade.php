<x-admin-layout title="Sửa thông báo - Admin">
    <h2 class="text-lg font-semibold text-gray-900 mb-4">Sửa thông báo: {{ $announcement->title }}</h2>

    <form method="POST" action="{{ route('admin.announcements.update', $announcement) }}">
        @include('admin.announcements._form')
    </form>
</x-admin-layout>
