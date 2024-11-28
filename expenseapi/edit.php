<?php
session_start();

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require 'connect.php';

try {
    // Parse input
    $input = $_POST; // For handling form data with file uploads

    if (!$input) {
        throw new Exception('No data received.');
    }

    $expense_id = mysqli_real_escape_string($con, trim($input['id']));
    $amount = mysqli_real_escape_string($con, trim($input['amount']));
    $description = mysqli_real_escape_string($con, trim($input['description']));
    $expense_date = mysqli_real_escape_string($con, trim($input['expense_date']));

    if (empty($expense_id) || empty($amount) || empty($description) || empty($expense_date)) {
        throw new Exception('Missing required fields.');
    }

    $imageName = null;

    // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $originalFileName = basename($_FILES['image']['name']);
        $imageName = $originalFileName; 
        $targetFilePath = $uploadDir . $imageName;

       
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
            throw new Exception('Failed to upload image.');
        }
    }

  
    $sql = "UPDATE `expenses` 
            SET 
                `amount` = '$amount',
                `description` = '$description',
                `expense_date` = '$expense_date'";

 
    if ($imageName) {
        $sql .= ", `imageName` = '$imageName'";
    }

    $sql .= " WHERE `id` = '{$expense_id}'";

    // Execute query
    if (mysqli_query($con, $sql)) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Expense updated successfully',
        ]);
    } else {
        throw new Exception('Failed to update expense: ' . mysqli_error($con));
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>