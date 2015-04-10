<?php
    $parameters = parse_ini_file("../parameters.ini");
        
    if(isset($_POST["id"]) && isset($_POST["description"])) {
        try {
            $conn = new PDO("mysql:host=" . $parameters["db_server"] . ";dbname=" . $parameters["db_name"] . ";charset=utf8", $parameters["db_user"], $parameters["db_password"]);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);    

            $sql = "UPDATE TRACKING SET TRK_DESCRIPTION = '" . $_POST["description"] . "' WHERE TRK_ID = ". $_POST["id"];
            $stmt = $conn->prepare($sql);    
            $stmt->execute();
            
            echo "OK";
        }
        catch(PDOException $e) {
            echo $e->getMessage();
        }
        $conn = null;
    } 
    else {
        echo "Incomplete request, nothing to do!";
    }       
?>