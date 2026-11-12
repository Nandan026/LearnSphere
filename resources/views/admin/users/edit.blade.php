@extends('layouts.app')
@section('title', 'Edit user')
@section('content')
<x-guide title="Edit user guide" :items="['Update status to suspend access instantly.','Role changes replace the previous assignment.','Leave password blank to keep the current hash.']" />
<div class="card ls-card"><div class="card-body">
<form method="POST" action="{{ route('admin.users.update', $user) }}">@csrf @method('PUT')
<div class="row g-3">
<div class="col-md-6"><label class="form-label">Name</label><input name="name" class="form-control" value="{{ $user->name }}" required></div>
<div class="col-md-6"><label class="form-label">Email</label><input name="email" type="email" class="form-control" value="{{ $user->email }}" required></div>
<div class="col-md-6"><label class="form-label">New password</label><input name="password" type="password" class="form-control"></div>
<div class="col-md-3"><label class="form-label">Status</label><select name="status" class="form-select">
@foreach (['active','suspended','pending'] as $st)<option value="{{ $st }}" @selected($user->status===$st)>{{ $st }}</option>@endforeach
</select></div>
<div class="col-md-3"><label class="form-label">Role</label>
<select name="role" class="form-select">
@foreach ($roles as $role)
<option value="{{ $role->slug }}" @selected($user->roles->contains('slug',$role->slug))>{{ $role->name }}</option>
@endforeach
</select></div>
</div>
<button class="btn btn-ls-primary mt-3">Save</button>
</form>
<form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="mt-2" onsubmit="return confirm('Delete user?')">@csrf @method('DELETE')
<button class="btn btn-outline-danger btn-sm">Delete</button></form>
</div></div>
@endsection
