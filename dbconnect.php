<?php
//SQL server and log-in
$host = "localhost";
$user = "root";
$pass = "";

//Connect to MySQLi
$conn = mysqli_connect($host, $user, $pass);
if ($conn -> connect_error) {
    die("Connection failed.");
}

//Make propertiesDB the current database
$db_selected = mysqli_select_db( $conn, 'propertiesDatabase');
if(!$db_selected) {
    //if we couldn't, then it either doesn't exist, or we can't see it.
    $sql = "CREATE DATABASE propertiesDatabase";
    if ($conn->query($sql) === TRUE) {
       // echo "SUCCESS - propertiesDatabase created\n";
        $db_selected = mysqli_select_db($conn, 'propertiesDatabase');

    } else {
        echo 'ERROR CREATING DATABASE: ' . mysqli_error($conn) . "\n";
    }
}

//Create tables
$sqlMakeTable = 'CREATE TABLE IF NOT EXISTS propertiesTable (
uuid VARCHAR(255) PRIMARY KEY, county VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL,
town VARCHAR (255) NOT NULL, description TEXT NOT NULL, address VARCHAR (255) NOT NULL,
image_full TEXT NOT NULL, image_thumbnail TEXT NOT NULL, latitude DECIMAL NOT NULL ,
longitude DECIMAL NOT NULL, num_bedrooms INT NOT NULL, num_bathrooms INT NOT NULL,
price DECIMAL NOT NULL, property_type VARCHAR(255) NOT NULL, sale_type VARCHAR(30) NOT NULL, updated_at DATETIME NOT NULL)';
$conn->query($sqlMakeTable);

$sqlMakePropTypeTable = 'CREATE TABLE IF NOT EXISTS propertyTypesTable (
id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, title VARCHAR(255) NOT NULL, description TEXT NOT NULL)';
$conn->query($sqlMakePropTypeTable);

// get data from api
function getDataFromAPI($pageNumber){
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://trialapi.craig.mtcdevserver.com/api/properties?page=%5B1%2C30%5D&api_key=3NLTTNlXsi6rBWl7nYGluOdkl2htFHug&page%5Bnumber%5D=' . $pageNumber,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "cache-control: no-cache",
        ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}

function createTablesFromAPIData($conn, $response)
{

    foreach ($response['data'] as $property) {

        $lutSql= 'SELECT updated_at FROM propertiesTable WHERE uuid = ' .$property["uuid"];
        $lutResult = $conn->query($lutSql);
        if($lutResult !== false){
            while($row = $lutResult->fetch_assoc()){
                $last_update_time = $row['updated_at'];
            }
            if($property["updated_at"] > $last_update_time){
                $query = $conn->prepare('REPLACE INTO propertiesTable(uuid, county, country, town, description,
address, image_full, image_thumbnail, latitude, longitude, num_bedrooms, num_bathrooms,
price, property_type, sale_type, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
?, ?, ?, ?)');
                $query->bind_param("ssssssssddiidsss", $property["uuid"], $property["county"],
                    $property["country"], $property["town"], $property["description"], $property["address"],
                    $property["image_full"], $property["image_thumbnail"], $property["latitude"],
                    $property["longitude"], $property["num_bedrooms"], $property["num_bathrooms"],
                    $property["price"], $property["property_type"]["title"], $property["type"],
                    $property["updated_at"]);
                $query->execute();
            }
        } else {
            $query = $conn->prepare('REPLACE INTO propertiesTable(uuid, county, country, town, description,
address, image_full, image_thumbnail, latitude, longitude, num_bedrooms, num_bathrooms,
price, property_type, sale_type, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
?, ?, ?, ?)');
            $query->bind_param("ssssssssddiidsss", $property["uuid"], $property["county"],
                $property["country"], $property["town"], $property["description"], $property["address"],
                $property["image_full"], $property["image_thumbnail"], $property["latitude"],
                $property["longitude"], $property["num_bedrooms"], $property["num_bathrooms"],
                $property["price"], $property["property_type"]["title"], $property["type"],
                $property["updated_at"]);
            $query->execute();
            $query->close();
        }

        $ptypesql = "REPLACE INTO propertyTypesTable(id, title, description) VALUES
('" . $property["property_type"]["id"] . "','" . $property["property_type"]["title"] . "','"
            . $property["property_type"]["description"] . "')";
        $conn->query($ptypesql);

    }
}

session_start();
if(!isset($_SESSION['apiPull'])){
    //Creates or updates tables from the API data
    for($pg = 1; $pg <= 34; $pg++) {
        createTablesFromAPIData($conn, json_decode(getDataFromAPI($pg), true));
    }
    $_SESSION['apiPull'] = true;
}

