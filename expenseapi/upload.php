<?php
session_start();

header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'connect.php';

try {
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true); // Create directory with proper permissions
    }

    $imageName = null;

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $originalFileName = basename($_FILES['image']['name']);

        // Sanitize the file name to prevent directory traversal or invalid characters
        $safeFileName = preg_replace('/[^a-zA-Z0-9\.\-_]/', '_', $originalFileName);
        $imageName = $safeFileName;
        $targetFilePath = $uploadDir . $imageName;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
            throw new Exception('Failed to upload image');
        }
    } elseif (isset($_FILES['image']['error']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        throw new Exception('File upload error: ' . $_FILES['image']['error']);
    }

    // Get other expense data
    $amount = mysqli_real_escape_string($con, trim($_POST['amount']));
    $description = mysqli_real_escape_string($con, trim($_POST['description']));
    $expense_date = mysqli_real_escape_string($con, trim($_POST['expense_date']));
    $user_id = 1; // Replace with actual user ID from session or authentication

    // Validate required fields
    if (empty($amount) || !is_numeric($amount)) {
        throw new Exception('Amount is required and must be numeric');
    }

    if (empty($expense_date)) {
        throw new Exception('Expense date is required');
    }

    // Insert into the database
    $sql = "INSERT INTO expenses (user_id, amount, description, expense_date, imageName) 
            VALUES ('$user_id', '$amount', '$description', '$expense_date', '$imageName')";

    if (!mysqli_query($con, $sql)) {
        throw new Exception('Failed to save expense: ' . mysqli_error($con));
    }

    $id = mysqli_insert_id($con);
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Expense created successfully',
        'data' => [
            'id' => $id,
            'amount' => $amount,
            'description' => $description,
            'expense_date' => $expense_date,
            'imageName' => $imageName
        ]
    ]);
} catch (Exception $e) {
    error_log('Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>