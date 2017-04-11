@inject('unitcost', 'App\Unitcost')
<meta charset="utf-8">
<table>
    <tbody>
    <tr></tr>
    <tr>
        <th>#</th>
        <th>ID</th>
        <th>Product</th>
        <th>Profile</th>
        <th>Unit Cost</th>
    </tr>
    <?php $i = 1; ?>
    @foreach($items as $item)
        @foreach($profiles as $profile)
        <tr>
            <td>{{$i}}</td>
            <td>{{$item->product_id}}</td>
            <td>{{$item->name}}</td>
            <td>{{$profile->name}}</td>
            <td>{{$unitcost::whereProfileId($profile->id)->whereItemId($item->id)->first()['unit_cost']}}</td>
        </tr>
        <?php $i ++; ?>
        @endforeach
    @endforeach
    </tbody>
</table>