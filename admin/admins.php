<?php
include '../components/connect.php';

$title = 'Dashboard - Messages';

$select_users = $conn->prepare("
SELECT 
  users.id,
  users.first_name, 
  users.last_name, 
  users.email, 
  users.username, 
  users.phone_number, 
  users.created_at 
FROM 
  users
");
$select_users->execute();

$content = '';

if ($select_users->rowCount() > 0) {
  while ($user = $select_users->fetch(PDO::FETCH_ASSOC)) {
    $content .= <<<HTML
      <div class="border p-4">
        <p class="p-0 m-0">user id : <span>{$user['id']}</span></p>
        <p class="p-0 m-0">user first_name : <span>{$user['first_name']}</span></p>
        <p class="p-0 m-0">user last_name : <span>{$user['last_name']}</span></p>
        <p class="p-0 m-0">user email : <span>{$user['email']}</span></p>
        <p class="p-0 m-0">user username : <span>{$user['username']}</span></p>
        <p class="p-0 m-0">user phone_number : <span>{$user['phone_number']}</span></p>
        <p class="p-0 m-0">user created_at : <span>{$user['created_at']}</span></p>
        <form action="" method="POST">
            <input type="hidden" name="delete_id" value="{$user['id']}">
            <input type="submit" value="delete user" onclick="return confirm('delete this user?');"
              name="delete" class="btn btn-primary">
        </form>
      </div>
    HTML;
  }
} else {
  $content = '<p>No recent messages!</p>';
}

// POST delete user request handler 
if (isset($_POST['delete'])) {
  $delete_id = $_POST['delete_id'];
  $filtered_input = filter_var($delete_id, 513); // 513 is the integer value for FILTER_SANITIZE_STRING

  $delete_message = $conn->prepare("DELETE FROM `users` WHERE id = ?");
  $delete_message->execute([$filtered_input]);
}

include '../components/admin_template.php';