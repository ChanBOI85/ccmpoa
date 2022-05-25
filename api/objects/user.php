<?php
// 'user' object
class User{
 
    // database connection and table name
    private $conn;
    private $table_name = "users";
 
    // object properties
    public $id;
    public $user_id;
    public $name;
    public $companyName;
    public $phoneNumber;
    public $telephoneNumber;
    public $address_1;
    public $address_2;
    public $zip_code_1;
    public $zip_code_2;
    public $website;
    public $fax;
    public $email;
    public $password;
    public $role;
 
    // constructor
    public function __construct($db){
        $this->conn = $db;
    }

    // read all users
    function readAllUsers() {
        $query = "SELECT users.id as id, users.name as name, users.email as email, company_info.company_name as companyName, users.status as status FROM users LEFT JOIN company_info ON users.id = company_info.user_id";

        // prepare the query
        $stmt = $this->conn->prepare($query);

        if ($stmt->execute()) {
            return $stmt;
        }
    }

    function readActiveUsers() {
        $query = "SELECT users.id as id, users.name as name, users.email as email, company_info.company_name as companyName FROM users LEFT JOIN company_info ON users.id = company_info.user_id WHERE users.status = 0 AND users.role != 'admin'";

        // prepare the query
        $stmt = $this->conn->prepare($query);

        if ($stmt->execute()) {
            return $stmt;
        }
    }

    function readSingleActiveUser() {
        $query = "SELECT users.id as id, users.name as name, users.email as email, company_info.company_name as companyName, company_info.address_1 as address, company_info.phone_number as phone, company_info.telephone_number as telephone, company_info.website as website FROM users LEFT JOIN company_info ON users.id = company_info.user_id WHERE users.status = 0 AND users.id = :job_client_id";

        // prepare the query
        $stmt = $this->conn->prepare($query);
        $this->id  = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(':job_client_id', $this->id);

        if ($stmt->execute()) {
            return $stmt;
        }
    }

    function readInactiveUsers() {
        $query = "SELECT users.id as id, users.name as name, users.email as email, company_info.company_name as companyName FROM users LEFT JOIN company_info ON users.id = company_info.user_id WHERE users.status = 1";

        // prepare the query
        $stmt = $this->conn->prepare($query);

        if ($stmt->execute()) {
            return $stmt;
        }
    }
 
    // create new user record
    function create(){
    
        // insert query
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    name = :name,
                    email = :email,
                    password = :password,
                    role = 'client',
                    status = 1";

        // prepare the query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email     = htmlspecialchars(strip_tags($this->email));
        $this->password  = htmlspecialchars(strip_tags($this->password));
    
        // bind the values
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':email', $this->email);
    
        // hash the password before saving to database
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        $stmt->bindParam(':password', $password_hash);
    
        // execute the query, also check if query was successful
        if($stmt->execute()){
            $this->user_id = $this->conn->lastInsertId();

            $query2 = "INSERT INTO company_info " . "
                SET
                    user_id = :user_id,
                    company_name = :companyName,
                    address_1 = :address_1,
                    address_2 = :address_2,
                    zip_code_1 = :zip_code_1,
                    zip_code_2 = :zip_code_2,
                    phone_number = :phone_number,
                    telephone_number = :telephone_number,
                    website = :website,
                    fax = :fax";
            
            // prepare query
            $stmt2 = $this->conn->prepare($query2);

            // sanitize
            $this->user_id      = htmlspecialchars(strip_tags($this->user_id));
            $this->companyName     = htmlspecialchars(strip_tags($this->companyName));
            $this->address_1  = htmlspecialchars(strip_tags($this->address_1));
            $this->address_2      = htmlspecialchars(strip_tags($this->address_2));
            $this->zip_code_1     = htmlspecialchars(strip_tags($this->zip_code_1));
            $this->zip_code_2  = htmlspecialchars(strip_tags($this->zip_code_2));
            $this->phone_number      = htmlspecialchars(strip_tags($this->phone_number));
            $this->telephone_number     = htmlspecialchars(strip_tags($this->telephone_number));
            $this->website  = htmlspecialchars(strip_tags($this->website));
            $this->fax  = htmlspecialchars(strip_tags($this->fax));
        
            // bind the values
            $stmt2->bindParam(':user_id', $this->user_id);
            $stmt2->bindParam(':companyName', $this->companyName);
            $stmt2->bindParam(':address_1', $this->address_1);
            $stmt2->bindParam(':address_2', $this->address_2);
            $stmt2->bindParam(':zip_code_1', $this->zip_code_1);
            $stmt2->bindParam(':zip_code_2', $this->zip_code_2);
            $stmt2->bindParam(':phone_number', $this->phone_number);
            $stmt2->bindParam(':telephone_number', $this->telephone_number);
            $stmt2->bindParam(':website', $this->website);
            $stmt2->bindParam(':fax', $this->fax);

            if ($stmt2->execute()) {
                return true;
            }
        }
    
        return false;
    }


    // approve user
    public function approveUser(){
    
        // if no posted password, do not update the password
        $query = "UPDATE " . $this->table_name . "
                SET
                    status = 0
                WHERE id = :id";
    
        // prepare the query
        $stmt = $this->conn->prepare($query);
    
        // unique ID of record to be edited
        $stmt->bindParam(':id', $this->id);
    
        // execute the query
        if($stmt->execute()){
            return true;
        }
    
        return false;
    }
    
    // check if given email exist in the database
    function emailExists(){
    
        // query to check if email exists
        $query = "SELECT id, name, password, role
                FROM " . $this->table_name . "
                WHERE email = ?
                LIMIT 0,1";
    
        // prepare the query
        $stmt = $this->conn->prepare( $query );
    
        // sanitize
        $this->email=htmlspecialchars(strip_tags($this->email));
    
        // bind given email value
        $stmt->bindParam(1, $this->email);
    
        // execute the query
        $stmt->execute();
    
        // get number of rows
        $num = $stmt->rowCount();
    
        // if email exists, assign values to object properties for easy access and use for php sessions
        if($num>0){
    
            // get record details / values
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
            // assign values to object properties
            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->password = $row['password'];
            $this->role = $row['role'];
    
            // return true because email exists in the database
            return true;
        }
    
        // return false if email does not exist in the database
        return false;
    }


    // check if given email exist in the database
    function checkStatus(){
    
        // query to check if email exists
        $query = "SELECT status
                FROM " . $this->table_name . "
                WHERE email = ?
                LIMIT 0,1";
    
        // prepare the query
        $stmt = $this->conn->prepare( $query );
    
        // sanitize
        $this->email=htmlspecialchars(strip_tags($this->email));
    
        // bind given email value
        $stmt->bindParam(1, $this->email);
    
        // execute the query
        $stmt->execute();
    
        // get number of rows
        $num = $stmt->rowCount();
    
        // if email exists, assign values to object properties for easy access and use for php sessions
        if($num>0){
    
            // get record details / values
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($row['status'] == 1) {
                return false;
            } else {
                return true;
            }
        }
    
        // return false if email does not exist in the database
        return false;
    }
    
    // update a user record
    public function update(){
    
        // if password needs to be updated
        $password_set=!empty($this->password) ? "password = :password" : "";
    
        // if no posted password, do not update the password
        $query = "UPDATE " . $this->table_name . "
                SET
                    name = :name,
                    email = :email,
                    {$password_set}
                WHERE id = :id";
    
        // prepare the query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
    
        // bind the values from the form
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':email', $this->email);
    
        // hash the password before saving to database
        if(!empty($this->password)){
            $this->password=htmlspecialchars(strip_tags($this->password));
            $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
            $stmt->bindParam(':password', $password_hash);
        }
    
        // unique ID of record to be edited
        $stmt->bindParam(':id', $this->id);
    
        // execute the query
        if($stmt->execute()){
            return true;
        }
    
        return false;
    }
}