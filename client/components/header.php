<header>
  <div class="d-flex flex-column flex-md-row align-items-center pb-3 mt-2 border-bottom">
    <a href="index.php" class="d-flex align-items-center link-body-emphasis text-decoration-none">
      <span class="fs-4">Boquopate Hotel & Resort</span>
    </a>
    <nav class="d-inline-flex mt-2 mt-md-0 ms-md-auto">
      <?php
      if ((isset($hide_header_link) && !$hide_header_link) || !isset($hide_header_link)) {
        echo '<a class="me-3 py-2 link-body-emphasis text-decoration-none" href="index.php#rooms">Rooms</a>';
        echo '<a class="me-3 py-2 link-body-emphasis text-decoration-none" href="index.php#booking">Reservation</a>';
        echo '<a class="me-3 py-2 link-body-emphasis text-decoration-none" href="index.php#contact">Contact Us</a>';
      }

      if (isset($_COOKIE['user_id'])) {
        echo '<a class="me-3 py-2 link-body-emphasis text-decoration-none" href="bookings.php">My Bookings</a>';
        echo '<a class="py-2 link-body-emphasis text-decoration-none" href="logout.php">Sign Out</a>';
      } else {
        echo '<a class="py-2 link-body-emphasis text-decoration-none" href="login.php">Sign In</a>';
      }
      ?>
    </nav>
  </div>
</header>