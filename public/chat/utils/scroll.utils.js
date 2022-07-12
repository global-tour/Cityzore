const scrollToForceBottom = (targetDom, timeout = 250) => {
  setTimeout(() => {
    targetDom.animate({scrollTop: targetDom[0].scrollHeight}, {duration: 400})
  }, timeout)
};

const scrollToTop = (targetDom, scrollTop, timeout = 250) => {
  setTimeout(() => {
    targetDom.animate({scrollTop: scrollTop}, {duration: 400})
  }, timeout)
}

const scrollToTopReplace = (targetDom, scrollTop) => {
  targetDom[0].scrollTop = scrollTop;
}
