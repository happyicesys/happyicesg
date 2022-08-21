@extends('template')
@section('title')
{{ $PERSON_TITLE }}
@stop
@section('content')

<div class="create_edit">
<div class="panel panel-primary">

    <div class="panel-heading">
        <h3 class="panel-title"><strong>New {{$PERSON_TITLE}}</strong></h3>
    </div>

    <div class="panel-body">
        {!! Form::model($person = new \App\Person, ['action'=>'PersonController@store']) !!}

            @include('person.form')

            <div class="col-md-12">
                <div class="form-group pull-right">
                    {!! Form::submit('Add', ['class'=> 'btn btn-success', 'onsubmit'=>'return storeDeliveryLatLng()']) !!}
                    <a href="/person" class="btn btn-default">Cancel</a>
                </div>
            </div>
        {!! Form::close() !!}
    </div>
</div>
</div>

<script>
    $('.select').select2({
        placeholder: 'Select..'
    });

    function storeDeliveryLatLng() {
        var url = window.location.href;
        var location = '';

        if(url.includes("my")) {
            location = 'Malaysia';
        }else if(url.includes("sg")) {
            location = 'Singapore';
        }

        var dataObj = {
            del_postcode: $('#del_postcode').val(),
            del_address: $('#del_address').val(),
            country: location,
            person_id: $('#person_id').val()
        };
        if(dataObj.del_postcode || dataObj.del_address) {
            return retrieveLatLng(dataObj);
        }else {
            return true;
        }
    }

    function retrieveLatLng(dataObj) {
        var geocoder = new google.maps.Geocoder();

        geocoder.geocode(
                        {componentRestrictions: {country: dataObj.country, postalCode: dataObj.del_postcode},
                        address: dataObj.del_address
                        }, function(results, status) {
            if(results[0]) {
                var data = JSON.parse(JSON.stringify(results[0].geometry.location));
                var coordObj = {
                    lat: data.lat,
                    lng: data.lng
                };
                axios.post('/api/person/storelatlng/' + dataObj.person_id, coordObj).then(function(response) {
                    return true;
                });
            }else {
                return true;
            }
        });
    }
</script>

@stop