<?php
include ('form.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submit Property</title>
    <style>
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
        }
        img {
            width = 200px;
            height: = auto;
        }
    </style>
</head>
<body>
<h1><?php echo $title ?></h1>
<form name="submitProperty" method="post" action="formSubmitted.php" onSubmit="return validateForm()" enctype="multipart/form-data">
    <label for="county">County</label>
    <input id="county" type="text" placeholder="County" name="county" value="<?php echo $county ?>" required><br>

    <label for="country">Country</label>
    <input id="country" type="text" placeholder="Country" name="country" value="<?php echo $country ?>" required><br>

    <label for="town">Town</label>
    <input id="town" type="text" placeholder="Town" name="town" value="<?php echo $town ?>" required><br>

    <label for="postcode">Postcode</label>
    <input id="postcode" type="text" placeholder="Postcode" name="postcode" value="<?php echo $postcode ?>" required><br>

    <label for="description">Description</label>
    <textarea id="description" placeholder="Write a description" name="description" ><?php echo $description ?></textarea><br>

    <label for="numbed">Number of Bedrooms</label>
    <input id="numbed" type="number" min="1" max="50"  placeholder="Bedrooms" name="numbed" value="<?php echo $num_bedrooms ?>" required><br>

    <label for="numbath">Number of Bathrooms</label>
    <input id="numbath" type="number" min="1" max="50" placeholder="Bathrooms" name="numbath" value="<?php echo $num_bathrooms ?>" required><br>

    <label for="price">Price</label>
    <input id="price" type="number" min="0.00" step="0.01" placeholder="Price" name="price" value="<?php echo $price ?>" required><br>

    <label for="propertyType">Property Type</label>
    <select id="propertyType" class="propertyType" name="propertyType">
        <?php
        $propTypesResult = $conn->query("SELECT * FROM propertyTypesTable");
        foreach ($propTypesResult as $type){
            if($type['title'] == $property_type){
                echo "<option value='".$type['title']."' selected>" . $type['title'] . "</option>";
            } else {
                echo "<option value='".$type['title']."'>" . $type['title'] . "</option>";
            }
        };
        ?>
    </select><br>
    Listing Type<br/>
    <label for="sale">Sale</label>
    <?php if ($sale_type === "sale" || $sale_type === "") {
        echo '<input type="radio" id="sale" value="sale" name="saleType" checked>';
    } else {
        echo '<input type="radio" id="sale" value="sale" name="saleType">';
    } ?>
    <label for="rent">Rent</label>
    <?php if ($sale_type === "rent") {
        echo '<input type="radio" id="rent" value="rent" name="saleType" checked><br>';
    } else {
        echo '<input type="radio" id="rent" value="rent" name="saleType"><br>';
    } ?>
    <label for="inputImage">Image</label>
    <?php if($image_thumbnail !== ""){
        echo '<img id="imagePreview" src="'.$image_thumbnail.'" width="200" alt="Image Preview"><br>';
    } else {
        echo '<img id="imagePreview" src="" width="200" alt="Image Preview"><br>';
    }?>
    <input name="inputImage" id="inputImage" type="file" accept=".jpg,.png,.jpeg" onchange="previewFile()" required><br>
    <input type="hidden" id="uuid" name="uuid" value="<?php echo $uuid ?>">
    <input type="submit" value="Submit"/>
</form>
<script src="formHelpers.js"></script>
<script src="formValidation.js"></script>
</body>
</html>
