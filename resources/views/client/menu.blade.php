@extends('template_client')
@section('title')
Healthier Life
@stop
@section('content')

<style>
  .col-centered{
    float: none;
    margin: 0 auto;
  }
</style>
  <div class="container">
    <div class="col-md-9 col-sm-10 col-xs-12 col-centered">
      <img src="{{$file['menu_1']}}" alt="menu_1" class="img-responsive center-block img-style">
      <img src="{{$file['menu_2']}}" alt="menu_2" class="img-responsive center-block img-style">
      <img src="{{$file['menu_3']}}" alt="menu_3" class="img-responsive center-block img-style">
      <img src="{{$file['menu_4']}}" alt="menu_4" class="img-responsive center-block img-style">
      {{-- <img src="{{$file['menu_5']}}" alt="menu_5" class="img-responsive center-block img-style"> --}}
      <img src="{{$file['menu_6']}}" alt="menu_6" class="img-responsive center-block img-style">
      <img src="{{$file['menu_7']}}" alt="menu_7" class="img-responsive center-block img-style">
    </div>
  </div>
@stop