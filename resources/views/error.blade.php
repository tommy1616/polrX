@extends('layouts.errors')

@section('content')
<h2>{{ __('polr.errors.title') }}</h2>
<h4>{{$message}}</h4>
@endsection
