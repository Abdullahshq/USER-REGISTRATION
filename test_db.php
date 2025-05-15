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

// Get the connection string from Azure App Service Configuration
$connString = "Server=tcp:abddatabase.database.windows.net,1433;Initial Catalog=abddb;Encrypt=True;TrustServerCertificate=False;Connection Timeout=30;Authentication=Active Directory Default;";

if (!$connString) {
    die("Error: Please set AZURE_SQL_CONNECTION_STRING in Azure App Service Configuration");
}

try {
    // Basic PDO connection with the connection string
    $options = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 30
    );
    
    echo "<h3>Attempting Connection...</h3>";
    $startTime = microtime(true);
    
    $conn = new PDO($connString, null, null, $options);
    
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
    
    echo "<br><strong>Troubleshooting Steps:</strong><br>";
    echo "1. Verify your connection string in Azure App Service Configuration<br>";
    echo "2. Make sure your Azure SQL Database server is running<br>";
    echo "3. Check if your IP is allowed in Azure SQL Database firewall rules<br>";
    echo "4. Verify the username and password in your connection string<br>";
}
?> 
