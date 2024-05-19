<?php
include '../shared/connect.php';

$title = 'Dashboard - Bookings';

$styles = <<<CSS
<style>
.card img {
   height: 150px;
   width: auto;
   background-size: cover;
   background-position: center;
}
</style>
CSS;

$select_bookings = $conn->prepare("
  SELECT 
    bookings.id, 
    bookings.check_in, 
    bookings.check_out,
    bookings.is_paid, 

    users.last_name,
    users.first_name,
    users.email,
    users.phone_number,

    rooms.name AS room_name,
    rooms.type AS room_type,
    rooms.image_url,

    bookings.adult_count,
    bookings.children_count,

    paymentinformations.card_holder,
    paymentinformations.card_number
  FROM 
    bookings
  JOIN users ON bookings.user_id = users.id 
  JOIN rooms ON bookings.room_id = rooms.id
  JOIN paymentinformations ON bookings.id = paymentinformations.booking_id
");
$select_bookings->execute();

$bookings = '';
if ($select_bookings->rowCount() > 0) {
  while ($booking = $select_bookings->fetch(PDO::FETCH_ASSOC)) {
    $check_in_dt = new DateTime($booking["check_in"]);
    $check_in_dt_formatted = $check_in_dt->format('M j, Y');

    $check_out_dt = new DateTime($booking["check_out"]);
    $check_out_dt_formatted = $check_out_dt->format('M j, Y');

    $actions = '';
    $is_paid = (bool) $booking["is_paid"];
    $paid_display = '';
    if ($is_paid) {
      $paid_display = '<span class="badge text-bg-success">Yes</span>';
    } else {
      $paid_display = '<span class="badge text-bg-danger">No</span>';
      $actions .= <<<HTML
        <form action="" method="POST">
            <input type="hidden" name="booking_id" value="{$booking['id']}">
            <button type="submit" name="mark_as_paid" class="btn btn-success" onclick="return confirm('Confirm to mark booking as paid.');">Mark As Paid</button>
        </form>
      HTML;
    }

    $actions .= <<<HTML
      <form action="" method="POST">
        <input type="hidden" name="booking_id" value="{$booking['id']}">
        <button type="submit" name="delete" class="btn btn-danger" onclick="return confirm('Confirm to delete booking.');">Delete</button>
      </form>
    HTML;

    $bookings .= <<<HTML
       <div class="card col-6 p-0" style="width: 16rem">
          <div>
             <img src={$booking["image_url"]} class="d-block w-100 img-fluid">
          </div>
          <div class="card-body">
             <h5 class="card-title">{$booking["room_name"]} <span><small class="card-text">({$booking["room_type"]})</small></span></h5>
          </div>
          <ul class="list-group list-group-flush">
          <li class="list-group-item d-flex justify-content-between">
                <span>Customer</span>
                <span>{$booking["last_name"]}, {$booking["first_name"]}</span>
             </li>
             <li class="list-group-item d-flex justify-content-between">
                <span>Check In</span>
                <span>{$check_in_dt_formatted}</span>
             </li>
             <li class="list-group-item d-flex justify-content-between">
                <span>Check Out</span>
                <span>{$check_out_dt_formatted}</span>
             </li>
             <li class="list-group-item d-flex justify-content-between">
                <span>Paid?</span>
                {$paid_display}
             </li>
             <li class="list-group-item d-flex justify-content-between">
                <span>Adults</span>
                <span>{$booking["adult_count"]}</span>
             </li>
             <li class="list-group-item d-flex justify-content-between">
                <span>Kids</span>
                <span>{$booking["children_count"]}</span>
             </li>
          </ul>
          <div class="card-body d-flex justify-content-end gap-2">
            {$actions}
          </div>
       </div>
  HTML;
  }
}

$content = <<<HTML
<div class="container">
   <h1 class="display-6 text-center my-4">Bookings</h1>
   <div class="row gap-4 justify-content-center">
     {$bookings}
  </div>
</div>
HTML;


// POST delete booking request handler 

if (isset($_POST['delete'])) {
  $delete_id = $_POST['booking_id'];
  $filtered_input = filter_var($delete_id, 513); // 513 is the integer value for FILTER_SANITIZE_STRING

  $delete_booking = $conn->prepare("DELETE FROM `bookings` WHERE id = ?");
  $delete_booking->execute([$filtered_input]);
  header("Refresh:0");
}

if (isset($_POST['mark_as_paid'])) {
  $booking_id = $_POST['booking_id'];
  $filtered_input = filter_var($booking_id, 513);

  $sql_mark_as_paid = $conn->prepare("UPDATE bookings SET is_paid = ? WHERE id = ?");
  $sql_mark_as_paid->execute([true, $booking_id]);
  header("Refresh:0");

}

include 'template.php';