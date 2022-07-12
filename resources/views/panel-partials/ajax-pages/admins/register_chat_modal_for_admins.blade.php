    
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel" style="display: inline-block; font-size: 18px"><i class="icon-cz-copy"></i> Register Chat Account Modal</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="font-size: 28px!important">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

             <form action="#" id="register-chat-account-form">
                <input type="hidden" name="user_id" value="{{$admin->id}}">
                
                
                <input type="hidden" name="action" value="after_create_admin_form_clicked_submit_button">
                @if($admin->chats()->count())
                <input type="hidden" name="type" value="update">
                <input type="hidden" name="endpoint" value="https://global-tickets-socket-service.herokuapp.com/api/staff">
                @else
                <input type="hidden" name="type" value="create">
                <input type="hidden" name="endpoint" value="https://global-tickets-socket-service.herokuapp.com/authentication/register/staff">
                @endif
                 
                 <div class="form-group">
                     <label for="">User Name</label>
                     <input type="text" name="chat_user_name" value="{{$admin->name}}">
                 </div>

                  <div class="form-group">
                     <label for="">User Name</label>
                     <input type="text" name="chat_user_surname" value="{{$admin->surname}}">
                 </div>
                  <div class="form-group">
                     <label for="">User Name</label>
                     <input type="text" name="email" value="{{$admin->email}}">
                 </div>

                 <div class="form-group">
                     <label for="">Password</label>
                     <input type="text" name="chat_password" value="">
                 </div>



              

                 <div class="form-group">
                    @if($admin->chats()->count())
                     <button class="btn btn-success btn-block active" id="create-chat-account-form-submit-button">
                         Update
                     </button>
                     @else

                     <button class="btn btn-success btn-block active" id="create-chat-account-form-submit-button">
                         Create
                     </button>

                     @endif
                 </div>
             </form>
               
                    
                 
                </div>

                 

                  


                
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    
                </div>
            