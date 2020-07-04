<?php

$initText =
"\\documentclass[12pt]{article}
\\usepackage{amsmath}
\\usepackage{amssymb}
\\usepackage{amsfonts}
\\usepackage{cancel}
\\usepackage{color}
\\usepackage{xcolor}
\\definecolor{my_custom_green}{HTML}{1f8012}
\\usepackage[utf8]{inputenc}
\\thispagestyle{empty}
\\begin{document}
\\begin{align*}
 & \\text{Let } y = x^{x} \\text{, find } \\frac{dy}{dx} \\\\
 & {\\color{my_custom_green} \\text{By using natural logarithm}} \\\\
 & \\ln(y) = \\ln\\left(x^{x}\\right) \\\\
 & \\ln(y) = x\\ln(x) \\\\
 & {\\color{blue} \\text{By using implicit differentiation}} \\\\
 & \\frac{d}{dx} \\ln(y) = \\frac{d}{dx} x\\ln(x) \\\\
 & \\frac{1}{y}\\frac{dy}{dx} = 1\\cdot\\ln(x) + \\cancel{x}\\frac{1}{\\cancel{x}} \\\\
 & \\frac{dy}{dx} = y(\\ln(x) + 1) \\\\
 & {\\color{red} \\text{Plug back } y \\text{ to the equation}} \\\\
 & \\boxed{y = x^{x}} \\\\
 & \\therefore \\frac{dy}{dx} = x^{x}(\\ln(x) + 1) \\\\
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
        #png_settings {
            width:804px;
            padding: 5px 0px 5px 0px;
            border: 1px solid #000;
            align-self: left;
            align-items: left;
            flex-wrap: wrap;
        }
        #list_file_panel table th,
        #list_file_panel table td {
            padding: 10px 15px 10px 15px;
            text-align: center;
        }
        #close_list_file_btn {
            margin-bottom: 10px;
        }
        #file_panel {
            width:804px;
            padding: 5px 0px 5px 0px;
            margin-bottom: 2px;
            border: 1px solid #000;
        }
        #unsaved_hint {
            color: red;
        }
        .file_panel_buttons, .file_panel_status {
            padding: 5px 10px 5px 10px;
            text-align: left;
        }
        #saved_notif {
            color: green;
        }
    </style>
</head>
<body>
<center>
    <div id="input_box">
        <h3>TeaLaTeX</h3>
        <div id="file_panel">
            <div class="file_panel_status">
                <span>Filename: <span id="unsaved_hint">*</span><span id="opened_filename_sw">Untitled</span>&nbsp;<span id="saved_notif" style="opacity:0;">(Saved)</span></span>
            </div>
            <div class="file_panel_buttons">
                <button id="open_btn">Open</button>
                <button id="save_btn">Save</button>
                <button id="save_as_btn">Save As</button>
            </div>
        </div>
        <div id="save_file_panel" style="display:none">
            <form action="javascript:void(0);" id="save_file_form">
                <h3>Save to a new File</h3>
                <div style="margin-bottom:20px;">
                    Filename: <input type="text" id="save_filename" required/>
                </div>
                <div>
                    <button type="submit">Save</button>
                    <button id="cancel_save_btn" type="button">Cancel</button>
                </div>
            </form>
        </div>
        <div id="list_file_panel" style="display:none;">
            <button id="close_list_file_btn">Close</button>
            <h1>Saved Files</h1>
            <table id="saved_files_table" border="1" style="border-collapse:collapse;">
                <tr><th align="center">No.</th><th align="center">Filename</th><th align="center">Size</th><th align="center">Last Modified</th><th align="center">Action</th></tr>
            </table>
        </div>
        <div id="main_panel">
            <form method="post" id="mform" action="javascript:void(0);">
                <div id="png_settings">
                    <span>PNG Settings</span>
                    <div>
                        Auto Scroll: <input type="checkbox" id="auto_scroll"/>
                        D: <input type="number" size="5" name="density" id="density" value="200"/>
                        Border: <input type="text" size="5" name="border" id="border" value="50x20"/>
                        Border Color: <input type="text" size="5" name="border_color" id="border_color" value="white"/>
                    </div>
                </div>
                <div>
                    <textarea id="content" required style="width:797px;height:316px;"><?php echo htmlspecialchars($initText, ENT_QUOTES, "UTF-8"); ?></textarea>
                </div>
                <div>
                    <button id="create_png" type="button">Create PNG</button>
                    <button id="create_png_no_op" type="button">Create PNG (no optimization)</button>
                    <button id="create_pdf" type="button">Create PDF</button>
                </div>
            </form>
        </div>
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

        <div id="result_pdf" style="display:none;">
            <a id="pdf_link" href="" target="_blank" style="color:blue;"><h2>Open PDF File</h2></a>
        </div>
    </div>
<script type="text/javascript"><?php ob_start(); ?>
        function escapeHtml(text) {
          const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
          };  
          return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
        function gid(id){return document.getElementById(id);}
        const fcontent = 0, fd = 1, fborder = 2, fbcolor = 3, flast = 4;
        let compiling = gid("compiling"),
            error_log = gid("error_log"),
            error_log_data = gid("error_log_data"),
            result = gid("result"),
            rimg = gid("rimg"),
            auto_scroll = gid("auto_scroll"),
            create_png_btn = gid("create_png"),
            link_rimg = gid("link_rimg"),
            create_pdf_btn =  gid("create_pdf"),
            result_pdf = gid("result_pdf"),
            pdf_link = gid("pdf_link"),
            main_panel = gid("main_panel"),
            file_panel = gid("file_panel"),
            save_file_panel = gid("save_file_panel"),
            open_btn = gid("open_btn"),
            save_btn = gid("save_btn"),
            save_as_btn = gid("save_as_btn"),
            close_list_file_btn = gid("close_list_file_btn"),
            ls = localStorage,
            opened_filename = null,
            cancel_save_btn = gid("cancel_save_btn"),
            save_filename = gid("save_filename"),
            opened_filename_sw = gid("opened_filename_sw"),
            unsaved_hint = gid("unsaved_hint"),
            saved_files_table = gid("saved_files_table"),
            saved_state = false,
            save_file_form = gid("save_file_form"),
            create_png_no_op_btn = gid("create_png_no_op");

        function get_saved_works() {
            let saved_works;
            try {
                saved_works = JSON.parse(ls.getItem("saved_works"));
                if (!saved_works) saved_works = {};
            } catch (e) {
                saved_works = {};
            }
            return saved_works;
        }
        function resolve_saved_files_table() {
            let i, j = 0, r = "", sfr, tbd, saved_works;

            for (; j < 3; j++) {
                sfr = document.getElementsByClassName("saved_file_row");
                if (typeof sfr !== "undefined") {
                    for (i = 0; i < sfr.length; i++) {
                        sfr[i].remove();
                    }
                    tbd = saved_files_table.getElementsByTagName("tbody");
                    for (i = 0; i < tbd.length; i++) {
                        if (!tbd[i].innerHTML.trim()) {
                            tbd[i].remove();
                        }
                    }
                }
            }

            j = 0;
            saved_works = get_saved_works();
            for (i in saved_works) {
                r += '<tr class="saved_file_row">'
                 +'<td>'+(j++)+'</td>'
                 +'<td>'+escapeHtml(i)+'</td>'
                 +'<td>'+saved_works[i][fcontent].length+'</td>'
                 +'<td>'+saved_works[i][flast]+'</td>'
                 +'<td><button type="button" onclick="ropen_file(\''
                    +encodeURIComponent(i)+'\')">Open</button>&nbsp;<button type="button" onclick="rdelete_file(\''
                    +encodeURIComponent(i)+'\')">Delete</button></td>'
                 +'</tr>';
            }
            saved_files_table.innerHTML += r;
        }
        close_list_file_btn.addEventListener("click", function () {
            main_panel.style.display = "";
            file_panel.style.display = "";
            list_file_panel.style.display = "none";
        });
        open_btn.addEventListener("click", function () {
            resolve_saved_files_table();
            main_panel.style.display = "none";
            file_panel.style.display = "none";
            list_file_panel.style.display = "";
        });
        cancel_save_btn.addEventListener("click", function () {
            main_panel.style.display = "";
            file_panel.style.display = "";
            save_file_panel.style.display = "none";
        });
        save_as_btn.addEventListener("click", function () {
            main_panel.style.display = "none";
            file_panel.style.display = "none";
            save_file_panel.style.display = "";
            return;
        });
        save_btn.addEventListener("click", function () {
            if (opened_filename === null) {
                main_panel.style.display = "none";
                file_panel.style.display = "none";
                save_file_panel.style.display = "";
                return;
            }

            if (!saved_state) {
                let saved_works = get_saved_works();
                saved_works[opened_filename] = [
                    gid("content").value,
                    parseInt(gid("density").value),
                    gid("border").value,
                    gid("border_color").value,
                    (new Date()).toString()
                ];
                ls.setItem("saved_works", JSON.stringify(saved_works));
                saved_state = true;
                unsaved_hint.style.display = "none";
            }
            save_notif();
        });
        function save_notif() {
            saved_notif.style.opacity = 1;
            let itv = setInterval(function () {
                saved_notif.style.opacity -= 0.3;
                if (saved_notif.style.opacity <= 0) {
                    clearInterval(itv);
                }
            }, 100);
        }
        function ropen_file(fname) {
            if ((!saved_state) && (opened_filename_sw.innerHTML.trim() !== "Untitled")) {
                if (!confirm("You have unsaved work, do you want to continue without saving?")) {
                    return;
                }
            }
            fname = decodeURIComponent(fname);
            let saved_works = get_saved_works();
            opened_filename = fname;
            saved_state = true;
            opened_filename_sw.innerHTML = fname;
            unsaved_hint.style.display = "none";
            gid("content").value = saved_works[fname][fcontent];
            gid("density").value = saved_works[fname][fd];
            gid("border").value = saved_works[fname][fborder]
            gid("border_color").value = saved_works[fname][fbcolor];
            main_panel.style.display = "";
            file_panel.style.display = "";
            list_file_panel.style.display = "none";
        }
        function rdelete_file(fname) {
            fname = decodeURIComponent(fname);
            if (confirm("Are you sure to delete \""+fname+"\"?")) {
                let saved_works = get_saved_works();
                delete saved_works[fname];
                ls.setItem("saved_works", JSON.stringify(saved_works));
                if (fname == opened_filename) {
                    opened_filename = null;
                    saved_state = false;
                    opened_filename_sw.innerHTML = "Untitled";
                    unsaved_hint.style.display = "";
                }
                resolve_saved_files_table();
            }
        }
        gid("content").addEventListener("input", function () {
            if (saved_state) {
                saved_state = false;
                unsaved_hint.style.display = "";
            }
        });
        function apply_save_callback() {
            let saved_works = get_saved_works();
            save_filename.value = save_filename.value.trim();
            if (save_filename.value === "") {
                alert("Filename cannot be empty!");
                return;
            }
            if (typeof saved_works[save_filename.value] !== "undefined") {
                if (!confirm("File \""+save_filename.value+"\" is existing on your saved file. Do you want to replace it?")) {
                    return;
                }
            }
            saved_works[save_filename.value] = [
                gid("content").value,
                parseInt(gid("density").value),
                gid("border").value,
                gid("border_color").value,
                (new Date()).toString()
            ];
            ls.setItem("saved_works", JSON.stringify(saved_works));
            opened_filename_sw.innerHTML = opened_filename = save_filename.value;
            save_filename.value = "";
            main_panel.style.display = "";
            file_panel.style.display = "";
            unsaved_hint.style.display = save_file_panel.style.display = "none";
            saved_state = true;
            save_notif();
        };
        save_file_form.addEventListener("submit", apply_save_callback);

        function tex2pdf(content) {
            result_pdf.style.display = result.style.display = error_log.style.display = "none";
            compiling.style.display = "";
            create_png_no_op_btn.disabled = create_png_btn.disabled = create_pdf_btn.disabled = 1;
            let ch = new XMLHttpRequest;
            ch.open("POST", "api.php?action=tex2pdf");
            ch.onreadystatechange = function () {
                if (this.readyState === 4) {
                    create_png_no_op_btn.disabled = create_png_btn.disabled = create_pdf_btn.disabled = 0;
                    compiling.style.display = "none";
                    let json = JSON.parse(this.responseText);
                    if (json.status === "error") {
                        error_log.style.display = "";
                        error_log_data.value = json.log;
                    } else if (json.status === "success") {
                        result_pdf.style.display = "";
                        pdf_link.href = "latex/pdf/"+json.res+".pdf";
                    }
                    if (auto_scroll.checked) {
                        window.scrollTo(0,document.body.scrollHeight * (0.5));
                    }
                }
            };
            ch.send(JSON.stringify({content: content}));
        }

        function tex2png(content, d = 450, border = null, bcolor = "white") {
            rimg.src = "";
            create_png_no_op_btn.disabled = create_png_btn.disabled = create_pdf_btn.disabled = 1;
            result_pdf.style.display = result.style.display = error_log.style.display = "none";
            compiling.style.display = "";
            let ch = new XMLHttpRequest;
            ch.open("POST", "api.php?action=tex2png");
            ch.onreadystatechange = function () {
                if (this.readyState === 4) {
                    create_png_no_op_btn.disabled = create_png_btn.disabled = create_pdf_btn.disabled = 0;
                    compiling.style.display = "none";
                    let json = JSON.parse(this.responseText);
                    if (json.status === "error") {
                        error_log.style.display = "";
                        error_log_data.value = json.log;
                    } else if (json.status === "success") {
                        result.style.display = "";
                        rimg.src = "latex/png/"+json.res+".png";
                        link_rimg.href = "latex/png/"+json.res+".png";
                    }
                    if (auto_scroll.checked) {
                        window.scrollTo(0,document.body.scrollHeight * (0.5));
                    }
                }
            };
            ch.send(JSON.stringify({content: content, d: d, border: border, bcolor: bcolor}));
        }

        function tex2png_no_op(content, d = 450, border = null, bcolor = "white") {
            rimg.src = "";
            create_png_no_op_btn.disabled = create_png_btn.disabled = create_pdf_btn.disabled = 1;
            result_pdf.style.display = result.style.display = error_log.style.display = "none";
            compiling.style.display = "";
            let ch = new XMLHttpRequest;
            ch.open("POST", "api.php?action=tex2png_no_op");
            ch.onreadystatechange = function () {
                if (this.readyState === 4) {
                    create_png_no_op_btn.disabled = create_png_btn.disabled = create_pdf_btn.disabled = 0;
                    compiling.style.display = "none";
                    let json = JSON.parse(this.responseText);
                    if (json.status === "error") {
                        error_log.style.display = "";
                        error_log_data.value = json.log;
                    } else if (json.status === "success") {
                        result.style.display = "";
                        rimg.src = "latex/png/"+json.res+".png";
                        link_rimg.href = "latex/png/"+json.res+".png";
                    }
                    if (auto_scroll.checked) {
                        window.scrollTo(0,document.body.scrollHeight * (0.5));
                    }
                }
            };
            ch.send(JSON.stringify({content: content, d: d, border: border, bcolor: bcolor}));
        }

        create_png_btn.addEventListener("click", function () {
            tex2png(
                gid("content").value,
                parseInt(gid("density").value),
                gid("border").value,
                gid("border_color").value
            );
        });
        create_png_no_op_btn.addEventListener("click", function () {
            tex2png_no_op(
                gid("content").value,
                parseInt(gid("density").value),
                gid("border").value,
                gid("border_color").value
            );
        });
        create_pdf_btn.addEventListener("click", function () {
            tex2pdf(gid("content").value);
        });
<?php

// echo str_replace(["\n", "    "], "", ob_get_clean());
echo ob_get_clean();

?></script>
</center>
</body>
</html>
