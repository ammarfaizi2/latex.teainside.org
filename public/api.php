<?php

require __DIR__."/../config.php";
require __DIR__."/../src/autoload.php";

header("Content-Type: application/json");

$log = [];
$status = "error";
$res = "no_action";
if (isset($_GET["action"])) {
    switch ($_GET["action"]) {
        case "tex2png":
            $json = json_decode(file_get_contents("php://input"), true);
            if (!(isset($json["content"]) && is_string($json["content"]))) {
                $res = "\"content\" string parameter required";
                goto ret;
            }
            if (isset($json["d"])) {
                if ((!is_int($json["d"])) || ($json["d"] < 10)) {
                    $res = "\"d\" parameter must be a positive integer more than 10";
                    goto ret;
                }
            } else {
                $json["d"] = 450;
            }
            if (isset($json["border"])) {
                if (!is_string($json["border"])) {
                    $res = "\"border\" parameter must be a string\"";
                    goto ret;
                }
            } else {
                $json["border"] = null;
            }
    
            $st = new \TeaLatex\TeaLatex($json["content"]);
            if (!$st->save()) {
                $res = "Error when saving tex file!";
                goto ret;
            }
            if (!$st->compile()) {
                $res = "Error when compiling tex file";
                $log = $st->getCompileLog();
                goto ret;
            }
            if (!($res = $st->convertPng($json["d"], $json["border"]))) {
                $res = "Error when converting to png!";
                goto ret;
            }
            $status = "success";
            break;
        
        default:
        break;
    }
}

ret:

if (isset($st)) {
    unset($st);
}

echo json_encode(
    [
        "status" => $status,
        "res" => $res,
        "log" => $log
    ]
);
