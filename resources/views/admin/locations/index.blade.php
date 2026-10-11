@extends('layouts.admin.app')

@section('content')
 

<div class="main-content-inner">
    <div class="card-area">
        <div class="row">

            <div class="col-lg-2 col-md-6 mt-5">
                <button class="btn btn-primary" id="newCoordinates" onclick="newCoordinates()">New</button>

                <input name="" id="location_name" placeholder="Location Name..." class="form-control" value="">
                <br>
                <button class="btn btn-info" id="save_loc" onclick="submitCoordinates()">Save Location</button>
                <div class="row">
                    <div class="col-lg-12 col-md-6 mt-5">
                        <h6>Locations:</h6>
                        <br>
                        <table>

                            <tbody id="loc_drp">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-10 col-md-6 mt-5" style="height:800px;">
                <div id="map" style="min-height: -webkit-fill-available;"></div>
            </div>
        </div>
    </div>



    @endsection


    @section('javascrip')

    <script>
        $(document).ready(function() {
            getLocations();
        });

        function getLocations() {
            var options = "";
            $.ajax({
                url: '/getlocations',
                type: 'get',
                success: function(res) {
                    for (var i = 0; i < res.length; i++) {
                        options += '<tr > <td id="' + res[i].id + '" class="loc_city" >' + res[i].city + '</td><td value="' + res[i].id + '" class="loc_update"> Update </td> </tr>';
                    }
                    $("#loc_drp").html(options);
                },
                error: function(msg) {
                    console.error(data);
                }
            });
        }

        $("body").on("click", ".loc_update", function() {
            var vertices = bermudaTriangle.getPath();
            var contentString = [];
            for (var i = 0; i < vertices.getLength(); i++) {
                var xy = vertices.getAt(i);
                contentString.push({
                    lat: xy.lat(),
                    lng: xy.lng()
                });
            }

            $.ajax({
                url: '/locations/' + $(this).attr("value"),
                type: 'put',
                data: {
                    coordinates: contentString
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    if (res=="Updated!") {
                        alert( "Coordinates Updated...!" );
                    }
                },
                error: function(msg) {
                    console.error(data);
                }
            });
        });

        $("body").on("click", ".loc_city", function() {
            $.ajax({
                url: '/locations/' + $(this).attr("id"),
                type: 'get',
                success: function(res) {
                    var triangleCoords = [];
                    for (var i = 0; i < res.length; i++) {
                        triangleCoords.push({
                            lat: parseFloat(res[i].lat),
                            lng: parseFloat(res[i].lng)
                        });
                    }
                    loadMap(triangleCoords, 12);
                },
                error: function(msg) {
                    console.error(data);
                }
            });
        });

        var bermudaTriangle = null;
        var map = null;

        function initMap() {
            loadMap();
        }

        function loadMap(triangleCoords, zom = 6) {
            var latv, lngv;
            if (!triangleCoords) {
                latv = 26.8206;
                lngv = 30.8025;
            } else {
                latv = triangleCoords[0].lat
                lngv = triangleCoords[0].lng
            }

            map = new google.maps.Map(document.getElementById('map'), {
                zoom: zom,
                center: {
                    lat: latv,
                    lng: lngv
                },
                mapTypeId: 'terrain'
            });


            // Construct the polygon.
            bermudaTriangle = new google.maps.Polygon({
                paths: triangleCoords,
                strokeColor: 'red',
                strokeOpacity: 0.2,
                strokeWeight: 2,
                editable: true,
                draggable: true,
                fillColor: 'green',
                fillOpacity: 0.35
            });
            bermudaTriangle.setMap(map);
        }

        function newCoordinates() {
            var triangleCoords = [{
                    lat: 30.01359314916789,
                    lng: 31.48049744537707
                },
                {
                    lat: 29.998430430364206,
                    lng: 31.480325784000115
                },
                {
                    lat: 30.008836467239153,
                    lng: 31.503843392642693
                }
            ];
            loadMap(triangleCoords, 12);

        }

        function submitCoordinates(event) {
            var vertices = bermudaTriangle.getPath();
            var contentString = [];
            for (var i = 0; i < vertices.getLength(); i++) {
                var xy = vertices.getAt(i);
                contentString.push({
                    lat: xy.lat(),
                    lng: xy.lng()
                });
            }
            $.ajax({
                url: '/locations',
                type: 'POST',
                data: {
                    coordinates: contentString,
                    name: $("#location_name").val()
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    if (res == "Done!") {
                        getLocations();
                    }
                },
                error: function(msg) {
                    console.error(data);
                }
            });
        }

        function addMarker(e) {
            console.log(e.latLng.lat(), e.latLng.lng())
            var marker = new google.maps.Marker({
                position: e.latLng,
                map: map
            });
        }
    </script>

    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAhtoUyHv6Q0Ch231O8LLPsJJ_QfSzh1rw&callback=initMap&libraries=places"></script>
    @endsection