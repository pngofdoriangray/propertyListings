<?php
require_once ('page_class.php');
require_once ('dbconnect.php');

session_start();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <style>
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
        }
    </style>
</head>
<body>

<h1>Property Listings</h1>
<button onclick="location.href='form_view.php'">Add New Listing</button><br>
Results Per Page:
<form action="?" method="post">
    <select id="per_page" name="per_page">
        <option value="30" >30</option>
        <option value="70" >70</option>
        <option value="100">100</option>
    </select>
    <input type="submit" name="submit" value="Select">
</form>


<script>
</script>
</body>
</html>

<?php
//shows image from url
function showImage($url){
    $imageData = base64_encode(file_get_contents($url));
    echo '<td><img src="data:image/jpeg;base64,'.$imageData.'"><br/>
Source: '.$url.'</td>';
}

if(isset($_POST['per_page'])){
    $_SESSION['perpage'] = $_POST['per_page'];
}
if(isset($_SESSION['perpage'])) {
    $per_page = $_SESSION['perpage'];
} else {
    $_SESSION['perpage'] = 30;
    $per_page = $_SESSION['perpage'];
}

$page = new page_class($per_page);
error_reporting(0); // disable the annoying error report
$sql="SELECT * FROM `propertiesTable`";
$result=mysqli_query($conn, $sql) or die('error'.mysqli_error($conn, $sql));
// paging start
$row_counts = mysqli_num_rows($result);
$page->specify_row_counts($row_counts);
$starting_record = $page->get_starting_record();

$sql="SELECT * FROM `propertiesTable` ORDER BY updated_at DESC LIMIT $starting_record, $per_page";
$result= mysqli_query($conn, $sql) or die('error'.mysqli_error($conn));
$number = $starting_record; //numbering
$num_rows = mysqli_num_rows($result);
if ($num_rows == 0 ) {
    // if no result is found
    echo "<div class=\"notice\">
    <span class=note>NO DATA</span>
    </div>";
}
else    {
    echo "<table>";
    // while goes here ...
    while ($row = $result->fetch_assoc()){
        createTableRow($row);
    }
    echo "</table>";
}

$page->show_pages_link();

function createTableRow($row){
    echo '<tr>';
    showImage($row['image_thumbnail']);
    echo '<td><header><h1>'.$row['property_type'].' for '.$row['sale_type'].' - £'.$row['price'].'</h1>';
    echo '<h2>'.$row['num_bedrooms'].' bedrooms, '.$row['num_bathrooms'].' bathrooms</h2>';
    echo '<p>'.$row['description'].'</p></td>';
    echo '<td><p>'.$row['address'].'</p>';
    echo '<b><p>'.$row['town'].', '.$row['county'].', '.$row['country'].'</p></b>';
    echo
    '<form action="form_view.php" method="post">
    <button type="submit" value="'.$row["uuid"].'" name="uuid">Edit Listing</button>
    </form></td></tr>';
}
?>