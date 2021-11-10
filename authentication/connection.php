<?php
    
    //Below are the database information
    $db_host = "localhost";
    $db_user = "root";
    $db_password = "";
    $db_name = "sidehustle_project_group_yellow";

    //Connect to the database
    //And check for any errors during connection
    $dsn = "mysql:host=$db_host;dbname=$db_name;charset=UTF8";
    try {
        $conn = new PDO($dsn, $db_user, $db_password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOEXCEPTION $e) {
        $e->getMessage();
    }
?>