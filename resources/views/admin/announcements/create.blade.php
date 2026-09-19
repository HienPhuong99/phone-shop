<x-admin-layout title="Thêm thông báo - Admin">
    <h2 class="text-lg font-semibold text-gray-900 mb-4">Thêm thông báo</h2>

    <form method="POST" action="{{ route('admin.announcements.store') }}">
        @include('admin.announcements._form')
    </form>
</x-admin-layout>
