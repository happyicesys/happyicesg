<!DOCTYPE html>
<html>
    <body>

        <p>
          <span style="font-weight:bold">
            Transaction ID:
          </span>
          <span style="padding-left: 15px;">
            {{ $transaction->id }}
          </span>
          <br>
          <span style="font-weight:bold">
            Submission Date:
          </span>
          <span style="padding-left: 15px;">
            {{ $transaction->deliveryorder->submission_datetime }}
          </span>
          <br>
          <span style="font-weight:bold">
            Requester Name:
          </span>
          <span style="padding-left: 15px;">
            {{ $transaction->deliveryorder->requester_name }}
          </span>
          <br>
          <span style="font-weight:bold">
            Requester Contact:
          </span>
          <span style="padding-left: 15px;">
            {{ $transaction->deliveryorder->requester_contact }}
          </span>
          <br>
          <span style="font-weight:bold">
            Job Type:
          </span>
          <span style="padding-left: 15px;">
            {{ $transaction->deliveryorder->job_type }}
          </span>
          <br>
          <span style="font-weight:bold">
            PO No:
          </span>
          <span style="padding-left: 15px;">
            {{ $transaction->deliveryorder->po_no }}
          </span>
          <br>
        </p>
        <p>
          <span style="font-weight:bold">
            Requested Pickup Date:
          </span>
          <span style="padding-left: 15px;">
            {{ $transaction->deliveryorder->pickup_date ? \Carbon\Carbon::parse($transaction->deliveryorder->pickup_date)->format('Y-m-d') : ''}}
          </span>
          <br>
          <span style="font-weight:bold">
            Pickup Timerange:
          </span>
          <span style="padding-left: 15px;">
            {{ $transaction->deliveryorder->pickup_timerange }}
          </span>
          <br>
          <span style="font-weight:bold">
            Pickup Attn:
          </span>
          <span style="padding-left: 15px;">
            {{ $transaction->deliveryorder->pickup_attn }}
          </span>
          <br>
          <span style="font-weight:bold">
            Pickup Contact:
          </span>
          <span style="padding-left: 15px;">
            {{ $transaction->deliveryorder->pickup_contact }}
          </span>
          <br>
          <span style="font-weight:bold">
            Pickup Postcode:
          </span>
          <span style="padding-left: 15px;">
            {{ $transaction->deliveryorder->pickup_postcode }}
          </span>
          <br>
          <span style="font-weight:bold">
            Pickup Location Name:
          </span>
          <span style="padding-left: 15px;">
            {{ $transaction->deliveryorder->pickup_location_name }}
          </span>
          <br>
          <span style="font-weight:bold">
            Pickup Address:
          </span>
          <span style="padding-left: 15px;">
            {{ $transaction->deliveryorder->pickup_address }}
          </span>
          <br>
          <span style="font-weight:bold">
            Pickup Comment:
          </span>
          <span style="padding-left: 15px;">
            {{ $transaction->deliveryorder->pickup_comment }}
          </span>
        </p>
        <p>
          <span style="font-weight:bold">
            Requested Delivery Date:
          </span>
          <span style="padding-left: 15px;">
            {{ $transaction->deliveryorder->delivery_date1 ? \Carbon\Carbon::parse($transaction->deliveryorder->delivery_date1)->format('Y-m-d') : ''}}
          </span>
          <br>
          <span style="font-weight:bold">
            Delivery Timerange:
          </span>
          <span style="padding-left: 15px;">
            {{ $transaction->deliveryorder->delivery_timerange }}
          </span>
          <br>
          <span style="font-weight:bold">
            Delivery Attn:
          </span>
          <span style="padding-left: 15px;">
            {{ $transaction->deliveryorder->delivery_attn }}
          </span>
          <br>
          <span style="font-weight:bold">
            Delivery Contact:
          </span>
          <span style="padding-left: 15px;">
            {{ $transaction->deliveryorder->delivery_contact }}
          </span>
          <br>
          <span style="font-weight:bold">
            Delivery Postcode:
          </span>
          <span style="padding-left: 15px;">
            {{ $transaction->deliveryorder->delivery_postcode }}
          </span>
          <br>
          <span style="font-weight:bold">
            Delivery Location Name:
          </span>
          <span style="padding-left: 15px;">
            {{ $transaction->deliveryorder->delivery_location_name }}
          </span>
          <br>
          <span style="font-weight:bold">
            Delivery Address:
          </span>
          <span style="padding-left: 15px;">
            {{ $transaction->deliveryorder->delivery_address }}
          </span>
          <br>
          <span style="font-weight:bold">
            Delivery Comment:
          </span>
          <span style="padding-left: 15px;">
            {{ $transaction->deliveryorder->delivery_comment }}
          </span>
        </p>

    </body>
</html>