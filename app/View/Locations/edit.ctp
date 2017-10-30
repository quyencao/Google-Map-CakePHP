<div class="locations form container">
    <?php echo $this->Form->create('Location'); ?>
    <fieldset>
        <legend><?php echo __('Sửa địa điểm'); ?></legend>
        <div class="form-group">
            <input type="text" class="form-control" id="LocationAddress" name="data[Location][address]" value="<?= $location['Location']['address']; ?>"/>
        </div>

        <?php
        echo $this->Form->input('id', array('type' => 'hidden', 'value' => $location['Location']['id']));
        echo $this->Form->input('latitude', array('type' => 'hidden', 'value' => $location['Location']['latitude']));
        echo $this->Form->input('longitude', array('type' => 'hidden', 'value' => $location['Location']['longitude']));
        ?>
        <div id="map" class="form-group" style="width: 100%;height: 600px"></div>
    </fieldset>
    <?php
    echo $this->Form->button('Lưu địa điểm', array('type' => 'submit', 'class' => 'btn btn-primary add-location'));
    ?>
    <button type="button" class="btn btn-outline-danger" id="clear-markers">Xóa tất cả marker</button>
</div>

<script>
    $(document).ready(function() {
        var map;
        var autocomplete;
        var infowindow = new google.maps.InfoWindow();
        var markers = [];
        var defaultColor = 'FE7569';

        function initialize() {
            var mapOptions = {
                center: new google.maps.LatLng(<?php echo $location['Location']['latitude']; ?>, <?php echo $location['Location']['longitude'] ?>),
                zoom: 15,
                mapTypeId: 'satellite'
            };

            map = new google.maps.Map(document.getElementById("map"), mapOptions);

            google.maps.event.addListener(map, 'rightclick', function (event) {
                addMarker(map, {lat: event.latLng.lat(), lng: event.latLng.lng()}, defaultColor, false);
            });

            google.maps.event.addListener(map, 'click', function (event) {
                setAllMarkersVisible();
                infowindow.close();
            });

            autocomplete = new google.maps.places.Autocomplete(
                document.getElementById('LocationAddress')
            );

            autocomplete.addListener('place_changed', onPlaceChanged);

            // Place default marker
            <?php foreach ($location['Pin'] as $index => $pin): ?>
            addMarker(map, {
                lat: <?php echo $pin['latitude']; ?>,
                lng: <?php echo $pin['longitude']; ?>,
            }, '<?php echo $pin['color']; ?>', true, '<?php echo $pin['name']; ?>');
            <?php endforeach; ?>

            // Set bound for markers
            var bound = new google.maps.LatLngBounds();
            for(var i = 0; i < markers.length; i++) {
                bound.extend(markers[i].getPosition());
            }
            map.fitBounds(bound);

            // Delete marker event
            google.maps.event.addListener(infowindow, 'domready', function () {
                $("#deleteMarker").on('click', function () {
                    deleteMarker($(this).data('id'));
                });

                $('#savePin').on('click', function () {
                    var value = $('#name').val();
                    var markerId = $(this).data('id');
                    changeNameMarker(markerId, value);
                    setMarkerVisible(markerId);
                    infowindow.close();
                });

                $('#markerColorpicker').colorpicker().on('changeColor', function (event) {
                    var color = event.color.toString('HEX');
                    var markerId = $(this).data('id');

                    $(this).css({'background-color': color});
                    changeColorMarker(markerId, color.substr(1));
                });

                google.maps.event.addListener(infowindow, 'position_changed', function () {
                    for(var i = 0; i < markers.length; i++) {
                        if (markers[i].id !== $('#savePin').data('id')) {
                            markers[i].setVisible(true);
                        }
                    }
                });
            });
        }

        function setMarkerVisible(id) {
            for(var i = 0; i < markers.length; i++) {
                if(markers[i].id === id) {
                    markers[i].setVisible(true);
                    return;
                }
            }
        }

        function setAllMarkersVisible() {
            for(var i = 0; i < markers.length; i++) {
                markers[i].setVisible(true);
            }
        }

        function onPlaceChanged() {
            var place = autocomplete.getPlace();
            if (place.geometry) {
                map.panTo(place.geometry.location);
                map.setZoom(15);
            } else {
                $('#LocationAddress').attr('placeholder', 'Enter a city');
            }

            $('#LocationLatitude').val(place.geometry.location.lat());
            $('#LocationLongitude').val(place.geometry.location.lng());
        }

        function addMarker(map, location, color, visible, name) {
            if (markers.length >= 3) {
                return;
            }

            var id = generateUniqueId();
            var marker = new google.maps.Marker({
                position: location,
                map: map,
                animation: google.maps.Animation.DROP,
                draggable: true,
                id: id,
                icon: getPinImage(color),
                color: color,
                name: name,
                visible: visible
            });

            marker.addListener('click', function () {
                var markerContent = getContentInfoWindow(marker);
                infowindow = setContentInfoWindow(
                    markerContent
                );
                infowindow.open(map, marker);
            });

            markers.push(marker);

            if (!visible) {
                // set infowindow
                var contentInfowindow = getContentInfoWindow(marker);
                setContentInfoWindow(contentInfowindow);
                infowindow.open(map, marker);

                var event = google.maps.event.addListener(infowindow, 'closeclick', function () {
                    marker.setVisible(true);
                    google.maps.event.removeListener(event);
                });
            }

            return marker;
        }

        function deleteMarker(id) {
            var index = markers.findIndex((m) => m.id == id);
            var marker = markers[index];
            marker.setMap(null);
            markers.splice(index, 1);
        }

        function setContentInfoWindow(content) {
            if (infowindow) {
                infowindow.close();
            }
            infowindow.setContent(content);
            return infowindow;
        }

        function getContentInfoWindow(marker) {
            var id = marker.id;
            var markerContent =
                '<h5>Vĩ độ: ' + marker.position.lat() + '</h5><h5>Kinh độ: ' + marker.position.lng() + '</h5>';

            if(marker.visible) {
                markerContent += '<button id="deleteMarker" data-id="' + id + '" type="button" class="btn btn-outline-danger mr-2"><i class="fa fa-trash" aria-hidden="true"></i></button>';
            }

            markerContent +=
                '<button class="btn btn-outline-info mr-2" data-id="' + id + '" id="markerColorpicker"><i class="fa fa-eyedropper" aria-hidden="true"></i></button>' +
                '  <a class="btn btn-outline-warning" data-toggle="collapse" href="#collapse" aria-expanded="false" aria-controls="collapseExample">' +
                '    <i class="fa fa-cog" aria-hidden="true"></i>' +
                '  </a>' +
                '<div class="collapse mt-2" id="collapse">' +
                '  <div class="card card-body">' +
                '  <div class="form-group">' +
                '   <input type="text" class="form-control" id="name" placeholder="Tên pin" value="' + (marker.name !== undefined ? marker.name : '') + '">' +
                '  </div>' +
                '<button type="button" class="btn btn-primary" data-id="' + id + '" id="savePin">Lưu lại</button>' +
                '  </div>' +
                '</div>';

            if (marker.name) {
                markerContent = '<h5>Tên: ' + marker.name + '</h5>' + markerContent;
            }

            return markerContent;
        }

        function getPinImage(color) {
            var pinImage = new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|" + color,
                new google.maps.Size(21, 34),
                new google.maps.Point(0, 0),
                new google.maps.Point(10, 34));
            return pinImage;
        }

        function changeColorMarker(id, color) {
            var index = markers.findIndex(m => m.id == id);
            var marker = markers[index];
            marker.color = color;
            marker.setIcon(getPinImage(color));
        }

        function changeNameMarker(id, value) {
            var index = markers.findIndex(m => m.id == id);
            var marker = markers[index];
            marker.name = value;
        }

        function generateUniqueId() {
            return '_' + Math.random().toString(36).substr(2, 9);
        }

        function updateLocation(event) {
            event.preventDefault();

            var formData = new FormData($('#LocationEditForm')[0]);

            var pins = {};

            for (var i = 0; i < markers.length; i++) {
                pins[i] = {
                    latitude: markers[i].internalPosition.lat(),
                    longitude: markers[i].internalPosition.lng(),
                    color: markers[i].color,
                    name: markers[i].name
                }
            }

            $.ajax({
                type: 'POST',
                url: '/locations/edit/' + formData.get('data[Location][id]'),
                data: {
                    Location: {
                        id: formData.get('data[Location][id]'),
                        address: formData.get('data[Location][address]'),
                        latitude: formData.get('data[Location][latitude]'),
                        longitude: formData.get('data[Location][longitude]')
                    },
                    Pin: pins
                },
                success: function (response) {
                    if (response.status === 'SUCCESS') {
                        window.location = '<?php echo $this->Html->url(array('controller' => 'locations', 'action' => 'index')); ?>';
                    }
                }
            });
        }

        google.maps.event.addDomListener(window, 'load', initialize);

        $('#LocationEditForm').on('submit', updateLocation);

        $('#clear-markers').on('click', function () {
            for (var i = 0; i < markers.length; i++) {
                markers[i].setMap(null);
            }
            markers = [];
        });
    });
</script>
