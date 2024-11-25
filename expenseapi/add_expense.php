<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'connect.php';

// Get posted data
$postdata = file_get_contents("php://input");

if (isset($postdata) && !empty($postdata)) {
    // Decode the JSON data
    $request = json_decode($postdata);

    if ($request === null) {
        error_log('JSON decode failed: ' . json_last_error_msg());
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON data']);
        exit();
    }

    // Extract and sanitize data
    $amount = mysqli_real_escape_string($con, trim($request->amount));
    $description = mysqli_real_escape_string($con, trim($request->description));
    $expense_date = mysqli_real_escape_string($con, trim($request->expense_date));

    // Validate required fields
    if (empty($amount) || !is_numeric($amount)) {
        http_response_code(400);
        echo json_encode(['error' => 'Amount is required and must be numeric']);
        exit();
    }

    if (empty($expense_date)) {
        http_response_code(400);
        echo json_encode(['error' => 'Expense date is required']);
        exit();
    }

    // Insert into the database
    $sql = "INSERT INTO expenses (amount, description, expense_date) 
            VALUES ('$amount', '$description', '$expense_date')";

    if (mysqli_query($con, $sql)) {
        $id = mysqli_insert_id($con);
        $expense = [
            'id' => $id,
            'amount' => $amount,
            'description' => $description,
            'expense_date' => $expense_date,
        ];
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Expense created successfully',
            'data' => $expense
        ]);
    } else {
        error_log('MySQL Error: ' . mysqli_error($con));
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Expense creation failed: ' . mysqli_error($con)
        ]);
    }
} else {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'No data provided'
    ]);
}
?>