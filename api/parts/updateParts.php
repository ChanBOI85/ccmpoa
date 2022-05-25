<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// required to encode json web token
include_once '../config/core.php';
include_once '../libs/php-jwt-master/src/BeforeValidException.php';
include_once '../libs/php-jwt-master/src/ExpiredException.php';
include_once '../libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;
 
// files needed to connect to database
include_once '../config/database.php';
include_once '../objects/parts.php';
 
// get database connection
$database = new Database();
$db = $database->getConnection();
 
// instantiate user object
$parts = new Parts($db);
 
// get posted data
$data = json_decode(file_get_contents("php://input"));
 
// get jwt
$jwt=isset($data->jwt) ? $data->jwt : "";
 
// if jwt is not empty
if($jwt){
 
    // if decode succeed, show user details
    try {
 
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));
 
        // set user property values
        $parts->label_description = $data->label_description;
        $parts->date_of_production = $data->date_of_production;
        $parts->quantity = $data->quantity;
        $parts->production_cost = $data->production_cost;
        $parts->sales_price = $data->sales_price;
        $parts->id = $data->id;
        
        
        // update the user record
        if($parts->update()){
            
            // set response code
            http_response_code(200);
            
            // show error message
            echo json_encode(array("message" => "Item Updated."));
        }
        
        // message if unable to update user
        else{
            // set response code
            http_response_code(401);
        
            // show error message
            echo json_encode(array("message" => "Unable to update item."));
        }
    } catch (Exception $e){
    
        // set response code
        http_response_code(401);
    
        // show error message
        echo json_encode(array(
            "message" => "Access denied.",
            "error" => $e->getMessage()
        ));
    }
} else {
 
    // set response code
    http_response_code(401);
 
    // tell the user access denied
    echo json_encode(array("message" => "Access deniedd."));
}
?>