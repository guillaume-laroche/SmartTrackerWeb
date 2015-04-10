      function initialize() {
        var mapOptions = {
          center: new google.maps.LatLng(<?php echo $locations[0]['TRE_LATITUDE'] . "," . $locations[0]['TRE_LONGITUDE'] ?>),
          zoom: 12,
          mapTypeId: google.maps.MapTypeId.TERRAIN
          };

        var map = new google.maps.Map(document.getElementById('map_tracking'), mapOptions);

        var trackingLocations = [
          <?php
            for($i = 0; $i < count($locations); $i++) {
              echo "new google.maps.LatLng(" . $locations[$i]['TRE_LATITUDE'] . "," . $locations[$i]['TRE_LONGITUDE'] . ")";
              if($i < count($locations) - 1) {
                echo ",";
              }
            }
          ?>
        ];

        var start = new google.maps.Marker({
          position: <?php echo "new google.maps.LatLng(" . $locations[0]['TRE_LATITUDE'] . "," . $locations[0]['TRE_LONGITUDE'] .")" ?>,
          map: map,
          icon: 'res/img/green-dot.png',
          title: "Start"
        });

        var finish = new google.maps.Marker({
          position: <?php echo "new google.maps.LatLng(" . $locations[count($locations) - 1]['TRE_LATITUDE'] . "," . $locations[count($locations) - 1]['TRE_LONGITUDE'] .")" ?>,
          map: map,
          icon: 'res/img/yellow-dot.png',
          title: "Finish"
        });
  
        var trackingPath = new google.maps.Polyline({
          path: trackingLocations,
          geodesic: true,
          strokeColor: '#FF0000',
          strokeOpacity: 1.0,
          strokeWeight: 2
        });

        trackingPath.setMap(map);
      }
      
      google.maps.event.addDomListener(window, 'load', initialize);