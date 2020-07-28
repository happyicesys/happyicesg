@extends('template_client')
@section('title')
Healthier Life
@stop
@section('content')
  <div style="background-color:#d1e4a4;" class="hidden-xs">
    <img src="{{$file['mainBanner']}}" alt="Every Morning Healthy Tea Vitalon" class="img-responsive center-block" style="padding-top:5px; padding-bottom:5px;" width="60%">
    <img src="{{$file['secondBanner']}}" alt="Every Morning Healthy Tea Reduce Body Fat" class="img-responsive center-block" style="padding-top:5px; padding-bottom:5px;" width="60%">
    <iframe width="60%" height="500px" class="center-block" src="https://www.youtube.com/embed/Xw2SmIXE9cI" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    <img src="{{$file['thirdBanner']}}" alt="Content1" class="img-responsive center-block" width="60%" style="padding-top:5px; padding-bottom:5px;">
    <img src="{{$file['fourthBanner']}}" alt="Content2" class="img-responsive center-block" width="60%" style="padding-top:5px; padding-bottom:5px;">
    <iframe width="60%" height="400" class="center-block" src="https://www.youtube.com/embed/wck8X8ifGyM" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    <img src="{{$file['award']}}" alt="award" class="img-responsive center-block" width="60%" style="padding-top:5px; padding-bottom:5px;">
    <img src="{{$file['cert']}}" alt="cert" class="img-responsive center-block" width="60%" style="padding-top:5px; padding-bottom:5px;">
  </div>
  <div style="background-color:#d1e4a4;" class="hidden-lg hidden-md hidden-sm">
    <img src="{{$file['mainBanner']}}" alt="Every Morning Healthy Tea Vitalon" class="img-responsive center-block" style="padding-top:5px; padding-bottom:5px;" width="100%">
    <img src="{{$file['secondBanner']}}" alt="Every Morning Healthy Tea Reduce Body Fat" class="img-responsive center-block" style="padding-top:5px; padding-bottom:5px;" width="100%">
    <iframe width="100%" height="200" class="center-block" src="https://www.youtube.com/embed/Xw2SmIXE9cI" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    <img src="{{$file['thirdBanner']}}" alt="Content1" class="img-responsive center-block" width="100%" style="padding-top:5px; padding-bottom:5px;">
    <img src="{{$file['fourthBanner']}}" alt="Content2" class="img-responsive center-block" width="100%" style="padding-top:5px; padding-bottom:5px;">
    <iframe width="100%" height="200" class="center-block" src="https://www.youtube.com/embed/wck8X8ifGyM" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    <img src="{{$file['award']}}" alt="award" class="img-responsive center-block" width="100%" style="padding-top:5px; padding-bottom:5px;">
    <img src="{{$file['cert']}}" alt="cert" class="img-responsive center-block" width="100%" style="padding-top:5px; padding-bottom:5px;">
  </div>
@stop