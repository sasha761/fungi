import 'intl-tel-input/build/css/intlTelInput.css';
import intlTelInput from 'intl-tel-input';
// import utils from 'intl-tel-input/build/js/utils';

export default async function initPhone(phoneSelector) {

  const input = document.querySelector(phoneSelector);
  if (!input) return;

  const iti = intlTelInput(input, {
    initialCountry: "auto",
    strictMode: true,
    // nationalMode: true,
    hiddenInput: () => ({ phone: "full_phone", country: "country_code" }),
    onlyCountries: [
      "al", "ad", "at", "by", "be", "bg", "hr", "cz", "dk", "ee", "fi", "fr",
      "de", "gi", "gr", "va", "hu", "is", "ie", "it", "lv", "li", "lt", "lu", "mk",
      "mt", "md", "mc", "me", "nl", "no", "pl", "pt", "ro", "sm", "rs", "sk", "si",
      "es", "se", "ch", "ua", "gb", "ge", "az", "kz", "tr", "us", "ca", "br", 
      "za", "eg", "ng", "ke", "gh", "ma", "dz", "tn"
    ],
    preferredCountries: ['ua', 'us', 'ca', 'gb', 'pl', 'de', 'no'],
    geoIpLookup: callback => {
      fetch("https://ipapi.co/json")
        .then(res => res.json())
        .then(data => callback(data.country_code))
        .catch(() => callback("us"));
    },
    loadUtils: () => import('intl-tel-input/build/js/utils.js'),
    // utilsScript: utils
  });

  return iti;
}
