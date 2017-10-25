<?= $this->Html->css('https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css'); ?>
<?= $this->Html->script("http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js", false); ?>
<?= $this->Html->script('http://maps.google.com/maps/api/js?key=AIzaSyALRT24edC7GaVGvOa8jABOvq4g1I20JZQ&libraries=places&sensor=true', false); ?>
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
        <div id="map" class="form-group" style="width: 100%;height: 400px"></div>
	</fieldset>
<?php
    echo $this->Form->button('Lưu địa điểm', array('type' => 'submit', 'class' => 'btn btn-primary add-location'));
?>
</div>

<script>
    var map;
    var autocomplete;
    var infowindow;
    var markers = [];

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
        var marker = new google.maps.Marker({
           position: location,
           map: map,
           animation: google.maps.Animation.DROP,
           draggable: true,
        });
        if(infowindow) {
            infowindow.close();
        }
        infowindow = new google.maps.InfoWindow({
            content: ''
        });
        infowindow.open(map, marker);
        markers.push(marker);
    }
    
    function addLocation(event) {
        event.preventDefault();

        var formData = new FormData($('#LocationAddForm')[0]);

        var pins = {};

        for(var i = 0; i < markers.length; i++) {
            pins[i] = {
                latitude: markers[i].internalPosition.lat(),
                longitude: markers[i].internalPosition.lng()
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
