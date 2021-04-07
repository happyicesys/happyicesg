@inject('custcategoryGroups', 'App\CustcategoryGroup')
@inject('custcatform', 'App\Custcategory')

<div class="form-group">
    @php
        $img_url = '';
        if($custcat->map_icon_file) {
            $img_url = $custcat::MAP_BASE_URL.$custcat::MAP_ICON_FILE[$custcat->map_icon_file];
        }
    @endphp
    <img src="{{$img_url}}" alt="">
</div>

<div class="form-group">
    {!! Form::label('name', 'Name', ['class'=>'control-label']) !!}
    {!! Form::label('required', '*', ['class'=>'control-label', 'style'=>'color:red;']) !!}
    {!! Form::text('name', null, ['class'=>'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('desc', 'Description', ['class'=>'control-label']) !!}
    {!! Form::textarea('desc', null, ['class'=>'form-control', 'rows'=>'2']) !!}
</div>

<div class="form-group">
    {!! Form::label('map_icon_file', 'Map Icon', ['class'=>'control-label search-title']) !!}
    <select name="map_icon_file" id="map_icon" class="select form-control">
        @foreach($custcatform::MAP_ICON_FILE as $index => $map_icon)
            <option value="{{$index}}" {{$custcat->map_icon_file == $index ? 'selected' : ''}}>
                {{$index}}
            </option>
        @endforeach
    </select>
</div>

<div class="form-group">
    {!! Form::label('custcategory_group_id', 'Custcategory Group', ['class'=>'control-label search-title']) !!}
    <select name="custcategory_group_id" class="select form-control">
        <option value=""></option>
        @foreach($custcategoryGroups->orderBy('name', 'asc')->get() as $index => $custcategoryGroup)
            <option value="{{$custcategoryGroup->id}}" {{$custcat->custcategory_group_id == $custcategoryGroup->id ? 'selected' : ''}}>
                {{$custcategoryGroup->name}}
            </option>
        @endforeach
    </select>
</div>

<script>
    $('.select').select2({
        placeholder: 'Select...'
    });
</script>