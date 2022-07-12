class SearchService {
  search = '';
  subject = new Subject().pipe(
    debounceTime(1000),
    distinctUntilChanged(),
  );

  get state() {
    return {
      search: this.search,
    };
  }

  onChangeText(text) {
    this.search = text;
    this.subject.next(this.state);
  }
}
