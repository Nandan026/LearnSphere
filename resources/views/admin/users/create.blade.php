@extends('layouts.app')
@section('title', 'Create user')
@section('content')
<x-guide title="Create user guide" :items="['Set email and temporary password.','Pick exactly one role slug.','Default status should be active for staff invites.']" />
<div class="card ls-card"><div class="card-body">
<form method="POST" action="{{ route('admin.users.store') }}">@csrf
<div class="row g-3">
<div class="col-md-6"><label class="form-label">Name</label><input name="name" class="form-control" required></div>
<div class="col-md-6"><label class="form-label">Email</label><input name="email" type="email" class="form-control" required></div>
<div class="col-md-6"><label class="form-label">Password</label><input name="password" type="password" class="form-control" required></div>
<div class="col-md-3"><label class="form-label">Status</label><select name="status" class="form-select"><option value="active">active</option><option value="suspended">suspended</option></select></div>
<div class="col-md-3"><label class="form-label">Role</label>
<select name="role" class="form-select" required>
@foreach ($roles as $role)<option value="{{ $role->slug }}">{{ $role->name }}</option>@endforeach
</select></div>
</div>
<button class="btn btn-ls-primary mt-3">Create</button>
</form></div></div>
@endsection
