<?= $this->Html->css('https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css'); ?>
<?= $this->Html->script("http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js", false); ?>
<?= $this->Html->script('http://maps.google.com/maps/api/js?key=AIzaSyALRT24edC7GaVGvOa8jABOvq4g1I20JZQ&libraries=places', false); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-2">
            <div class="card border-info text-primary">
                <h4 class="card-header">Địa điểm</h4>
                <div class="card-body">
                    <h6 class="card-title">Địa điểm</h6>
                    <p class="card-text"><?php echo h($location['Location']['address']); ?></p>
                    <h6 class="card-title">Vĩ độ</h6>
                    <p class="card-text"><?php echo h($location['Location']['latitude']); ?></p>
                    <h6 class="card-title">Kinh độ</h6>
                    <p class="card-text"><?php echo h($location['Location']['longitude']); ?></p>
                </div>
            </div>
        </div>
        <div class="col">
            <div id="map" style="width: 100%; height: 85vh"></div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        var map;
        var infowindow;

        function initialize() {
            var mapOptions = {
                center: new google.maps.LatLng(<?php echo $location['Location']['latitude']; ?>, <?php echo $location['Location']['longitude']; ?>),
                zoom: 15,
                mapTypeId: 'satellite'
            };

            map = new google.maps.Map(document.getElementById("map"), mapOptions);

            <?php foreach ($location['Pin'] as $pin): ?>
            placeMarker(map, {
                lat: <?php echo $pin['latitude']; ?>,
                lng: <?php echo $pin['longitude']; ?>
            }, "<?php echo $pin['color']; ?>", "<?php echo $pin['name']; ?>");
            <?php endforeach; ?>
        }

        function placeMarker(map, location, color, name) {
            var marker = new google.maps.Marker({
                position: location,
                map: map,
                animation: google.maps.Animation.DROP,
                draggable: true,
                icon: getPinImage(color)
            });

            marker.addListener('click', function (event) {
                if (infowindow) {
                    infowindow.close();
                }

                var content = '<h5>Vĩ độ: ' + event.latLng.lat() + '</h5><h5>Kinh độ: ' + event.latLng.lng() + '</h5>';

                if (name) {
                    content = '<h5>Tên: ' + name + '</h5>' + content;
                }

                infowindow = new google.maps.InfoWindow({
                    content: content
                });

                infowindow.open(map, marker);
            });
        }

        function getPinImage(color) {
            var pinImage = new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|" + color,
                new google.maps.Size(21, 34),
                new google.maps.Point(0, 0),
                new google.maps.Point(10, 34));
            return pinImage;
        }

        google.maps.event.addDomListener(window, 'load', initialize);
    });
</script>