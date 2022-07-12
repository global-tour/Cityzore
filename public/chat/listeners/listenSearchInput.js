const listenSearchInput = () => {
  searchInput.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
      searchRoom();
    }
  });
};