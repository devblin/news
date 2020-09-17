<?php
$variables = [
    "API_KEY" => "YOUR NEWS.API.ORG API",
    "BASE_URL" => "YOUR_BASE_URL", #Example: http://localhost
    "FOLDER" => "PROJECT_FOLDER_NAME" #Example: /News
];
foreach ($variables as $key => $val) {
    putenv("$key=$val");
}