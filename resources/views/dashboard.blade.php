<x-app-layout>
    <div id="tabs" class="ui top attached tabular menu">
        @for($x = 0; $x < sizeof($equipamentos); $x++) @if($x==0) <div id="equip-{{$equipamentos[$x]->id}}" class="active item tabbar" onclick="switchChart(<?php echo $equipamentos[$x]->id ?>,'<?php echo $equipamentos[$x]->leitura_limite ?>')">{{$equipamentos[$x]->descricao}}</div>
    @else
    <div id="equip-{{$equipamentos[$x]->id}}" class="item tabbar" onclick="switchChart(<?php echo $equipamentos[$x]->id ?>,'<?php echo $equipamentos[$x]->leitura_limite ?>')">{{$equipamentos[$x]->descricao}}</div>
    @endif
    @endfor
    </div>
    <div id="div" class="ui bottom attached active tab segment">
        <div id="chartdiv"></div>
    </div>
</x-app-layout>


<!-- Styles -->
<style>
    #tabs {
        padding-top: 55px;
    }
</style>

<!-- Resources -->
<script src="https://cdn.amcharts.com/lib/4/core.js"></script>
<script src="https://cdn.amcharts.com/lib/4/charts.js"></script>
<script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>


<!-- Chart code -->
<script>
    var equipamentos = <?php echo $equipamentos ?>;
    var maximo;
    var chart;
    var bullet;
    var valueAxis;
    var series;
    var scrollbarX;
    var timerId = 0;
    var range;
    
    //ao alterar tamanho da tela, redimensiona as divs
    $(window).resize(function() {
        redimensionarDivs();
    });

    //redimensiona as divs
    function redimensionarDivs() {
        document.getElementById('chartdiv').style.height = (window.innerHeight - 130) + 'px';
    }

    $(document).ready(function() {
        redimensionarDivs();
    });

    am4core.ready(function() {
        setChart();
        if (equipamentos.length > 0) {
            maximo = equipamentos[0].leitura_limite;
            updateChart(equipamentos[0].id);
        }
    });

    function switchChart(id, new_limite) {
        clearInterval(timerId);

        $(".tabbar").removeClass("active");
        $("#equip-" + id).addClass("active");
        maximo = new_limite;
        updateChart(id);
    }

    function setChart() {
        am4core.useTheme(am4themes_animated);
        chart = am4core.create("chartdiv", am4charts.XYChart);

        chart.data = [];

        // Create axes
        var categoryAxis = chart.xAxes.push(new am4charts.DateAxis());
        categoryAxis.dataFields.category = "date";

        // Create value axis
        valueAxis = chart.yAxes.push(new am4charts.ValueAxis());

        // Create series
        series = chart.series.push(new am4charts.LineSeries());
        series.dataFields.valueY = "value";
        series.dataFields.dateX = "date";
        series.fillOpacity = 0.2;
        series.strokeWidth = 2;

        // Add scrollbar
        scrollbarX = new am4charts.XYChartScrollbar();
        scrollbarX.series.push(series);
        chart.scrollbarX = scrollbarX;

        range = valueAxis.createSeriesRange(series);
        range.endValue = 999999;
        range.contents.stroke = am4core.color("#FF0000");
        range.contents.fill = am4core.color("#FF0000");
        range.contents.fillOpacity = 0.2;
        // bullet is added because we add tooltip to a bullet for it to change color
        bullet = series.bullets.push(new am4charts.Bullet());
        bullet.tooltipText = "{valueY} PPM";

        bullet.adapter.add("fill", function(fill, target) {
            if (target.dataItem.valueY > maximo) {
                return am4core.color("#FF0000");
            }
            return fill;
        });
        chart.cursor = new am4charts.XYCursor();
    }

    function updateChart(id) {
        var data = [];
        $.get("/leituras/" + id, function(resultado) {
            console.log(resultado)

            var leituras = resultado;

            for (var i = 0; i < leituras.length; i++) {
                var date = new Date(leituras[i]['created_at']);
                value = leituras[i]['leitura'];
                data.push({
                    date: date,
                    value: value
                });
            }

            chart.data = data;

            range.value = maximo;
            timerId = setInterval(function() {
                $.get("/leituras/new/" + id, function(resultado) {
                    var leituras = resultado;

                    var date = new Date(leituras['created_at']);
                    value = leituras['leitura'];

                    chart.addData({
                        date: date,
                        value: value
                    }, 1);

                });
            }, 10000);
        });
    }
</script>