<?php
$variables = [
    "API_KEY" => "YOUR NEWS.API.ORG API"
];
foreach ($variables as $key => $val) {
    putenv("$key=$val");
}