<?php

define('DB_SERVER', 'sql.freedb.tech');
define('DB_USERNAME', 'freedb_examsecureadmin'); 
define('DB_PASSWORD', '&6Qa!aF$vw@ETDy');      
define('DB_NAME', 'freedb_exam_secure');


$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);


if ($conn->connect_error) {
    die("Error:" . $conn->connect_error);
}
