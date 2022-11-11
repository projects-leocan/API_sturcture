<?php

//require '.././libs/Slim/Slim.php';
require '../vendor/autoload.php';
include '../include/DbHandler.php';
include '../models/Messages.php';
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");

$config = ['settings' => [
        'addContentLengthHeader' => false,
        'displayErrorDetails' => true
]];
    
$app = new \Slim\App($config);

function verifyRequiredParams($required_fields) {
        $error = false;
        $error_fields = "";
        $request_params = array();
        $request_params = $_REQUEST;

        foreach ($required_fields as $field) {
            if (!isset($request_params[$field])) {
                $error = true;
                $error_fields .= $field . ', ';
            }
        }

        if ($error) {
            $result = array();
            $result = array(
                'success' => false,
                'message' => 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing'
            );
            return $result;
        }
    }
  
$app->get('/fetchAllStudents',function($request, $response, $args) use ($app) {      
        $db = new DbHandler();
        $result = $db->fetchAllStudents();
        return $response->withJson($result);
});

$app->get('/getStudent',function($request, $response, $args) use ($app) {   
        $result = verifyRequiredParams(array('id'));   
        if($result == null){
                $db = new DbHandler();
                $id = $request->getParam('id');
                $result = $db->getStudent($id);
        }
        return $response->withJson($result);
});

$app->get('/getStudentImages',function($request, $response, $args) use ($app) {   
        $result = verifyRequiredParams(array('id'));   
        if($result == null){
                $db = new DbHandler();
                $id = $request->getParam('id');
                $result = $db->getStudentImages($id);
        }
        return $response->withJson($result);
});

$app->post('/addStudent', function($request, $response, $args) use ($app) {
        $result = verifyRequiredParams(array('name', 'dept', 'mobile'));   
        if($result == null){
                $db = new DbHandler();
                $name = $request->getParam('name');
                $dept = $request->getParam('dept');
                $mobile = $request->getParam('mobile');
                $result = $db->addStudent($name, $dept, $mobile);
        }
        return $response->withJson($result);
});
    
$app->post('/updateStudent', function($request, $response, $args) use ($app) {
        $result = verifyRequiredParams(array('id', 'name'));   
        if($result == null){
                $db = new DbHandler();
                $id = $request->getParam('id');
                $name = $request->getParam('name');
                $result = $db->updateStudent($id, $name);
        }
        return $response->withJson($result);
});
    
$app->post('/deleteStudent', function($request, $response, $args) use ($app) {
        $result = verifyRequiredParams(array('id'));   
        if($result == null){
                $db = new DbHandler();
                $id = $request->getParam('id');
                $result = $db->deleteStudent($id);
        }
        return $response->withJson($result);
});
    
$app->post('/UpdateProfilePic',function($request, $response, $args) use ($app) 
{
        $result = verifyRequiredParams(array('id'));   
        if($result == null){
	        $id = $request->getParam('id');
	        if (isset($_FILES['image']))
	        {
		        $photo = $_FILES["image"];
		        $is_photo_set=1;
	        }
	        else
		        $is_photo_set=0;

                $db = new DbHandler();
	        $result = $db->updateStudentProfilePic($id,$photo);
        }
	return $response->withJson($result);
});

$app->post('/uploadMultipleImage',function($request, $response, $args) use ($app) 
{
        $result = verifyRequiredParams(array('id'));   
        if($result == null){
	        $id = $request->getParam('id');
	        $is_photo_set = false;
	        $photos = null;
	        if (isset($_FILES['images'])) {
		        $is_photo_set = true;
		        $photos = $_FILES['images'];
	        }

                $db = new DbHandler();
	        $result = $db->uploadMultipleImage($id,$photos,$is_photo_set);
        }
	return $response->withJson($result);
});

$app->run();
?>
