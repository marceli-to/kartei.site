// Numbering of categories and registers in the archive structure.
//
// index is zero-based; type is the numeral type stored with the category
// ('decimal', 'alpha', 'roman'). Categories get a trailing dot and upper case
// letters (1. / A. / I.), registers none and lower case letters (1 / a / I).
export function formatNumber(index, type = 'decimal', isCategory = false) {
  switch (type) {
    case 'alpha': {
      const letters = toLetters(index);
      return isCategory ? `${letters}.` : letters.toLowerCase();
    }
    case 'roman':
      return isCategory ? `${toRoman(index + 1)}.` : toRoman(index + 1);
    default:
      return isCategory ? `${index + 1}.` : `${index + 1}`;
  }
}

// 0 -> A, 25 -> Z, 26 -> AA, 27 -> AB, ... (spreadsheet columns)
export function toLetters(index) {
  let letters = '';
  for (let n = index + 1; n > 0; n = Math.floor((n - 1) / 26)) {
    letters = String.fromCharCode(65 + ((n - 1) % 26)) + letters;
  }
  return letters;
}

export function toRoman(num) {
  const romans = [
    ['M', 1000], ['CM', 900], ['D', 500], ['CD', 400],
    ['C', 100], ['XC', 90], ['L', 50], ['XL', 40],
    ['X', 10], ['IX', 9], ['V', 5], ['IV', 4], ['I', 1]
  ];
  return romans.reduce((acc, [letter, value]) => {
    while (num >= value) {
      acc += letter;
      num -= value;
    }
    return acc;
  }, '');
}
