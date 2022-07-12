<?php

namespace App\Http\Controllers\Admin;

use App\Admin;
use App\Adminlog;
use App\Mails;
use App\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use GuzzleHttp\Client;
use App\ChatAdmin;

class AdminController extends Controller
{

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $admins = Admin::all();
        $isSuperUser = auth()->guard('admin')->user()->isSuperUser == 1;
        return view('panel.admins.index',
            [
                'admins' => $admins,
                'isSuperUser' => $isSuperUser
            ]
        );
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $roles = Role::all();

        return view('panel.admins.create', ['roles' => $roles]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        //Validate name, email and password fields
        $messages = array(
            "name.required" => "Name field is required",
            "name.max" => "Name field can be maximum of 120 characters",
            "email.required" => "Email field is required",
            "email.email" => "Email does not have email type",
            "email.unique" => "Email record already exists in admins",
            "password.required" => "Password field is required",
            "password.min" => "Password field can be minimum of 6 characters",
            "password.confirmed" => "Password confirmation is incorrect"
        );
        $this->validate($request, [
            'name'=>'required|max:120',
            'email'=>'required|email|unique:admins',
            'password'=>'required|min:6|confirmed',
        ], $messages);

        $admin = new Admin();
        $password = Hash::make($request->password);
        $admin->name = $request->name;
        $admin->surname = $request->surname;
        $admin->email = $request->email;
        $admin->password = $password;
        $admin->company = $request->company;
        $roles = $request->roles;
        $roleArray = [];
        foreach ($roles as $role) {
            array_push($roleArray, Role::findOrFail($role)->name);
        }
        $admin->roles = json_encode($roleArray);
        $admin->permissions = $request->permissions ? json_encode($request->permissions) : "[]";
        $admin->save();

        $admin->save();

        return redirect('/admin')->with(['success' => 'New Admin Created Successfully!']);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        if (in_array('Super Admin', json_decode(auth()->guard('admin')->user()->roles, true)) || $id == Auth::guard('admin')->user()->id) {
            $admin = Admin::findOrFail($id);
            $roles = Role::all();
            return view('panel.admins.edit', ['admin' => $admin, 'roles' => $roles]);
        }
        abort(404);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, $id)
    {

        $admin = Admin::findOrFail($id);

        //Validate name, email and password fields

        $this->validate($request, [
            'name'=>'required|max:255',
            'surname'=>'required|max:255',
            'email'=>'required|max:255'

        ]);
        if(!empty($request->password)){
            $password = Hash::make($request->password);
            $admin->password = $password;
        }

        $admin->name = $request->name;
        $admin->surname = $request->surname;
        $admin->email = $request->email;

        $admin->company = $request->company;
        $roles = $request->roles;
        $roleArray = [];

        if(empty($roles)){
            if(empty($admin->roles)){
                $admin->roles = "[Admin]";
            }
            $roles = json_decode($admin->roles, true);

            foreach ($roles as $role) {
            array_push($roleArray, Role::where("name",$role)->first()->name);
        }
        }else{
            foreach ($roles as $role) {
            array_push($roleArray, Role::findOrFail($role)->name);
        }
        }


        $admin->roles = json_encode($roleArray);
        $admin->permissions = $request->permissions ? json_encode($request->permissions) : "[]";
        $admin->save();
        return redirect('/admin')->with(['success' => 'changes has been done successfully!']);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $admin = Admin::findOrFail($id);
        $admin->delete();
        return redirect('/admin');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getUserLogs()
    {
        return view('panel.userlogs.index');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getApiLogs()
    {
        return view('panel.apilogs.index');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getErrorLogs()
    {
        return view('panel.errorlogs.index');
    }

    public function ajax(Request $request){
        switch ($request->action) {

            case 'chat_status_set_to_zero':
               $data = ChatAdmin::where("user_id", $request->user_id)->first();
               $data->status = 0;
               $data->save();
                break;


            case 'after_create_admin_form_clicked_submit_button':

              if($request->type == "create"){
                $response = $this->createChatAdmin($request);
            }else{
                $response = $this->updateChatAdmin($request);
            }

                return $response;






                break;
            case 'register_for_chat_for_admin_layout':

            $admin = Admin::findOrFail($request->user_id);

             $view = view("panel-partials.ajax-pages.admins.register_chat_modal_for_admins", compact("admin"))->render();
                  return response()->json(['view' => $view]);


                break;

            default:
                // code...
                break;
        }
    }



    protected function createChatAdmin($request){

         $kontrol = "/\S*((?=\S{8,})(?=\S*[A-Z]))\S*/";


             if(!preg_match($kontrol,$request->chat_password))
             {
              return response()->json(["status" => "error", "message" => "Your password must contain at least one capital letter"]);
             }

         $baseUrl = "https://global-tickets-socket-service.herokuapp.com";

                $headers = [

                    'Accept' => 'application/json'
                ];



          $client = new Client(['base_uri' => $baseUrl, 'headers' => $headers]);




           $response = $client->request('POST', '/authentication/register/staff', [

                'json' => [
                'name' => $request->chat_user_name,
                'surname' => $request->chat_user_surname,
                'password' => $request->chat_password,
                'email' => $request->email

               ]
            ]);



        $response = json_decode($response->getBody()->getContents(), true);
        if($response["success"]){

            // database kaydediyoruz
            $chatUser = new ChatAdmin();

             $chatUser->user_id = $request->user_id;
             $chatUser->chat_password = $request->chat_password;
             $chatUser->response_data = json_encode($response["data"]);
             $chatUser->status = 1;



            if($chatUser->save()){
                return response()->json(["status" => "success", "message" => "Account Created Successfully"]);
            }

            return response()->json(["status" => "error", "message" => "Response OK But Cannot Save Record to Our Database"]);



        }elseif (!$response["success"]) {
            $errorMessage = $response["error"]["message"] ?? "Database Response Error!";
            return response()->json(["status" => "error", "message" => $errorMessage]);

        }else{
            return response()->json(["status" => "error", "message" => "An Error Occured!"]);

        }



    }




    public function updateChatAdmin($request){
          $kontrol = "/\S*((?=\S{8,})(?=\S*[A-Z]))\S*/";


             if(!preg_match($kontrol,$request->chat_password))
             {
              return response()->json(["status" => "error", "message" => "Your password must contain at least one capital letter"]);
             }

         $user = Admin::findOrFail($request->user_id);
         $responseData = $user->chats->response_data;
         $responseData = json_decode($responseData, true);



         $baseUrl = "https://global-tickets-socket-service.herokuapp.com";

                $headers = [

                    'x-access-token' => $responseData["token"],
                    'Accept' => 'application/json'
                ];



          $client = new Client(['base_uri' => $baseUrl, 'headers' => $headers]);




           $response = $client->request('PUT', '/api/staff/'.$responseData["_id"], [

                'json' => [
                 'password' => $request->chat_password
                ]



            ]);




        $response = json_decode($response->getBody()->getContents(), true);
        if($response["success"]){

            // database kaydediyoruz
            $chatUser =  ChatAdmin::where("user_id", $user->id)->first();


             $chatUser->chat_password = $request->chat_password;
             //$chatUser->response_data = json_encode($response["data"]);
             $chatUser->status = 1;



            if($chatUser->save()){
                return response()->json(["status" => "success", "message" => "Account Updated Successfully"]);
            }

            return response()->json(["status" => "success", "message" => "Response OK But Cannot Save Record to Our Database"]);



        }elseif (!$response["success"]) {
            $errorMessage = $response["error"]["message"] ?? "Database Response Error!";
            return response()->json(["status" => "error", "message" => $errorMessage]);

        }else{
            return response()->json(["status" => "error", "message" => "An Error Occured!"]);

        }

    }
    public function createAdminLogs(Request $request){

        $log = new Adminlog();
        $log->userID = Auth::guard('admin')->user()->id;
        $log->page = $request->page;
        $log->url = $request->url;
        $log->action = $request->action;
        $log->details = 'Booking id:'.$request->booking_id.', Mail to:'.$request->mail_to.', Mail Title:'.$request->mail_title.', Mail Message:'.$request->mail_message;
        $log->tableName = $request->tablename;
        $log->result = $request->result;





        return response()->json(["status" => "success", "message" => "Ajax Çalışıyor","check"=> $log->save()]);
    }

    public function createMailLogs(Request $request){

        $mail = new Mails();
        $data = [];
        array_push($data, [
            'subject' => $request->mail_title,
            'message' => $request->mail_message,
            'action' => $request->action,
        ]);
        $mail->to = $request->mail_to;
        $mail->bookingID = $request->booking_id;
        $mail->status = $request->status;
        $mail->data = json_encode($data);
        $mail->blade = $request->blade;
        if ($mail->save()) return response()->json(["status" => "success", "message" => "Ajax Çalışıyor"]);

        return response()->json(["status" => "error", "message" => "Mail Logs Error"]);
    }
}
