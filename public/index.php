<?php

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


?><!DOCTYPE html>
<html>
<head>
    <title>TeaLaTeX</title>
    <style type="text/css">
        html {
            background-color: #000;
        }
        button {
        	cursor: pointer;
        }
    </style>
</head>
<body>
<center>
    <div>
    	<h3>TeaLaTeX</h3>
        <form method="post" action="javascript:void(0);">
        	<div>
            <textarea required style="width:804px;height:400px;"><?php echo htmlspecialchars($initText, ENT_QUOTES, "UTF-8"); ?></textarea>
            </div>
            <div>
            	<button type="submit">Compile</button>
            </div>
        </form>
    </div>
    <script type="text/javascript">
    	
    </script>
</center>
</body>
</html>