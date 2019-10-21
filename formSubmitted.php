<?php
require ('dbconnect.php');
require_once('functions.php');

$successfulSubmission = false;

function gen_uuid() {
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

        // 16 bits for "time_mid"
        mt_rand( 0, 0xffff ),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand( 0, 0x0fff ) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand( 0, 0x3fff ) | 0x8000,

        // 48 bits for "node"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
}

function safePOST($conn, $text){
    if (isset($_POST["$text"])){
        return $conn->real_escape_string(strip_tags($_POST[$text]));
    } else {
        return "";
    }
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $county = safePOST($conn, "county");
    $country = safePOST($conn, "country");
    $town = safePOST($conn, "town");
    $postcode = safePOST($conn, "postcode");
    $description = safePOST($conn, "description");
    $num_bedrooms = safePOST($conn, "numbed");
    $num_bathrooms = safePOST($conn, "numbath");
    $price = safePOST($conn, "price");
    $property_type = safePOST($conn, "propertyType");
    $sale_type = safePOST($conn, "saleType");
    $updated_at = date('Y-m-d H:i:s');
    $latitude = 20.00;
    $longitude= 20.00;

    if(isset($_POST['uuid']) && $_POST['uuid'] != ""){
        $uuid = $_POST['uuid'];
        //We are editing an existing property
        if($_FILES['image']['size'] == 0){
            //Image has not been changed - no worries
            //We need to put the old URLs back in the database
            $sql='SELECT * FROM `propertiesTable` WHERE uuid="'.$_POST["uuid"].'"';
            $result = mysqli_query($conn, $sql) or die ('error' . mysqli_error($conn));
            if(!$result) exit ("The query did not succeed.");
            while ($listing = $result->fetch_assoc()) {
                $image_full = $listing['image_full'];
                $image_thumbnail = $listing['image_thumbnail'];
                $latitude = $listing['latitude'];
                $longitude = $listing['longitude'];
            }
        } else {
            //New image has been added and needs to be processed
            $imageArray = validateAndUploadImage();
            $image_full = $imageArray[0];
            $image_thumbnail = $imageArray[1];
        }
    } else {
        //We are adding a new property
        $uuid = gen_uuid();
        $imageArray = validateAndUploadImage();
        $image_full = $imageArray[0];
        $image_thumbnail = $imageArray[1];
    }

    //If there are no errors, send the content to the database. Otherwise, show an error
    $query = $conn->prepare("REPLACE INTO `propertiesTable` (`uuid`, `county`, `country`, `town`, `description`, 
`address`, `image_full`, `image_thumbnail`, `latitude`, `longitude`, `num_bedrooms`, `num_bathrooms`,
`price`, `property_type`, `sale_type`, `updated_at`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
    $query->bind_param( 'ssssssssddiidsss',$uuid, $county, $country, $town, $description, $postcode, $image_full,
        $image_thumbnail, $latitude, $longitude, $num_bedrooms, $num_bathrooms, $price, $property_type, $sale_type, $updated_at);
    if($query->execute() or die(mysqli_error($conn))){
        $successfulSubmission = true;
    } else {
        $successfulSubmission = false;
    }

    if($successfulSubmission){
        //Move to the main page.
        header("Location: /mtcDBApp/browseListings_view.php");
        exit;
    } else {
        echo "There was an error";
    }
}

function validateAndUploadImage(){
    if(isset($_FILES['image']))
    {
        $output['status']=FALSE;
        set_time_limit(0);
        $allowedImageType = array("image/jpeg",   "image/jpg",   "image/png");

        if ($_FILES['image']["error"] > 0) {
            $output['error']= "File Error";
        }
        elseif (!in_array($_FILES['image']["type"], $allowedImageType)) {
            $output['error']= "Invalid image format";
        }
        elseif (round($_FILES['image']["size"] / 1024) > 4096) {
            $output['error']= "Maximum file upload size is exceeded";
        } else {
            $temp_path = $_FILES['image']['tmp_name'];
            $file = pathinfo($_FILES['image']['name']);
            $fileType = $file["extension"];
            $fileName = rand(222, 888) . time() . ".$fileType";

            $small_thumbnail_path = "uploads/small/";
            createFolder($small_thumbnail_path);
            $small_thumbnail = $small_thumbnail_path . $fileName;

            $large_thumbnail_path = "uploads/large/";
            createFolder($large_thumbnail_path);
            $large_thumbnail = $large_thumbnail_path . $fileName;

            $thumb1 = createThumbnail($temp_path, $small_thumbnail,$fileType, 150, 93);
            $thumb3 = createThumbnail($temp_path, $large_thumbnail,$fileType, 550, 340);

            if($thumb1  && $thumb3) {
                $output['status']=TRUE;
                $output['small']= $small_thumbnail;
                $output['large']= $large_thumbnail;

                return array($large_thumbnail, $small_thumbnail);
            }
        }
    }
    return "";
}





