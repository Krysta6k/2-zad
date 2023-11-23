<?php
header('Content-Type: text/html; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT");
header("Access-Control-Max-Age: 0");

$contentSecurityPolicy = 
  "default-src 'self';". // Default policy for loading html elements
  "script-src 'self' *.yandex.ru *.jsdelivr.net;". // allows js from self, jquery and google analytics.  Inline allows inline js
  "style-src 'self' 'unsafe-inline';".// allows css from self and inline allows inline css
  "object-src 'none';". // valid object embed and applet tags src
  "img-src 'self' data:;". // vaid sources for media (audio and video html tags src)
  "media-src 'self';". // vaid sources for media (audio and video html tags src)
  "frame-ancestors 'self';". //allow parent framing - this one blocks click jacking and ui redress
  "frame-src *;". // vaid sources for frames
  "font-src 'self';". 
  "connect-src *;"; // XMLHttpRequest (AJAX request), WebSocket or EventSource.
header("Content-Security-Policy:" . $contentSecurityPolicy);
header("X-Content-Security-Policy:" . $contentSecurityPolicy);
header("X-Webkit-CSP: default-src *; connect-src *; script-src 'unsafe-inline' 'unsafe-eval' *; object-src *;");
// header("X-Frame-Options: SAMEORIGIN");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
header("Strict-Transport-Security: max-age=31536000");

function csrf_token() {
  if(empty($_SESSION['token']))
    $_SESSION['token'] = bin2hex(random_bytes(35));

  return $_SESSION['token'];
}

