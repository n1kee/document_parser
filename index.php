<?php

require_once './APP.php';
require_once './DB.php';

if (isset($_FILES['file'])) {
    require_once './Controller.php';

    uploadFile($_FILES['file']['tmp_name']);
} else {

    require_once './View.php';
}
