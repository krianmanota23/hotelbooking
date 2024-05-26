<?php
include '../shared/connect.php';

$title = 'Checkout';
$hide_header_link = true;

if (isset($_POST['booking'])) {
  // User Info
  $first_name = $_POST['first-name'];
  $last_name = $_POST['last-name'];
  $username = $_POST['username'];
  $contact_number = $_POST['contact-number'];
  $email = $_POST['email'];

  $sql_user = $conn->prepare("SELECT users.id FROM users WHERE email = ? OR username = ?");
  $sql_user->execute([$email, $username]);

  $user_id = null;
  $created_new_user = true;
  if ($sql_user->rowCount() > 0) {
    $created_new_user = false;
    $user_id = $sql_user->fetch(PDO::FETCH_ASSOC)["id"];
  } else {
    $default_password = "5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8"; // "password"
    $sql_create_user = $conn->prepare("
      INSERT 
      INTO users 
      (first_name, last_name, username, email, password, phone_number)
      VALUES(?,?,?,?,?,?)
    ");
    $sql_create_user->execute([$first_name, $last_name, $username, $email, $default_password, $contact_number,]);
    $user_id = $conn->lastInsertId();
  }
  setcookie('user_id', $user_id, time() + 60 * 60 * 24 * 30, '/');

  // Booking Info 
  $check_in = $_POST['check-in'];
  $check_out = $_POST['check-out'];
  $room_id = $_POST['room-id'];
  $adults = $_POST['adults'];
  $kids = $_POST['kids'];

  $sql_create_booking = $conn->prepare("
    INSERT 
    INTO bookings
    (user_id, room_id, adult_count, children_count, check_in, check_out) 
    VALUES(?,?,?,?,?,?) 
  ");
  $sql_create_booking->execute([$user_id, $room_id, $adults, $kids, $check_in, $check_out]);
  $booking_id = $conn->lastInsertId();

  // Payment Info
  $address = $_POST['billing-address'];
  $card_holder = $_POST['card-holder'];
  $card_number = $_POST['card-number'];
  $card_expiry = $_POST['card-expiry'];
  $card_cvv = $_POST['card-cvv'];

  $sql_create_payment_info = $conn->prepare("
    INSERT 
    INTO paymentinformations
    (booking_id, card_holder, card_number, card_expiry, card_cvv)
    VALUES(?,?,?,?,?) 
  ");
  $sql_create_payment_info->execute([$booking_id, $card_holder, $card_number, $card_expiry, $card_cvv]);
  if ($created_new_user) {
    header('location:update_password.php');
  }
  header('location:bookings.php');
}

$query_params = [];
parse_str($_SERVER['QUERY_STRING'], $query_params);

$room_id = $query_params['room_id'] ?? null;
$adults = $query_params['adults'] ?? null;
$kids = $query_params['kids'] ?? null;
$check_in = $query_params['check_in'] ?? null;
$check_out = $query_params['check_out'] ?? null;

if ($room_id) {
  $sql_room = $conn->prepare("SELECT * FROM rooms WHERE id = ?");
  $sql_room->execute([$room_id]);
  if ($sql_room->rowCount() == 1) {
    $room = $sql_room->fetch(PDO::FETCH_ASSOC);

    $check_in_dt = new DateTime($check_in);
    $check_in_dt_formatted = $check_in_dt->format('M j, Y');

    $check_out_dt = new DateTime($check_out);
    $check_out_dt_formatted = $check_out_dt->format('M j, Y');

    $interval = $check_in_dt->diff($check_out_dt);
    $days = $interval->days;
    $total = $days * $room["price_per_night"];
  } else {
    header('location:index.php');
  }
} else {
  header('location:index.php');
}

$booking_details = <<<HTML
<div class="col-md-5 col-lg-4 order-md-last">
  <h4 class="d-flex justify-content-between align-items-center mb-3">
    <span class="lead">Booking Details</span>
  </h4>
  <ul class="list-group mb-3">
    <li class="list-group-item d-flex justify-content-between lh-sm">
      <div>
        <small class="text-body-secondary">Check In</small>
      </div>
      <span class="text-body-secondary">{$check_in_dt_formatted}</span>
    </li>
    <li class="list-group-item d-flex justify-content-between lh-sm">
      <div>
        <small class="text-body-secondary">Check Out</small>
      </div>
      <span class="text-body-secondary">{$check_out_dt_formatted}</span>
    </li>
    <li class="list-group-item d-flex justify-content-between lh-sm">
      <div>
        <h6 class="my-0">{$room["name"]}</h6>
        <small class="text-body-secondary">{$room["type"]}</small>
      </div>
      <span class="text-body-secondary">₱{$room["price_per_night"]}</span>
    </li>
    <li class="list-group-item d-flex justify-content-between lh-sm">
      <p class="my-0 text-body-secondary">Stay duration (in days)</p>
      <span class="text-body-secondary">x {$days}</span>
    </li>
    <li class="list-group-item d-flex justify-content-between">
      <span>Total (PHP)</span>
      <strong>₱{$total}</strong>
    </li>
  </ul>
</div>
HTML;

$checkout_form = <<<HTML
<div class="col-md-7 col-lg-8">
  <h4 class="mb-3">Billing address</h4>
  <form action="" method="POST" novalidate>
    <div class="row g-3">
      <div class="col-sm-6">
        <label for="firstName" class="form-label">First name</label>
        <input type="text" class="form-control" id="firstName" placeholder="" value="" required
          name="first-name">
        <div class="invalid-feedback">
          Valid first name is required.
        </div>
      </div>

      <div class="col-sm-6">
        <label for="lastName" class="form-label">Last name</label>
        <input type="text" class="form-control" id="lastName" placeholder="" value="" required name="last-name">
        <div class="invalid-feedback">
          Valid last name is required.
        </div>
      </div>

      <div class="col-6">
        <label for="username" class="form-label">Username</label>
        <div class="input-group has-validation">
          
          <input type="text" class="form-control" id="username" placeholder="Username" required name="username">
          <div class="invalid-feedback">
            Your username is required.
          </div>
        </div>
      </div>
      <div class="col-6">
        <label for="username" class="form-label">Contact Number</label>
        <div class="input-group has-validation">
          <span class="input-group-text">+63</span>
          <input type="text" class="form-control" id="contact" placeholder="" required name="contact-number">
          <div class="invalid-feedback">
            Your contact number is required.
          </div>
        </div>
      </div>

      <div class="col-12">
        <label for="email" class="form-label">Email <span class="text-body-secondary">Must !!</span></label>
        <input type="email" class="form-control" id="email" placeholder="" name="email">
        <div class="invalid-feedback">
          Please enter a valid email address for billing updates.
        </div>
      </div>

      <div class="col-12">
        <label for="address" class="form-label">Address</label>
        <input type="text" class="form-control" id="address" placeholder="1234 Main St" required
          name="billing-address">
        <div class="invalid-feedback">
          Please enter your billing address.
        </div>
      </div>
    </div>
    <hr class="my-4">

    <h4 class="mb-3">Payment</h4>
    <div class="row gy-3">
      <div class="col-md-6">
        <label for="cc-name" class="form-label">Name on card</label>
        <input type="text" class="form-control" id="cc-name" placeholder="" required name="card-holder">
        <small class="text-body-secondary">Full name as displayed on card</small>
        <div class="invalid-feedback">
          Name on card is required
        </div>
      </div>

      <div class="col-md-6">
        <label for="cc-number" class="form-label">Credit card number</label>
        <input type="text" class="form-control" id="cc-number" placeholder="" required name="card-number">
        <div class="invalid-feedback">
          Credit card number is required
        </div>
      </div>

      <div class="col-md-3">
        <label for="cc-expiration" class="form-label">Expiration</label>
        <input type="text" class="form-control" id="cc-expiration" placeholder="" required name="card-expiry">
        <div class="invalid-feedback">
          Expiration date required
        </div>
      </div>
      <div class="col-md-3">
        <label for="cc-cvv" class="form-label">CVV</label>
        <input type="text" class="form-control" id="cc-cvv" placeholder="" required name="card-cvv">
        <div class="invalid-feedback">
          Security code required
        </div>
      </div>
    </div>
    <hr class="my-4">
    <!-- hidden fields -->
    <input type="hidden" name="check-in" value={$check_in}>
    <input type="hidden" name="check-out" value={$check_out}>
    <input type="hidden" name="room-id" value={$room_id}>
    <input type="hidden" name="adults" value={$adults}>
    <input type="hidden" name="kids" value={$kids}>
    <button class="w-100 btn btn-primary btn-lg" type="submit" name="booking">Confirm Booking</button>
  </form>
</div>
HTML;

$content = <<<HTML
<div class="container">
  <main>
    <div class="py-5 text-center">
      <h2>Checkout Form</h2>
    </div>
    <div class="row g-5">
      {$booking_details}
      {$checkout_form}
    </div>
  </main>
</div>
HTML;


include 'template.php';