@extends('template_client')
@section('title')
Healthier Life
@stop
@section('content')
  <embed src="{{ $file }}" width="100%" height="100%" alt="pdf" />
@stop