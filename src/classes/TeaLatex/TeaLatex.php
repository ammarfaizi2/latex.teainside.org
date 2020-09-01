<?php

namespace TeaLatex;

defined("TEALATEX_DIR") or die("TEALATEX_DIR is not defined");

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @version 0.0.1
 * @package TeaLatex
 */
final class TeaLatex
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
     * @var string
     */
    private $texFile;

    /**
     * @var string
     */
    private $auxFile;

    /**
     * @var bool
     */
    private $useIsolate;

    /**
     * @var string
     */
    private $isolateCmd;

    /**
     * @var string
     */
    private $latexIsolateDir;

    /**
     * Constructor.
     *
     * @param string $content
     * @param bool   $useIsolate
     */
    public function __construct(string $content, bool $useIsolate = false)
    {
        $this->content = $content;
        $this->hash = sha1($content);

        if ($useIsolate) {
            $this->latexDir = "/var/local/lib/isolate/6969/box/latex";
            /*
               Add "invalid:x:66969:invalid" to /etc/group
               Add "invalid:x:66969:66969:Invalid,,,:/box/latex:/bin/bash" to /etc/passwd.
            */

            $this->latexIsolateDir = "/box/latex";

            $this->isolateCmd = "/usr/local/bin/isolate --box-id 6969 --cg --cg-mem=131072 --cg-timing --time=10 --wall-time=10 --extra-time=10 --mem=131072 --processes=3 --dir=/usr:maybe --dir=/etc:maybe --dir=/var:maybe --env=PATH=/bin:/usr/bin:/usr/sbin";

            if (!is_dir("/var/local/lib/isolate/6969/box")) {
                shell_exec($this->isolateCmd." --init");
            }

        } else {
            $this->latexDir = TEALATEX_DIR;
        }

        $this->useIsolate = $useIsolate;

        $this->auxFile = $this->latexDir."/tex/".$this->hash.".aux";
        $this->logFile = $this->latexDir."/tex/".$this->hash.".log";
        $this->dviFile = $this->latexDir."/tex/".$this->hash.".dvi";
        $this->texFile = $this->latexDir."/tex/".$this->hash.".tex";
        is_dir($this->latexDir) or mkdir($this->latexDir);
        is_dir($this->latexDir."/png") or mkdir($this->latexDir."/png");
        is_dir($this->latexDir."/tex") or mkdir($this->latexDir."/tex");
        is_dir($this->latexDir."/pdf") or mkdir($this->latexDir."/pdf");
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        @unlink($this->auxFile);
        @unlink($this->logFile);
    }

    /**
     * @return bool
     */
    public function save(): bool
    {
        $ret = file_exists($this->texFile);
        if (!$ret) {
            file_put_contents($this->texFile, $this->content);
            $ret = file_exists($this->texFile);
        }
        
        return $ret;
    }

    /**
     * @return string
     */
    public function getCompileLog(): string
    {
        return (string)@file_get_contents($this->logFile);
    }

    /**
     * @return bool
     */
    public function compile(): bool
    {
        $ret = file_exists($this->dviFile);

        if (!$ret) {

            if ($this->useIsolate) {
                $escapedOutDir = escapeshellarg($this->latexIsolateDir."/tex");
                $cmd =
                    $this->isolateCmd
                    ." --chdir {$escapedOutDir} --run --"
                    ." /usr/bin/env TEXMFOUTPUT={$escapedOutDir}"
                    ." ".self::LATEX_BIN
                    ." -output-directory {$escapedOutDir}"
                    ." -shell-escape"
                    ." ".escapeshellarg($this->latexIsolateDir."/tex/".basename($this->texFile))." < /dev/null";
            } else {
                $escapedOutDir = escapeshellarg($this->latexDir."/tex");
                $cmd =
                    "cd ".$escapedOutDir.";".
                    "/usr/bin/env TEXMFOUTPUT=".
                    $escapedOutDir." ".
                    self::LATEX_BIN.
                    " -output-directory ".
                    $escapedOutDir.
                    " -shell-escape ".
                    escapeshellarg($this->texFile).
                    " < /dev/null";
            }

            shell_exec($cmd);
            $ret = file_exists($this->dviFile);
        }

        if (!$ret) {
            @unlink($this->texFile);
        }
        return $ret;
    }

    /**
     * @param int $d
     * @param ?string $border
     * @return ?string
     */
    public function convertPdf(): ?string
    {
        $ret = file_exists($this->latexDir."/pdf/".$this->hash.".pdf");
        if (!$ret) {

            if ($this->useIsolate) {
                $escapedOutDir = escapeshellarg($this->latexIsolateDir."/tex");
                $cmd =
                    $this->isolateCmd
                    ." --chdir {$escapedOutDir} --run --"
                    ." /usr/bin/env TEXMFOUTPUT={$escapedOutDir} "
                    ." ".self::PDFLATEX_BIN
                    ." -output-directory {$escapedOutDir} "
                    ." -shell-escape"
                    ." ".escapeshellarg($this->latexIsolateDir."/tex/".basename($this->texFile))." < /dev/null";
            } else {
                $escapedOutDir = escapeshellarg($this->latexDir."/tex");
                $cmd =
                    "cd ".$escapedOutDir.";".
                    "/usr/bin/env TEXMFOUTPUT=".
                    $escapedOutDir." ".
                    self::PDFLATEX_BIN.
                    " -output-directory ".
                    $escapedOutDir.
                    " -shell-escape ".
                    escapeshellarg($this->texFile).
                    " < /dev/null";
            }

            shell_exec($cmd);

            
            if (file_exists($this->latexDir."/tex/".$this->hash.".pdf")) {
                rename(
                    $this->latexDir."/tex/".$this->hash.".pdf",
                    $this->latexDir."/pdf/".$this->hash.".pdf");
            }
            $ret = file_exists($this->latexDir."/pdf/".$this->hash.".pdf");
        }

        if (!$ret) {
            @unlink($this->texFile);
            return null;
        }
        return $this->hash;
    }

    /**
     * @param int $d
     * @param ?string $border
     * @return ?string
     */
    public function convertPng(int $d = 400, ?string $border = null, ?string $bColor = "white"): ?string
    {
        $pngHash = sha1($this->hash.$d.$border.$bColor);
        $pngFile = $this->latexDir."/png/".$pngHash.".png";

        shell_exec(
            self::DVIPNG_BIN."  -q -T tight -D ".$d." ".
            escapeshellarg($this->dviFile).
            " -o ".escapeshellarg($pngFile).
            " 2>&1");

        if (!file_exists($pngFile)) {
            return null;
        }

        if (is_string($border)) {
            shell_exec(
                self::CONVERT_BIN." ".$pngFile." ".
                "-fuzz 10% -trim +repage -bordercolor ".escapeshellarg($bColor)." -border ".
                escapeshellarg($border)." ".
                $pngFile
            );
        }

        return $pngHash;
    }

    /**
     * @param int $d
     * @param ?string $border
     * @return ?string
     */
    public function convertPngNoOp(int $d = 400, ?string $border = null, ?string $bColor = "white"): ?string
    {
        $pngHash = sha1($this->hash.$d.$border.$bColor."_no_optimization");
        $pngFile = $this->latexDir."/png/".$pngHash.".png";
        $pdfHash = $this->convertPdf();

        if (!$pdfHash) {
            return null;
        }

        $pdfFile = $this->latexDir."/pdf/".$pdfHash.".pdf";
        shell_exec(
            self::CONVERT_BIN.
            " -trim -density {$d} ".escapeshellarg($pdfFile).
            " -fuzz 10%".
            " +repage".
            " -bordercolor ".escapeshellarg($bColor).
            " -border ".escapeshellarg($border).
            " -quality 100 ".
            " +profile \"*\" ".escapeshellarg($pngFile)
        );

        if (!file_exists($pngFile)) {
            return null;
        }

        return $pngHash;
    }
}

