<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// files needed to connect to database
include_once '../config/database.php';
include_once '../objects/jobs.php';
 
// get database connection
$database = new Database();
$db = $database->getConnection();
 
// instantiate user object
$jobs = new Jobs($db);
 
// get posted data
$data = json_decode(file_get_contents("php://input"));
 
// set product property values
$jobs->job_name = $data->job_name;
$jobs->job_description = $data->job_description;
$jobs->job_client_id = $data->job_client_id;
$jobs->job_start_date = $data->job_start_date;
 
// generate json web token
include_once '../config/core.php';
include_once '../libs/php-jwt-master/src/BeforeValidException.php';
include_once '../libs/php-jwt-master/src/ExpiredException.php';
include_once '../libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;

//get jwt
$jwt=isset($data->jwt) ? $data->jwt : "";
 
// check if email exists and if password is correct
if($jwt){

    try {
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        
        if (!empty($jobs->job_name) &&
                !empty($jobs->job_description) &&
                !empty($jobs->job_client_id) &&
                !empty($jobs->job_start_date) &&
                $jobs->create()
            ) 
        {
            // set response code
            http_response_code(200);
        
            // display message: job was created
            echo json_encode(array("message" => "Job created successfully"));
        
        } else {
            // set response code
            http_response_code(400);
        
            // display message: unable to create user
            echo json_encode(array("message" => "Unable to create job."));
        }

    } catch (Exception $e){
    
        // set response code
        http_response_code(401);
    
        // tell the user access denied  & show error message
        echo json_encode(array(
            "message" => "Access denied.",
            "error" => $e->getMessage()
        ));
    }
    
} else {
 
    // set response code
    http_response_code(401);
 
    // tell the user login failed
    echo json_encode(array("message" => "You are not logged in."));
}
?>