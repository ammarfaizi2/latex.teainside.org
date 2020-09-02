<?php

namespace TeaLaTeX;

defined("TEALATEX_DIR") or die("TEALATEX_DIR is not defined");

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @version 0.0.2
 * @package TeaLaTeX
 */
class TeaLaTeX
{
  /**
   * @const string
   */
  const LATEX_BIN = "/usr/bin/latex";

  /**
   * @const string
   */
  const DVIPNG_BIN = "/usr/bin/dvipng";

  /**
   * @const string
   */
  const CONVERT_BIN = "/usr/bin/convert";

  /**
   * @const string
   */
  const PDFLATEX_BIN = "/usr/bin/pdflatex";

  /**
   * @var string
   */
  private $content;

  /**
   * @var string
   */
  private $hash;

  /**
   * @var bool
   */
  private $useIsolate;

  /**
   * @var string
   */
  private $latexDir;

  /**
   * @var array
   */
  private $saveDir;

  /**
   * @var string
   */
  private $isolateDir;

  /**
   * @var string
   */
  private $compileDir;

  /**
   * @var string
   */
  private $isolateCmd;

  /**
   * @var string
   */
  private $compileRelDir;

  /**
   * @var string
   */
  private $texFile;

  /**
   * @var string
   */
  private $dviFile;

  /**
   * @var string
   */
  private $logFile;

  /**
   * @var string
   */
  private $auxFile;

  /**
   * @var string
   */
  private $shCompileOut;

  /**
   * Constructor.
   *
   * @param string $content
   * @param bool   $useIsolate
   */
  public function __construct(string $content, bool $useIsolate = false)
  {
    $this->content     = $content;
    $this->hash        = sha1($content);
    $this->useIsolate  = $useIsolate;
    $this->latexDir    = TEALATEX_DIR;

    $this->saveDir = [
      "tex" => "{$this->latexDir}/tex",
      "png" => "{$this->latexDir}/png",
      "pdf" => "{$this->latexDir}/pdf",
    ];

    if ($useIsolate) {
      /*
        Add "invalid:x:66969:invalid" to /etc/group
        Add "invalid:x:66969:66969:Invalid,,,:/box/latex:/bin/bash" to /etc/passwd.
      */
      $this->isolateDir    = "/var/local/lib/isolate/6969";
      $this->compileDir    = "{$this->isolateDir}/box/tex";
      $this->compileRelDir = "/box/tex";
      $this->isolateCmd    = "/usr/local/bin/isolate --box-id 6969 --cg --cg-mem=131072 --cg-timing --time=3 --wall-time=3 --extra-time=5 --mem=131072 --processes=3 --dir=/usr:maybe --dir=/etc:maybe --dir=/var:maybe --env=PATH=/bin:/usr/bin:/usr/sbin";

      if (!is_dir($this->isolateDir)) {
        shell_exec("exec {$this->isolateCmd} --init >> /dev/null 2>&1");
      }

    } else {
      $this->compileRelDir = $this->compileDir = $this->latexDir."/tex";
    }

    is_dir($this->compileDir) or mkdir($this->compileDir);
    is_dir($this->saveDir["tex"]) or mkdir($this->saveDir["tex"]);
    is_dir($this->saveDir["png"]) or mkdir($this->saveDir["png"]);
    is_dir($this->saveDir["pdf"]) or mkdir($this->saveDir["pdf"]);
    $this->texFile = "{$this->compileDir}/{$this->hash}.tex";
    $this->dviFile = "{$this->compileDir}/{$this->hash}.dvi";
    $this->logFile = "{$this->compileDir}/{$this->hash}.log";
    $this->auxFile = "{$this->compileDir}/{$this->hash}.aux";
  }

  /**
   * Destructor.
   */
  public function __destruct()
  {
    if ($this->useIsolate && file_exists($this->file)) {
      
    }
  }

  /**
   * @return bool
   */
  public function putTexFile(): bool
  {    
    if (!$ret = file_exists($this->texFile)) {
      file_put_contents($this->texFile, $this->content);
      return file_exists($this->texFile);
    }
    return true;
  }

  /**
   * @return bool
   */
  public function latexCompile(): bool
  {
    if (file_exists($this->dviFile)) {
      return true;
    }

    $compileDir = escapeshellarg($this->compileRelDir);

    $cmd =
      "/usr/bin/env TEXMFOUTPUT={$compileDir} "
      .self::LATEX_BIN." -output-directory {$compileDir}"
      ." -shell-escape ".escapeshellarg($this->texFile)." < /dev/null";

    if ($this->useIsolate) {
      $cmd = "{$this->isolateCmd} --chdir {$compileDir} --run -- ".$cmd;
    }

    $this->shCompileOut = shell_exec($cmd." 2>&1");

    return file_exists($this->dviFile);
  }

  /**
   * @return string
   */
  public function getCompileLog(): string
  {
    return (string) (
      file_exists($this->logFile)
      ? file_get_contents($this->logFile)
      : $this->shCompileOut
    );
  }
}
