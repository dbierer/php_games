<?php
use FileCMS\Common\File\Upload;
use FileCMS\Common\Security\Profile;
use FileCMS\Common\Generic\Messages;
// process contact post (if any)
// $OBJ == calling instance (usually from /public/index.php)
if (!empty($OBJ)) {
    $uri    = $OBJ->uri;
    $config = $OBJ->config;
}
// check to see if authenticated
$upload = new Upload($config);
if (Profile::verify() === FALSE) {
    Profile::logout();
    (Messages::getInstance())->addMessage(Profile::PROFILE_AUTH_UNABLE);
    $upload->errors[] = Profile::PROFILE_AUTH_UNABLE;
    $response = $upload->getErrorResponse();
} else {
    $response = $upload->handle('upload');
}
header('Content-type: application/json');
echo json_encode($response);
