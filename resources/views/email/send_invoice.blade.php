<!DOCTYPE html>
<html>
    <body>
        <p>Hello {{$person->name}}, </p>
        <p>Thanks you for confirm purchase, order by {{$person->parent_name}}, and to be delivered on {{$transaction->delivery_date}}.</p>
        <p>{!! nl2br(e($email_draft)) !!}</p>
    </body>
</html>