const listenSearchInput = () => {
  searchInput.addEventListener('input', (e) => {
    let text = `${e.target.value}`.trim();
    searchService.onChangeText(text);
  });
};

const listenSearchAction = () => {
  searchService.subject.subscribe(({search}) => {
    if (search !== '') {
      roomSearch = true;
      roomPagination = { page: 0, limit: 20, total: 0 };

      rooms = [];
      roomsWrapper.empty();
      searchRoom();
    } else {
      roomSearch = false;
      roomPagination = { page: 0, limit: 20, total: 0 };

      rooms = [];
      roomsWrapper.empty();
      getRooms();
    }
  });
}
