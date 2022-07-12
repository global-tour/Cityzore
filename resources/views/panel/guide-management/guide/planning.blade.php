@include('panel-partials.head', ['page' => 'guides-planning'])
@include('panel-partials.header', ['page' => 'guides-planning'])
@include('panel-partials.sidebar')

<?php
	$_SESSION['token'] = time();

    if(!isset($_SESSION['filter']))
        $_SESSION['filter'] = ''; // 24.09.2021 - Kerem
?>

<div class="container-fluid" id="main-container">
	<div class="sb2-2-3">
		<h1>Planning</h1>
		
		<!-- Calendar Modal -->
    <div class="modal fade" id="calendarModal" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
          	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          	<h4 id="details-body-title"></h4>
          </div>
          <div class="modal-body">

            <div class="loadingDiv"></div>

            <!-- QuickSave/Edit FORM -->
          	<form id="modal-form-body">
          		<input type="hidden" id="user_id" name="user_id" value="0">
          		<input type="hidden" name="url" value="">
          		<input type="hidden" name="allDay" value="false">
            	<p>
            		<label>Title: </label>
                	<input class="form-control" name="title" value="" type="text">
                </p>
                <p>
                	<label>Description: </label>
                	<textarea class="form-control" name="description"></textarea>
                </p>
                <p>
                    <label>Category: </label>
                    <select name="categorie" class="form-control">
                        <?php
							foreach($calendar->getCategories() as $categorie)
							{
								$_SESSION['filter'] = str_replace('&amp;', '&', $_SESSION['filter']);
								echo '<option value="'.$categorie.'">'.$categorie.'</option>';
							}
                        ?>
                    </select>
                </p>
                <p>
                	 <label>Event Color:</label>
                	 <input type="text" class="form-control input-sm" value="#587ca3" name="color" id="colorp">
                </p>
                <div class="pull-left mr-10">
                	<p id="repeat-type-select">
                	<label>Repeat:</label>
                	<select id="repeat_select" name="repeat_method" class="form-control">
                        <option value="no" selected>No</option>
                        <option value="every_day">Every Day</option>
                        <option value="every_week">Every Week</option>
                        <option value="every_month">Every Month</option>
                	</select>
                    </p>
                </div>
                <div class="pull-left">
                	<p id="repeat-type-selected">
                	<label>Times:</label>
                	<select id="repeat_times" name="repeat_times" class="form-control">
                    	<option value="1" selected>1</option>
						<?php
                            for($i = 2; $i <= 30; $i++) {
                                echo '<option value="'.$i.'">'.$i.'</option>';
                            }
                        ?>
                	</select>
                    </p>
                </div>
                <div class="clearfix"></div>
                <p id="event-type-select">
                    <label>Type: </label>
                    <select id="event-type" name="all-day" class="form-control">
                        <option value="true">Make event 24H (all day)</option>
                        <option value="false">Make event as I wish</option>
                    </select>
                </p>
                <div id="event-type-selected">
                	<div class="pull-left mr-10">
                    	<p>
                    	<label>Start Date:</label>
                    	<input type="text" name="start_date" class="form-control input-sm" placeholder="Y-M-D" id="startDate">
                        </p>
                    </div>
                    <div class="pull-left">
                    	<p>
                   		<label>Start Time:</label>
                    	<input type="text" class="form-control input-sm" name="start_time" placeholder="HH:MM" id="startTime">
                        </p>
                    </div>
                    <div class="clearfix"></div>
                    <div class="pull-left mr-10">
                    	<p>
                    	<label>End Date:</label>
                    	<input type="text" class="form-control input-sm" name="end_date" placeholder="Y-M-D" id="endDate">
                        </p>
                    </div>
                    <div class="pull-left">
                    	<p>
                    	<label>End Time:</label>
                    	<input type="text" class="form-control input-sm" name="end_time" placeholder="HH:MM" id="endTime">
                        </p>
                    </div>
                </div>
                <div class="clearfix"></div>
				<div class="custom-fields">
				<?php
					$form->generate();
				?>
				</div>
            </form>

            <!-- Modal Details -->
            <div id="details-body">
                <div id="details-body-content"></div>
            </div>

          </div>
          <div class="modal-footer">
            <button type="button" id="export-event" class="btn btn-warning">Export</button>
            <button type="button" id="delete-event" class="btn btn-danger">Delete</button>
            <button type="button" id="edit-event" class="btn btn-info">Edit</button>
            <button type="button" id="add-event" class="btn btn-primary">Add</button>
            <button type="button" id="save-changes" class="btn btn-primary">Save</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Delete Prompt -->
    <div id="cal_prompt" class="modal fade">
    	<div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
        	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        </div>
        <div class="modal-body"></div>
        <div class="modal-footer">
        	<a href="#" class="btn btn-danger" data-option="remove-this">Delete this</a>
            <a href="#" class="btn btn-danger" data-option="remove-repetitives">Delete all</a>
        	<a href="#" class="btn btn-default" data-dismiss="modal">Close</a>
        </div>
        </div>
        </div>
    </div>

    <!-- Modal Edit Prompt -->
    <div id="cal_edit_prompt_save" class="modal fade">
    	<div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
        	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        </div>
        <div class="modal-body-custom"></div>
        <div class="modal-footer">
        	<a href="#" class="btn btn-info" data-option="save-this">Save this</a>
            <a href="#" class="btn btn-info" data-option="save-repetitives">Save all</a>
        	<a href="#" class="btn btn-default" data-dismiss="modal">Close</a>
        </div>
        </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div id="cal_import" class="modal fade">
    	<div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-body-import" style="white-space: normal;">
        	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4>Import Event</h4>

        	<p class="help-block">Copy & Paste the event code from your .ics file, open it using an text editor</p>
            <textarea class="form-control" rows="10" id="import_content" style="margin-bottom: 10px;"></textarea>
            <input type="button" class="pull-right btn btn-info" onClick="calendar.calendarImport()" value="Import" />
        </div>
        </div>
        </div>
    </div>

    <input type="hidden" name="cal_token" id="cal_token" value="<?php echo $_SESSION['token']; ?>" />

    <!---------------------------------------------- THEME ---------------------------------------------->

	<div class="container-fluid" style="margin-top: 80px;">

        <div class="row">
            <div class="col-lg-6">
                    <?php if($calendar->getCategories() !== false) { ?>
                      <form id="filter-category">
                          <select class="form-control input-sm" style="width: auto;">
                            <?php
                                $selected = (isset($_SESSION['filter']) && $_SESSION['filter'] == 'all-fields' ? 'selected' : '');
                                echo '<option '.$selected.' value="all-fields">All</option>';
                                foreach($calendar->getCategories() as $categorie)
                                {
                                    $selectedLoop = (isset($_SESSION['filter']) && $_SESSION['filter'] == $categorie ? 'selected' : '');
                                    echo '<option '.$selectedLoop.' value="'.$categorie.'">'.$categorie.'</option>';
                                }
                            ?>
                          </select>
                      </form>
                    <?php } ?>
            </div>
            <div class="col-lg-6" style="padding: 4px;">
                  {{-- <a href="/guide/planning/exporter" class="btn btn-warning pull-right" style="margin-right: 10px;">Export</a> --}}
                  <button onclick="redirectToExporter()" class="btn btn-warning pull-right" style="margin-right: 10px;">Export</button>
                  <a href="#cal_import" class="btn btn-info pull-right" data-toggle="modal" style="margin-right: 10px;">Import</a>
            </div>
       </div>

      <div class="row">
            <div class="col-lg-6">
                <div style="margin-bottom: 10px;">
                    <select id="guide-selection" class="form-control input-md" style="width: auto;">
                         {{-- <option value="" selected>Please select a guide</option> --}}
                         @foreach($guides as $guide)
                            <option value="{{$guide->id}}">{{$guide->name . " " . $guide->surname}}</option>
                         @endforeach
                    </select>
              </div>      
            </div>
            <div class="col-lg-6">
                    <form class="pull-right form-inline" style="margin-top: 8px; margin-left: 20px;" id="search">
                        <button class="btn" style="margin-top: 10px; float: right;" type="button">Search</button>

                        <div class="form-group">
                            <input class="form-control" type="text" style="float: right;">
                        </div>
                    </form>
            </div>
        </div>

      <div class="clearfix"></div>

      <!-- Filter by Category (required if you want to have categories filtering) -->
      
            
      

      <div class="box">
        <div class="header"><h4>Calendar</h4></div>
        <div class="content">
            <div id="calendar"></div>
			<div id="loading" class="spinner">
			  <div class="bounce1"></div>
			  <div class="bounce2"></div>
			  <div class="bounce3"></div>
			</div>
        </div>
    </div>

    </div> <!-- /container -->
	</div>
</div>

@include('panel-partials.scripts', ['page' => 'guides-planning'])
{{-- @include('panel-partials.datatable-scripts', ['page' => 'guides-index']) --}}