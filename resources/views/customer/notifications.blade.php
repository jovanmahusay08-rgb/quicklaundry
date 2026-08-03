@extends('customer.layout')

@section('content')
@php($pageTitle = 'Notifications')
<div class="mx-auto max-w-5xl py-2">
    @include('notifications.list', ['markAllRoute' => 'customer.notifications.mark-all-read', 'gotoRoute' => 'customer.notifications.goto'])
</div>
@endsection
