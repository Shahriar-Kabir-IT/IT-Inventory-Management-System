<?php
// Database connection
$servername = "localhost";
$username = "root"; // default XAMPP username
$password = "";     // default XAMPP password
$dbname = "it_inventory";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Open the CSV file
if (($handle = fopen("test.csv", "r")) !== FALSE) {
    // Skip the first row (headers)
    fgetcsv($handle, 1000, ",");
    
    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO assets (asset_id, asset_name, category, brand, model, serial_number, status, location, assigned_to, department, purchase_date, purchase_price, warranty_expiry, last_maintenance, priority, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    // Bind parameters
    $stmt->bind_param("ssssssssssssdsss", 
        $asset_id, $asset_name, $category, $brand, $model, $serial_number, 
        $status, $location, $assigned_to, $department, $purchase_date, 
        $purchase_price, $warranty_expiry, $last_maintenance, $priority, $notes);
    
    // Read each line of the CSV
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        // Assign values from CSV to variables
        $asset_id = empty($data[0]) ? NULL : $data[0];
        $asset_name = $data[1];
        $category = $data[2];
        $brand = $data[3];
        $model = $data[4];
        $serial_number = $data[5];
        $status = $data[6];
        $location = $data[7];
        $assigned_to = $data[8];
        $department = $data[9];
        $purchase_date = empty($data[10]) ? NULL : $data[10];
        $purchase_price = empty($data[11]) ? 0 : $data[11];
        $warranty_expiry = empty($data[12]) ? NULL : $data[12];
        $last_maintenance = empty($data[13]) ? NULL : $data[13];
        $priority = empty($data[14]) ? 'Medium' : $data[14];
        $notes = $data[15];
        
        // Execute the statement
        $stmt->execute();
    }
    
    // Close the statement and file
    $stmt->close();
    fclose($handle);
    
    echo "Data imported successfully!";
} else {
    echo "Error opening CSV file";
}

$conn->close();
?>