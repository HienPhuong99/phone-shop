<x-admin-layout title="Sửa bài viết - Admin">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-gray-900">Sửa bài: {{ $post->title }}</h2>
        <a href="{{ route('posts.show', $post->slug) }}" target="_blank" class="text-sm text-indigo-600 hover:underline">Xem trên web &rarr;</a>
    </div>

    <form method="POST" action="{{ route('admin.posts.update', $post) }}" enctype="multipart/form-data">
        @include('admin.posts._form')
    </form>
</x-admin-layout>
