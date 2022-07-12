const listenLobbyList = () => {
  $('#contact-list').scroll(function() {
    if($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight && rooms.length < roomPagination.total) {
      if(!roomLoading) {
        roomSearch ? searchRoom() : getRooms();
      }
    }
  })
};
