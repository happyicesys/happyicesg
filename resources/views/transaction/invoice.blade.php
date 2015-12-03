<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    </head>

<body>
<table>
    <tbody>
        <tr></tr>
        <div class="col-md-12">
            {{-- <tr>
                <h2 class="text-center">{{$profile->name}}</h2>
            </tr>
            <tr>
                <h5 class="text-center">{{$profile->address}}</h5>
            </tr>
            <tr>
                <h5 class="text-center">Tel: {{$profile->contact}}</h5>
            </tr>    
            <tr>
                <h5 class="text-center">Co Reg No: {{$profile->roc_no}}</h5>
            </tr>   --}}           
        </div>

        <tr></tr>
        <tr></tr>

        <div class="col-md-12">
            <tr>
                <td class="text-center">
                    {{-- <strong>Bill To:</strong> {{$person->cust_id}} {{$person->company}}
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-center col-md-3">
                    <strong>Send To:</strong> {{$person->company}} --}}
                </td>
                <td></td>
                <td class="text-center col-md-5">
                    <h2> DO/ INVOICE </h2>
                </td>

            </tr>
            <tr>
                <td class="text-center">
                    {{-- <strong>{{$person->bill_to}}</strong> --}}
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td  class="text-center">
                    {{-- <strong>{{$person->del_address}}</strong> --}}
                </td>
                <td></td>
                <td>
                    DO & INV No: 
                </td>
                {{-- <td>
                    <strong>{{$transaction->id}}</strong>
                </td>
            </tr>
            <tr>
                <td class="text-center">
                    {{-- <strong>Attn:</strong> {{$person->name}} --}}
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>
                    Order Date: 
                </td>
                <td>
                    {{-- <strong>{{$transaction->created_at}}</strong> --}}
                </td>                
            </tr>
            <tr>
                <td class="text-center">
                   {{-- <strong>Tel:</strong> {{$person->contact}}  --}}
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>
                    Delivery Date: 
                </td>
                <td>
                    {{-- <strong>{{$transaction->delivery_date}}</strong> --}} --}}
                </td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>
                    Term: 
                </td>
                <td>
                    {{-- <strong>{{$person->payterm}}</strong> --}}
                </td>
            </tr> 
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>
                    SalesPerson: 
                </td>
            </tr> 
        </div>

        <div class="col-md-12">        
            <tr></tr>
            <tr>
                <th class="text-center">
                    <strong>Item Code</strong>
                </th>
                <th class="text-center">
                    <strong>Description</strong>
                </th> 
                <th></th>              
                <th class="text-center">
                    <strong>Quantity</strong>
                </th>
                <th class="text-center">
                    <strong>Unit Price</strong>
                </th>
                <th class="text-center">
                    <strong>Amount</strong>
                </th>                                                     
            </tr>
        </div>
    </tbody>
</table>
</body>
</html>