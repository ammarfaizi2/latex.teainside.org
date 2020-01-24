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
     * Constructor.
     *
     * @param string $content
     */
    public function __construct(string $content)
    {
        $this->content = $content;
        $this->hash = sha1($content);
        $this->auxFile = TEALATEX_DIR."/tex/".$this->hash.".aux";
        $this->logFile = TEALATEX_DIR."/tex/".$this->hash.".log";
        $this->dviFile = TEALATEX_DIR."/tex/".$this->hash.".dvi";
        $this->texFile = TEALATEX_DIR."/tex/".$this->hash.".tex";
        is_dir(TEALATEX_DIR."/png") or mkdir(TEALATEX_DIR."/png");
        is_dir(TEALATEX_DIR."/tex") or mkdir(TEALATEX_DIR."/tex");
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        unlink($this->auxFile);
        unlink($this->logFile);
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
     * @return bool
     */
    public function compile(): bool
    {
        $ret = file_exists($this->dviFile);
        if (!$ret) {
            shell_exec(
                "cd ".escapeshellarg(TEALATEX_DIR."/tex")."; ".
                self::LATEX_BIN." ".escapeshellarg($this->texFile).
                " < /dev/null");
            $ret = file_exists($this->dviFile);
        }

        if (!$ret) {
            @unlink($this->texFile);
        }
        return $ret;
    }

    /**
     * @param int $d
     * @return ?string
     */
    public function convertPng(int $d = 400): ?string
    {
        $pngHash = sha1($this->hash.$d);
        $pngFile = TEALATEX_DIR."/png/".$pngHash.".png";

        shell_exec(
            self::DVIPNG_BIN."  -q -T tight -D ".$d." ".
            escapeshellarg($this->dviFile).
            " -o ".escapeshellarg($pngFile).
            " 2>&1");

        if (!file_exists($pngFile)) {
            return null;
        }

        return $pngHash;
    }
}
