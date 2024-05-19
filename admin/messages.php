<?php
include '../shared/connect.php';

$title = 'Dashboard - Messages';

$select_messages = $conn->prepare("SELECT * FROM `messages`");
$select_messages->execute();

$table_body = '';
if ($select_messages->rowCount() > 0) {
  while ($message = $select_messages->fetch(PDO::FETCH_ASSOC)) {
    $table_body .= <<<HTML
      <tr>
        <td class="align-middle">{$message['id']}</td>
        <td class="align-middle">{$message['email']}</td>
        <td class="align-middle">{$message['contact_number']}</td>
        <td class="align-middle">{$message['content']}</td>
        <td class="align-middle">
        <form action="mailto:{$message['email']}" method="post" enctype="text/plain">
          <input type="submit" class="btn btn-primary" value="Reply">
        </form>
        </td>
      </tr>
    HTML;
  }
} else {
  $table_body = '<p>No adminstrators!</p>';
}

$content = <<<HTML
  <div class="container">
   <h1 class="display-6 text-center my-4">Messages</h1>
  <div class="table-responsive small">
    <table class="table table-md">
      <thead>
        <tr>
        <th scope="col">ID</th>
        <th scope="col">Email</th>
        <th scope="col">Contact Number</th>
        <th scope="col">Content</th>
        <th scope="col">Action(s)</th>
        </tr>
      </thead>
      <tbody>$table_body</tbody>
    </table>
  </div>
  </div>
HTML;


// POST delete message request handler 
if (isset($_POST['delete'])) {
  $delete_id = $_POST['delete_id'];
  $filtered_input = filter_var($delete_id, 513); // 513 is the integer value for FILTER_SANITIZE_STRING

  $delete_message = $conn->prepare("DELETE FROM `messages` WHERE id = ?");
  $delete_message->execute([$filtered_input]);
}

include 'template.php';