<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'connect.php';

try {
    // Check if data is sent via POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Invalid request method']);
        exit();
    }

    // Initialize variables
    $amount = $description = $expense_date = $imageName = null;
    $uploadDir = 'uploads/';
    
    // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $originalFileName = basename($_FILES['image']['name']);
        $imageName = uniqid() . '_' . $originalFileName; // Generate a unique file name
        $targetFilePath = $uploadDir . $imageName;

        // Ensure the uploads directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Move uploaded file to target directory
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to upload image']);
            exit();
        }
    }

    // Get other data from POST
    $amount = mysqli_real_escape_string($con, trim($_POST['amount'] ?? ''));
    $description = mysqli_real_escape_string($con, trim($_POST['description'] ?? ''));
    $expense_date = mysqli_real_escape_string($con, trim($_POST['expense_date'] ?? ''));

    // Validate required fields
    if (empty($amount) || !is_numeric($amount)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Amount is required and must be numeric']);
        exit();
    }

    if (empty($expense_date)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Expense date is required']);
        exit();
    }

    // Insert data into the database
    $sql = "INSERT INTO expenses (amount, description, expense_date, imageName) 
            VALUES ('$amount', '$description', '$expense_date', '$imageName')";

    if (mysqli_query($con, $sql)) {
        $id = mysqli_insert_id($con);
        $expense = [
            'id' => $id,
            'amount' => $amount,
            'description' => $description,
            'expense_date' => $expense_date,
            'imageName' => $imageName
        ];
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Expense created successfully',
            'data' => $expense
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Expense creation failed: ' . mysqli_error($con)
        ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'An error occurred: ' . $e->getMessage()]);
}
?>