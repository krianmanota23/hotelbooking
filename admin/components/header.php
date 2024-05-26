<header class="border-bottom">
  <div class="container">
    <navbar class="navbar navbar-expand-lg d-flex align-items-center justify-content-between">
      <a class="navbar-brand fw-bold" href="index.php">Admin Panel</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-toggler"
        aria-controls="navbar-toggler" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-lg-end" id="navbar-toggler">
        <div
          class="d-flex align-items-start align-items-lg-center flex-column flex-lg-row flex-lg-grow-1 justify-content-lg-between gap-4">
          <ul class="nav flex-column flex-lg-row justify-content-center">
            <li><a href="index.php" class="nav-link px-2 link-body-emphasis">Home</a></li>
            <li><a href="bookings.php" class="nav-link px-2 link-body-emphasis">Bookings</a></li>
            <li><a href="admins.php" class="nav-link px-2 link-body-emphasis">Administrators</a></li>
            <li><a href="messages.php" class="nav-link px-2 link-body-emphasis">Messages</a></li>
            <li><a href="" class="nav-link px-2 link-body-emphasis d-lg-none">Profile</a></li>
            <li><a href="" class="nav-link px-2 link-body-emphasis d-lg-none">Sign out</a></li>
          </ul>
          <div class="dropdown text-start d-none d-lg-inline">
            <a href="#" class="d-block link-body-emphasis text-decoration-none dropdown-toggle"
              data-bs-toggle="dropdown" aria-expanded="false">
              <i class="bi bi-person-circle"></i>
            </a>
            <ul class="dropdown-menu text-small">
              <li><a class="dropdown-item" href="logout.php" onclick="return confirm('Confirm logout?');">Sign
                  out</a></li>
            </ul>
          </div>
        </div>
      </div>
    </navbar>
  </div>
</header>