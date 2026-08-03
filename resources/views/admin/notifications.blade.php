@extends('admin.layouts.app')
@section('title', 'Notifications')
@section('pageTitle', 'Notifications')
@section('content')
<div class="mx-auto max-w-5xl">
    @include('notifications.list', ['markAllRoute' => 'admin.notifications.mark-all-read', 'gotoRoute' => 'admin.notifications.goto'])
</div>
@endsection
