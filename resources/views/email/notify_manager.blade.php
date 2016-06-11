<!DOCTYPE html>
<html>
    <body>
        <p>For customer {{$person->cust_id}} - {{$person->name}}, </p>
        <p>{{$notification->title}}</p>
        <p>{!! nl2br(e($notification->content)) !!}</p>
    </body>
</html>