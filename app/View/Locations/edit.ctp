<?= $this->Html->css('https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css'); ?>
<?= $this->Html->css('font-awesome.min'); ?>
<?= $this->Html->script("https://code.jquery.com/jquery-3.2.1.min.js", false); ?>
<?= $this->Html->script("https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js", false); ?>
<?= $this->Html->script("https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js", false); ?>
<?= $this->Html->script('http://maps.google.com/maps/api/js?key=AIzaSyALRT24edC7GaVGvOa8jABOvq4g1I20JZQ&libraries=places&sensor=true', false); ?>
<?= $this->Html->script('jscolor.min'); ?>
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
    <button class="btn text-white jscolor {valueElement:null, value:'FE7569', onFineChange:'changeColorPin(this)'}">Chọn màu pin</button>
</div>

<script>
    var map;
    var autocomplete;
    var infowindow = new google.maps.InfoWindow();
    var markers = [];
    var pinColor = 'FE7569';

    function initialize()
    {
        var mapOptions = {
            center: new google.maps.LatLng(<?php echo $location['Location']['latitude']; ?>, <?php echo $location['Location']['longitude'] ?>),
            zoom: 15,
            mapTypeId: 'satellite'
        };

        map = new google.maps.Map(document.getElementById("map"),mapOptions);

        google.maps.event.addListener(map, 'click', function (event) {
            addMarker(map, event.latLng, pinColor);
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
            }, '<?php echo $pin['color']; ?>', '<?php echo $pin['name']; ?>');
        <?php endforeach; ?>

        // Delete marker event
        google.maps.event.addListener(infowindow, 'domready', function () {

            $("#deleteMarker").on('click', function() {
                deleteMarker($(this).data('id'));
            });

            $('#changeColorMarker').on('click', function () {
                changeColorMarker($(this).data('id'));
            });

            $('#savePin').on('click', function () {
               var value = $('#name').val();
               var id = $(this).data('id');
               changeNameMarker(id, value);
               infowindow.close();
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
    
    function addMarker(map, location, color, name) {
        if(markers.length >= 3) {
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
            name: name
        });

        marker.addListener('dragend', function () {
           console.log(markers);
        });

        marker.addListener('click', function (event) {
            var markerContent =
                '<h5>Vĩ độ: ' + event.latLng.lat() +  '</h5><h5>Kinh độ: ' + event.latLng.lng() +  '</h5>' +
                '<button id="deleteMarker" data-id="' + id + '" type="button" class="btn btn-outline-danger mr-2"><i class="fa fa-trash" aria-hidden="true"></i></button>' +
                '<button id="changeColorMarker" data-id="' + id + '" type="button" class="btn btn-outline-info"><i class="fa fa-cog" aria-hidden="true"></i></button>' +
                '  <a class="btn btn-primary ml-2" data-toggle="collapse" href="#collapse" aria-expanded="false" aria-controls="collapseExample">' +
                '    <i class="fa fa-cog" aria-hidden="true"></i>' +
                '  </a>' +
                '<div class="collapse mt-2" id="collapse">' +
                '  <div class="card card-body">' +
                '  <div class="form-group">' +
                '   <input type="text" class="form-control" id="name" placeholder="Tên pin" value="' + (marker.name ? marker.name : name) + '">' +
                '  </div>' +
                '<button type="button" class="btn btn-primary" data-id="' + id + '" id="savePin">Lưu lại</button>' +
                '  </div>' +
                '</div>';

            if(marker.name) {
                markerContent = '<h5>Tên: ' + marker.name + '</h5>' + markerContent;
            } else if(name) {
                markerContent = '<h5>Tên: ' + name + '</h5>' + markerContent;
            }

            infowindow = setContentInfoWindow(
                markerContent
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

    function getPinImage(color) {
        var pinImage = new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|" + color,
            new google.maps.Size(21, 34),
            new google.maps.Point(0,0),
            new google.maps.Point(10, 34));
        return pinImage;
    }

    function changeColorPin(color) {
        pinColor = color.toHEXString().substr(1);
    }

    function changeColorMarker(id) {
        var index = markers.findIndex(m => m.id == id);
        var marker = markers[index];
        marker.color = pinColor;
        marker.setIcon(getPinImage(pinColor));
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

        for(var i = 0; i < markers.length; i++) {
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
       for(var i = 0; i < markers.length; i++) {
           markers[i].setMap(null);
       }
       markers = [];
    });
</script>
