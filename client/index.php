<?php
include '../shared/connect.php';

$title = 'Home';

$styles = <<<CSS
<style>
.container {
max-width: 1080px;
}

.bi {
vertical-align: -.125em;
fill: currentColor;
}

.carousel {
margin: 64px 0;
}

.carousel-item {
height: 550px;
overflow: hidden;
border-radius: 16px;
}

.carousel-item img {
height: 550px;
width: auto;
background-size: cover;
background-position: center;
}

.developer {
height: 250px;
border-radius: 16px;
}

.developer-image {
  height: 200px;
  overflow: hidden;
}

.developer img {
height: 100%;
width: 100%;
background-size: cover;
background-position: center;
}

.carousel-caption {
z-index: 10;
}

.amenities-icon {
height: 40px;
width: 40px;
}

.amenities {
margin: 90px 0;
}

.amenities-group {
margin-bottom: 64px;
}

.introduction-cover {
width: 100%;
max-height: 500px;
overflow: hidden;
}

.introduction-cover img {
width: 100%;
background-size: cover;
background-position: center;
}

.booking {
padding: 36px 0;
}

.booking p {
margin-bottom: 4px
}

.available-room-image {
width: 100%;
}

.information div {
flex-basis: 0;
}
</style>
CSS;

if (isset($_POST['search'])) {
  $check_in = DateTime::createFromFormat('Y-m-d', $_POST['check-in']);
  $check_out = DateTime::createFromFormat('Y-m-d', $_POST['check-out']);

  $adults = $_POST['adults'];
  $kids = $_POST['kids'];

  if (
    $check_in && $check_out && $check_in <
    $check_out
  ) {
    $check_in = $check_in->format('Y-m-d');
    $check_out = $check_out->format('Y-m-d');

    // To check all available rooms
    $sql_all_rooms = "
      SELECT *
      FROM rooms r
      WHERE NOT EXISTS (
      SELECT 1
      FROM bookings b
      WHERE b.room_id = r.id
      AND (b.check_in <= :check_out AND b.check_out>= :check_in)
    )
    ";
    try {
      $stmt_all = $conn->prepare($sql_all_rooms);
      $stmt_all->execute([':check_in' => $check_in, ':check_out' => $check_out]);
      $available_rooms = $stmt_all->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $error) {
      echo $error;
    }

    if (count($available_rooms) > 0) {
      $check_in_dt = new DateTime($check_in);
      $check_out_dt = new DateTime($check_out);

      // Calculate the difference between the two dates
      $interval = $check_in_dt->diff($check_out_dt);

      // Format dates for display
      $check_in_dt_formatted = $check_in_dt->format('M j, Y'); // e.g., May 16, 2024
      $check_out_dt_formatted = $check_out_dt->format('M j, Y'); // e.g., May 23, 2024

      // Get the number of nights as the difference in days
      $number_of_nights = $interval->days;

      // Display the results
      $results_description = "Results for: {$check_in_dt_formatted}-{$check_out_dt_formatted} | {$number_of_nights} night(s)";
    }
  } else {
    echo '<script>alert("Invalid request!");</script>';
  }
}

if (isset($_POST['send_message'])) {
  $name = $_POST['full_name'];
  $name = filter_var($name, FILTER_SANITIZE_STRING);
  $email = $_POST['email_address'];
  $email = filter_var($email, FILTER_SANITIZE_STRING);
  $number = $_POST['contact_number'];
  $number = filter_var($number, FILTER_SANITIZE_STRING);
  $message = $_POST['content'];
  $message = filter_var($message, FILTER_SANITIZE_STRING);

  $insert_message = $conn->prepare("INSERT INTO `messages`(name, email, contact_number, content) VALUES(?,?,?,?)");
  $insert_message->execute([$name, $email, $number, $message]);
}

$introduction_section = <<<HTML
<div class="bg-body-tertiary introduction">
  <div class="introduction-cover">
    <img src="../images/gallery-img-5.webp" class="img-fluid">
  </div>
  <div class="p-4 p-md-5 mb-4 rounded text-body-emphasis bg-body-secondary">
    <div class="px-0">
      <h1 class="display-4 fst-italic">Boquopate Hotel & Resort</h1>
      <p class="lead my-3">Welcome to Boquotate Hotel & Resort, your luxurious escape in nature's paradise. Enjoy
        stunning views, world-class dining, and rejuvenating spa treatments. Every moment at Boquotate is crafted
        for
        your perfect relaxation and adventure. Discover tranquility and elegance at its finest.</p>
      <p class="lead mb-0"><a href="#booking" class="text-body-emphasis fw-normal">See
          rooms...</a></p>
    </div>
  </div>
</div>
HTML;

$room_search_form = <<<HTML
<section id="booking" class="booking">
  <form action="" method="post" class="d-flex flex-column flex-md-row mx-5 justify-content-center gap-4">
    <div class="d-flex flex-row gap-4 justify-content-center">
      <div class="flex-grow-1">
        <p class="fw-bold text-uppercase">Check-in</p>
        <input type="date" name="check-in" class="form-control" required>
      </div>
      <div class="flex-grow-1">
        <p class="fw-bold text-uppercase">Check-out</p>
        <input type="date" name="check-out" class="form-control" required>
      </div>
    </div>
    <div class="d-flex flex-row gap-4 justify-content-center">
      <div class="flex-grow-1">
        <p class="fw-bold text-uppercase">Adults</p>
        <input class="form-control" type="number" name="adults" min="1" max="6" value="1">
      </div>
      <div class="flex-grow-1">
        <p class="fw-bold text-uppercase">Kids</p>
        <input class="form-control" type="number" name="kids" min="0" max="6" value="0">
      </div>
    </div>
    <div>
      <p class="text-light">Search</p>
      <button type="submit" class="btn btn-primary" name="search">Search</button>
    </div>
  </form>
HTML;

$available_rooms_modal_shown = false;
$available_rooms_content = "";
if (isset($available_rooms)) {
  if (count($available_rooms) > 0) {
    $available_rooms_modal_shown = true;
    foreach ($available_rooms as $room) {
      $available_rooms_content .= <<<HTML
      <div class="row border border-dark-subtle p-2">
        <div class="col-md-6">
          <img src={$room["image_url"]} class="available-room-image" alt="room image">
        </div>
        <div class="col-md-6 d-flex flex-column justify-content-evenly">
          <div>
            <p class="text-body-secondary">Room ID: {$room['id']}</p>
            <p class="lead fw-bold">{$room['type']}</p>
          </div>
          <div class="d-flex flex-column">
            <p class="fw-normal"><span class="lead">{$room['price_per_night']}</span> | <span class="text-body-secondary">per night</span></p>
            <a class="btn btn-primary" href="checkout.php?room_id={$room['id']}&adults={$adults}&kids={$kids}&check_in={$check_in}&check_out={$check_out}">Book</a>
          </div>
        </div>
      </div>
    HTML;
    }
  } else {
    $info_msg[] = "No rooms available!";
  }
}

$results_description = isset($results_description) ? $results_description : '';

$available_rooms_modal = <<<HTML
<div class="fade modal" id="available-rooms" tabindex="-1" aria-labelledby="available-rooms-label"
  aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <p class="modal-title">{$results_description}</p>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body d-flex flex-column gap-1">{$available_rooms_content}</div>
    </div>
  </div>
</div>
HTML;

$booking_section = <<<HTML
<section id="booking" class="booking">
  {$room_search_form}
  {$available_rooms_modal}
</section>
HTML;

$modal_handler = '';
if ($available_rooms_modal_shown) {
  $modal_handler = <<<JS
    <script>
        window.onload = function () {
            var myModal = new bootstrap.Modal(document.getElementById('available-rooms'), {
                keyboard: false
            });
            myModal.show();
        };
    </script>
JS;
}

$amenities_section = <<<HTML
<section id="amenities" class="amenities p-4 my-6">
  <div class="row amenities-group">
    <div class="col-4 d-flex flex-column align-items-center gap-1">
      <img src="../images/icon-1.png" class="w-36 h-36 amenities-icon" alt="">
      <h2 class="fw-normal fs-4 lead mt-4">Food & Drinks</h2>
      <p class="text-center">Enjoy gourmet cuisine and handcrafted cocktails in our elegant dining venues and
        bars.
      </p>
    </div>
    <div class="col-4 d-flex flex-column align-items-center gap-1">
      <img src="../images/icon-2.png" class="w-36 h-36 amenities-icon" alt="">
      <h2 class="fw-normal fs-4 lead mt-4">Outdoor Dining</h2>
      <p class="text-center">Savor delicious meals with breathtaking views in our exquisite outdoor dining spaces.
      </p>
    </div>
    <div class="col-4 d-flex flex-column align-items-center gap-1">
      <img src="../images/icon-3.png" class="w-36 h-36 amenities-icon" alt="">
      <h2 class="fw-normal fs-4 lead mt-4">Beach View</h2>
      <p class="text-center">Relax and unwind with stunning ocean vistas that create a perfect beachside ambiance.
      </p>
    </div>
  </div>
  <div class="row">
    <div class="col-4 d-flex flex-column align-items-center gap-1">
      <img src="../images/icon-4.png" class="w-36 h-36 amenities-icon" alt="">
      <h2 class="fw-normal fs-4 lead mt-4">Decorations</h2>
      <p class="text-center">Experience our resort's elegant, stylish decor, designed to evoke a sense of luxury
        and
        comfort.
      </p>
    </div>
    <div class="col-4 d-flex flex-column align-items-center gap-1">
      <img src="../images/icon-5.png" class="w-36 h-36 amenities-icon" alt="">
      <h2 class="fw-normal fs-4 lead mt-4">Swimming Pool</h2>
      <p class="text-center">Dive into our sparkling, refreshing pool, an oasis of relaxation and leisure for all
        guests.</p>
    </div>
    <div class="col-4 d-flex flex-column align-items-center gap-1">
      <img src="../images/icon-6.png" class="w-36 h-36 amenities-icon" alt="">
      <h2 class="fw-normal fs-4 lead mt-4">Resort Beach</h2>
      <p class="text-center">Lounge on our pristine, private beach, where the soft sand and gentle waves await
        your
        arrival.
      </p>
    </div>
  </div>
</section>
HTML;

$rooms_carousel_section = <<<HTML
<section id="rooms" class="carousel slide carousel-fade">
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img class="d-block w-100 img-fluid" src="../images/room-img-1.jpg" alt="">
      <div class="carousel-caption d-none d-md-block">
        <p class="fs-2 fw-bold">Single Room</p>
      </div>
    </div>
    <div class="carousel-item">
      <img class="d-block w-100 img-fluid" src="../images/room-img-2.jpg" alt="">
      <div class="carousel-caption d-none d-md-block">
        <p class="fs-2 fw-bold">Couple Room</p>
      </div>
    </div>
    <div class="carousel-item">
      <img class="d-block w-100 img-fluid" src="../images/room-img-3.jpg" alt="">
      <div class="carousel-caption d-none d-md-block">
        <p class="fs-2 fw-bold">Twin Bed Room</p>
      </div>
    </div>
    <div class="carousel-item">
      <img class="d-block w-100 img-fluid" src="../images/room-img-1.jpg" alt="">
      <div class="carousel-caption d-none d-md-block">
        <p class="fs-2 fw-bold">Family Room</p>
      </div>
    </div>
    <div class="carousel-item">
      <img class="d-block w-100 img-fluid" src="../images/room-img-2.jpg" alt="">
      <div class="carousel-caption d-none d-md-block">
        <p class="fs-2 fw-bold">Deluxe Room</p>
      </div>
    </div>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#rooms" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#rooms" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>
</section>
HTML;

$contact_us_section = <<<HTML
<div class="flex-grow-1 px-5" id="contact">
  <p class="fw-light lead text-center">Send us a message!</p>
  <form action="" method="POST">
    <div class="mb-2">
      <label for="contact-name" class="form-label">Name</label>
      <input type="text" class="form-control" id="contact-name" name="full_name">
    </div>
    <div class="mb-2">
      <label for="contact-email" class="form-label">Email address</label>
      <input type="email" class="form-control" id="contact-email" aria-describedby="emailHelp" name="email_address">
      <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
    </div>
    <div class="mb-2">
      <label for="contact-number" class="form-label">Contact number</label>
      <input type="text" class="form-control" id="contact-number" name="contact_number">
    </div>
    <div class="mb-2">
      <label for="contact-message" class="form-label">Message</label>
      <textarea class="form-control" id="floatingTextarea" name="content"></textarea>
    </div>
    <button type="submit" class="mt-2 btn btn-primary" name="send_message">Send message</button>
  </form>
</div>
HTML;

$faq_section = <<<HTML
<div class="accordion flex-grow-1 mt-5 mt-md-0 px-5 px-md-0" id="accordion">
  <p class="fw-light lead text-center">Frequently Asked Questions</p>
  <div class="accordion-item">
    <h2 class="accordion-header">
      <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq-1"
        aria-expanded="true" aria-controls="faq-1">
        What time is check-in and check-out at Boquotate Hotel & Resort?
      </button>
    </h2>
    <div id="faq-1" class="accordion-collapse collapse show" data-bs-parent="#accordion">
      <div class="accordion-body">
        <p>Check-in time is at 3:00 PM, and check-out time is at 11:00 AM.</p>
      </div>
    </div>
  </div>
  <div class="accordion-item">
    <h2 class="accordion-header">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq-2"
        aria-expanded="false" aria-controls="faq-2">
        Does the hotel offer airport transportation or shuttle services?
      </button>
    </h2>
    <div id="faq-2" class="accordion-collapse collapse" data-bs-parent="#accordion">
      <div class="accordion-body">
        <p>Yes, we offer airport transportation and shuttle services upon request.</p>
      </div>
    </div>
  </div>
  <div class="accordion-item">
    <h2 class="accordion-header">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq-3"
        aria-expanded="false" aria-controls="faq-3">
        Are pets allowed at the resort?
      </button>
    </h2>
    <div id="faq-3" class="accordion-collapse collapse" data-bs-parent="#accordion">
      <div class="accordion-body">
        <p>Unfortunately, pets are not allowed at our resort.
        </p>
      </div>
    </div>
  </div>
  <div class="accordion-item">
    <h2 class="accordion-header">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq-4"
        aria-expanded="false" aria-controls="faq-4">
        Can guests book tours or excursions through the hotel?
      </button>
    </h2>
    <div id="faq-4" class="accordion-collapse collapse" data-bs-parent="#accordion">
      <div class="accordion-body">
        <p>Yes, our concierge can assist guests in booking tours and excursions.
        </p>
      </div>
    </div>
  </div>
  <div class="accordion-item">
    <h2 class="accordion-header">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq-5"
        aria-expanded="false" aria-controls="faq-5">
        What dining options are available on-site, and are there any nearby restaurants or cafes within walking
        distance?
      </button>
    </h2>
    <div id="faq-5" class="accordion-collapse collapse" data-bs-parent="#accordion">
      <div class="accordion-body">
        <p>We offer gourmet dining options on-site. Additionally, there are several restaurants and cafes within
          walking distance for guests to explore.</p>
      </div>
    </div>
  </div>
</div>
HTML;

$information_section = <<<HTML
<section class="information d-flex flex-column flex-md-row mt-4 pt-4 ">
  {$contact_us_section}
  {$faq_section}
<section>
HTML;

$developers = <<<HTML

<section>
  <p class="fw-light lead text-center mb-3">Meet the Developers</p>
  <div class="d-flex gap-1">
    <div class="card developer" style="width: 18rem;">
      <div class="developer-image">
        <img src="../images/devs/dev_1.png" class="d-block w-100 img-fluid" alt="">
      </div>
      <div class="card-body">
        <h5 class="card-title">Ace</h5>
      </div>
    </div>
    <div class="card developer" style="width: 18rem;">
      <div class="developer-image">
        <img src="../images/devs/dev_2.png" class="d-block w-100 img-fluid" alt="">
      </div>
      <div class="card-body">
        <h5 class="card-title">Fulgencio</h5>
      </div>
    </div>
    <div class="card developer" style="width: 18rem;">
      <div class="developer-image">
        <img src="../images/devs/dev_3.png" class="d-block w-100 img-fluid" alt="">
      </div>
      <div class="card-body">
        <h5 class="card-title">Francis</h5>
      </div>
    </div>
    <div class="card developer" style="width: 18rem;">
      <div class="developer-image">
        <img src="../images/devs/dev_4.png" class="d-block w-100 img-fluid" alt="">
      </div>
      <div class="card-body">
        <h5 class="card-title">Krian</h5>
      </div>
    </div>
    <div class="card developer" style="width: 18rem;">
      <div class="developer-image">
        <img src="../images/devs/dev_5.png" class="d-block w-100 img-fluid" alt="">
      </div>
      <div class="card-body">
        <h5 class="card-title">Adrian</h5>
      </div>
    </div>
  </div>
</section>

HTML;

$content = $introduction_section . $booking_section . $modal_handler . $amenities_section . $rooms_carousel_section . $developers . $information_section;



include 'template.php';
