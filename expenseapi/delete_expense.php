<?php
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

$expense_id = ($_GET['id'] ?? '');

if(!empty($expense_id))
{

  $expense_id = mysqli_real_escape_string($con, (int)$expense_id);
  
  $sql = "DELETE FROM `expenses` WHERE `id` = '{$expense_id}' LIMIT 1";

  if(mysqli_query($con, $sql))
  {
    http_response_code(204);
  }
  else
  {
    http_response_code(422);
  }
}
else
{
    http_response_code(400);
}
?> 