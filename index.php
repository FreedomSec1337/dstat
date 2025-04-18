<?php
require 'config/config.php';
$dataName = ($zone == 'EU') ? (($lang == 'FR') ? "Octets" : "Bytes") : 'Bits';
$requestLang = ($lang == 'FR') ? 'Requetes' : 'Requests';
$perSecondLang = ($lang == 'FR') ? 'par seconde' : 'per second';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $sitename; ?></title>
    <?php error_log(" \r\n", 3, 'data/layer7-logs'); ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highcharts/8.2.2/highcharts.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highcharts/8.2.2/modules/exporting.min.js" crossorigin="anonymous"></script>

    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500&display=swap" rel="stylesheet">
    <style>
        body {
            background: #0d1117;
            color: #c9d1d9;
            font-family: 'Orbitron', monospace;
            margin: 0;
            padding: 20px;
        }

        #layer7, #layer4 {
            background: linear-gradient(135deg, #0d1117, #1f1f1f);
            border: 1px solid #30363d;
            box-shadow: 0 0 20px #00ffcc44;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 40px;
            transition: all 0.5s ease-in-out;
        }

        h2 {
            text-align: center;
            color: #58a6ff;
        }

        .fade-in {
            animation: fadeInUp 1.5s ease-in-out;
        }

        @keyframes fadeInUp {
            0% {opacity: 0; transform: translateY(20px);}
            100% {opacity: 1; transform: translateY(0);}
        }
    </style>
</head>
<body class="fade-in">
    <h2><?php echo $sitename; ?> dstat L7 & L4</h2>
    <div id="layer7"></div>
    <div id="layer4"></div>

<script>
$(document).ready(function () {
    Highcharts.setOptions({
        chart: {
            backgroundColor: '#161b22',
            style: {
                fontFamily: "'Orbitron', monospace",
                color: '#c9d1d9'
            }
        },
        title: {
            style: {
                color: '#58a6ff',
                fontSize: '20px'
            }
        },
        xAxis: {
            labels: {
                style: {
                    color: '#8b949e'
                }
            }
        },
        yAxis: {
            labels: {
                style: {
                    color: '#8b949e'
                }
            },
            gridLineColor: '#30363d'
        },
        legend: {
            itemStyle: {
                color: '#c9d1d9'
            }
        }
    });

    let layer7 = new Highcharts.Chart({
        chart: {
            renderTo: "layer7",
            type: "spline",
            events: {
                load: requestData(0),
            },
        },
        title: {
            text: "<?php echo $Layer7Title;?>",
        },
        xAxis: {
            type: "datetime",
            tickPixelInterval: 150,
            maxZoom: 20 * 1000,
        },
        yAxis: {
            title: {
                text: "<?php echo $requestLang;?> <?php echo $perSecondLang;?>",
            },
        },
        series: [{
            name: "<?php echo $requestLang;?>/s",
            data: [],
            color: "#00ffcc"
        }]
    });

    let layer4 = new Highcharts.Chart({
        chart: {
            renderTo: "layer4",
            type: "spline",
            events: {
                load: requestData(1),
            },
        },
        title: {
            text: "<?php echo $Layer4Title;?>",
        },
        xAxis: {
            type: "datetime",
            tickPixelInterval: 150,
            maxZoom: 20 * 1000,
        },
        yAxis: {
            title: {
                text: "<?php echo $dataName;?> <?php echo $perSecondLang;?>",
            },
        },
        series: [{
            name: "<?php echo $dataName;?>/s",
            data: [],
            color: "#ff00ff"
        }]
    });

    function requestData(type) {
        $.ajax({
            url: "data/" + (!type ? "layer7" : "layer4") + ".php",
            success: function (point) {
                var series = (!type ? layer7 : layer4).series[0],
                    shift = series.data.length > 20;
                series.addPoint(point, true, shift);
                setTimeout(() => requestData(type), 1000);
            },
            cache: false
        });
    }
});
</script>
</body>
</html>
