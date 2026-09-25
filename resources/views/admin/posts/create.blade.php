<x-admin-layout title="Viết bài mới - Admin">
    <h2 class="text-lg font-semibold text-gray-900 mb-4">Viết bài mới</h2>

    <form method="POST" action="{{ route('admin.posts.store') }}" enctype="multipart/form-data">
        @include('admin.posts._form')
    </form>
</x-admin-layout>
