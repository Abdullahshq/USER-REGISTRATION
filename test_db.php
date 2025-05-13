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
                ''
            ),
            $connString
        );
    
    error_log("PDO Connection string: " . $pdoConnString);
    
    $conn = new PDO(
        $pdoConnString,
        null,
        null,
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );
    echo "Connection successful!";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    error_log("Connection Error Details: " . $e->getMessage());
}
?> 