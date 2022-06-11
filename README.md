# php_read_csv_create_sql
Read a csv file with php and take data from each row to contruct sql statments

## Notes:
  * CSV file must have the first row be column headers
  * columns in first row of csv must be same as table column names

## conn.php
  * example mysql connection file

## read_csv_style.php
  * css style file for the php pages

## read_csv.php
  * Initial "home" page of this project
  * User enters table name into textbox
  * User selects either Insert sql statments or Update sql statments
  * User selects a csv file
  * User pressed submit button to go to the next page

## create_sql_from_csv.php
  * Takes the data from user input in read_csv.php
  * Opens the csv file and loops through line by line
  * Saves the first row as column names
  * Every row after is treated as data
  * If user selected Insert sql in read_csv.php
    - creates sql statments in the format of: 'INSERT INTO tablename (col1,col2,col3...) VALUES("value1","value2","value3"...)';
  * If user selected Update sql in read_csv.php
    - this file assumes that the first column of the csv is the primary key or some other unique identifier of the row
    - creates sql statments in the format of: 'UPDATE tablename SET col2="value1",col3="value2"...' WHERE col1 = "value1";
  * Once sql strings are created for all rows in csv, they are displayed in a textbox
    - this will allow user to edit the sql strings if needed before final submission where the statmetns are actually executed
  * There is a "||" at the end of each sql string to mark the end and start between each sql statment
  * Table column names are compared with the column names in the csv
    - if all column names match the page continues as normal
    - if even one name does not match the page is stopped and an error message is displayed that the column names in the csv don't match the table user is trying to Insert or Update into.
  * If all column names match then user presses the submit sql button
  * All sql strings are then executed
  * Success or error message is then shown

## test_sql.sql
  * example sql file with a create table script
  * this table was used to test the insert and update statments
  * test csv files match this tables column names

## test_csv_insert.csv
  * example csv used to test insert statments

## test_csv_update.csv
  * example csv used to test update statments
