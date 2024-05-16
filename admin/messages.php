<?php
include '../components/connect.php';

$title = 'Dashboard - Messages';

$select_messages = $conn->prepare("SELECT * FROM `messages`");
$select_messages->execute();

$content = '';

if ($select_messages->rowCount() > 0) {
  while ($message = $select_messages->fetch(PDO::FETCH_ASSOC)) {
    $content .= <<<HTML
      <div class="border p-4">
        <p class="p-0 m-0">message id : <span>{$message['id']}</span></p>
        <p class="p-0 m-0">message email : <span>{$message['email']}</span></p>
        <p class="p-0 m-0">message contact_number : <span>{$message['contact_number']}</span></p>
        <p class="p-0 m-0">message content : <span>{$message['content']}</span></p>
        <form action="" method="POST">
            <input type="hidden" name="delete_id" value="{$message['id']}">
            <input type="submit" value="delete message" onclick="return confirm('delete this message?');"
              name="delete" class="btn btn-primary">
        </form>
      </div>
    HTML;
  }
} else {
  $content = '<p>No recent messages!</p>';
}

// POST delete message request handler 
if (isset($_POST['delete'])) {
  $delete_id = $_POST['delete_id'];
  $filtered_input = filter_var($delete_id, 513); // 513 is the integer value for FILTER_SANITIZE_STRING

  $delete_message = $conn->prepare("DELETE FROM `messages` WHERE id = ?");
  $delete_message->execute([$filtered_input]);
}

include '../components/admin_template.php';