<?php

require_once ('dbconnect.php');

$uuid = "";
$county = "";
$country = "";
$town = "";
$postcode = "";
$description = "";
$num_bedrooms = "";
$num_bathrooms = "";
$price = "";
$property_type = "";
$sale_type = "";
$image_full = "";
$image_thumbnail = "";
$latitude = "";
$longitude = "";
$title = "Add New Property";

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['uuid']) && $_POST['uuid'] != ""){
        $sql='SELECT * FROM `propertiesTable` WHERE uuid="'.$_POST["uuid"].'"';
        $result = mysqli_query($conn, $sql) or die ('error' . mysqli_error($conn));
        if(!$result) exit ("The query did not succeed.");
        while ($listing= mysqli_fetch_array($result)){
            $uuid = $listing['uuid'];
            $county = $listing['county'];
            $country = $listing['country'];
            $town = $listing['town'];
            $postcode = $listing['address'];
            $description = $listing['description'];
            $num_bedrooms = $listing['num_bedrooms'];
            $num_bathrooms = $listing['num_bathrooms'];
            $price = $listing['price'];
            $property_type = $listing['property_type'];
            $sale_type = $listing['sale_type'];
            $image_full = $listing['image_full'];
            $image_thumbnail = $listing['image_thumbnail'];
            $latitude = $listing['latitude'];
            $longitude = $listing['longitude'];
            $title = "Edit Property";
        }

    }
}

