<x-app-layout>
    <div id="chartdiv"></div>
</x-app-layout>
<!-- Styles -->
<style>
    #chartdiv {
        margin-top: 65px;
        width: 100%;
    }
</style>

<!-- Resources -->
<script src="https://cdn.amcharts.com/lib/4/core.js"></script>
<script src="https://cdn.amcharts.com/lib/4/charts.js"></script>
<script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>

<!-- Chart code -->
<script>
    //ao alterar tamanho da tela, redimensiona as divs
    $(window).resize(function() {
        redimensionarDivs();
    });

    //redimensiona as divs
    function redimensionarDivs() {
        document.getElementById('chartdiv').style.height = (window.innerHeight - 66) + 'px';
    }

    $(document).ready(function() {
        redimensionarDivs();

    });
    am4core.ready(function() {

        // Themes begin
        am4core.useTheme(am4themes_animated);
        // Themes end

        var chart = am4core.create("chartdiv", am4charts.XYChart);
        var leituras = <?php echo $leituras ?>;
        var data = [];
        var maximo = 128;
        for (var i = 0; i < leituras.length; i++) {
            var date = new Date(leituras[i]['created_at']);
            value = leituras[i]['leitura'];
            data.push({
                date: date,
                value: value
            });
        }

        chart.data = data;

        // Create axes
        var categoryAxis = chart.xAxes.push(new am4charts.DateAxis());
        categoryAxis.dataFields.category = "date";

        // Create value axis
        var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());

        // Create series
        var series = chart.series.push(new am4charts.LineSeries());
        series.dataFields.valueY = "value";
        series.dataFields.dateX = "date";
        series.fillOpacity = 0.2;
        series.strokeWidth = 2;

        // bullet is added because we add tooltip to a bullet for it to change color
        var bullet = series.bullets.push(new am4charts.Bullet());
        bullet.tooltipText = "{valueY} PPM";

        bullet.adapter.add("fill", function(fill, target) {
            if (target.dataItem.valueY > maximo) {
                return am4core.color("#FF0000");
            }
            return fill;
        });

        var range = valueAxis.createSeriesRange(series);
        range.value = maximo;
        range.endValue = 999999;
        range.contents.stroke = am4core.color("#FF0000");
        range.contents.fill = am4core.color("#FF0000");
        range.contents.fillOpacity = 0.2;

        // Add scrollbar
        var scrollbarX = new am4charts.XYChartScrollbar();
        scrollbarX.series.push(series);
        chart.scrollbarX = scrollbarX;

        chart.cursor = new am4charts.XYCursor();

    }); // end am4core.ready()
</script>