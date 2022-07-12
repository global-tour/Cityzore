
<style>

	.datepicker{
		width: 100% !important;

	}

	.hoursWrapper{
		min-height: 60px;
	}

	.hoursWrapper .hours, .optionsWrapper .col-md-12, .meetingsWrapper .col-md-12{
		display: flex; justify-content: center;
    flex-wrap: wrap;
	}

	.hoursWrapper .hours span.hour-item{
      border-radius: 10px;
      border: solid 1px #8134AF;
      padding: 4px 8px;
      cursor: pointer;
      margin: 5px;
      background: #DD2A7B;
      color: #fff;
      font-size: 15px !important;
      transition: all .3s;
	}

	.hoursWrapper .hours span.hour-item:hover{
		transform: scale(1.4);
	}



	.clicked-hour{
		transform: scale(1.4);
		background: #8E44AD !important;
	}

	.hoursWrapper .col-md-12{
		margin-top: 15px; margin-bottom: 15px;
	}


	.optionsWrapper .options input[type=checkbox]{
		opacity: 1;
		display: block;
		position: relative;
		left: 0;
		display: inline-block;
		display: none;

	}
	.optionsWrapper .options .option-item div{
		display: inline-block;
	}

	.hoursWrapper .hours, .optionsWrapper ul li{
		    border: solid 0.5px #ccc;
		    margin: 5px;
		    padding: 7px;
		    border-radius: 10px;
		    /*min-width: 300px;*/


	}

	.optionsWrapper ul li{
	position: relative;
  background: #DD2A7B;
/*	background: rgb(129,52,175);*/
/*	background: linear-gradient(90deg, rgba(129,52,175,1) 0%, rgba(255,255,255,255.39539565826330536) 100%, rgba(221,42,123,0.03125) 100%);

background: rgb(221,42,123);
background: linear-gradient(90deg, rgba(221,42,123,1) 8%, rgba(255,255,255,0.8211659663865546) 100%, rgba(255,255,255,1) 100%, rgba(255,255,255,1) 100%);*/
	transition: all .3s;

	}

	.optionsWrapper ul li:hover{
		cursor: pointer;
		transform: translateX(10px);

	}
	.optionsWrapper ul li label{
		  color: #fff !important;
          font-weight: 700;
	}
	.optionsWrapper ul li label:before{
		border: solid 1px #fff;
	}

	[type="checkbox"]:checked + label:before{
		    border-right: 3px solid #fff !important;
            border-bottom: 3px solid #fff !important;
	}



   .meetingsWrapper{
   	margin-bottom: 50px !important;
    padding-top: 50px;
   }

   .meetingsWrapper .meetings{
   	width: 100%;

   }

   .meetingsWrapper .meeting-wrap{
		margin-top:10px;
		background: #fafafa;
		margin: auto;
		text-align: center;
		padding: 15px;

	}

	.meetingsWrapper .meeting-wrap h3{
		text-decoration: underline;
		margin-bottom: 10px;
	}

	.meetingsWrapper .meeting-wrap ul li{
		font-size: 15px !important;
		background: #f2f2f2;
		padding: 4px 8px;
		margin: 2px;

	}

  .meetingsWrapper .meeting-wrap ul li:hover{
    background-color: #ccc;
    cursor: pointer;
  }

    .meetingsWrapper .meeting-wrap ul li b{
     font-size: 11px;
     font-weight: 500;
    }

    .meetingsWrapper .meeting-wrap ul li span{
      font-weight: 700;
    }


    .print-search-wrap{
      display: flex;
      justify-content: space-between;
      flex-wrap: wrap;
    }

	.meetingsWrapper .printArea{





	}

    .meetingsWrapper .searchArea{




    clear: both;
  }





	.meetingsWrapper .all-meeting-guides ul{
      display:flex;
      justify-content: center;
        flex-wrap: wrap;
	}

	.meetingsWrapper .all-meeting-guides ul li{
      background-color: #EBDEF0;

	}

  .meetingsWrapper .customer{
    text-align: left;
  }

  .meetingsWrapper .customer span{
    font-size: 14px !important;
    border: solid 1px #ccc;
  }

  .meetingsWrapper .searchArea{
    width: 300px !important;
  }

  .meetingsWrapper .search-td{
   width: 100% !important;
   height: 30px !important;
   background: #FEF5E7 !important;
  /* border: solid 1px #ccc !important;*/
  }


	.hoursWrapper, .optionsWrapper, .meetingsWrapper{
		display: none;
	}

	select{
		display: inline-block !important;
		/*width: 300px !important;*/


     padding: 10px;
     border: 1px solid #f2f2f2;
    border-radius: 2px;

     margin-left: 15px !important;
     font-size: 13px !important;
     background-color: #F5B7B1;

	}

  @media(min-width: 992px) {
  select{

    width: 300px !important;
  }

  }



	.btn, .btn-large, .btn-flat{
	 height: 25px !important;
     line-height: 25px !important;
	}

.findit{
  border: solid 3px #16A085;
}


@media(min-width: 992px) {

  #fixedBar{
    display: block;
    position: fixed;
    top:20%;
    right: 0;
    width: 300px;
    min-height: 200px;
    border: solid 1px #ccc;
    padding: 15px;
  }

  #go-top{
    position: fixed;
    right: 10px;
    bottom: 10px;
  }

  #go-top label{
    font-size: 14px;
  }
}

  #top-wrap{
    position: relative;
  }




  html{
    scroll-behavior: smooth;
  }

  #summary-table .checkin-trr{
      display: none;
  }

  .coming-ornot-total{
    font-size: 15px;
    font-weight: bold;
    color: #0e76a8;
    vertical-align: middle;
    padding: 10px;
    text-transform: capitalize;
    text-decoration: underline;
    position: absolute;
    right: 20px;
  }

  .all-option-total-coming-or-not span{
    font-size: 22px;
    font-weight: bold;
    color: #a8320e;
    vertical-align: middle;
    padding: 10px;
    text-transform: capitalize;
    text-decoration: underline;
  }

</style>
@include('panel-partials.head', ['page' => 'meetings-index'])
@include('panel-partials.header', ['page' => 'meetings-index'])
{{-- @include('panel-partials.sidebar')--}}




{{--<div id="fixedBar">




</div>--}}



<a href="#top" id="go-top"><label class="label label-warning">To Top of Page <i class="icon-cz-angle-up"></i></label> </a>


<div class="container" id="main-container">



<div class="sb2-2-3">

	<input type="hidden" name="mindate" id="mindate" value="{{$minDate}}">
	<input type="hidden" name="maxdate" max="maxdate" value="{{$maxDate}}">



<div class="row">


  <div class="col-md-12 col-sm-12 col-xs-12" id="top-wrap">

    <div id="movement-wrap">

   <div class="suppliersWrapper wrapperClass col-md-12" style="border-style: solid; border-color: #e0e0e0; margin-top: 50px">



        <div class="row">
        <div class="col-md-12">

        	<div class="suppliers">

             <select data-info="" class="shaselect" id="supplier-select" name="suppliers" style="height: 50px; width:100% !important; margin-left: 0 !important;">
            <option value="">Select A Supplier</option>
         	<option value="all">All Suppliers</option>

             @foreach($suppliers as $key => $supplier)
            <option value="{{$key}}" @if($key == 33) selected  @endif>{{$supplier}}</option>
             @endforeach
             </select>


        	</div>

        </div>

        </div>
    </div>




   <div class="wrapperClass col-md-12" style="border-style: solid; border-color: #e0e0e0; margin-top: 10px">
        <label class="col-md-12" style="font-size: 20px; margin-top: 20px;"><b><a style="font: inherit!important; color: inherit!important;" id="calendarOperations">Calendar Operations</a></b></label>


        <div class="row">
        <div class="col-md-12">
            <div class="alert alert-info" role="alert">
                Management of all meetings
            </div>
        </div>
        <div data-language='en' class="meeting-datepicker col-md-12" style="margin-top: 30px; margin-bottom: 20px; margin: auto; display: flex; justify-content: center;">


        </div>
        </div>
    </div>





   <div class="hoursWrapper wrapperClass col-md-12" style="border-style: solid; border-color: #e0e0e0; margin-top: 10px">



        <div class="row">
        <div class="col-md-12">

        	<div class="hours">




        	</div>

        </div>

        </div>
    </div>



       <div class="optionsWrapper wrapperClass col-md-12" style="border-style: solid; border-color: #e0e0e0; margin-top: 10px; margin-bottom: 10px; padding: 10px;">



        <div class="row">
        <div class="col-md-12">

        	<div class="options">
        <form action="#" id="option-check-form">

         <ul>

         </ul>


         </form>


        	</div>


        </div>

        </div>

        <div class="row">
        	<div class="col-md-12">
        	<div class="option-button-wrap">
        		<a href="#" class="btn btn-primary" id="get-meeting-button">Get Meetings</a>
        	</div>
        	</div>
        </div>
    </div>
</div>

</div> <!--end of col-wrap-->




<div class="col-md-12 col-sm-12 col-xs-12" id="bottom-wrap">


           <div class="meetingsWrapper wrapperClass col-md-12" style="border-style: solid; border-color: #e0e0e0; margin-top: 10px; margin-bottom: 10px;  position: relative;">


  <div class="print-search-wrap">



            <div class="searchArea">
              <input type="text" class="search-td" placeholder="Search Item & Press Enter...">

            </div>
            <div class="all-option-total-coming-or-not">
              <span>Total: 0 - Exist: 0</span>
            </div>

           	<div class="printArea">

           		<a href="#" id="get-excel-button" href="#" class="btn btn-primary">Excel</a>
           		<a href="#" target="_blank" id="get-pdf-button" href="#" class="btn btn-primary">PDF</a>
              <a href="#" onclick="window.print()" id="get-print-button" href="#" class="btn btn-primary">Print</a>
           	</div>

</div>

        <div class="row">
        <div class="col-md-12">

        	<div class="meetings">











        	</div>


        </div>

        </div>

        <div class="row">
        	<div class="col-md-12">
        	<div class="meeting-button-wrap">
        		{{--<a href="#" class="btn btn-primary">PDF</a>--}}



        	</div>
        	</div>
        </div>
    </div>

</div> <!-- end of col-wrap -->

</div> <!--end of row -->

</div>

</div>


@include('panel-partials.scripts', ['page' => 'meetings-index'])
@include('panel-partials.datatable-scripts', ['page' => 'meetings-index'])
