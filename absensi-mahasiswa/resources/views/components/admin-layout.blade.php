{{-- resources/views/components/admin-layout.blade.php --}}
@props(['title' => 'Admin Panel'])
@include('admin.layout', ['title' => $title, 'slot' => $slot])
