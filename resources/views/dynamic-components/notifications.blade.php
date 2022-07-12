<div style="width: 250%" class="dropdown show">
    <a href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></a>
    <div class="col-md-12 dropdown-menu" style="position: absolute;top: 57px;left: -450px" aria-labelledby="dropdownMenuLink">
        <div class="col-md-12 buttons">
            <a role="button" style="" id="deleteAllNotifications"><i class="icon-cz-trash"></i>Delete all</a>
            <a role="button" style="" id="markAllAsRead">Mark all as read</a>
        </div>
        <div data-count="{{count(\App\Notification::where('softDelete', '=', 0)->get())}}" style="background: #F5FFFA" id="data" class="col-md-12">
            @if(count(\App\Notification::where('softDelete', '=', 0)->get()) > 0)
                @foreach(\App\Notification::where('softDelete', '=', 0)->orderBy('created_at', 'desc')->get() as $notification)
                    @if($notification->type === 'USER_REGISTER')
                        <div data-id="{{$notification->id}}" style="border-top: 1px #eeeeee solid;border-bottom: 1px #eeeeee solid" @if($notification->isRead == 0) class="notification notRead col-md-12" @else class="col-md-12" @endif >
                            <span style="margin-left:-11px;" class="icon col-md-2"><i style="color: #d9534f" class="icon-cz-user"></i></span>
                            <text style="float: right;">
                                <label class="label label-info title">{{json_decode($notification->data, true)['message']}}</label><br>
                                <a class="deleteNotification" data-id="{{$notification->id}}" style="float: right;" role="button"><i class="icon-cz-cancel"></i></a>
                                <button onclick="window.location.href = '/notification/{{$notification->id}}/details'">Show</button>
                                <span class="date">{{\App\Http\Controllers\Helpers\TimeRelatedFunctions::calculateElapsedTimeOver($notification->created_at)}}</span>
                            </text>
                        </div>
                    @elseif($notification->type === 'COMPANY_REGISTER' || $notification->type == 'SUPPLIER_REGISTER')
                        <div data-id="{{$notification->id}}" style="border-bottom: 1px #eeeeee solid" @if($notification->isRead == 0) class="notification notRead col-md-12" @else class="col-md-12" @endif >
                            <span style="margin-left:-11px;" class="icon col-md-2"><i style="color: #d9534f" class="icon-cz-add-commission"></i></span>
                            <text style="float: right;">
                                <label class="label label-info title">{{json_decode($notification->data, true)['message']}}</label><br>
                                <a class="deleteNotification" data-id="{{$notification->id}}" style="float: right;" role="button"><i class="icon-cz-cancel"></i></a>
                                <button onclick="window.location.href='/notification/{{$notification->id}}/details'">Show</button>
                                <span class="date">{{\App\Http\Controllers\Helpers\TimeRelatedFunctions::calculateElapsedTimeOver($notification->created_at)}}</span>
                            </text>
                        </div>
                    @elseif($notification->type === 'GYG_BOOKING')
                        <div data-id="{{$notification->id}}" style="border-bottom: 1px #eeeeee solid" @if($notification->isRead == 0) class="notification notRead col-md-12" @else class="col-md-12" @endif >
                            <span style="margin-left:-14px;" class="icon col-md-2"><i style="color:#ff5533" class="icon-cz-getyourguide"></i></span>
                            <text style="float: right;">
                                <label class="label label-warning title">{{json_decode($notification->data, true)['message']}}</label><br>
                                <a class="deleteNotification" data-id="{{$notification->id}}" style="float: right;" role="button"><i class="icon-cz-cancel"></i></a>
                                <button onclick="window.location.href='/notification/{{$notification->id}}/details'">Show</button>
                                <span class="date">{{\App\Http\Controllers\Helpers\TimeRelatedFunctions::calculateElapsedTimeOver($notification->created_at)}}</span>
                            </text>
                        </div>
                    @elseif($notification->type === 'BOKUN_BOOKING')
                        <div data-id="{{$notification->id}}" style="border-bottom: 1px #eeeeee solid" @if($notification->isRead == 0) class="notification notRead col-md-12" @else class="col-md-12" @endif >
                            <span style="margin-left:-16px;" class="icon col-md-2"><i style="color:rgb(0,94,158);font-size: 25px" class="icon-cz-bokun"></i></span>
                            <text style="float: right;">
                                <label class="title label label-info">{{json_decode($notification->data, true)['message']}}</label><br>
                                <a class="deleteNotification" data-id="{{$notification->id}}" style="float: right;" role="button"><i class="icon-cz-cancel"></i></a>
                                <button onclick="window.location.href='/notification/{{$notification->id}}/details'">Show</button>
                                <span class="date">{{\App\Http\Controllers\Helpers\TimeRelatedFunctions::calculateElapsedTimeOver($notification->created_at)}}</span>

                            </text>
                        </div>
                    @elseif($notification->type === 'CITYZORE_BOOKING')
                        <div data-id="{{$notification->id}}" style="border-bottom: 1px #eeeeee solid" @if($notification->isRead == 0) class="notification notRead col-md-12" @else class="col-md-12" @endif >
                            <span style="margin-left:-20px;" class="icon cityzore col-md-2"><i class="icon-cz-cityzore"></i></span>
                            <text style="float: right;">
                                <label class="title label label-danger">{{json_decode($notification->data, true)['message']}}</label><br>
                                <a class="deleteNotification" data-id="{{$notification->id}}" style="float: right;" role="button"><i class="icon-cz-cancel"></i></a>
                                <button onclick="window.location.href='/notification/{{$notification->id}}/details'">Show</button>
                                <span class="date">{{\App\Http\Controllers\Helpers\TimeRelatedFunctions::calculateElapsedTimeOver($notification->created_at)}}</span>
                            </text>
                        </div>
                    @elseif($notification->type === 'TICKET_ALERT')
                        <div data-id="{{$notification->id}}" style="border-bottom: 1px #eeeeee solid" @if($notification->isRead == 0) class="notification notRead col-md-12" @else class="col-md-12" @endif >
                            <span style="margin-left:-16px;" class="icon col-md-2"><i style="color:rgb(0,94,158);font-size: 25px" class="icon-cz-ticket"></i></span>
                            <text style="float: right;">
                                <label class="title label label-ticket">{{json_decode($notification->data, true)['message']}}</label><br>
                                <a class="deleteNotification" data-id="{{$notification->id}}" style="float: right;" role="button"><i class="icon-cz-cancel"></i></a>
                                <button onclick="window.location.href='/notification/{{$notification->id}}/details'">Show</button>
                                <span class="date">{{\App\Http\Controllers\Helpers\TimeRelatedFunctions::calculateElapsedTimeOver($notification->created_at)}}</span>
                            </text>
                        </div>
                    @elseif($notification->type == 'AVAILABILITY_EXPIRED')
                        <div data-id="{{$notification->id}}" style="border-bottom: 1px #eeeeee solid" @if($notification->isRead == 0) class="notification notRead col-md-12" @else class="col-md-12" @endif >
                            <span style="margin-left:-16px;" class="icon col-md-2"><i style="color:rgb(0,94,158);font-size: 25px" class="icon-cz-ticket"></i></span>
                            <text style="float: right;">
                                <label class="title label label-ticket">{{json_decode($notification->data, true)['message']}}</label><br>
                                <a class="deleteNotification" data-id="{{$notification->id}}" style="float: right;" role="button"><i class="icon-cz-cancel"></i></a>
                                <button onclick="window.location.href='/notification/{{$notification->id}}/details'">Show</button>
                                <span class="date">{{\App\Http\Controllers\Helpers\TimeRelatedFunctions::calculateElapsedTimeOver($notification->created_at)}}</span>
                            </text>
                        </div>
                    @elseif($notification->type == 'NEW_COMMENT')
                        <div data-id="{{$notification->id}}" style="border-bottom: 1px #eeeeee solid" @if($notification->isRead == 0) class="notification notRead col-md-12" @else class="col-md-12" @endif >
                            <span style="margin-left:-16px;" class="icon col-md-2"><i style="color:rgb(0,94,158);font-size: 25px" class="icon-cz-comment"></i></span>
                            <text style="float: right;">
                                <label class="title label label-ticket">{!! html_entity_decode(json_decode($notification->data, true)['message']) !!}</label><br>
                                <a class="deleteNotification" data-id="{{$notification->id}}" style="float: right;" role="button"><i class="icon-cz-cancel"></i></a>
                                <button onclick="window.location.href='/notification/{{$notification->id}}/details'">Show</button>
                                <span class="date">{{\App\Http\Controllers\Helpers\TimeRelatedFunctions::calculateElapsedTimeOver($notification->created_at)}}</span>
                            </text>
                        </div>
                    @endif
                @endforeach
            @else
                <p style='font-size:12px!important;padding: 10px!important;'>There's no notifications !</p>
            @endif
        </div>
    </div>
</div>

<style>


    #markAllAsRead{
        padding:5px 10px;
        background: #d9f5ff;
        border: 1px solid #d9f5ff;
        border-radius: 5px;
        font-size:12px!important;
        margin-right:10px;
        margin-bottom:10px;
        margin-top: 5px;
        float: right;
    }

    #deleteAllNotifications{
        font-size:14px!important;
        margin-right:15px;
        margin-bottom:10px;
        margin-top: 9px;
        float: right;
        color: #d9534f;
    }

    .dropdown-menu{
        border: none!important;
        border-radius: 0;
    }
    #data{
        padding: 0;
        height: 250px;
        overflow-x: auto;
    }
    #data div {
        padding: 15px 5px 5px 5px;
        font-size: 12px!important;
    }
    div#data i{
        font-size: 25px;
        margin-top: -58px!important;
    }
    div #data i:before{
        display: initial;
    }
    #data div span.cityzore {
        background:-moz-linear-gradient(rgba(236,100,41,1) 0%, rgba(199,50,101,1) 50%, rgba(126,78,161,1) 100%);
        background: -webkit-linear-gradient(rgba(236,100,41,1) 0%, rgba(199,50,101,1) 50%, rgba(126,78,161,1) 100%);
        background: linear-gradient(rgba(236,100,41,1) 0%, rgba(199,50,101,1) 50%, rgba(126,78,161,1) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color:transparent;
    }

    #data div span.icon{
        float: left;
        margin-top: -8px;
    }

    text{
        margin-top: -10px!important;
        margin-right: 15px;

    }
    text button{
        border: 1px solid rgba(111,179,255,1);
        float: right;
        margin-top: 10px;
        background: rgba(111,179,255,1);
        border-radius: 5px;
        color: white;
        margin-right: 12px;
    }

    text a{
        float:right;
        margin-top: 8px;
        color: #dd2c00;
    }

    text a i {
        font-size: 15px!important;

    }

    .notRead{
        background: rgba(111, 179, 255, 0.5);
    }
    span.date{
        margin-top: 12px;
        float: right;
        margin-right: 15px;
    }
    label.title{
        font-size: 11px!important;
    }

    .label-ticket {
        background-color: #004038;
    }
    .label-ticket[href]:hover,
    .label-ticket[href]:focus {
        background-color: #c9302c;
    }

</style>
