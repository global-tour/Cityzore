@include('panel-partials.head', ['page' => 'finance-bills'])
@include('panel-partials.header', ['page' => 'finance-bills'])
@include('panel-partials.sidebar')

<style>

    .imagewrap{
        transition: all ease .4s;
        position: relative;
        display: flex;
        align-content: center;
        align-items: center;
        justify-content: center;
    }
    .imagewrap img{
        width: 100%;
        cursor: pointer;
    }

    .imagewrap .icon{
        opacity: 0;
        position: absolute;
        transition: all ease .1s;
        color: #fff;
        transform: scale(3);
    }

    .imagewrap:hover .icon{
        opacity: 0.5;
        

    }

    .imagewrap:hover{
      opacity: 0.85;
      transform: scale(1.1);
    }


        .datepicker{
        width: 100% !important;

    }

</style>


<div class="sb2-2-2">
    <ul>
        <li>
            <a href="index.html"><i class="fa fa-home" aria-hidden="true"></i> Home</a>
        </li>
        <li class="active-bre">
            <a href="#"> Bills</a>
        </li>
        <li class="page-back">
            <a href="{{url('/')}}" style="font-size: 18px;"><i class="icon-cz-double-left" aria-hidden="true"></i> Panel</a>
        </li>
    </ul>
</div>
<div class="sb2-2-add-blog sb2-2-1">
   
    <div class="inn-title">
        <h4>Bills</h4>
    </div>

  <div class="row" style="margin-bottom: 30px;">

    <div class="date-wrap">
        
        <div data-language='en' class="billing-datepicker col-md-12" style="margin-top: 30px; margin-bottom: 20px; margin: auto; display: flex; justify-content: center;">
       
     
        </div>
        

    </div>
      

  </div>


    <div class="row" id="billing-image-wrapper">

    </div>
</div>


@include('panel-partials.scripts', ['page' => 'finance-bills'])
@include('panel-partials.datatable-scripts', ['page' => 'finance-bills'])

