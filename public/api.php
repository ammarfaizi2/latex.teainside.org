<?php

if (!defined(OPCACHE_PRELOAD)) {
  require_once __DIR__."/../config.php";
  require_once __DIR__."/../src/autoload.php";
}

header("Content-Type: application/json");

const CONTENT_TYPE_MAP = [
  "pdf" => "application/pdf",
  "png" => "image/png"
];

$log = [];
$status = "error";
$res = "no_action";
$useIsolate = true;

if (isset($_GET["action"])) {
  switch ($_GET["action"]) {
    case "file":
      if (!isset($_GET["hash"], $_GET["type"])) {
        exit;
      }
      if (!(is_string($_GET["hash"]) && is_string($_GET["type"]))) {
        exit;
      }
      if (!isset(CONTENT_TYPE_MAP[$_GET["type"]])) {
        exit;
      }

      $_GET["hash"] = basename($_GET["hash"]);
      $filename   = $_GET["hash"].".".$_GET["type"];
      $targetFile = __DIR__."/latex/".$_GET["type"]."/".$filename;

      if (!file_exists($targetFile)) {
        http_response_code(404);
        header("Content-Type: text/plain");
        echo "404 Not Found!";
        exit;
      }

      date_default_timezone_set("UTC");

      if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
        $ifModifiedSince = explode(";", $_SERVER['HTTP_IF_MODIFIED_SINCE'])[0];
      } else {
        $ifModifiedSince = "";
      }

      $mtime = filemtime($targetFile);
      $gmdateMod = date("D, d M Y H:i:s", $mtime)." GMT";

      if ($ifModifiedSince === $gmdateMod) {
        http_response_code(304);
        exit;
      }

      header("Expires: 31536000");
      header("Last-Modified: ".$gmdateMod);
      header("Content-Type: ".CONTENT_TYPE_MAP[$_GET["type"]]);
      header("Content-Disposition: inline; filename=\"{$filename}\"");
      header("Content-Length: ".filesize($targetFile));

      readfile($targetFile);

      exit;
      break;

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
  
      $st = new \TeaLatex\TeaLatex($json["content"], $useIsolate);
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

      $st = new \TeaLatex\TeaLatex($json["content"], $useIsolate);
      if (!$st->save()) {
        $res
         = "Error when saving tex file!";
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
