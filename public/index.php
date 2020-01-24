<?php

$initText =
"\\documentclass[30pt]{article}
\\usepackage{amsmath}
\\usepackage{amssymb}
\\usepackage{amsfonts}
\\usepackage{cancel}
\\usepackage[utf8]{inputenc}
\\thispagestyle{empty}
\\begin{document}
\\begin{align*}
 & \\textbf{Solve } I = \\int e^{x} \\sin(x)\\;dx \\\\
 & \\textbf{By using integration by part} \\\\
 & \\boxed{\\int udv = uv - \\int vdu} \\\\
 & \\textbf{Let } u = e^{x} \\rightarrow \\frac{du}{dx} = e^{x} \\rightarrow du = e^{x}\\;dx \\\\
 & \\textbf{Let } dv = \\sin(x)\\;dx \\rightarrow \\frac{dv}{dx} = \\sin(x) \\rightarrow v = -\\cos(x)\\\\
 & \\int e^{x} \\sin(x)\\;dx = e^{x} \\cdot \\left(-\\cos(x)\\right) - \\int -\\cos(x)\\;e^{x}\\;dx \\\\
 & \\int e^{x} \\sin(x)\\;dx = -e^{x} \\cos(x) + \\int \\cos(x)\\;e^{x}\\;dx \\\\
 & \\textbf{Solve this first: } \\int \\cos(x)\\;e^{x}\\;dx \\\\
 & \\textbf{By using integration by part again} \\\\
 & \\textbf{Let } t = e^{x} \\rightarrow \\frac{dt}{dx} = e^{x} \\rightarrow dt = e^{x}\\;dx \\\\
 & \\textbf{Let } dw = \\cos(x)\\;dx \\rightarrow \\frac{dw}{dx} = \\cos(x) \\rightarrow w = \\sin(x) \\\\
 & \\boxed{\\int tdw = tw - \\int wdt} \\\\
 & \\int \\cos(x)\\;e^{x}\\;dx = e^{x} \\sin(x) - \\int \\sin(x) e^{x}\\;dx \\\\
 & \\textbf{Stop here, don't use integration by part again,} \\\\ & \\textbf{if you do it will never end!} \\\\
 & \\textbf{Plug back the result of } \\int \\cos(x)\\;e^{x}\\;dx \\\\
 & \\int e^{x} \\sin(x)\\;dx = -e^{x} \\cos(x) + e^{x} \\sin(x) - \\int \\sin(x) e^{x}\\;dx \\\\
 & \\textbf{Move } \\left[\\int \\sin(x) e^{x}\\;dx\\right] \\textbf{ to the left side} \\\\
 & \\int \\sin(x) e^{x}\\;dx + \\int e^{x} \\sin(x)\\;dx = -e^{x} \\cos(x) + e^{x} \\sin(x) \\\\
 & 2 \\int e^{x} \\sin(x)\\;dx = -e^{x} \\cos(x) + e^{x} \\sin(x) \\\\
 & \\int e^{x} \\sin(x)\\;dx = \\frac{e^{x}}{2} \\left(\\sin(x) - \\cos(x)\\right) + C \\\\
 & \\textbf{by @ammarfaizi2}
\\end{align*}
\\end{document}";


?><!DOCTYPE html>
<html>
<head>
    <title>TeaLaTeX</title>
    <style type="text/css">
        html {
            font-family: Arial;
            background-color: #000;
        }
        button {
            cursor: pointer;
        }
        #input_box, #result_box {
            background-color: #fff;
        }
        #input_box {
            padding: 20px;
            margin-bottom: 40px;
        }
        #result_box {
            padding-top: 10px;
            padding-bottom: 50px;
            margin-bottom: 100px;
        }
        #rimg {
            border: 1px solid #000;
        }
        #input_opt {
            width:804px;
            border: 1px solid #000;
            align-self: left;
            align-items: left;
            flex-wrap: wrap;
        }
    </style>
</head>
<body>
<center>
    <div id="input_box">
        <h3>TeaLaTeX</h3>
        <form method="post" id="mform" action="javascript:void(0);">
            <div id="input_opt">
                Auto Scroll: <input type="checkbox" id="auto_scroll"/>
                D: <input type="number" name="density" id="density" value="250"/>
                Border: <input type="text" name="border" id="border" value="50x20"/>
            </div>
            <div>
                <textarea id="content" required style="width:804px;height:200px;"><?php echo htmlspecialchars($initText, ENT_QUOTES, "UTF-8"); ?></textarea>
            </div>
            <div>
                <button id="compile_btn" type="submit">Compile</button>
            </div>
        </form>
    </div>
    <div id="result_box">
        <h1 id="compiling" style="display:none;">Compiling...</h1>

        <div id="error_log" style="display:none;">
            <h1>Error Log:</h1>
            <textarea style="width:400px;height:400px;" id="error_log_data"></textarea>
        </div>

        <div id="result" style="display:none;">
            <h1>Result:</h1>
            <a href="" id="link_rimg" target="_blank"><img id="rimg"/></a>
        </div>
    </div>
    <script type="text/javascript">

        function doc() {
            return document;
        }

        let compiling = doc().getElementById("compiling"),
            error_log = doc().getElementById("error_log"),
            error_log_data = doc().getElementById("error_log_data"),
            result = doc().getElementById("result"),
            rimg = doc().getElementById("rimg"),
            auto_scroll = doc().getElementById("auto_scroll"),
            compile_btn = doc().getElementById("compile_btn"),
            link_rimg = doc().getElementById("link_rimg");

        function tex2png(content, d = 450, border = null) {
            compile_btn.disabled = 1;
            result.style.display = error_log.style.display = "none";
            compiling.style.display = "";
            let ch = new XMLHttpRequest;
            ch.open("POST", "/api.php?action=tex2png");
            ch.onreadystatechange = function () {
                if (this.readyState === 4) {
                    compile_btn.disabled = 0;
                    compiling.style.display = "none";
                    let json = JSON.parse(this.responseText);
                    if (json.status === "error") {
                        error_log.style.display = "";
                        error_log_data.value = json.log;
                    } else if (json.status === "success") {
                        result.style.display = "";
                        rimg.src = "/latex/png/"+json.res+".png";
                        link_rimg.href = "/latex/png/"+json.res+".png";
                    }
                    if (auto_scroll.checked) {
                        window.scrollTo(0,document.body.scrollHeight);
                    }
                }
            };
            ch.send(JSON.stringify({content: content, d: d, border: border}));
        }

        doc().getElementById("mform").addEventListener("submit", function () {
            tex2png(
                doc().getElementById("content").value,
                parseInt(doc().getElementById("density").value),
                doc().getElementById("border").value,
            );
        });
    </script>
</center>
</body>
</html>
