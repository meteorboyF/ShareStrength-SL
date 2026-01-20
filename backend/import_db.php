<?php
// Import database from SQL file
$host = 'localhost';
$dbname = 'togetheraplus';
$username = 'root';
$password = '1234';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Read SQL file
    $sql = file_get_contents(__DIR__ . '/../database/togetheraplusdatabase.sql');
    
    // Execute SQL
    $pdo->exec($sql);
    
    echo "Database imported successfully!\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
