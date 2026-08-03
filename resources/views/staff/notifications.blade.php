@extends('staff.layout')
@section('content')
@php($pageTitle = 'Notifications')
<div class="mx-auto max-w-5xl">
    @include('notifications.list', ['markAllRoute' => 'staff.notifications.mark-all-read', 'gotoRoute' => 'staff.notifications.goto'])
</div>
@endsection
