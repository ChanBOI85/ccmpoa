<?php
// 'jobs' object
class Jobs{
 
    // database connection and table name
    private $conn;
    private $table_name = "jobs";
 
    // object properties
    public $id;
    public $user_id;
    public $job_name;
    public $job_description;
    public $job_start_date;
    public $job_client_id;
 
    // constructor
    public function __construct($db){
        $this->conn = $db;
    }
 
    // create new job record
    function create(){
    
        // insert query
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    job_name = :job_name,
                    job_description = :job_description,
                    job_client_id = :job_client_id,
                    job_start_date = :job_start_date,
                    job_status = 'active'";

        // prepare the query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->job_name      = htmlspecialchars(strip_tags($this->job_name));
        $this->job_description     = htmlspecialchars(strip_tags($this->job_description));
        $this->job_client_id  = htmlspecialchars(strip_tags($this->job_client_id));
    
        // bind the values
        $stmt->bindParam(':job_name', $this->job_name);
        $stmt->bindParam(':job_description', $this->job_description);
        $stmt->bindParam(':job_client_id', $this->job_client_id);
        $stmt->bindParam(':job_start_date', $this->job_start_date);
    
        // execute the query, also check if query was successful
        if($stmt->execute()){
            return true;
        }
    
        return false;
    }

    //read all job details
    function readAll()
    {   
        $query = "SELECT * From jobs WHERE job_status = 'active' ORDER BY id DESC";
        // prepare the query
        $stmt = $this->conn->prepare($query);

        if ($stmt->execute()) {
            return $stmt;
        }
    }


    //read all job details
    function readSingleJob()
    {   
        $query = "SELECT * From jobs WHERE job_status = 'active' AND id = :id ORDER BY id DESC";
        // prepare the query
        $stmt = $this->conn->prepare($query);

        $this->id  = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return $stmt;
        }
    }

    //read all archive job details
    function readArchiveJobs()
    {   
        $query = "SELECT * From jobs WHERE job_status = 'archive' ORDER BY id DESC";
        // prepare the query
        $stmt = $this->conn->prepare($query);

        if ($stmt->execute()) {
            return $stmt;
        }
    }

    // Read all Trash Jobs
    function readTrashJobs()
    {   
        $query = "SELECT * From jobs WHERE job_status = 'trash' ORDER BY id DESC";
        // prepare the query
        $stmt = $this->conn->prepare($query);

        if ($stmt->execute()) {
            return $stmt;
        }
    }

    // Read dall client jobs
    function readClientJob() {
        $query = "SELECT * FROM jobs WHERE job_status = 'active' AND job_client_id = :job_client_id ORDER BY id DESC";
        
        // prepare the query
        $stmt = $this->conn->prepare($query);

        $this->job_client_id  = htmlspecialchars(strip_tags($this->job_client_id));
        $stmt->bindParam(':job_client_id', $this->job_client_id);

        if ($stmt->execute()) {
            return $stmt;
        }
    }
    
    // update a user record
    public function update(){
    
        // if no posted password, do not update the password
        $query = "UPDATE " . $this->table_name . "
                SET
                    job_name = :job_name,
                    job_description = :job_description,
                    job_client_id = :job_client_id,
                    job_start_date = :job_start_date
                WHERE id = :id";
    
        // prepare the query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->job_name = htmlspecialchars(strip_tags($this->job_name));
        $this->job_description = htmlspecialchars(strip_tags($this->job_description));
        $this->job_client_id = htmlspecialchars(strip_tags($this->job_client_id));
        $this->job_start_date = htmlspecialchars(strip_tags($this->job_start_date));
    
        // bind the values from the form
        $stmt->bindParam(':job_name', $this->job_name);
        $stmt->bindParam(':job_description', $this->job_description);
        $stmt->bindParam(':job_client_id', $this->job_client_id);
        $stmt->bindParam(':job_start_date', $this->job_start_date);
    
        // unique ID of record to be edited
        $stmt->bindParam(':id', $this->id);
    
        // execute the query
        if($stmt->execute()){
            return true;
        }
    
        return false;
    }


    // update a user record
    public function moveToTrash(){
    
        // if no posted password, do not update the password
        $query = "UPDATE " . $this->table_name . "
                SET
                    job_status = 'trash'
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

    // update a user record
    public function archiveJob(){
    
        // if no posted password, do not update the password
        $query = "UPDATE " . $this->table_name . "
                SET
                    job_status = 'archive'
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

    // update a user record
    public function restoreJob(){
    
        // if no posted password, do not update the password
        $query = "UPDATE " . $this->table_name . "
                SET
                    job_status = 'active'
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
}