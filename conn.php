<?php
putenv("hostname=localhost");
putenv("sql_user=username");
putenv("sql_pass=password");
putenv("db=db_name");

//sql conn string
$conn = mysqli_connect(getenv("hostname"), getenv("sql_user"), getenv("sql_pass"), getenv("db"));
?>