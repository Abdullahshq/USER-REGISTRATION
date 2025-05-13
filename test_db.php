<?php
// Display all loaded extensions
echo "<h2>Loaded PHP Extensions:</h2>";
echo "<pre>";
print_r(get_loaded_extensions());
echo "</pre>";

// Specifically check for SQL Server extensions
echo "<h2>SQL Server Extensions Status:</h2>";
echo "PDO SQL Server Extension: " . (extension_loaded('pdo_sqlsrv') ? 'Loaded' : 'Not Loaded') . "<br>";
echo "SQL Server Extension: " . (extension_loaded('sqlsrv') ? 'Loaded' : 'Not Loaded') . "<br>";

// Try to get PDO drivers
echo "<h2>Available PDO Drivers:</h2>";
echo "<pre>";
print_r(PDO::getAvailableDrivers());
echo "</pre>";

// Try to connect to the database
echo "<h2>Database Connection Test:</h2>";

// Get the connection string from environment variable
$connString = getenv('AZURE_SQL_CONNECTION_STRING');
error_log("Connection string is " . ($connString ? "set" : "not set"));

// Validate connection string
if (!$connString) {
    die("Error: AZURE_SQL_CONNECTION_STRING environment variable is not set");
}

// Extract server name for ping test
preg_match('/Server=tcp:([^,;]+)/', $connString, $matches);
$serverName = $matches[1] ?? null;

if ($serverName) {
    echo "<h3>Network Connectivity Test:</h3>";
    $pingResult = shell_exec("ping -c 1 " . escapeshellarg($serverName) . " 2>&1");
    echo "<pre>Ping result for $serverName:\n$pingResult</pre>";
}

try {
    // Convert ADO.NET connection string to PDO format
    $pdoConnString = "sqlsrv:Server=" . 
        str_replace(
            array(
                'Server=tcp:',
                'Initial Catalog=',
                ';Persist Security Info=False',
                ';MultipleActiveResultSets=False',
                ';Encrypt=True',
                ';TrustServerCertificate=False',
                ';Connection Timeout=30'
            ),
            array(
                '',
                'Database=',
                '',
                '',
                ';Encrypt=yes',
                ';TrustServerCertificate=no',
                ';ConnectionTimeout=60' // Increased timeout to 60 seconds
            ),
            $connString
        );
    
    error_log("PDO Connection string: " . $pdoConnString);
    
    // Set connection options
    $options = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 60, // Set PDO timeout to 60 seconds
        PDO::SQLSRV_ATTR_QUERY_TIMEOUT => 60 // Set query timeout to 60 seconds
    );
    
    echo "<h3>Attempting Connection...</h3>";
    $startTime = microtime(true);
    
    $conn = new PDO(
        $pdoConnString,
        null,
        null,
        $options
    );
    
    $endTime = microtime(true);
    $connectionTime = round(($endTime - $startTime) * 1000, 2);
    
    echo "Connection successful!<br>";
    echo "Connection established in {$connectionTime}ms<br>";
    
    // Test a simple query
    $stmt = $conn->query("SELECT @@VERSION as version");
    $version = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "SQL Server Version: " . $version['version'];
    
} catch (PDOException $e) {
    $errorCode = $e->getCode();
    $errorMessage = $e->getMessage();
    
    echo "<h3>Connection Error Details:</h3>";
    echo "Error Code: $errorCode<br>";
    echo "Error Message: $errorMessage<br>";
    
    // Log detailed error information
    error_log("Connection Error Details:");
    error_log("Error Code: $errorCode");
    error_log("Error Message: $errorMessage");
    error_log("Connection String (masked): " . preg_replace('/Password=[^;]+/', 'Password=*****', $pdoConnString));
    
    // Provide troubleshooting suggestions based on error code
    switch ($errorCode) {
        case 'HYT00':
            echo "<br><strong>Troubleshooting Suggestions:</strong><br>";
            echo "1. Check if the server is accessible from your network<br>";
            echo "2. Verify firewall rules allow connections to the database server<br>";
            echo "3. Ensure the server name and port are correct<br>";
            echo "4. Check if the database server is running and accepting connections<br>";
            break;
        case '28000':
            echo "<br><strong>Troubleshooting Suggestions:</strong><br>";
            echo "1. Verify the username and password are correct<br>";
            echo "2. Check if the user has permission to access the database<br>";
            break;
        default:
            echo "<br>Please check the error message above for specific details.";
    }
}
?> 