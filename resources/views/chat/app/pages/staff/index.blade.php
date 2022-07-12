<!DOCTYPE html>
<html lang="en">
<meta charset="UTF-8">
<title>Staff Socket</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6"
      crossorigin="anonymous">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css"
      integrity="sha512-3pIirOrwegjM6erE5gPSwkUzO+3cTjpnV9lexlNZqvupR64iZBnOOTiiLPb9M36zpMScbmUNIcHUqKD47M719g=="
      crossorigin="anonymous" />
<link href="{{asset('chat')}}/styles/main.css" rel="stylesheet">
<link href="{{asset('chat')}}/styles/emojis.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
      integrity="sha512-iBBXm8fW90+nuLcSKlbmrPcLa0OT92xO1BIsZ+ywDWZCvqsWgccV3gFoRBv0z+8dLJgyAHIhR35VZc2oM/gI1w=="
      crossorigin="anonymous"
/>
<body>

                <input type="hidden" name="defaultUserID" value="{{auth()->guard("admin")->user()->id ?? '0'}}">
                <input type="hidden" name="_token" value="{{csrf_token()}}">


                @if(auth()->guard("admin")->user()->chats()->count())
                @php
                    $data = auth()->guard("admin")->user()->chats()->first();



                @endphp
                <input type="hidden" name="defaultUserName" value="{{json_decode($data->response_data, true)["email"]}}">
                <input type="hidden" name="defaultPassword" value="{{$data->chat_password}}">


                @else
                 @php
                    $data = [];



                @endphp
                <input type="hidden" name="defaultUserName" value="">
                <input type="hidden" name="defaultPassword" value="">

                @endif






<div id="app" class="container-fluid" data-response="{{$data->response_data ?? ""}}" data-status="{{$data->status ?? ""}}"></div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-JEW9xMcG8R+pH31jmWH6WWP0WintQrMb4s7ZOdauHnUtxwoG2vI5DkLtS3qm9Ekf"
        crossorigin="anonymous"></script>
<script src="https://twemoji.maxcdn.com/v/13.0.2/twemoji.min.js"
        integrity="sha384-wyB/MspSJ/r2bT2kCj44qtsYRYlpzO2oAPhRj5myrWD63dt6qWv4x8AZe7Fl3K3b"
        crossorigin="anonymous"></script>
<script src="{{asset('chat')}}/scripts/DisMojiPicker.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"
        integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw=="
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/3.1.3/socket.io.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/rxjs/6.6.7/rxjs.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.21.1/axios.min.js"
        integrity="sha512-bZS47S7sPOxkjU/4Bt0zrhEtWx0y0CRkhEp8IckzK+ltifIIE9EMIMTuT/mEzoIMewUINruDBIR/jJnbguonqQ=="
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qs/6.9.4/qs.min.js"
        integrity="sha512-BHtomM5XDcUy7tDNcrcX1Eh0RogdWiMdXl3wJcKB3PFekXb3l5aDzymaTher61u6vEZySnoC/SAj2Y/p918Y3w=="
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"
        integrity="sha512-qTXRIMyZIFb8iQcfjXWCO8+M5Tbc38Qi5WzdPOYZHIlZpzBHG3L3by84BBBOiRGiEb7KKtAOAs5qYdUiZiQNNQ=="
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/compressorjs/1.0.7/compressor.min.js"></script>
</body>


<!--Core-->
<script src="{{asset('chat')}}/core/extensions/toPromiseArray.js"></script>
<script src="{{asset('chat')}}/core/extensions/toPromiseArrayApi.js"></script>
<script src="{{asset('chat')}}/core/base.api.js"></script>
<script src="{{asset('chat')}}/core/url.js"></script>

<!--Utils-->
<script src="{{asset('chat')}}/utils/scroll.utils.js"></script>
<script src="{{asset('chat')}}/utils/date.utils.js"></script>
<script src="{{asset('chat')}}/utils/alert.utils.js"></script>

<!--Services-->
<script src="{{asset('chat')}}/services/SocketService.js"></script>
<script src="{{asset('chat')}}/services/MessageService.js"></script>
<script src="{{asset('chat')}}/services/SearchService.js"></script>

<!--Helper Services-->
<script src="{{asset('chat')}}/utils/socket_helpers/SocketHelper.js"></script>
<script src="{{asset('chat')}}/utils/socket_helpers/ActivitySocketHelper.js"></script>
<script src="{{asset('chat')}}/utils/socket_helpers/ChatSocketHelper.js"></script>
<script src="{{asset('chat')}}/utils/socket_helpers/LobbySocketHelper.js"></script>

<!--Api-->
<script src="{{asset('chat')}}/api/auth.api.js"></script>
<script src="{{asset('chat')}}/api/message.api.js"></script>
<script src="{{asset('chat')}}/api/room.api.js"></script>
<script src="{{asset('chat')}}/api/status.api.js"></script>

<script>
  var defaultUserName = "";
  var defaultPassword = "";
  $(document).ready(function() {
      defaultUserName =  $("input[name='defaultUserName']").val();
      defaultPassword = $("input[name='defaultPassword']").val();
  });

  /*** Singleton Services and imports ***/
  const {debounceTime, map, distinctUntilChanged} = rxjs.operators;
  const {Subject} = rxjs;

  const authApi = new AuthApi();
  const messageApi = new MessageApi();
  const roomApi = new RoomApi();
  const statusApi = new StatusApi();

  const socketService = new SocketService();
  const messageService = new MessageService();
  const searchService = new SearchService();
  /*** Singleton Services and imports ***/

  /*** Rechangeable states ***/
  let chatStatus = true;

  let theme = 'Light';
  let roomDetailIsOpen = false;
  let roomSearch = false;
  /*** Rechangeable states ***/

  /*** Global usage elements ***/
  // App
  let appWrapper;
  let appLeft;
  let appRight;

  // Lobby
  let roomsWrapper;

  // Chat
  let messagesWrapper;
  let roomDetailWrapper;
  let roomDetailMembers;
  let chatBottomWrapper;
  let attachDropWrapper;

  // Message
  let messageBox;
  const messageBoxEvent = new Event('input');
  let emojiPicker;

  // Attach
  const attachInputEvent = new Event('change');
  let attachInput;
  let attachModal;

  let searchInput;
  /*** Global usage elements ***/

  /*** Global app variables ***/
  let socket;
  let user;
  let currentRoom;

  /*** List and Pagination ***/
  let excludeRooms = [];
  let rooms = [];
  let roomLoading = false;
  let roomPagination = { page: 0, limit: 20, total: 0 };

  let messageLoading = false;
  let messagePagination = { page: 1, limit: 30, total: 0 };

  let onlineCustomers = [];
  let onlineStaffs = [];
  let activities = [];

  let staff = {
    email: defaultUserName,
    password: defaultPassword
  };

  // Message
  let emojiKeyboardActive = false;

  let message = '';
  let quoteMessage;

  let typing = false;
  let attachFile = null;

  const messageSubject = new Subject();
  const messageSubjectEndChange = new Subject();
  /*** Global app variables ***/
</script>

<!--INTERCEPTOR-->
<script src="{{asset('chat')}}/core/interceptor.js"></script>

<!--PAGES-->
<script src="{{asset('chat')}}/core/layout/Component.js"></script>

<script src="{{asset('chat')}}/pages/Login/actions.js"></script>
<script src="{{asset('chat')}}/pages/Login/index.js"></script>

<script src="{{asset('chat')}}/pages/Chat/actions.js"></script>
<script src="{{asset('chat')}}/pages/Chat/index.js"></script>

<script src="{{asset('chat')}}/pages/Lobby/actions.js"></script>
<script src="{{asset('chat')}}/pages/Lobby/index.js"></script>

<script src="{{asset('chat')}}/pages/App/actions.js"></script>
<script src="{{asset('chat')}}/pages/App/index.js"></script>

<!--Listeners-->
<script src="{{asset('chat')}}/utils/listeners/listenMessageBox.js"></script>
<script src="{{asset('chat')}}/utils/listeners/listenBubbleDoubleClick.js"></script>
<script src="{{asset('chat')}}/utils/listeners/listenAttachInput.js"></script>
<script src="{{asset('chat')}}/utils/listeners/listenSearchInput.js"></script>
<script src="{{asset('chat')}}/utils/listeners/listenLobbyList.js"></script>
<script src="{{asset('chat')}}/utils/listeners/listenMessageList.js"></script>
<script src="{{asset('chat')}}/utils/listeners/listenAttachDrop.js"></script>

<!--Components-->
<script src="{{asset('chat')}}/components/ChatHeader.js"></script>
<script src="{{asset('chat')}}/components/ChatMessageCreator.js"></script>
<script src="{{asset('chat')}}/components/ChatJoinButton.js"></script>
<script src="{{asset('chat')}}/components/ChatQuoteMessage.js"></script>
<script src="{{asset('chat')}}/components/LobbyItem.js"></script>
<script src="{{asset('chat')}}/components/MessageBubble.js"></script>
<script src="{{asset('chat')}}/components/MessageDateBubble.js"></script>
<script src="{{asset('chat')}}/components/QuoteMessage.js"></script>
<script src="{{asset('chat')}}/components/Profile.js"></script>
<script src="{{asset('chat')}}/components/Search.js"></script>
<script src="{{asset('chat')}}/components/RoomDetail.js"></script>
<script src="{{asset('chat')}}/components/MemberItem.js"></script>
<script src="{{asset('chat')}}/components/ActivityModal.js"></script>
<script src="{{asset('chat')}}/components/ActivityItem.js"></script>
<script src="{{asset('chat')}}/components/AttachModal.js"></script>
</html>
