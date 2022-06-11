<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Read CSV File</title>
    <?php include("read_csv_style.php") ?>
</head>
<body>

<?php
include_once("../conn.php");

echo "<div id=csvForm>";
echo "<form method=post action=create_sql_from_csv.php enctype=multipart/form-data>";
    echo "<label for=table_name>Table Name</label>";    
    echo "<input type=text name=table_name id=table_name />";
    echo "<input type=radio name=sql_type id=insert_sql value=insert checked />";
    echo "<label for=insert_sql>Insert</label>";
    echo "<input type=radio name=sql_type id=update_sql value=update />";
    echo "<label for=contact_phone>Update</label>";
    echo "<br>Select csv: <input type=file name=csvFile id=csvFile accept=.csv>";
    echo "<br><input type=submit value='create sql' name=csv_submit>";
echo "</form>";
echo "</div>";
?>

</body>
</html>