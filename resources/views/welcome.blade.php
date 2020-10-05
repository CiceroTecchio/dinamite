<style>
    #map{
        margin-top: 65px !important;
    }
</style>

<x-app-layout>
    <div id="map"></div>
</x-app-layout>

<script>
    let map;

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
        document.getElementById('map').style.height = (window.innerHeight - 66) + 'px';
    }

    $(document).ready(function() {
        redimensionarDivs();

    });
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBjG1spI2tp65rU0m0XG8KNDp1pfOhSjcc&callback=initMap&libraries=&v=weekly" defer></script>