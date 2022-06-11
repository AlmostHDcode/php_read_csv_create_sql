<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="description" content="">
<meta name="author" content="">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create sql from CSV</title>
<?php include("read_csv_style.php") ?>
</head>
<body>

<?php
include_once("../conn.php");

if (isset($_POST["sql_submit"])) { //if form has been submitted
    
    //process the posted sql strings text box
    $sql_type = $_POST["sql_type"];
    $posted_sql = $_POST["sql_text"];
    $posted_sql = strip_tags($posted_sql); //strip html tags
    $posted_sql = trim($posted_sql); //strip whitespace
    $posted_sql = htmlspecialchars($posted_sql);
    
    $sql_strings = explode("||", htmlspecialchars_decode($posted_sql)); //split strings into an array
    $sql_success = false;
    $rows = 0;
    
    //loop through array and run each command to insert data into table
    for($i = 0; $i < sizeof($sql_strings); $i++) {
        $sql = $sql_strings[$i];
        $sql_stmt = mysqli_prepare($conn, $sql);
        if (mysqli_stmt_execute($sql_stmt)) {
            $sql_success = true;
            $rows++;
        } else {
            $sql_success = false;
        }
    }
    
    if ($sql_success) {
        echo "<p class=msg>$rows rows of $sql_type Data From CSV Successfull</p>";
    } else {
        echo "<p class=error>There was an error</p>" . mysqli_error($conn);
    }
    
} else { //form has not been submitted
    //get values from previous form from read_csv.php
    $table = $_POST["table_name"]; //user insert table name
    $sql_type = $_POST["sql_type"]; //user insert sql type
    $csv_file = $_FILES["csvFile"]["tmp_name"]; //use tmp_name to get the full path of the selected file so fopen does not fail
    $sql_strings = array();
    $cols = "";
    $row_num = 1;
    
    //open the selected csv file
    if (($handle = fopen($csv_file, "r")) !== false) {
        while (($data = fgetcsv($handle, 2000, ",")) !== false) { //loop through each row of csv file
            $col_num = count($data); //num of columns in csv file
            $values = "";
            for ($i = 0; $i < $col_num; $i++) {
                if ($row_num == 1) { //first row contains the column names
                    //if not last column, have commas between, otherwise on last column no comma
                    if ($i == $col_num - 1) {
                        $cols .= $data[$i];
                    } else {
                        $cols .= $data[$i] . ",";
                    }
                    $cols_array = explode(',', $cols); //this will help if sql_type is update
                } else { //every other row contains data
                    if ($i == $col_num - 1) { 
                        if ($data[$i] == "") {
                            $values .= "NULL";
                        } elseif ($data[$i] == "default" || $data[$i] == "DEFAULT") {
                            $values .= "default";
                        } else {
                            $values .= "\"$data[$i]\"";
                        }
                    } else {
                        if ($data[$i] == "") {
                            $values .= "NULL,";
                        } elseif ($data[$i] == "default" || $data[$i] == "DEFAULT") {
                            $values .= "default,";
                        } else {
                            $values .= "\"$data[$i]\",";
                        }
                    }
                }
            }
            
            $values_array = explode(',', $values); //used for update sql_type
            $update_string = ""; //used for update sql_type
            
            if ($row_num !== 1) {
                if ($sql_type == 'insert') {
                    //if the sql_type is insert you simply use the cols and values strings as they are
                    $sql = "INSERT INTO $table ($cols) VALUES($values);"; //create sql string
                    array_push($sql_strings, $sql); //add each sql string to array   
                } elseif($sql_type == 'update') {
                    //if the sql_type is update you need to use the array versions of the cols and values and loop through
                    //update sql needs different syntax than insert sql, SET col1 = value1, col2 = value2..... etc...
                    for($i = 1; $i < $col_num; $i++) {
                        if ($i == $col_num - 1) {
                            $update_string .= "$cols_array[$i] = $values_array[$i]"; //if last col_num don't add comma at end
                        } else {
                            $update_string .= "$cols_array[$i] = $values_array[$i], "; //else add comma between
                        }
                    }
                    $sql = "UPDATE $table SET $update_string WHERE $cols_array[0] = $values_array[0];"; //create the full update sql string
                    array_push($sql_strings, $sql); //add each sql string to array 
                }
            }
            $row_num++;
        }
        fclose($handle);
    }
    
    $col_check_status = false;
    $table_cols = array();
    
    $col_check_sql = "SHOW COLUMNS FROM $table";
    $col_check_stmt = mysqli_prepare($conn, $col_check_sql);
    mysqli_stmt_execute($col_check_stmt);
    $col_check_result = mysqli_stmt_get_result($col_check_stmt);
    while ($row = mysqli_fetch_array($col_check_result, MYSQLI_NUM)) {
        array_push($table_cols, $row[0]);
    }

    foreach ($cols_array as $c) {
        if (in_array($c, $table_cols)) {
            $col_check_status = true;
        } else {
            $col_check_status = false;
            break; //if only one column match returns false, break out of loop
        }
    }
    
    if ($col_check_status) { //if all csv columns match the table columns
        //show form
        echo "<form method=post action=''>";
        echo "<p class=msg>Make Sure all statments look correct<br>
        Make any changes before submitting<br>
        Make sure the table name is correct<br>";
        //show different messages depending on what sql type is being created
        if($sql_type == "insert") {
            echo "<input type=hidden name=sql_type id=sql_type value=$sql_type>";
            echo "Make sure every sql statment fits the format of: INSERT INTO tablename (column_name1, column_name2,.......) VALUES(\"value1\",\"value2\",.......);||<br>";
        } elseif($sql_type == "update") {
            echo "<input type=hidden name=sql_type id=sql_type value=$sql_type>";
            echo "Make sure every sql statment fits the format of: UPDATE tablename SET column2 = \"value2\", column3 = \"value3\",...... WHERE column1 = \"value1\" ||<br>";
            echo "(column1 should be an id or some other unique value that can identify the row)<br>";
        }
        echo "Make sure there is a || at the end of each sql statment, then the next one after starts on a new line (very last line does not need to have || at the end)</p>";
        //display the created sql strings into a textarea
        //allows user to look over and make changes if neccessary before running the sql commands
        echo '<textarea id=sql_text name=sql_text cols=23 rows=50 wrap=hard>';
        for ($i = 0; $i < sizeof($sql_strings); $i++) {
            if($i == sizeof($sql_strings) - 1) {
                echo $sql_strings[$i];
            } else {
                echo $sql_strings[$i] . "||\n"; //used as a delimeter between each sql string
            }
        }
        echo '</textarea>';
        echo "<br><br><input type=submit name=sql_submit id=sql_submit value='Submit Sql' />";
        echo "</form>";
    } else { //if csv columns don't match table columns
        //show error, do not show form or create form
        echo "<p class=error>Error: Column names do not match the column names in table $table<br></p>";
        echo "<p class=error>Column names supplied by csv are: $cols</p>";
    }
}
?>

</body>
</html>