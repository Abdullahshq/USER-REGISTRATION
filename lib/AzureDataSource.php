<?php
namespace Phppot;

/**
 * Azure SQL Database connection class
 * Uses PDO for secure database operations
 */
class AzureDataSource
{
    private $conn;
    private $connectionString;

    function __construct()
    {
        $this->connectionString = "Server=tcp:appdbhtml.database.windows.net,1433;Initial Catalog=abddb;Persist Security Info=False;User ID={your_username};Password={your_password};MultipleActiveResultSets=False;Encrypt=True;TrustServerCertificate=False;Authentication=\"Active Directory Password\";";
        $this->conn = $this->getConnection();
    }

    /**
     * Get PDO connection instance
     * 
     * @return \PDO
     */
    public function getConnection()
    {
        try {
            $conn = new \PDO($this->connectionString);
            $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (\PDOException $e) {
            exit("Connection Error: " . $e->getMessage());
        }
    }

    /**
     * Execute a select query
     * 
     * @param string $query
     * @param array $params
     * @return array
     */
    public function select($query, $params = array())
    {
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            exit("Query Error: " . $e->getMessage());
        }
    }

    /**
     * Execute an insert query
     * 
     * @param string $query
     * @param array $params
     * @return int
     */
    public function insert($query, $params = array())
    {
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $this->conn->lastInsertId();
        } catch (\PDOException $e) {
            exit("Insert Error: " . $e->getMessage());
        }
    }

    /**
     * Execute an update/delete query
     * 
     * @param string $query
     * @param array $params
     * @return int
     */
    public function execute($query, $params = array())
    {
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (\PDOException $e) {
            exit("Execute Error: " . $e->getMessage());
        }
    }
} 