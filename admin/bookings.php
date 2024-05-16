<?php
include '../components/connect.php';

$title = 'Dashboard - Bookings';

$select_bookings = $conn->prepare("
  SELECT 
    bookings.id, 
    bookings.check_in, 
    bookings.check_out, 
    users.last_name,
    users.first_name,
    users.email,
    users.phone_number,
    rooms.name AS room_name,
    bookings.adult_count,
    bookings.children_count,
    rooms.type AS room_type
  FROM 
    bookings
  JOIN users ON bookings.user_id = users.id 
  JOIN rooms ON bookings.room_id = rooms.id
");
$select_bookings->execute();

$content = '';

if ($select_bookings->rowCount() > 0) {
  while ($booking = $select_bookings->fetch(PDO::FETCH_ASSOC)) {
    $content .= <<<HTML
      <div class="border p-4">
        <p class="p-0 m-0">booking id : <span>{$booking['id']}</span></p>
        <p class="p-0 m-0">name : <span>{$booking['last_name']}, {$booking['first_name']}</span></p>
        <p class="p-0 m-0">email : <span>{$booking['email']}</span></p>
        <p class="p-0 m-0">contact number : <span>{$booking['phone_number']}</span></p>
        <p class="p-0 m-0">check in : <span>{$booking['check_in']}</span></p>
        <p class="p-0 m-0">check out : <span>{$booking['check_out']}</span></p>
        <p class="p-0 m-0">room name : <span>{$booking['room_name']}</span></p>
        <p class="p-0 m-0">adults: <span>{$booking['adult_count']}</span></p>
        <p class="p-0 m-0">room type : <span>{$booking['room_type']}</span></p>
        <p class="p-0 m-0">kids: <span>{$booking['children_count']}</span></p>
        <form action="" method="POST">
            <input type="hidden" name="delete_id" value="{$booking['id']}">
            <input type="submit" value="delete booking" onclick="return confirm('delete this booking?');"
              name="delete" class="btn btn-primary">
        </form>
      </div>
    HTML;
  }
} else {
  $content = '<p>No recent bookings!</p>';
}

// POST delete booking request handler 

if (isset($_POST['delete'])) {
  $delete_id = $_POST['delete_id'];
  $filtered_input = filter_var($delete_id, 513); // 513 is the integer value for FILTER_SANITIZE_STRING

  $delete_booking = $conn->prepare("DELETE FROM `bookings` WHERE id = ?");
  $delete_booking->execute([$filtered_input]);
}

include '../components/admin_template.php';