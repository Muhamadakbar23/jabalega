<?php
// React owns the interface; PHP remains the API runtime.
$frontend = __DIR__ . '/public/index.html';
if (!file_exists($frontend)) {
    http_response_code(503);
    echo 'Frontend belum dibuild. Jalankan npm install && npm run build.';
    exit;
}

readfile($frontend);
