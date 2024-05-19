<?php
include '../shared/connect.php';

if (isset($_COOKIE['user_id'])) {
   $user_id = $_COOKIE['user_id'];
} else {
   header('location:login.php');
}


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

$title = 'Bookings';
$hide_header_link = true;

$sql_bookings = $conn->prepare("
  SELECT 
    bookings.check_in, 
    bookings.check_out,

    rooms.name AS room_name,
    rooms.type AS room_type,
    rooms.image_url
  FROM 
    bookings
  JOIN rooms ON bookings.room_id = rooms.id
  WHERE bookings.user_id = ?
");
$sql_bookings->execute([$user_id]);

$bookings = '';

if ($sql_bookings->rowCount() > 0) {
   while ($booking = $sql_bookings->fetch(PDO::FETCH_ASSOC)) {
      $check_in_dt = new DateTime($booking["check_in"]);
      $check_in_dt_formatted = $check_in_dt->format('M j, Y');

      $check_out_dt = new DateTime($booking["check_out"]);
      $check_out_dt_formatted = $check_out_dt->format('M j, Y');

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
                  <span>Check In</span>
                  <span>{$check_in_dt_formatted}</span>
               </li>
               <li class="list-group-item d-flex justify-content-between">
                  <span>Check Out</span>
                  <span>{$check_out_dt_formatted}</span>
               </li>
            </ul>
         </div>
    HTML;
   }
} else {
   $bookings = '<p>No recent bookings!</p>';
}


$content = <<<HTML
<div class="container">
   <h1 class="display-6 text-center my-4">My Bookings</h1>
   <div class="row gap-4 justify-content-center">
     {$bookings}
  </div>
</div>
HTML;


include 'template.php';