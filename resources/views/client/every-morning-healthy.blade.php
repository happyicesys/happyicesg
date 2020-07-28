@extends('template_client')
@section('title')
Healthier Life
@stop
@section('content')
<style>
  .iframe-container{
    position: relative;
    width: 100%;
    padding-bottom: 56.25%;
    height: 0;
  }
  .iframe-container iframe{
    position: absolute;
    top:0;
    left: 0;
    width: 100%;
    height: 100%;
    padding: 5px 0px 5px 0px;
  }
  .img-style {
    padding: 5px 0px 5px 0px;
  }
  @media (min-width:1025px) { .iframe-container { width: 60% !important;} }
  @media (min-width:1025px) { .img-style { width: 60% !important;} }
</style>

  <div style="background-color:#d1e4a4;">
    <img src="{{$file['mainBanner']}}" alt="Every Morning Healthy Tea Vitalon" class="img-responsive center-block img-style">
    <img src="{{$file['secondBanner']}}" alt="Every Morning Healthy Tea Reduce Body Fat" class="img-responsive center-block img-style">
    <div class="iframe-container center-block">
      <iframe src="https://www.youtube.com/embed/Xw2SmIXE9cI" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    </div>
    <img src="{{$file['thirdBanner']}}" alt="Content1" class="img-responsive center-block img-style">
    <img src="{{$file['fourthBanner']}}" alt="Content2" class="img-responsive center-block img-style">
    <div class="iframe-container center-block">
      <iframe src="https://www.youtube.com/embed/wck8X8ifGyM" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    </div>
    <img src="{{$file['award']}}" alt="award" class="img-responsive center-block img-style">
    <img src="{{$file['cert']}}" alt="cert" class="img-responsive center-block img-style">
    <img src="{{$file['fifthBanner']}}" alt="review1" class="img-responsive center-block img-style">
  </div>
  <div class="row" style="padding: 10px 0px 20px 0px;">
    <div class="row text-center" style="padding-top: 10px;">
      <strong>
        2020每朝健康 教練篇
      </strong>
    </div>
    <div class="iframe-container center-block">
      <iframe src="https://www.youtube.com/embed/IEjkA1J5hG0" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    </div>
    <div class="row text-center" style="padding-top: 10px;">
      <strong>
        2014每朝健康 改變篇
      </strong>
    </div>
    <div class="iframe-container center-block">
      <iframe src="https://www.youtube.com/embed/8nI5PcyRJD8" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    </div>
    <div class="row text-center" style="padding-top: 10px;">
      <strong>
        2013年每朝健康 業績篇 咕噜咕噜
      </strong>
    </div>
    <div class="iframe-container center-block">
      <iframe src="https://www.youtube.com/embed/tb3Ogr0gBe4" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    </div>
    <div class="row text-center" style="padding-top: 10px;">
      <strong>
        2012年每朝健康 愛吃鬼篇
      </strong>
    </div>
    <div class="iframe-container center-block">
      <iframe src="https://www.youtube.com/embed/hUDnXHYmgh4" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    </div>
    <div class="row text-center" style="padding-top: 10px;">
      <strong>
        2012年每朝健康 揭密篇
      </strong>
    </div>
    <div class="iframe-container center-block">
      <iframe src="https://www.youtube.com/embed/MhYhaaubPd0" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    </div>
    <div class="row text-center" style="padding-top: 10px;">
      <strong>
        2012年每朝健康 辦公室篇 咕噜咕噜
      </strong>
    </div>
    <div class="iframe-container center-block">
      <iframe src="https://www.youtube.com/embed/t8PEtT2HITA" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    </div>
    <div class="row text-center" style="padding-top: 10px;">
      <strong>
        街訪篇第一集 心聲篇
      </strong>
    </div>
    <div class="iframe-container center-block">
      <iframe src="https://www.youtube.com/embed/4OLGCAhUKaQ" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    </div>
    <div class="row text-center" style="padding-top: 10px;">
      <strong>
        街訪篇第二集 有效篇
      </strong>
    </div>
    <div class="iframe-container center-block">
      <iframe src="https://www.youtube.com/embed/qjCupakD7ck" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    </div>
  </div>
@stop