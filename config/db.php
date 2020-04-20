<?php

try {
    $pdo = new PDO("mysql:host=localhost;dbname=pharma", "root", "toor");
    // set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e)
{
    echo "Connection failed: " . $e->getMessage();
    die();
}
   
