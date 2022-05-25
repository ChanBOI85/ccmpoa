<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// database connection will be here
// files needed to connect to database
include_once 'config/database.php';
include_once 'objects/user.php';
 
// get database connection
$database = new Database();
$db = $database->getConnection();
 
// instantiate product object
$user = new User($db);
 
// get posted data
$data = json_decode(file_get_contents("php://input"));
 
// set product property values
$user->name = $data->name;
$user->email = $data->email;
$user->password = $data->password;

$user->companyName = $data->companyName;
$user->address_1 = $data->address_1;
$user->address_2 = $data->address_2;
$user->zip_code_1 = $data->zip_code_1;
$user->zip_code_2 = $data->zip_code_2;
$user->phone_number = $data->phone_number;
$user->telephone_number = $data->telephone_number;
$user->website = $data->website;
$user->fax = $data->fax;
 
// create the user
if(
    !empty($user->name) &&
    !empty($user->email) &&
    !empty($user->password) &&
    !empty($user->companyName) &&
    !empty($user->address_1)  &&
    !empty($user->zip_code_1) &&
    !empty($user->phone_number) &&
    !empty($user->telephone_number) &&
    !empty($user->website) &&
    $user->create()
){
 
    // set response code
    http_response_code(200);
 
    // display message: user was created
    echo json_encode(array("message" => "User was created. You can now login"));
}
 
// message if unable to create user
else{
 
    // set response code
    http_response_code(400);
 
    // display message: unable to create user
    echo json_encode(array("message" => "Unable to create user."));
}
?>