@include('panel-partials.head', ['page' => 'dashboard'])

<div class="availability">
    <div class="day">
        {{$availability["hourly"]}}
    </div>
</div>


@include('panel-partials.scripts', ['page' => 'dashboard'])
@include('layouts.modal-box.release-notes')
