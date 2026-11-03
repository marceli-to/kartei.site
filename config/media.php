<?php

return [

  /*
  |--------------------------------------------------------------------------
  | Image uploads
  |--------------------------------------------------------------------------
  |
  | max_upload_mb: maximum size per image file. PHP's upload_max_filesize
  | and post_max_size must be at least as large (see public/.user.ini).
  | Keep in sync with MAX_UPLOAD_MB in resources/js/spa/config/upload.js.
  |
  | max_dimension: images wider or taller than this (px) are rejected,
  | because decoding them for resizing needs too much memory.
  |
  | resize_memory_limit: PHP memory limit while an image is resized.
  |
  */

  'max_upload_mb' => (int) env('MEDIA_MAX_UPLOAD_MB', 32),

  'max_dimension' => (int) env('MEDIA_MAX_DIMENSION', 12000),

  'resize_memory_limit' => env('MEDIA_RESIZE_MEMORY_LIMIT', '1024M'),

];
