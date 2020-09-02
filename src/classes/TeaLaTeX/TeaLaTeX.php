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
   * @var bool
   */
  private $ioiInit = false;

  /**
   * @var string
   */
  private $counterFile;

  /**
   * Constructor.
   *
   * @param string $content
   * @param bool   $useIsolate
   */
  public function __construct(string $content, bool $useIsolate = false)
  {
    $this->content      = $content;
    $this->hash         = sha1($content);
    $this->useIsolate   = $useIsolate;
    $this->latexDir     = TEALATEX_DIR;

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
      $this->counterFile   = "{$this->latexDir}/ct";
      $this->isolateCmd    = "/usr/local/bin/isolate --box-id 6969 --cg --cg-mem=131072 --cg-timing --time=3 --wall-time=3 --extra-time=5 --mem=131072 --processes=3 --fsize=8192 --dir=/usr:maybe --dir=/etc:maybe --dir=/var:maybe --env=PATH=/bin:/usr/bin:/usr/sbin";

      $this->isolateInit();

    } else {
      $this->compileRelDir = $this->compileDir = $this->latexDir."/tex";
    }

    is_dir($this->latexDir) or mkdir($this->latexDir);
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
    @unlink($this->texFile);
    @unlink($this->dviFile);
    @unlink($this->logFile);
    @unlink($this->auxFile);

    if ($this->useIsolate) {
      if (file_exists($this->counterFile)) {
        $handle  = fopen($this->counterFile, "r+");
        flock($handle, LOCK_EX);
        $counter = (int)fread($handle, 10);
        rewind($handle);

        if ($counter >= 20) {
          $counter = 0;
          shell_exec("exec /usr/local/bin/isolate --box-id 6969 --cleanup");
        }

        fwrite($handle, (string)(++$counter));

      } else {
        $handle  = fopen($this->counterFile, "w");
        flock($handle, LOCK_EX);
        $counter = 1;
        fwrite($handle, "1");
      }


      flock($handle, LOCK_UN);
      fclose($handle);
    }
  }

  /**
   * @return void
   */
  private function isolateInit(): void
  {
    if ((!$this->ioiInit) && (!is_dir($this->isolateDir))) {
      $this->ioiInit = true;
      shell_exec("exec {$this->isolateCmd} --init");
    }
  }

  /**
   * @return bool
   */
  public function putTexFile(): bool
  {
    if (file_exists($this->texFile)) {
      return true;
    }

    return (bool)file_put_contents($this->texFile, $this->content);
  }

  /**
   * @return bool
   */
  public function latexCompile(): bool
  {
    if (file_exists($this->dviFile)) {
      return true;
    }

    $escCompileDir = escapeshellarg($this->compileRelDir);
    $texFileName   = "{$this->hash}.tex";
    $realTexFile   = "{$this->compileDir}/{$texFileName}";
    $texFile       = "{$this->compileRelDir}/{$texFileName}";

    $cmd =
      "/usr/bin/env TEXMFOUTPUT={$escCompileDir} "
      .self::LATEX_BIN." -output-directory {$escCompileDir}"
      ." -shell-escape ".escapeshellarg($texFile)." < /dev/null";

    if ($this->useIsolate) {
      $cmd = "{$this->isolateCmd} --chdir {$escCompileDir} --run -- {$cmd}";
    }

    $this->shCompileOut = shell_exec("exec {$cmd} 2>&1");

    $targetCompFile = "{$this->saveDir["tex"]}/{$texFileName}.gz";

    if (!file_exists($targetCompFile)) {
      file_put_contents($targetCompFile, gzencode($this->content, 9));
    }

    return file_exists($this->dviFile);
  }

  /**
   * @return string
   */
  public function getCompileLog(): string
  {
    return (string) (
      file_exists($this->logFile)
      ? @file_get_contents($this->logFile)
      : $this->shCompileOut
    );
  }

  /**
   * @param  int     $d
   * @param  ?string $border
   * @param  ?string $bColor
   * @return ?string
   */
  public function convertToPng(int $d = 400, ?string $border = null, ?string $bColor = "white"): ?string
  {
    $pngHash    = sha1($this->hash.$d.$border.$bColor);
    $targetSave = "{$this->saveDir["png"]}/{$pngHash}.png";

    if (file_exists($targetSave)) {
      return $pngHash;
    }

    @unlink($this->logFile);
    $escCompileDir = escapeshellarg($this->compileRelDir);
    $pngFileName   = "{$pngHash}.png";
    $realPngFile   = "{$this->compileDir}/{$pngFileName}";
    $pngFile       = "{$this->compileRelDir}/{$pngFileName}";

    $cmd = 
      self::DVIPNG_BIN." -q -T tight -D {$d} "
      .escapeshellarg($this->dviFile)." -o "
      .escapeshellarg($pngFile);

    if ($this->useIsolate) {
      $cmd = "{$this->isolateCmd} --chdir {$escCompileDir} --run -- {$cmd}";
    }

    $this->shCompileOut = shell_exec("exec {$cmd} 2>&1");


    if (!file_exists($realPngFile)) {
      return null;
    }

    if (!empty($border)) {
      $cmd =
        self::CONVERT_BIN." {$pngFile} "
        ."-fuzz 10% -trim +repage -bordercolor "
        .escapeshellarg($bColor)." -border "
        .escapeshellarg($border)." {$pngFile}";

      if ($this->useIsolate) {
        $cmd = "{$this->isolateCmd} --processes=4 --chdir {$escCompileDir} --run -- {$cmd}";
      }

      $this->shCompileOut = shell_exec("exec {$cmd} 2>&1");
    }

    if (!file_exists($realPngFile)) {
      return null;
    }

    rename($realPngFile, $targetSave);

    return file_exists($targetSave) ? $pngHash : null;
  }

  /**
   * @param bool $dontRename
   * @return ?string
   */
  public function convertToPdf(bool $dontRename = false): ?string
  {
    $pdfFile     = "{$this->compileRelDir}/{$this->hash}.pdf";
    $targetSave  = "{$this->saveDir["pdf"]}/{$this->hash}.pdf";
    $pdfFileName = "{$this->hash}.pdf";

    if (file_exists($targetSave)) {
      if ($dontRename) {
        @rename($targetSave, "{$this->compileDir}/{$pdfFileName}");
        return "{$this->compileDir}/{$pdfFileName}";
      } else {
        return $this->hash;
      }
    }

    @unlink($this->logFile);
    $escCompileDir = escapeshellarg($this->compileRelDir);
    $realPdfFile   = "{$this->compileDir}/{$pdfFileName}";
    $pdfFile       = "{$this->compileRelDir}/{$pdfFileName}";

    $cmd = 
      "/usr/bin/env TEXMFOUTPUT={$escCompileDir} "
      .self::PDFLATEX_BIN." -output-directory {$escCompileDir} "
      ."-shell-escape ".escapeshellarg($this->texFile)." < /dev/null";

    if ($this->useIsolate) {
      $cmd = "{$this->isolateCmd} --chdir {$escCompileDir} --run -- {$cmd}";
    }

    $this->shCompileOut = shell_exec("exec {$cmd} 2>&1");

    if (!file_exists($realPdfFile)) {
      return null;
    }

    /* Private use only, don't use outside of the class. */
    if ($dontRename) {
      return $realPdfFile;
    }

    rename($realPdfFile, $targetSave);
    return file_exists($targetSave) ? $this->hash : null;
  }

  /**
     * @param int $d
     * @param ?string $border
     * @return ?string
     */
  public function convertPngNoOp(int $d = 400, ?string $border = null, ?string $bColor = "white"): ?string
  {
    $pngHash    = sha1($this->hash.$d.$border.$bColor."no_op");
    $targetSave = "{$this->saveDir["png"]}/{$pngHash}.png";

    if (file_exists($targetSave)) {
      return $pngHash;
    }

    @unlink($this->logFile);
    $escCompileDir = escapeshellarg($this->compileRelDir);
    $pngFileName   = "{$pngHash}.png";
    $realPngFile   = "{$this->compileDir}/{$pngFileName}";
    $pngFile       = "{$this->compileRelDir}/{$pngFileName}";

    $pdfFile = $this->convertToPdf(true);
    $cmd =
      self::CONVERT_BIN
      ." -trim -density {$d} ".escapeshellarg($pdfFile)
      ." -fuzz 10%"
      ." +repage"
      .(!empty($bColor) ? " -bordercolor ".escapeshellarg($bColor) : "")
      .(!empty($border) ? " -border ".escapeshellarg($border) : "")
      ." -quality 100 "
      ." +profile \"*\" ".escapeshellarg($pngFile);

    if ($this->useIsolate) {
      $cmd = "{$this->isolateCmd} --processes=4 --chdir {$escCompileDir} --run -- {$cmd}";
    }

    $this->shCompileOut = shell_exec("exec {$cmd} 2>&1");

    if (!file_exists($realPngFile)) {
      return null;
    }

    rename($realPngFile, $targetSave);
    rename($pdfFile, "{$this->saveDir["pdf"]}/{$this->hash}.pdf");

    return file_exists($targetSave) ? $pngHash : null;
  }
}
