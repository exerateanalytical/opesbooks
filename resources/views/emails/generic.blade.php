@extends('emails.layout')

@section('content')
    <h1 style="font-size:19px;color:#fff;margin:0 0 16px;">{{ $heading }}</h1>
    @foreach($lines as $line)
        <p style="margin:0 0 12px;">{!! $line !!}</p>
    @endforeach
@endsection

@if(!empty($cta))
    @section('action')
        <a href="{{ $cta['url'] }}" style="display:inline-block;padding:12px 22px;background:#F59E0B;color:#0B1120;font-weight:800;font-size:14px;border-radius:8px;text-decoration:none;">{{ $cta['label'] }}</a>
    @endsection
@endif
