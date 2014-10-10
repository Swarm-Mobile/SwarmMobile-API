<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET");
header("Access-Control-Allow-Headers: X-PINGOTHER");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Max-Age: 1728000");
header("Pragma: no-cache");
header("Cache-Control: no-store; no-cache;must-revalidate; post-check=0; pre-check=0");
echo json_encode($result);
