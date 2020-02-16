<!DOCTYPE html>
<html>
<head>
    <title></title>
</head>
<body>
    <center>
        <div>
            <form>
                <div style="border: 1px solid #000; width: 400px; height:200px;">
                    <h2>Temperature Converter</h2>
                    <table>
                        <tr><td>From: </td><td><input type="number" step="0.1" id="from_input"/></td><td><select id="from_unit"></select></td></tr>
                        <tr><td>Result: </td><td><input type="number" id="target_result" readonly/></td><td><select id="target_unit"></select></td></tr>
                    </table>
                    <h4>by <a href="https://t.me/ammarfaizi2">@ammarfaizi2</a></h4>
                </div>
            </form>
        </div>
    </center>
    <script type="text/javascript">
        function doc() {
            return document;
        }
        let x, y, units = {
            "":"",
            "Kelvin": [273.15, 373.1339],
            "Celcius": [0.00, 99.9839],
            "Fahrenheit": [32.00, 211.97102],
            "Rankine": [491.67, 671.64102],
            "Desile": [150.00, 0.00],
            "Newton": [0.00, 33.00],
            "Réaumur": [0.00, 80.00],
            "Rømer": [7.50, 60.00]
        },
            from_input = doc().getElementById("from_input"),
            from_unit = doc().getElementById("from_unit"),
            target_unit = doc().getElementById("target_unit"),
            target_result = doc().getElementById("target_result"),
            calculate = function () {
                try {
                    let inp = parseInt(from_input.value),
                        fu = JSON.parse(from_unit.value),
                        tu = JSON.parse(target_unit.value);

                    // (t1 - tb1)/(ta1 - tb1) = (t2 - tb2)/(ta2 - tb2)
                    // (t1 - tb1)(ta2 - tb2)/(ta1 - tb1) = (t2 - tb2)
                    // ((t1 - tb1)(ta2 - tb2)/(ta1 - tb1)) + tb2 = t2
                    // t2 = ((t1 - tb1)(ta2 - tb2)/(ta1 - tb1)) + tb2

                    target_result.value = ((((inp - fu[0]) * (tu[1] - tu[0])) / (fu[1] - fu[0])) + tu[0]);
                } catch (e) {
                    target_result.value = "";
                }
            };
            from_unit.innerHTML = target_unit.innerHTML = "";
        for (x in units) {
            y = '<option value="'+JSON.stringify(units[x])+'">'+x+'</option>';
            from_unit.innerHTML += y
            target_unit.innerHTML += y;
        }
        from_unit.addEventListener("input", calculate);
        from_input.addEventListener("input", calculate);
        target_unit.addEventListener("input", calculate);
        target_result.value = "";
    </script>
</body>
</html>
