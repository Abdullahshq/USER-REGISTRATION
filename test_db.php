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
try {
    $conn = new PDO(
        "sqlsrv:Server=tcp:dtbase1.database.windows.net,1433;Database=db;Encrypt=True;TrustServerCertificate=False;Connection Timeout=30",
        "abdullah",
        getenv('AZURE_SQL_CONNECTION_STRING_PASSWORD'),
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );
    echo "Connection successful!";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    // Add more detailed error information
    error_log("Connection Error Details: " . $e->getMessage());
    error_log("Server: dtbase1.database.windows.net");
    error_log("Database: db");
    error_log("Username: abdullah");
}
?> 