<?php
// 'jobs' object
class Parts{
 
    // database connection and table name
    private $conn;
    private $table_name = "parts";
 
    // object properties
    public $id;
    public $label_description;
    public $date_of_production;
    public $quantity;
    public $production_cost;
    public $sales_price;
    public $status;
    public $job_id;
 
    // constructor
    public function __construct($db){
        $this->conn = $db;
    }
 
    // create new job record
    function create(){
    
        // insert query
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    job_id = :job_id,
                    label_description = :label_description,
                    date_of_production = :date_of_production,
                    quantity = :quantity,
                    production_cost = :production_cost,
                    sales_price = :sales_price,
                    status = 'Pending'
                ";

        // prepare the query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->job_id      = htmlspecialchars(strip_tags($this->job_id));
        $this->label_description      = htmlspecialchars(strip_tags($this->label_description));
        $this->date_of_production     = htmlspecialchars(strip_tags($this->date_of_production));
        $this->quantity  = htmlspecialchars(strip_tags($this->quantity));
        $this->production_cost     = htmlspecialchars(strip_tags($this->production_cost));
        $this->sales_price  = htmlspecialchars(strip_tags($this->sales_price));
    
        // bind the values
        $stmt->bindParam(':job_id', $this->job_id);
        $stmt->bindParam(':label_description', $this->label_description);
        $stmt->bindParam(':date_of_production', $this->date_of_production);
        $stmt->bindParam(':quantity', $this->quantity);
        $stmt->bindParam(':production_cost', $this->production_cost);
        $stmt->bindParam(':sales_price', $this->sales_price);
    
        // execute the query, also check if query was successful
        if($stmt->execute()){
            return true;
        }
    
        return false;
    }

    //read all job details
    function readAll()
    {   
        $query = "SELECT * From parts WHERE job_id = :job_id ORDER BY id DESC";
        // prepare the query
        $stmt = $this->conn->prepare($query);
        $this->job_id  = htmlspecialchars(strip_tags($this->job_id));
        $stmt->bindParam(':job_id', $this->job_id);

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
                    label_description = :label_description,
                    date_of_production = :date_of_production,
                    quantity = :quantity,
                    production_cost = :production_cost,
                    sales_price = :sales_price
                WHERE id = :id";
    
        // prepare the query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->label_description      = htmlspecialchars(strip_tags($this->label_description));
        $this->date_of_production     = htmlspecialchars(strip_tags($this->date_of_production));
        $this->quantity  = htmlspecialchars(strip_tags($this->quantity));
        $this->production_cost     = htmlspecialchars(strip_tags($this->production_cost));
        $this->sales_price  = htmlspecialchars(strip_tags($this->sales_price));

        // bind the values from the form
        $stmt->bindParam(':label_description', $this->label_description);
        $stmt->bindParam(':date_of_production', $this->date_of_production);
        $stmt->bindParam(':quantity', $this->quantity);
        $stmt->bindParam(':production_cost', $this->production_cost);
        $stmt->bindParam(':sales_price', $this->sales_price);
    
        // unique ID of record to be edited
        $stmt->bindParam(':id', $this->id);
    
        // execute the query
        if($stmt->execute()){
            return true;
        }
    
        return false;
    }


    // update a user record
    public function deletePart(){
    
        // if no posted password, do not update the password
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
    
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
    public function updateStatus(){
    
        // if no posted password, do not update the password
        $query = "UPDATE " . $this->table_name . "
                SET
                    status = :status
                WHERE id = :id";
    
        // prepare the query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->status      = htmlspecialchars(strip_tags($this->status));

        // bind the values from the form
        $stmt->bindParam(':status', $this->status);
    
        // unique ID of record to be edited
        $stmt->bindParam(':id', $this->id);
    
        // execute the query
        if($stmt->execute()){
            return true;
        }
    
        return false;
    }

}