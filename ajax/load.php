<html>
<body>
<?php		
	if(isset($_POST["filename"])) {
		$parameters = parse_ini_file("../parameters.ini");

		$datafile = $_POST["filename"];
		$data = file_get_contents("../data/" . $datafile);
		$dataArray = json_decode($data, true);
		$line = 1;
		$sql = "";
		$eventCount = 0;
		$locationCount = 0;

		try {		
			$conn = new PDO("mysql:host=" . $parameters["db_server"] . ";dbname=" . $parameters["db_name"] . ";charset=utf8", $parameters["db_user"], $parameters["db_password"]);
			foreach($dataArray as $key => $tracking) {
				try {
					if($tracking["type"] == "event" && $tracking["what"] == "start") {				   
	    				$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);        				
	    				$conn->exec("INSERT INTO TRACKING (TRK_DATE, TRK_DATAFILE, TRK_TITLE, TRK_DESCRIPTION) 
	    							 VALUES (str_to_date('" . $tracking["timestamp"] . "', '%d-%m-%Y %H:%i:%s'), '" . $datafile . "', '', '')");        			    			
						$tracking_id = $conn->lastInsertId("TRK_ID");	
						echo "<p>ID tracking = " . $tracking_id . "</p>";																			
					}

					if($tracking["type"] == "event") {
						$sql = "INSERT INTO TRACKING_EVENTS(TRE_TRK_ID, TRE_LINE, TRE_TIMESTAMP, TRE_EVENT_TYPE, TRE_EVENT_WHAT)
							    VALUES(" . $tracking_id . ", " . $line . ", str_to_date('" . $tracking["timestamp"] . "', '%d-%m-%Y %H:%i:%s'), '" . $tracking["type"] . "', '" . $tracking["what"] . "')";
						$conn->exec($sql);
						echo "<p>Ligne " . $line . ", évènement " . $tracking["what"] . " </p>";
						$eventCount++;
					}
					else {
						
						$locationCount++;
						echo "<p>Line " . $line . ", position (" . $tracking["latitude"] . ", " . $tracking["longitude"] . ") </p>";

						if($tracking["speed"] == NULL) {
							$speed = 0;
						}
						else {
							$speed = $tracking["speed"];
						}

						if($tracking["altitude"] == NULL) {
							$altitude = 0;
						}
						else {
							$altitude = $tracking["altitude"];
						}
						
						if($tracking["distance"] == NULL) {
							$distance = 0;
						}
						else {
							$distance = $tracking["distance"];
						}

						if($tracking["accuracy"] == NULL) {
							$accuracy = 0;
						}
						else {
							$accuracy = $tracking["accuracy"];
						}

						$sql = "INSERT INTO TRACKING_EVENTS(TRE_TRK_ID, TRE_LINE, TRE_TIMESTAMP, TRE_EVENT_TYPE, TRE_LATITUDE, TRE_LONGITUDE, TRE_ALTITUDE, TRE_SPEED, TRE_DISTANCE, TRE_ACCURACY)
							  	VALUES(" . $tracking_id . ", " . $line . ", str_to_date('" . $tracking["timestamp"] . "', '%d-%m-%Y %H:%i:%s'), '" . $tracking["type"] . "', " . $tracking["latitude"] . ", " . $tracking["longitude"] . ", " . $altitude . ", " . $speed . ", " . $distance . ", " . $accuracy . ")";
						$conn->exec($sql);	  		
					}
					$line++;				
				}			
				catch(PDOException $e) {  
					echo $sql;      			
	    			echo "Error: " . $e->getMessage();
	    		}
			}

			//Déplacement du fichier dans les archives 
			rename("../data/" . $datafile, "../data/archives/" . $datafile);

			echo "<p>Chargement terminé avec succès.</p>";
			echo "<p>Nombre d'évènements : " . $eventCount . "</p>";
			echo "<p>Nombre de positions : " . $locationCount . "</p>";			
			echo "<p><a href='tracking.php?id=" . $tracking_id . "'><img src='res/img/zoom.png' alt='Zoom'/></a></p>";
		}	

		catch (Exception $e) {
	        die('Error : ' . $e->getMessage());
		}
	}
	else {
		echo ("No filename specified, nothing to do!");
	}	
?>
</body>
</html>
