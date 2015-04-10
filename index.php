<html>
 	<head>
    	<meta charset="UTF-8">
    	<meta name="description" content="Smart Tracker Web">
    	<title>Smart Tracker Web</title>

    	<link rel="shortcut icon" href="res/img/favicon.ico" type="image/x-icon">
    	<link rel="icon" href="res/img/favicon.ico" type="image/x-icon">
    	<link rel="stylesheet" type="text/css" href="res/css/tracking.css">

		<script type="text/javascript" src="../res/js/jquery-2.1.3.js"></script>			

	    <script type="text/javascript">
	      $(document).ready(function() {
	                
	        $(".tracking-card").mouseenter(function() {
	          	$(this).css("background-color", "#e8e8e8");
	        });

	        $(".tracking-card").mouseleave(function(){
	          	$(this).css("background-color", "white");
	        });

	        $(".tracking-card").click(function() {	        	
				window.location = "tracking.php?id=" + $(this).children("div .tracking-id").text();	        		
	        });			

			$("img[src='res/img/delete.png']").click(function() {
				var trkId = $(this).siblings("span").text();
				var conf = window.confirm("Suppression de la piste n°" + trkId + "?");
				if(conf == true) {										
		            var xmlhttp = new XMLHttpRequest();
		            xmlhttp.open("POST", "ajax/delete.php", false);
		            xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		            xmlhttp.send("id=" + trkId);
		            if(xmlhttp.responseText == "OK") {
		              $("#container-" + trkId).hide(); 
		            }
		            else {
		              alert(xmlhttp.responseText);
		            }		            
				}
			});

	      });
	    </script>

	</head>
	<body>
		
		<?php include 'res/html/navigation.htm'; ?>

    	<div style="margin-top: 10px;">
			<?php        

	        	$parameters = parse_ini_file("parameters.ini");        
	        
	          	try {             	        	
		            $conn = new PDO("mysql:host=" . $parameters["db_server"] . ";dbname=" . $parameters["db_name"] . ";charset=utf8", $parameters["db_user"], $parameters["db_password"]);
		            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		                 
		            $stmt = $conn->prepare("SELECT TRK_ID, 
		            							   TRK_TITLE, 	
		            							   DATE_FORMAT(TRK_DATE, '%d/%m/%Y') AS TRK_DATE,            							   
		                                           ROUND(getTotalDistance(TRK_ID) / 1000, 2) AS DISTANCE,
		                                           getDuration(TRK_ID) AS DURATION, 	                                           	                                          
		                                           getPositiveHeightDifference(TRK_ID) AS HEIGHT_POSITIVE_DIFF	            							   
		            							   FROM TRACKING
		            							   ORDER BY TRK_ID DESC");
		            $stmt->execute();
		            $trackings = $stmt->fetchAll();   
		          }
		          catch(Exception $e) {
		            die("Error: " . $e->getMessage());
	          	} 
	      ?>	      
	      <div>
	      	<?php
		  		for($i = 0; $i < count($trackings); $i++) {	 
		  			echo "<div id='container-" . $trackings[$i]['TRK_ID'] . "'>"; 			
		  			echo "<div class='toolbar'><span class='tracking-id'>" . $trackings[$i]['TRK_ID'] . "</span><img src='res/img/delete.png' class='btn-action' alt='Suppression'/></div>";
		  			echo "<div class='tracking-card' id='tracking-" . $trackings[$i]['TRK_ID'] . "'>";
		  			echo "<div class='tracking-id'>" . $trackings[$i]['TRK_ID'] . "</div>";	  			
		        	echo "<span class='boundary'>" . $trackings[$i]['TRK_DATE'] . "</span>";	            
		        	echo "<span class='title'>" . $trackings[$i]['TRK_TITLE'] . " (". $trackings[$i]['TRK_ID'] . ")</span>";	  
			        echo "<ul class='inline-list' id='figures-" . $trackings[$i]['TRK_ID'] . "'>";		          
			        	echo "<li><span class='indicator'><span class='figure-alt'>" . $trackings[$i]['DISTANCE'] . "</span><span class='small_unit'>km</span></span><div class='label'>Distance</div></li>";		
			          	echo "<li><span class='indicator'><span class='figure-alt'>" . $trackings[$i]['DURATION'] . "</span></span><div class='label'>Durée totale</div></li>";
			          	echo "<li><span class='indicator'><span class='figure-alt'>" . $trackings[$i]['HEIGHT_POSITIVE_DIFF'] . "</span><span class='small_unit'>m</span></span><div class='label'>D+</div></li>";         		        	
			        echo "</ul>";		        	        
		        	echo "</div>";	        		
		        	echo "</div>";        	
		        } 
		    ?>     	
	      </div>  		
	</body>
</html>