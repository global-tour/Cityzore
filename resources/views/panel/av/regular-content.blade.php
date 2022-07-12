<div class="row">
    <div class="col-md-2">
        <input style="margin: 0 30px 20px 0;" type="time" class="validate form-control col-md-12 s12 hourRegularFrom" value="">
    </div>
    <div class="col-md-2">
        <select id="intervalRegular">
            @foreach($intervals as $interval)
                <option value="{{$interval->value}}">{{$interval->name}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="row">
    <div class="col-md-2">
        <input style="margin: 0 30px 20px 0;" type="time" class="validate form-control col-md-12 s12 hourRegularTo" value="">
    </div>
    <div class="col-md-2">
        <button class="btn btn-primary divideButton">Divide</button>
    </div>
</div>