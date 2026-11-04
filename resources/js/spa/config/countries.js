// Countries as ISO code => German name (resources/data/countries.json, also read
// by the server). addresses.country and companies.country store the code.
import names from '../../../data/countries.json';

export const countries = Object.entries(names).map(([code, name]) => ({ value: code, label: name }));
