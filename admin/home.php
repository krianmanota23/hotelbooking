<?php
include '../components/connect.php';

$title = 'Dashboard - Home';

$styles = <<<CSS
<style>
.icon-square {
  width: 3rem;
  height: 3rem;
  border-radius: 0.75rem;
}

.card-footer {
  width: 180px;
  margin-left: 20px;
}

.card-footer a {
  padding: 8px 32px;
}
</style>
CSS;

/* Cards SQL Statements */
$select_bookings = $conn->prepare("SELECT COUNT(*) FROM `bookings`");
$select_bookings->execute();
$bookings_count = $select_bookings->fetchColumn();

$select_admins = $conn->prepare("SELECT COUNT(*) FROM `users` WHERE is_admin = ?");
$select_admins->execute([true]);
$admins_count = $select_admins->fetchColumn();

$select_messages = $conn->prepare("SELECT COUNT(*) FROM `messages`");
$select_messages->execute();
$messages_count = $select_messages->fetchColumn();

/* Cards Template  */
$cards = <<<HTML
<div class="align-items-center pt-3 pb-2 mb-3 border-bottom">
  <div class="container" id="hanging-icons">
    <div class="card-group d-flex flex-column flex-lg-row p-4">
      <div class="card border-0 mb-4">
        <div class="card-body p-0">
          <div class="d-flex flex-col">
            <div
              class="icon-square text-body-emphasis bg-body-secondary d-inline-flex align-items-center justify-content-center fs-4 flex-shrink-0 me-3">
              <span>$bookings_count</span>
            </div>
            <div>
              <div class="d-flex gap-1">
                <span class="fs-3 lead text-body-emphasis">Bookings</span>
              </div>
              <p>Displays current and historical booking data, allowing for management and analysis of customer
                reservations.</p>
            </div>
          </div>
        </div>
        <div class="card-footer d-flex justify-content-center bg-transparent border-0 p-0">
          <a href="bookings.php" class="btn btn-primary">
            View
          </a>
        </div>
      </div>
      <div class="card border-0 mb-4">
        <div class="card-body p-0">
          <div class="d-flex flex-col">
            <div
              class="icon-square text-body-emphasis bg-body-secondary d-inline-flex align-items-center justify-content-center fs-4 flex-shrink-0 me-3">
              <span>$admins_count</span>
            </div>
            <div>
              <div class="d-flex gap-1">
                <span class="fs-3 lead text-body-emphasis">Administrators</span>
              </div>
              <p>Provides information on admin user roles, activity logs, and permissions settings to oversee
                platform management.</p>
            </div>
          </div>
        </div>
        <div class="card-footer d-flex justify-content-center bg-transparent border-0 p-0">
          <a href="admins.php" class="btn btn-primary">
            View
          </a>
        </div>
      </div>
      <div class="card border-0 mb-4">
        <div class="card-body p-0">
          <div class="d-flex flex-col">
            <div
              class="icon-square text-body-emphasis bg-body-secondary d-inline-flex align-items-center justify-content-center fs-4 flex-shrink-0 me-3">
              <span>$messages_count</span>
            </div>
            <div>
              <div class="d-flex gap-1">
                <span class="fs-3 lead text-body-emphasis">Messages</span>
              </div>
              <p>Shows recent user communications, including inquiries and feedback, facilitating prompt and
                effective responses.</p>
            </div>
          </div>
        </div>
        <div class="card-footer d-flex justify-content-center bg-transparent border-0 p-0">
          <a href=" messages.php" class="btn btn-primary">
            View
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
HTML;

/* Recent Bookings SQL Statements  */
$select_bookings = $conn->prepare("
  SELECT 
    bookings.id, 
    bookings.check_in, 
    bookings.check_out, 
    users.last_name,
    users.first_name,
    rooms.name AS room_name,
    rooms.type AS room_type
  FROM 
    bookings
  JOIN users ON bookings.user_id = users.id 
  JOIN rooms ON bookings.room_id = rooms.id
  LIMIT 5
");
$select_bookings->execute();

/* Recent Bookings table body  */
$table_body = '';

if ($select_bookings->rowCount() > 0) {
  $table_body = '';
  while ($booking = $select_bookings->fetch(PDO::FETCH_ASSOC)) {
    $table_body .= <<<HTML
      <tr>
        <td>{$booking['id']}</td>
        <td>{$booking['check_in']}</td>
        <td>{$booking['check_out']}</td>
        <td>{$booking['last_name']}</td>
        <td>{$booking['first_name']}</td>
        <td>{$booking['room_name']}</td>
        <td>{$booking['room_type']}</td>
      </tr>
    HTML;
  }
} else {
  $table_body = '<p>No recent bookings!</p>';
}

/* Recent Bookings table */
$table = <<<HTML
  <p class="fs-4 lead">Recent Bookings</p>
  <div class="table-responsive small">
    <table class="table table-md">
      <thead>
        <tr>
          <th scope="col">ID</th>
          <th scope="col">Room</th>
          <th scope="col">Check In</th>
          <th scope="col">Check Out</th>
          <th scope="col">Customer</th>
        </tr>
      </thead>
      <tbody>$table_body</tbody>
    </table>
  </div>
HTML;

$content = $cards . $table;
include '../components/admin_template.php';
