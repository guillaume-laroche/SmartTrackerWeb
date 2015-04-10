<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <meta name="description" content="Smart Tracker Web">
    <title>Smart Tracker Web</title>

    <link rel="shortcut icon" href="res/img/favicon.ico" type="image/x-icon">
    <link rel="icon" href="res/img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="res/css/tracking.css">

    <script type="text/javascript" src="../res/js/jquery-2.1.3.js"></script>      
    
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB3sJTIrXCrRTW7LqOGL5pg1cRBWtYy6x0"></script>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>

    <script type="text/javascript">
      $(document).ready(function(){
                
        $("#edit_title").click(function(){
          var newTitle = window.prompt("Saisir un titre pour cette piste : ", $(this).siblings("span").text());
          if(newTitle != null && newTitle.length > 0 && newTitle != $(this).siblings("span").text()) {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.open("POST", "ajax/updateTitle.php", false);
            xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            xmlhttp.send("id=" + $("#tracking_id").text() + "&title=" + newTitle);
            if(xmlhttp.responseText == "OK") {
              $(this).siblings("span").text(newTitle);    
            }
            else {
              alert(xmlhttp.responseText);
            }
          }
        });

        $("#edit_description").click(function(){
          var newDescription = window.prompt("Saisir une description pour cette piste : ", $(this).siblings("span").text());
          if(newDescription != null && newDescription.length > 0 && newDescription != $(this).siblings("span").text()) {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.open("POST", "ajax/updateDescription.php", false);
            xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            xmlhttp.send("id=" + $("#tracking_id").text() + "&description=" + newDescription);
            if(xmlhttp.responseText == "OK") {
              $(this).siblings("span").text(newDescription);    
            }
            else {
              alert(xmlhttp.responseText);
            }
          }
        });        

      });
    </script>

    <script type="text/javascript">

      <?php        

        $parameters = parse_ini_file("parameters.ini");        

        if(isset($_GET["id"])) {
          try {   
            
            $tracking_id = $_GET["id"];  
            $conn = new PDO("mysql:host=" . $parameters["db_server"] . ";dbname=" . $parameters["db_name"] . ";charset=utf8", $parameters["db_user"], $parameters["db_password"]);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                 
            $stmt = $conn->prepare("SELECT TRE_LATITUDE, TRE_LONGITUDE, TRE_ALTITUDE, ROUND(getIntermediateDistance(TRE_TRK_ID, TRE_LINE) / 1000, 2) as INTERMEDIATE_DISTANCE FROM TRACKING_EVENTS WHERE TRE_EVENT_TYPE = 'location' AND TRE_TRK_ID = " . $tracking_id . " ORDER BY TRE_LINE");
            $stmt->execute();
            $locations = $stmt->fetchAll();            
            include 'gmap.php';
          }
          catch(Exception $e) {
            die("Error: " . $e->getMessage());
          }
        }      
        else {
          echo "console.log('No tracking ID specified, no map to diplay!');";          
        }   
      ?>
    </script> 

    <script type="text/javascript">
      <?php
        if(isset($_GET["id"])) {
          include 'elevation.php';
        }
        else {
          echo "console.log('No tracking ID specified, no chart to diplay!');"; 
        }
      ?>
    </script>

  </head>   
  <body>  
    
    <div>

      <?php include 'res/html/navigation.htm'; ?>

      <div id="tracking" class="top-frame">
        <div style="width:40%;">
        <?php
          if(isset($tracking_id)) {
            try {   
              
              $conn = new PDO("mysql:host=" . $parameters["db_server"] . ";dbname=" . $parameters["db_name"] . ";charset=utf8", $parameters["db_user"], $parameters["db_password"]);
              $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
              
              $query = $conn->query("SELECT DATE_FORMAT(TRK_DATE, '%d/%m/%Y') AS TRK_DATE, 
                                            TRK_ID,
                                            TRK_TITLE, 
                                            TRK_DESCRIPTION,
                                            ROUND(getTotalDistance(" . $tracking_id . ") / 1000, 2) AS DISTANCE,
                                            getDuration(" . $tracking_id . ") AS DURATION, 
                                            getMoveDuration(" . $tracking_id . ") AS MOVE_DURATION, 
                                            getStart(" . $tracking_id . ") AS START,
                                            getFinish(" . $tracking_id . ") AS FINISH,
                                            getPositiveHeightDifference(" . $tracking_id . ") AS HEIGHT_POSITIVE_DIFF,
                                            ROUND(getMaxSpeed(" . $tracking_id . "), 1) AS MAX_SPEED,
                                            ROUND(getMaxAltitude(" . $tracking_id . ")) AS MAX_ALTITUDE,
                                            ROUND(getAverageSpeed(" . $tracking_id . ")) AS AVERAGE_SPEED                                          
                                     FROM TRACKING 
                                     WHERE TRK_ID = " . $tracking_id);
              $tracking = $query->fetch();
            }
            catch(Exception $e) {
              die("Error: " . $e->getMessage());
            }
          }
        ?>        

        <p class="title"><span><?php echo $tracking['TRK_TITLE']; ?></span><img class="btn-action" id="edit_title" src="res/img/edit.png" alt="Edit"/></p>      
        <div class="boundary">Départ : <?php echo $tracking['START']; ?></div>
        <div class="boundary">Arrivée : <?php echo $tracking['FINISH']; ?></div>
        <div class="description"><span><?php echo $tracking['TRK_DESCRIPTION']; ?></span><img class="btn-action" id="edit_description" src="res/img/edit.png" alt="Edit"/></div>
        <div id="tracking_id" style="visibility: hidden;"><?php echo $tracking['TRK_ID'] ?></div>

        </div>

        <div id="figures">
          
          <ul id="figures-main" class="inline-list">
            <li><span class="indicator"><span class="figure"><?php echo $tracking['DISTANCE'] ?></span><span class="unit">km</span></span><div class="label">Distance</div></li>          
            <li><span class="indicator"><span class="figure"><?php echo $tracking['MOVE_DURATION'] ?></span></span><div class="label">Durée déplacement</div></li>          
            <li><span class="indicator"><span class="figure"><?php echo $tracking['HEIGHT_POSITIVE_DIFF'] ?></span><span class="unit">m</span></span><div class="label">D+</div></li>
          </ul>
        
          <ul id="figures-alt" class="inline-list">
            <li><span class="indicator"><span class="figure-alt"><?php echo $tracking['AVERAGE_SPEED'] ?></span><span class="small_unit">km/h</span></span><div class="label">Vmoy</div></li>
            <li><span class="indicator"><span class="figure-alt"><?php echo $tracking['DURATION'] ?></span></span><div class="label">Durée totale</div></li>
            <li><span class="indicator"><span class="figure-alt"><?php echo $tracking['MAX_SPEED'] ?></span><span class="small_unit">km/h</span></span><div class="label">Vmax</div></li>
            <li><span class="indicator"><span class="figure-alt"><?php echo $tracking['MAX_ALTITUDE'] ?></span><span class="small_unit">m</span></span><div class="label">Alt max</div></li>
          </ul>  

        </div>
      </div>

      <div id="map_tracking"></div>

      <div id="elevation"></div>

    </div>  
  </body>  
</html>
