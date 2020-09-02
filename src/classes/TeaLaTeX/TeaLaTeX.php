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
      "png" => "{$this->latexDir}/png",
      "pdf" => "{$this->latexDir}/pdf",
    ];

    if ($useIsolate) {
      /*
        Add "invalid:x:66969:invalid" to /etc/group
        Add "invalid:x:66969:66969:Invalid,,,:/box/latex:/bin/bash" to /etc/passwd.
      */
      $this->isolateDir    = "/var/local/lib/isolate/6969";
      $this->compileDir    = $this->isolateDir."/box/tex";
      $this->compileRelDir = "/box/tex";
      $this->isolateCmd    = "/usr/local/bin/isolate --box-id 6969 --cg --cg-mem=512000 --cg-timing --time=300 --wall-time=300 --extra-time=310 --mem=512000 --processes=3 --dir=/usr:maybe --dir=/etc:maybe --dir=/var:maybe --env=PATH=/bin:/usr/bin:/usr/sbin";

      if (!is_dir($this->isolateDir)) {
        shell_exec("exec {$this->isolateCmd} --init >> /dev/null 2>&1");
      }

    } else {
      $this->compileRelDir = $this->compileDir = $this->latexDir."/tex";
    }

    is_dir($this->compileDir) or mkdir($this->compileDir);
    is_dir($this->saveDir["png"]) or mkdir($this->saveDir["png"]);
    is_dir($this->saveDir["pdf"]) or mkdir($this->saveDir["pdf"]);
  }
}
