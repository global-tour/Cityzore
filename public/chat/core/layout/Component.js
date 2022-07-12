class Component {
  constructor({ initialize, component }) {
    this.initialize = initialize;
    this.component = component;
  }

  build() {
    setTimeout(() => {
      this.initialize();
    }, 200);

    return this.component();
  }
}
