<!DOCTYPE html>
<html>
    <body>
        <p>The following Member has been created:</p>
        <p><span style="font-weight:bold">Name:</span>&nbsp;{{ $person->name }}</p>
        <p><span style="font-weight:bold">ID:</span>&nbsp;{{ $person->cust_id }}</p>
        <p><span style="font-weight:bold">Position:</span>&nbsp;{{ $person->cust_type }}</p>
        <p><span style="font-weight:bold">Created By:</span>&nbsp;{{ $person->parent_name }}</p>
    </body>
</html>