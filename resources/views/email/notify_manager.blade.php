<!DOCTYPE html>
<html>
    <body>
        <p>For customer <a href="http://happyice.com.sg/market/customer/{{$person->id}}/edit">{{$person->cust_id}}</a> - {{$person->name}}, </p>
        <p>{{$notification->title}}</p>
        <p>{!! nl2br(e($notification->content)) !!}</p>
        <p>For any information, please visit us at http://happyice.com.sg/auth/login</p>
    </body>
</html>