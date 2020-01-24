<?php

require __DIR__."/../config.php";
require __DIR__."/../src/autoload.php";

$initText =
"\\documentclass[30pt]{article}
\\usepackage{amsmath}
\\usepackage{amssymb}
\\usepackage{amsfonts}
\\usepackage[utf8]{inputenc}
\\thispagestyle{empty}
\\begin{document}
\\begin{align*}
\\text{Hello World!}
\\end{align*}
\\end{document}";

$st = new TeaLatex\TeaLatex($initText);
$st->save();
$st->compile();
$st->convertPng(800);
