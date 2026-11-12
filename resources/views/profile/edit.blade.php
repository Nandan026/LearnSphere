@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<x-guide title="Profile guide" :items="[
    'Update your display name and contact phone.',
    'Add a short bio visible to instructors in discussions.',
    'Upload an avatar (requires storage link for public URLs).',
    'Change your password when needed.',
]" />

<div class="card ls-card">
    <div class="card-body">
        <h1 class="h4 mb-3">Edit profile</h1>
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Bio</label>
                    <textarea name="bio" class="form-control" rows="3">{{ old('bio', $user->bio) }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">New password</label>
                    <input type="password" name="password" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Confirm password</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>
                <div class="col-12">
                    <label class="form-label">Avatar</label>
                    <input type="file" name="avatar" class="form-control" accept="image/*">
                    @if ($user->avatar_path)
                        <img src="{{ asset('storage/'.$user->avatar_path) }}" alt="" class="mt-2 rounded" height="64">
                    @endif
                </div>
            </div>
            <button type="submit" class="btn btn-ls-primary mt-3">Save changes</button>
        </form>
    </div>
</div>
@endsection
