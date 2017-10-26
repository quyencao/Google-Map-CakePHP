<?= $this->Html->css('https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css'); ?>
<?= $this->Html->css('bootstrap-colorpicker.min'); ?>
<?= $this->Html->script("http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js", false); ?>
<?= $this->Html->script('http://maps.google.com/maps/api/js?key=AIzaSyALRT24edC7GaVGvOa8jABOvq4g1I20JZQ&libraries=places&sensor=true', false); ?>
<?= $this->Html->script('bootstrap-colorpicker.min'); ?>
<div class="locations form container">
<?php echo $this->Form->create('Location'); ?>
	<fieldset>
		<legend><?php echo __('Thêm địa điểm'); ?></legend>

        <div class="form-group">
            <input type="text" class="form-control" id="LocationAddress" name="data[Location][address]"/>
        </div>

        <?php
            echo $this->Form->input('latitude', array('type' => 'hidden'));
            echo $this->Form->input('longitude', array('type' => 'hidden'));
        ?>
        <div id="map" class="form-group" style="width: 100%;height: 600px"></div>
	</fieldset>
<?php
    echo $this->Form->button('Lưu địa điểm', array('type' => 'submit', 'class' => 'btn btn-primary add-location'));
?>
    <button class="btn text-white" id="colorPicker" style="background-color: #FE7569">Chọn màu pin</button>
</div>

<script>
    var map;
    var autocomplete;
    var infowindow;
    var markers = [];
    var pinColor = "FE7569";

    function initialize()
    {
        var mapOptions = {
            center: new google.maps.LatLng(10.771971, 106.697845),
            zoom: 15,
            mapTypeId: 'satellite'
        };

        map = new google.maps.Map(document.getElementById("map"),mapOptions);

        google.maps.event.addListener(map, 'click', function (event) {
           placeMarker(map, event.latLng);
        });

        autocomplete = new google.maps.places.Autocomplete(
          document.getElementById('LocationAddress')
        );

        autocomplete.addListener('place_changed', onPlaceChanged);

        $('#colorPicker').colorpicker().on('changeColor', function (event) {
            var color = event.color.toString('Hex');
            $(this).css({ 'background-color' : color });
            pinColor = color.substr(1);
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

    function placeMarker(map, location) {
        if(markers.length >= 3) {
            return;
        }

        var marker = new google.maps.Marker({
           position: location,
           map: map,
           animation: google.maps.Animation.DROP,
           draggable: true,
           icon: getPinImage(),
           color: pinColor
        });

        marker.addListener('click', function () {
            createAndShowInfowindow(location, marker);
        });

        createAndShowInfowindow(location, marker);
        markers.push(marker);
    }
    
    function createAndShowInfowindow(location, marker) {
        if(infowindow) {
            infowindow.close();
        }
        infowindow = new google.maps.InfoWindow({
            content: '<h6>Vĩ độ: ' + location.lat() +  '</h6><h6>Kinh độ: ' + location.lng() +  '</h6>'
        });
        infowindow.open(map, marker);
    }

    function getPinImage() {
        var pinImage = new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|" + pinColor,
            new google.maps.Size(21, 34),
            new google.maps.Point(0,0),
            new google.maps.Point(10, 34));
        return pinImage;
    }

    function addLocation(event) {
        event.preventDefault();

        var formData = new FormData($('#LocationAddForm')[0]);

        var pins = {};

        for(var i = 0; i < markers.length; i++) {
            pins[i] = {
                latitude: markers[i].internalPosition.lat(),
                longitude: markers[i].internalPosition.lng(),
                color: markers[i].color
            }
        }

        $.ajax({
           type: 'POST',
           url: '/locations/add',
           data: {
               Location: {
                   address: formData.get('data[Location][address]'),
                   latitude: formData.get('data[Location][latitude]'),
                   longitude: formData.get('data[Location][longitude]'),
               },
               Pin: pins
           },
           success: function (message) {
               if (message.status === 'SUCCESS') {
                    window.location = '<?php echo $this->Html->url(array('controller' => 'locations', 'action' => 'index')); ?>';
               }
           }
        });
    }

    google.maps.event.addDomListener(window, 'load', initialize);
    
    $('#LocationAddForm').on('submit', addLocation);
</script>
