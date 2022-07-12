<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Admin;
use App\Meeting;
use App\Shift;
use App\Shiftlog;
use Carbon\Carbon;
use Auth;

use File;
use App\Http\Controllers\Calendar\includes\ICal;

class GuideManagementController extends Controller
{



   public function index(){

   	$max_date_meeting = Meeting::max('date');
   	$min_date_meeting = Meeting::min('date');

   	$to_future = Carbon::parse(Carbon::now())->diffInMonths($max_date_meeting, false);
   	$to_past = Carbon::parse(Carbon::now())->diffInMonths($min_date_meeting, false);
    $to_past = $to_past + 1;


    $to_past = abs($to_past);
    $to_future = abs($to_future);




    $guides = Admin::where('roles', 'LIKE', '%Guide%')->get();

    $all_meetings = Meeting::where(function($q){
      if(isset(request()->index_guide_search)){
      	$parts = explode("#", request()->index_guide_search);
        $q->where("date",">=", $parts[0]);
        $q->where("date","<=", $parts[1]);

      }

    })->orderBy('clock_in', 'asc')->get();
   	return view('panel.guide-management.guide.index', compact('guides','all_meetings', 'to_past', 'to_future'));
   }



    public function detail(Request $request, $guide_id){
        $target_guide = Admin::find($guide_id);
    	$days_of_month = !isset($request->index_guide_search) ? Carbon::now()->firstOfMonth()->diffInDays(Carbon::now()->lastOfMonth()) + 1 : Carbon::parse(explode("#", $request->index_guide_search)[0])->firstOfMonth()->diffInDays(Carbon::parse(explode("#", $request->index_guide_search)[0])->lastOfMonth())+1;

    	$guides = Admin::where('roles', 'LIKE', '%Guide%')->get();

    	   	$max_date_meeting = Meeting::max('date');
   	        $min_date_meeting = Meeting::min('date');

   	$to_future = Carbon::parse(Carbon::now())->diffInMonths($max_date_meeting, false);
   	$to_past = Carbon::parse(Carbon::now())->diffInMonths($min_date_meeting, false);
    $to_past = $to_past + 1;

    $to_past = abs($to_past);
    $to_future = abs($to_future);


   	return view('panel.guide-management.guide.detail', compact('to_past', 'to_future', 'guides','days_of_month', 'target_guide'));
   }


    public function ajax(Request $request){

    switch ($request->action) {
    	case 'get_shift_modal':
    		$shift = Shift::findOrFail($request->data_id);
    		$view = view('panel.guide-management.modal.inout', compact('shift'))->render();
    		return response()->json(['status' => 'success', 'view' => $view]);
    		break;

        case 'get_shift_modal_create':
        $meeting = Meeting::findOrFail($request->data_meeting_id);
        $meeting_point = $request->data_meeting_point;
        $guide_id = $request->data_guide_id;

        $view = view('panel.guide-management.modal.inout-create', compact('meeting','meeting_point','guide_id'))->render();
        return response()->json(['status' => 'success', 'view' => $view]);
        break;

        case 'create_shift_form':

         $shift = new Shift();
         $shift->time_in = Carbon::parse($request->from_date." ".$request->shift_clock_in)->format('Y-m-d H:i:s');
         $shift->time_out = Carbon::parse($request->to_date." ".$request->shift_clock_out)->format('Y-m-d H:i:s');
         $shift->supervisor_message = $request->shift_supervisor_message;
         $shift->meeting_point = $request->meeting_point;
         $shift->meeting_id = $request->meeting_id;
         $shift->guide_id = $request->guide_id;
         $shift->is_approved = 1;

         if($shift->save()){

          $log = new Shiftlog();
          $log->logger_id = Auth::guard('admin')->user()->id;
          $log->logger_email = Auth::guard('admin')->user()->email;
          $log->activity_message = "Shift Created By Admin";
          $log->shift_id = $shift->id;
          $log->save();

           return response()->json(["status"=>"success","statusCode" => 200, "data" => ["message" => "Shift Created Successfully!"]]);
         }
         return response()->json(["status"=>"error","statusCode" => 400, "data" => ["message" => "An Error Occured!"]]);





          break;


    		case 'update_shift_form':

    		$data_before = [];
    		$data_after = [];
    		$activity_message = '';
    		$log = new Shiftlog();
    		$log->logger_id = Auth::guard('admin')->user()->id;
    		$log->logger_email = Auth::guard('admin')->user()->email;


             $shift = Shift::findOrFail($request->shift_id);

             $data_before = [
              "clock_in" => $shift->time_in->format("H:i"),
              "clock_out" => !empty($shift->time_out) ? $shift->time_out->format("H:i") : '00:00:00',
              "supervisor_message" => $shift->supervisor_message
             ];
             $log->activity_data_before = json_encode($data_before);



             $shift->time_in = $request->from_date." ".trim($request->shift_clock_in);
             $shift->time_out = $request->to_date." ".trim($request->shift_clock_out);
             $shift->supervisor_message = trim($request->shift_supervisor_message);


             $data_after = [
              "clock_in" => $shift->time_in->format("H:i"),
              "clock_out" => $shift->time_out->format("H:i"),
              "supervisor_message" => $shift->supervisor_message
             ];
             $log->activity_data_after = json_encode($data_after);
             //$shift->is_approved =  1;


             if($data_before['clock_in'] != $data_after['clock_in']){
              $activity_message .= "<br> Clock_in changed from <mark>".$data_before['clock_in'] ."</mark> to <mark>".$data_after['clock_in']." </mark>";
             }

             if($data_before['clock_out'] != $data_after['clock_out']){
              $activity_message .= "<br> Clock_out changed from <mark>".$data_before['clock_out'] ."</mark> to <mark>".$data_after['clock_out']." </mark>";
             }


             if($data_before['supervisor_message'] != $data_after['supervisor_message']){
              $activity_message .= "<br> Supervisor Message changed from <mark>".$data_before['supervisor_message'] ."</mark> to <mark>".$data_after['supervisor_message']."</mark>";
             }
             $log->activity_message = $activity_message;

             if($shift->save()){
             	$log->shift_id = $shift->id;
             	$log->save();

             	return response()->json(['status' => 'success', 'statusCode' => 200, 'data' => ['message' => 'Shift has been updated successfully!']]);
             }
             return response()->json(['status' => 'error', 'statusCode' => 400, 'error' => ['message' => 'An Error Occured!']]);


    			break;

    			case 'approve_or_not':



    			$shift = Shift::findOrFail($request->data_id);
    			//$shift->is_approved = abs($shift->is_approved - 1);
    			$shift->is_approved = 1;
    			if($shift->save()){
                 $log = new Shiftlog();
                 $log->logger_id = Auth::guard('admin')->user()->id;
                 $log->logger_email = Auth::guard('admin')->user()->email;
                 $log->shift_id = $shift->id;
                 $log->activity_message = "Shift Approved";
                 $log->save();



    				return response()->json(["status" => "success", "statusCode" => 200, "data" => ["message" => "Shift Approved Successfully!"]]);
    			}

    				break;

    				case 'delete_shift':

    					 $shift = Shift::findOrFail($request->data_id);


		    			if($shift->delete()){
		                 $log = new Shiftlog();
		                 $log->logger_id = Auth::guard('admin')->user()->id;
		                 $log->logger_email = Auth::guard('admin')->user()->email;
		                 $log->shift_id = $shift->id;
		                 $log->activity_message = "Shift Deleted";
		                 $log->save();

		                 return response()->json(["status" => "success", "statusCode" => 200, "data" => ["message" => "Shift Deleted Successfully!"]]);
		             }

    					break;

    			case 'delete_log':
    		     $log = Shiftlog::findOrFail($request->data_id);
    		     if($log->delete()){
                  return response()->json(['status' => 'success', 'statusCode' => 200, 'data' => ['message' => 'Log Removed Successfully!']]);
    		     }
    		     return response()->json(['status' => 'error', 'statusCode' => 400, 'error' => ['message' => 'An Error Occured!']]);




    				break;


                    case 'set_day_off':
                    $targetGuide = Admin::findOrFail($request->guide_id);
                    $date = $request->off_date;

                    if( $targetGuide->offday()->where("date", $date)->where("status", 1)->count() ){
                      $result = $targetGuide->offday()->where("date", $date)->where("status", 1)->delete();
                      if($result){
                        return response()->json(["status" => "success", "type" => "remove"]);
                      }
                      return response()->json(["status" => "error", "message" => "An Error Occurred!"]);

                    }else{

                     $result = $targetGuide->offday()->create([
                      "status" => 1,
                      "date" => $date
                     ]);
                      if($result){
                        return response()->json(["status" => "success", "type" => "add"]);
                      }
                      return response()->json(["status" => "error", "message" => "An Error Occurred!"]);


                    }


                        break;

    	default:
    		# code...
    		break;
    }


    }

    public function planning() {
        include(app_path() . '/Http/Controllers/Calendar/includes/loader.php');

        $guides = Admin::where('roles', 'LIKE', '%Guide%')->get();
        return view('panel.guide-management.guide.planning', compact('calendar', 'form', 'guides'));
    }

    public function cal_events(Request $request) {
        include(app_path() . '/Http/Controllers/Calendar/includes/loader.php');

        $user_id = isset($request->user_id) ? $request->user_id : "-1";
        if(isset($is_api) && $is_api == true)
        {
            echo $calendar->json_transform($user_id);
        } else {
            if(isset($request->token) && $request->token == $_SESSION['token'])
            {
                echo $calendar->json_transform($user_id);
            }
        }
    }

    public function cal_quicksave(Request $request) {
        include(app_path() . '/Http/Controllers/Calendar/includes/loader.php');

        if(isset($request->token) && $request->token == $_SESSION['token'])
        {
            $request->all()['start_date'] = (strlen($request->all()['start_date']) !== 0 ? $request->all()['start_date'] : date('Y-m-d', time()));
            $request->all()['start_time'] = (strlen($request->all()['start_time']) !== 0 ? $request->all()['start_time'] : '00:00:00');
            $request->all()['end_date'] = (strlen($request->all()['end_date']) !== 0 ? $request->all()['end_date'] : date('Y-m-d', strtotime('+1 day', strtotime($start_date))));
            $request->all()['end_time'] = (strlen($request->all()['end_time']) !== 0 ? $request->all()['end_time'] : '00:00:00');

            // $request->all()['user_id'] = 0;

            // Category Handler - Core
            if(isset($request->all()['categorie']) && strlen($request->all()['categorie']) !== 0)
            {
                $request->all()['categorie'] = $request->all()['categorie'];
            } else {
                $request->all()['categorie'] = '';
            }

            if(strlen($request->all()['title']) == 0)
            {
                echo 0;
            } else {

                // extract checkbox
                foreach($request->all() as $pk => $pv)
                {
                    if(is_array($pv))
                    {
                        $checkboxes[$pk] = $pv;
                        unset($request->all()[$pk]);
                    }
                }

                $checkbox_i = array();
                if(isset($checkboxes))
                {
                    foreach($checkboxes as $ck => $cv)
                    {
                        $checkbox_i[$ck] = serialize($cv);
                    }
                }

                $test = $request->all();
                $test = array_merge($test, $checkbox_i);

                if(isset($_FILES))
                {
                    $add_event = $calendar->addEvent($test, $_FILES);
                } else {
                    $add_event = $calendar->addEvent($test, '');
                }

                if($add_event == true)
                {
                    echo 1;
                } else {
                    echo 0;
                }
            }
        }
    }

    public function cal_description(Request $request) {
        include(app_path() . '/Http/Controllers/Calendar/includes/loader.php');

        if(isset($request->token) && $request->token == $_SESSION['token'])
        {
            $c = $calendar->get_event($request->all()['id']);
            if($calendar->check($request->all(), $c))
            {
                $content = $c['description'];
                $cat = $c['category'];
                $color = $c['color'];

                if($cat == '') { $cat = 'General'; }

                if($content == '') { $content = '$null'; } else {
                    $content = $formater->html_format($embed->oembed($content));
                    $content = $maps->to_maps($content);
                }

                $content_editable = $c['description'];

                if($color == '') { $color = '$null'; }

                $array = array('description' => $content, 'description_editable' => $content_editable, 'category' => $cat, 'categorie' => $cat, 'all-day' => $c['allDay'], 'color' => $color, 'categories' => $calendar->categories);

                unset($c['description'], $c['category'], $c['categorie'], $c['color'], $c['allDay']);

                $nc = array();
                foreach($c as $ck => $cv)
                {
                    if($calendar->is_serialized($cv))
                    {
                        $unser = unserialize($cv);
                        $unser = array_filter($unser);
                        $nc[$ck] = $unser;
                    } else {
                        $nc[$ck] = $cv;
                    }
                }

                $array = array_merge($array, $nc);

                if($content == true)
                {
                    if(isset($request->all()['mode']) && $request->all()['mode'] == 'edit')
                    {
                        echo json_encode($array);
                    } else {
                        echo $content_editable;
                    }
                } else {
                    echo '';
                }
            }
        }
    }

    public function cal_check_rep_events(Request $request) {
        include(app_path() . '/Http/Controllers/Calendar/includes/loader.php');

        if(isset($request->token) && $request->token == $_SESSION['token'])
        {
            $rep = $calendar->check_repetitive_events($request->all()['id']);

            if($rep == true)
            {
                echo 'REP_FOUND';
            } else {
                echo 'REP_NOT_FOUND';
            }
        }
    }

    public function cal_edit_update(Request $request) {
        include(app_path() . '/Http/Controllers/Calendar/includes/loader.php');

        if(isset($request->token) && $request->token == $_SESSION['token'])
        {
            $c = $calendar->get_event($request->all()['id']);
            if($calendar->editCheck($request->all(), $c))
            {
                $request->all()['url'] = 'false';

                if(isset($request->all()['rep_id']) && isset($request->all()['method']) && $request->all()['method'] == 'repetitive_event')
                {
                    $request->all()['rep_id'] = $request->all()['rep_id'];
                }

                if(isset($request->all()['categorie']))
                {
                    $request->all()['categorie'] = $request->all()['categorie'];
                } else {
                    $request->all()['categorie'] = '';
                }

                if($request->all()['start_time'] !== '00:00' || $request->all()['end_time'] !== '00:00')
                {
                    $request->all()['allDay'] = 'false';
                } else {
                    $request->all()['allDay'] = 'true';
                }

                if(strtotime($request->all()['end_date']) < strtotime($request->all()['start_date']))
                {
                    return false;
                }

                // extract checkbox
                foreach($request->all() as $pk => $pv)
                {
                    if(is_array($pv))
                    {
                        $checkboxes[$pk] = $pv;
                        unset($request->all()[$pk]);
                    }
                }

                $checkbox_i = array();
                if(isset($checkboxes))
                {
                    foreach($checkboxes as $ck => $cv)
                    {
                        $checkbox_i[$ck] = serialize($cv);
                    }
                }

                $test = $request->all();
                $test = array_merge($test, $checkbox_i);

                if($calendar->updates($test, $_FILES) === true) {
                    //return true;
                    return ''; // 23.09.2021 - Kerem
                } else {
                    //return false;
                    return 'false'; // 23.09.2021 - Kerem
                }
            } else {
                echo 0;
            }
        }
    }

    public function cal_delete(Request $request) {
        include(app_path() . '/Http/Controllers/Calendar/includes/loader.php');

        if(isset($request->token) && $request->token == $_SESSION['token'])
        {
            $c = $calendar->get_event($request->all()['id']);
            if($calendar->editCheck($request->all(), $c))
            {
                if(isset($request->all()['method']) && $request->all()['method'] == 'repetitive_event')
                {
                    $method = true;
                    $rep_id = $request->all()['rep_id'];
                    $id = $request->all()['id'];
                } else {
                    $method = '';
                    $rep_id = $request->all()['id'];
                    $id = $request->all()['id'];
                }
                $calendar->delete($id, $rep_id, $method);
            }
        }
    }

    public function cal_update(Request $request) {
        include(app_path() . '/Http/Controllers/Calendar/includes/loader.php');

        if(isset($request->token) && $request->token == $_SESSION['token'])
        {
            $c = $calendar->get_event($request->all()['original_id']);
            if($calendar->dropResizeCheck($request->all(), $c))
            {
                // Catch start, end and id from javascript
                $start = $request->all()['start'];
                $end = $request->all()['end'];
                $id = $request->all()['id'];
                $allDay = $request->all()['allDay'];
                $original_id = $request->all()['original_id'];

                echo $calendar->update($allDay, $start, $end, $id, $original_id);
            }
        }
    }

    public function exporter(Request $request) {
        include(app_path() . '/Http/Controllers/Calendar/includes/loader.php');

        // set correct content-type-header

        // header('Content-type: text/calendar; charset=utf-8');
        // header('Content-Disposition: inline; filename=calendar.ics');

        $user_id = $request->user_id ? $request->user_id : "-1";

        header("Content-type:text/calendar");
        header('Content-Disposition: attachment; filename="calendar.ics"');
        Header('Content-Length: '.strlen($calendar->icalExport_all($user_id)));
        Header('Connection: close');

        // echo preg_replace('#/[[^\]]+\]#', '', $calendar->icalExport_all());
        echo $calendar->icalExport_all($user_id);
    }

    public function cal_export(Request $request) {
        include(app_path() . '/Http/Controllers/Calendar/includes/loader.php');

        if(isset($request->token) && $request->token == $_SESSION['token'])
        {
            $c = $calendar->get_event($request->all()['id']);
            if($calendar->exportCheck($request->all(), $c))
            {
                if(isset($request->all()['method']) && $request->all()['method'] == 'export') {
                    // Catch data from javascript
                    $id = $request->all()['id'];
                    $start_date = date('Ymd\THis', strtotime($request->all()['start_date'])).'Z';
                    $end_date = date('Ymd\THis', strtotime($request->all()['end_date'])).'Z';

                    $data = $calendar->icalExport($id, $start_date, $end_date);
                    header("Content-type:text/calendar");
                    header('Content-Disposition: attachment; filename=Event-'.$id.'.ics');
                    Header('Content-Length: '.strlen($data));
                    Header('Connection: close');
                    //echo preg_replace('#\[[^\]]+\]#', '', $data);
                    echo $data;
                } else {
                    $id = intval($request->all()['id']);
                    if(file_exists(getcwd().'/'.'Event-'.$id.'.ics')) {
                        @unlink(getcwd().'/'.'Event-'.$id.'.ics');
                    }
                }
            }
        }
    }

    public function importer(Request $request) {
        include(app_path() . '/Http/Controllers/Calendar/includes/loader.php');
        //include(app_path() . '\Http\Controllers\Calendar\includes\ics.parser.class.php');

        error_reporting(0);

        if(IMPORT_EVENTS == true)
        {
            if(strlen($request->all()['import']) !== 0)
            {
                $file = 'calendar/includes/ics_template.ics';
                //$save = file_put_contents($file, $request->all()['import']);
                $save = File::put($file, $request->all()['import']);

                if($save)
                {
                    //$ical = new ical($file);
                    $ical = new ICal($file);

                    $events = $ical->events();

                    if(!empty($events))
                    {
                        $post = array('repeat_method' => 'no', 'repeat_times' => 1);

                        foreach($events as $ev)
                        {
                            $post['start_date'] = date('Y-m-d', $ical->iCalDateToUnixTimestamp($ev['DTSTART']));
                            $post['start_time'] = date('H:i', $ical->iCalDateToUnixTimestamp($ev['DTSTART']));
                            $post['end_date'] = date('Y-m-d', $ical->iCalDateToUnixTimestamp($ev['DTEND']));
                            $post['end_time'] = date('H:i', $ical->iCalDateToUnixTimestamp($ev['DTEND']));
                            $post['description'] = $ev['DESCRIPTION'];
                            $post['title'] = $ev['SUMMARY'];

                            if(isset($ev['AFFC-COLOR'])) { $post['color'] = $ev['AFFC-COLOR']; } else { $post['color'] = '#587ca3'; }
                            if(isset($ev['AFFC-ALLDAY'])) { $post['all-day'] = $ev['AFFC-ALLDAY']; } else { $post['all-day'] = 'false'; }
                            if(isset($ev['AFFC-URL'])) { $post['url'] = $ev['AFFC-URL']; } else { $post['url'] = 'false'; }

                            if(isset($ev['AFFC-UID'])) { $post['user_id'] = $ev['AFFC-UID']; } else { $post['user_id'] = 0; }
                            if(isset($post['categorie'])) { $post['categorie'] = $ev['CATEGORIES'];} else { $post['categorie'] = 'General'; }

                            $calendar->addEvent($post, '');
                        }
                        echo $ical->event_count.' Events were imported!';
                    }
                }
            } else {
                echo 'Nothing to import';
            }
        }
    }

    public function loader(Request $request) {
        $_POST = [];
        foreach($request->all() as $key => $req) {
            $_POST[$key] = $req;

            if($key == "search" && $req == null)
                $_POST["search"] = '';
        }

        include(app_path() . '/Http/Controllers/Calendar/includes/loader.php');
    }
}
