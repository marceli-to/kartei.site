<?php

/*
 * Countries as ISO 3166-1 alpha-2 code => German name, in display order
 * (neighbours first). Shared with the SPA (resources/js/spa/config/countries.js).
 * Addresses and companies store the code.
 */
return json_decode(file_get_contents(resource_path('data/countries.json')), true);
