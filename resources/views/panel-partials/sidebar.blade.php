<style>
    .dash-icon-button{transition: .5s;color:#bbbbbb;cursor: pointer;font-size: 20px;padding-right: 5px;padding-left: 5px}
    .dash-icon-button:hover{color: #000000;transition: .5s}
</style>

@if(Auth::guard('admin')->check())
    @include('panel-partials.admin-sidebar')
@elseif(Auth::guard('supplier')->check() ||Auth::guard('subUser')->check())
    @include('panel-partials.supplier-sidebar')
@endif
