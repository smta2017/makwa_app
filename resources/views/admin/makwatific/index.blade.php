@extends('layouts.admin.app')

@section('content')


<div class="main-content-inner">
    <div class="card-area">
        <div class="row">
            <div class="col-lg-12 col-md-6 mt-5" style="height:500px;">
                <div id="map" style="min-height: -webkit-fill-available;"></div>
            </div>


        </div>
    </div>



    @endsection


    @section('javascrip')

    <script>
        $(document).ready(function() {});



        var bermudaTriangle = null;
        var map = null;

        function initMap() {
            var x = 1;
            if (x == 1) {

                loadMap();
            }
        }


        function loadMap(triangleCoords, zom = 12) {
            // Map options
            const directionsService = new google.maps.DirectionsService();
            const directionsRenderer = new google.maps.DirectionsRenderer();
            var options = {
                zoom: zom,
                center: {
                    lat: 30.0189275,
                    lng: 31.497513
                }
            }
            map = new google.maps.Map(document.getElementById('map'), options);
            directionsRenderer.setMap(map);
            calculateAndDisplayRoute(directionsService, directionsRenderer);
        }

        function calculateAndDisplayRoute(directionsService, directionsRenderer) {
            const waypts = [{
                location: "30.024,31.4299",
                stopover: true
            }, {
                location: "30.0118,31.492",
                stopover: true
            }, {
                location: "30.0205,31.4715",
                stopover: true
            }];
            const checkboxArray = document.getElementById("waypoints");

            directionsService.route({
                    origin: '30.034,31.407',
                    destination: '30.0306,31.4697',
                    waypoints: waypts,
                    optimizeWaypoints: true,
                    travelMode: google.maps.TravelMode.DRIVING,
                },
                (response, status) => {
                    if (status === "OK") {
                        console.log(response);
                        directionsRenderer.setDirections(response);
                        const route = response.routes[0];

                    } else {
                        window.alert("Directions request failed due to " + status);
                    }
                }
            );
        }
    </script>

    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAhtoUyHv6Q0Ch231O8LLPsJJ_QfSzh1rw&callback=initMap&libraries=places"></script>
    @endsection