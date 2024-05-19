<?php
include '../shared/connect.php';

$title = 'Dashboard - Messages';

$select_users = $conn->prepare("
SELECT 
  users.id,
  users.first_name, 
  users.last_name, 
  users.email, 
  users.username, 
  users.phone_number
FROM 
  users
WHERE is_admin = ?
");
$select_users->execute([true]);

$table_body = '';

if ($select_users->rowCount() > 0) {
  while ($user = $select_users->fetch(PDO::FETCH_ASSOC)) {
    $table_body .= <<<HTML
      <tr>
        <td class="align-middle">{$user['id']}</td>
        <td class="align-middle">{$user['first_name']}</td>
        <td class="align-middle">{$user['last_name']}</td>
        <td class="align-middle">{$user['email']}</td>
        <td class="align-middle">{$user['username']}</td>
        <td class="align-middle">{$user['phone_number']}</td>
      </tr>
    HTML;
  }
} else {
  $table_body = '<p>No adminstrators!</p>';
}

$content = <<<HTML
  <div class="container">
   <h1 class="display-6 text-center my-4">Administrators</h1>
  <div class="table-responsive small">
    <table class="table table-md">
      <thead>
        <tr>
        <th scope="col">ID</th>
        <th scope="col">First Name</th>
        <th scope="col">Last Name</th>
        <th scope="col">Email</th>
        <th scope="col">Username</th>
        <th scope="col">Phone Number</th>
        </tr>
      </thead>
      <tbody>$table_body</tbody>
    </table>
    <a href="register.php" class="btn btn-primary">
        Register new admin
      </a>
  </div>
  </div>
HTML;

include 'template.php';