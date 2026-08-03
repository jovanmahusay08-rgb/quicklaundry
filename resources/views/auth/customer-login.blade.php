@extends('layouts.app')

@section('content')
    @include('auth.partials.portal-login', ['selectedPortal' => 'customer'])
@endsection
