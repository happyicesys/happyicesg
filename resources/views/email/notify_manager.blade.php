<!DOCTYPE html>
<html>
    <body>
        <p>For customer {{$person->id}} - {{$person->name}}, </p>
        <p>{{$notification->title}}</p>
        <p>{!! nl2br(e($notification->content)) !!}</p>
    </body>
</html>