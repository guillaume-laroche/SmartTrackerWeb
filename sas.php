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
				$(".btn-load").click(function() {
					var filename = $(this).parent("td").siblings(".filename").children("a").text();
					var conf = window.confirm("Intégrer le fichier " + filename + "?");
					if(conf == true) {										
		            	var xmlhttp = new XMLHttpRequest();
		            	xmlhttp.open("POST", "ajax/load.php", false);
		            	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		            	xmlhttp.send("filename=" + filename);
		            	$("#logging").append(xmlhttp.responseText);		
		            	$(this).fadeOut();		            	
					}
				});    			
	    	});
	    </script>
	</head>

	<body>

	<?php include 'res/html/navigation.htm'; ?>

		<div>
			<div class="page-title">Fichiers en cours de traitement</div>
			<div class="horizontal-align">
				<table>				
				<tr>
					<th>Fichier</th>
					<th>Intégrer</th>
				</tr>				
				<?php
					$files = scandir("data");
					for($i = 0; $i < count($files); $i++) {
						if(!is_dir($files[$i]) && pathinfo($files[$i], PATHINFO_EXTENSION) == "json") {
							echo "<tr>";
							echo "<td class='filename'><a href='data\\" . $files[$i] . "' alt='View file' target='_blank'>" . $files[$i] . "</a></td>";	
							echo "<td align='center'><img src='res/img/process.png' class='btn-load' alt='Intégrer'/></a></td>";	
							echo "</tr>";
						}
					}

				?>
				</table>
			</div>
			<div id="logging" class="horizontal-align loading">

			</div>
		</div>
	</body>
</html>