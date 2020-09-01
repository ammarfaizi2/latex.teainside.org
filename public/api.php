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
        case "tex2png_no_op":
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

            if (isset($json["bcolor"])) {
                if (!is_string($json["bcolor"])) {
                    $res = "\"bcolor\" parameter must be a string\"";
                    goto ret;
                }
            } else {
                $json["bcolor"] = "white";
            }
    
            $st = new \TeaLatex\TeaLatex($json["content"], true);
            if (!$st->save()) {
                $res = "Error when saving tex file!";
                goto ret;
            }
            if (!$st->compile()) {
                $res = "Error when compiling tex file";
                $log = $st->getCompileLog();
                goto ret;
            }
            if ($_GET["action"] === "tex2png_no_op") {
                if (!($res = $st->convertPngNoOp($json["d"], $json["border"], $json["bcolor"]))) {
                    $res = "Error when converting to png!";
                    goto ret;
                }
            } else {
                if (!($res = $st->convertPng($json["d"], $json["border"], $json["bcolor"]))) {
                    $res = "Error when converting to png!";
                    goto ret;
                }
            }
            $status = "success";
            break;
        
        case "tex2pdf":
            $json = json_decode(file_get_contents("php://input"), true);
            if (!(isset($json["content"]) && is_string($json["content"]))) {
                $res = "\"content\" string parameter required";
                goto ret;
            }

            $st = new \TeaLatex\TeaLatex($json["content"]);
            if (!$st->save()) {
                $res = "Error when saving tex file!";
                goto ret;
            }
            if (!($res = $st->convertPdf())) {
                $res = "Error when compiling tex file";
                $log = $st->getCompileLog();
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
