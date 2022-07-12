const calculateDateDifference = (firstDate, secondDate) => {
  let d1 = moment(firstDate);
  let d2 = moment(secondDate);

  let difference = {
    day: 0,
    month: 0,
    year: 0,
  }

  difference.day = d1.diff(d2, 'day')
  difference.month = d1.diff(d2, 'month')
  difference.year = d1.diff(d2, 'year')

  return difference;
}

const findDateDifferences = (currentDate, previousDate) => {
  let current = currentDate && moment(currentDate);

  if (!previousDate) {
    return current;
  }

  else if (currentDate) {
    let difference = calculateDateDifference(currentDate, previousDate)
    let {day, month, year} = difference;

    if (day > 0 || month > 0 || year > 0) {
      return current;
    } else {
      return null;
    }
  }

  else {
    return null;
  }
}

const calculateTimeOrDateTime = (current, other) => {
  let mCurrent = moment(current)
  let mOther = moment(other)

  if(mCurrent.month() !== mOther.month() || mCurrent.year() !== mOther.year() || mCurrent.date() !== mOther.date()) {
    return mOther.format('HH:mm DD-MM-YYYY')
  } else {
    return mOther.format('HH:mm');
  }
}

