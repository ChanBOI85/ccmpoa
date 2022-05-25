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
    $parts->job_id = $data->id;
    // get jwt
    $jwt=isset($data->jwt) ? $data->jwt : "";
    
    // if jwt is not empty
    if($jwt){
    
        // if decode succeed, show user details
        try {
    
            // decode jwt
            $decoded = JWT::decode($jwt, $key, array('HS256'));


            $stmt = $parts->readAll();
            $parts_arr = array();

            $parts_arr["parts"] = array();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $parts_item = array(
                    "id" => $row['id'],
                    "label_description" =>  $row['label_description'],
                    "date_of_production" =>  $row['date_of_production'],
                    "quantity" =>  $row['quantity'],
                    "production_cost" =>  $row['production_cost'],
                    "sales_price" =>  $row['sales_price'],
                    "status" => $row['status']
                );
        
                array_push($parts_arr["parts"], $parts_item);
            }

            // set response code
            http_response_code(200);
                
            // show jobs data in json format
            echo json_encode($parts_arr);

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
        echo json_encode(array("message" => "Access denied."));
    }
?>