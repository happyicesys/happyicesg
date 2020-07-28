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
    <img src="{{$file['fifthBanner']}}" alt="review1" class="img-responsive center-block" width="60%" style="padding-top:5px; padding-bottom:5px;">
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
    <img src="{{$file['fifthBanner']}}" alt="review1" class="img-responsive center-block" width="100%" style="padding-top:5px; padding-bottom:5px;">
  </div>
  <div class="row" style="padding: 20px 0px 20px 0px;">
    <div class="col-md-12 col-sm-12 col-sm-12 text-center" style="padding-top: 5px;">
      <strong>
        2020每朝健康 教練篇
      </strong>
    </div>
    <iframe width="75%" height="450" class="center-block" src="https://www.youtube.com/embed/IEjkA1J5hG0" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    <div class="col-md-12 col-sm-12 col-sm-12 text-center" style="padding-top: 10px;">
      <strong>
        2014每朝健康 改變篇
      </strong>
    </div>
    <iframe width="75%" height="450" class="center-block" src="https://www.youtube.com/embed/8nI5PcyRJD8" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    <div class="col-md-12 col-sm-12 col-sm-12 text-center" style="padding-top: 10px;">
      <strong>
        2013年每朝健康 業績篇 咕噜咕噜
      </strong>
    </div>
    <iframe width="75%" height="450" class="center-block" src="https://www.youtube.com/embed/tb3Ogr0gBe4" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    <div class="col-md-12 col-sm-12 col-sm-12 text-center" style="padding-top: 10px;">
      <strong>
        2012年每朝健康 愛吃鬼篇
      </strong>
    </div>
    <iframe width="75%" height="450" class="center-block" src="https://www.youtube.com/embed/hUDnXHYmgh4" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    <div class="col-md-12 col-sm-12 col-sm-12 text-center" style="padding-top: 10px;">
      <strong>
        2012年每朝健康 揭密篇
      </strong>
    </div>
    <iframe width="75%" height="450" class="center-block" src="https://www.youtube.com/embed/MhYhaaubPd0" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    <div class="col-md-12 col-sm-12 col-sm-12 text-center" style="padding-top: 10px;">
      <strong>
        2012年每朝健康 辦公室篇 咕噜咕噜
      </strong>
    </div>
    <iframe width="75%" height="450" class="center-block" src="https://www.youtube.com/embed/t8PEtT2HITA" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    <div class="col-md-12 col-sm-12 col-sm-12 text-center" style="padding-top: 10px;">
      <strong>
        街訪篇第一集 心聲篇
      </strong>
    </div>
    <iframe width="75%" height="450" class="center-block" src="https://www.youtube.com/embed/4OLGCAhUKaQ" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    <div class="col-md-12 col-sm-12 col-sm-12 text-center" style="padding-top: 10px;">
      <strong>
        街訪篇第二集 有效篇
      </strong>
    </div>
    <iframe width="75%" height="450" class="center-block" src="https://www.youtube.com/embed/qjCupakD7ck" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

  </div>
@stop