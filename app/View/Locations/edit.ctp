<?= $this->Html->css('https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css'); ?>
<?= $this->Html->script("http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js", false); ?>
<?= $this->Html->script('http://maps.google.com/maps/api/js?key=AIzaSyALRT24edC7GaVGvOa8jABOvq4g1I20JZQ&libraries=places&sensor=true', false); ?>
<?//= debug($location); ?>
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
        <div id="map" class="form-group" style="width: 100%;height: 400px"></div>
    </fieldset>
    <?php
    echo $this->Form->button('Lưu địa điểm', array('type' => 'submit', 'class' => 'btn btn-primary add-location'));
    ?>
    <button type="button" class="btn btn-outline-danger" id="clear-markers">Xóa tất cả marker</button>
</div>

<script>
    var map;
    var autocomplete;
    var infowindow = new google.maps.InfoWindow();
    var markers = [];
//    var pins = {};

    function initialize()
    {
        var mapOptions = {
            center: new google.maps.LatLng(<?php echo $location['Location']['latitude']; ?>, <?php echo $location['Location']['longitude'] ?>),
            zoom: 15,
            mapTypeId: 'satellite'
        };

        map = new google.maps.Map(document.getElementById("map"),mapOptions);

        google.maps.event.addListener(map, 'click', function (event) {
            placeNewMarker(map, event.latLng);
        });

        autocomplete = new google.maps.places.Autocomplete(
            document.getElementById('LocationAddress')
        );

        autocomplete.addListener('place_changed', onPlaceChanged);

        // Place default marker
        <?php foreach ($location['Pin'] as $index => $pin): ?>
            addMarker(map, {
                lat: <?php echo $pin['latitude']; ?>,
                lng: <?php echo $pin['longitude']; ?>
            });
        <?php endforeach; ?>

        // Delete marker event
        google.maps.event.addListener(infowindow, 'domready', function () {

            $("#deleteMarker").click(function() {

                deleteMarker($(this).data('id'));
            });
        });
    }

    function onPlaceChanged() {
        var place = autocomplete.getPlace();
        if (place.geometry) {
            map.panTo(place.geometry.location);
            map.setZoom(15);
        } else {
            document.getElementById('LocationAddress').placeholder = 'Enter a city';
        }

        document.getElementById('LocationLatitude').value = place.geometry.location.lat();
        document.getElementById('LocationLongitude').value = place.geometry.location.lng();
    }
    
    function addMarker(map, location) {
        var id = Date.now();
        var marker = new google.maps.Marker({
            position: location,
            map: map,
            animation: google.maps.Animation.DROP,
            draggable: true,
            id: id
        });

        marker.addListener('dragend', function () {
           console.log(markers);
        });

        marker.addListener('click', function (event) {
            infowindow = setContentInfoWindow(
                '<h5>Vĩ độ: ' + event.latLng.lat() +  '</h5><h5>Kinh độ: ' + event.latLng.lng() +  '</h5>'
                + '<button id="deleteMarker" data-id="' + id + '" type="button" class="btn btn-outline-danger">Xóa</button>'
            );
            infowindow.open(map, marker);
        });

        markers.push(marker);

        return marker;
    }

    function deleteMarker(id) {
        var index = markers.findIndex((m) => m.id == id);
        var marker = markers[index];
        marker.setMap(null);
        markers.splice(index, 1);
    }

    function setContentInfoWindow(content) {
        if(infowindow) {
            infowindow.close();
        }
        infowindow.setContent(content);

        return infowindow;
    }

    function placeNewMarker(map, location) {
        var marker = addMarker(map, location);
    }

    function updateLocation(event) {
        event.preventDefault();

        var formData = new FormData($('#LocationEditForm')[0]);

        var pins = {};

        for(var i = 0; i < markers.length; i++) {
            pins[i] = {
                latitude: markers[i].internalPosition.lat(),
                longitude: markers[i].internalPosition.lng()
            }
        }

        console.log(pins);

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
       for(var i = 0; i < markers.length; i++) {
           markers[i].setMap(null);
       }
       markers = [];
    });
</script>
