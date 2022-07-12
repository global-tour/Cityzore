const areYouSure = (action, title = 'Are you sure?', ) => {
  let answer = confirm(title);

  if (action && answer) {
    action();
  }

  return answer;
}
