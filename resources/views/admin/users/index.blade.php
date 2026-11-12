@extends('layouts.app')
@section('title', 'Users')
@section('content')
<x-guide title="Users guide" :items="['Search and paginate accounts.','Create or edit users and assign a single role.','Suspend accounts instead of deleting when unsure.']" />
<div class="d-flex justify-content-between mb-3">
    <form class="d-flex gap-2" method="GET">
        <input name="q" class="form-control form-control-sm" value="{{ request('q') }}" placeholder="Search name or email">
        <button class="btn btn-sm btn-ls-primary">Search</button>
    </form>
    <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-ls-primary">New user</a>
</div>
<div class="card ls-card table-responsive">
<table class="table table-sm mb-0"><thead><tr><th>Name</th><th>Email</th><th>Roles</th><th>Status</th><th></th></tr></thead><tbody>
@foreach ($users as $user)
<tr>
    <td>{{ $user->name }}</td>
    <td>{{ $user->email }}</td>
    <td>{{ $user->roles->pluck('name')->join(', ') }}</td>
    <td>{{ $user->status }}</td>
    <td><a href="{{ route('admin.users.edit', $user) }}">Edit</a></td>
</tr>
@endforeach
</tbody></table></div>
{{ $users->links() }}
@endsection
