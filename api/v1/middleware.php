<?php
require dirname(__DIR__,2).'/app/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store');
function api_json(array $data,int $status=200): never { http_response_code($status); echo json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); exit; }
function api_method(string $method): void { if(($_SERVER['REQUEST_METHOD']??'GET')!==$method) api_json(['ok'=>false,'error'=>'Method not allowed'],405); }
