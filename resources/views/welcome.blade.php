<style>
    #tabs {
        padding-top: 55px !important;
    }
</style>

<x-app-layout>
    <div id="tabs"></div>
    <div id="map"></div>
</x-app-layout>

<script>
    let map, heatmap;
    var timerId = 0;

    function initMap() {
        map = new google.maps.Map(document.getElementById("map"), {
            center: {
                lat: -25.7507575,
                lng: -53.0612766
            },
            zoom: 14,
        });

    }
    //ao alterar tamanho da tela, redimensiona as divs
    $(window).resize(function() {
        redimensionarDivs();
    });

    //redimensiona as divs
    function redimensionarDivs() {
        document.getElementById('map').style.height = (window.innerHeight - 56) + 'px';
    }

    $(document).ready(function() {
        redimensionarDivs();
        timerId = setInterval(function() {
            $.get("/pontos/perigo", function(resultado) {
                console.log(resultado);
                var pontos = [];
                for (var x = 0; x < resultado.length; x++) {
                    pontos.push(new google.maps.LatLng(resultado[x].latitude, resultado[x].longitude));
                }
                if (heatmap != null) {
                    heatmap.setMap(null);
                }
                heatmap = new google.maps.visualization.HeatmapLayer({
                    data: pontos,
                    map: map,
                });
                heatmap.set("radius", 40);
            });
        }, 5000);

    });
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBjG1spI2tp65rU0m0XG8KNDp1pfOhSjcc&callback=initMap&libraries=visualization" defer></script>