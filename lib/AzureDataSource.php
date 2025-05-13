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
        $this->connectionString = getenv('AZURE_SQL_CONNECTION_STRING');
        if (empty($this->connectionString)) {
            throw new \Exception('Database connection string not found in environment variables');
        }
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
            // Convert ADO.NET connection string to PDO format
            $connString = str_replace('Server=', 'sqlsrv:Server=', $this->connectionString);
            // Debug output
            error_log("Connection string being used: " . $connString);
            
            $conn = new \PDO($connString, null, null, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_TIMEOUT => 30
            ]);
            return $conn;
        } catch (\PDOException $e) {
            error_log("Connection Error Details: " . $e->getMessage());
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