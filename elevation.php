      google.load('visualization', '1.0', {'packages':['corechart']});
      google.setOnLoadCallback(drawElevation);

      function drawElevation() {
        var data = new google.visualization.DataTable();
        data.addColumn('number', 'Distance');
        data.addColumn('number', 'Altitude');
        data.addRows ([
          <?php
            for($i = 0; $i < count($locations); $i++) {
              echo "[" . $locations[$i]['INTERMEDIATE_DISTANCE'] . "," . $locations[$i]['TRE_ALTITUDE'] . "]";
              if($i < count($locations) - 1) {
                echo ",";
              }
            }
          ?>
          ]);

        var options = { title:'Profil',
                        width:1000,
                        height:200,
                        hAxis: {title: 'Distance (km)', baseline: 0},
                        vAxis: {title: 'Altitude (m)', baseline: 0}
                     };
      
        var chart = new google.visualization.AreaChart(document.getElementById('elevation'));
        chart.draw(data, options);        
      }