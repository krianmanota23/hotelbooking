<?php
// Check if user is logged in 
if (!isset($_COOKIE['admin_id'])) {
  header('location:login.php');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($title) ? $title : 'Admin Dashboard'; ?></title>
  <!-- bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <!-- bootstrap-icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">

  <style>
    .parent-container {
      max-width: 1080px;
      height: 100vh;
    }
  </style>
  <!-- custom styles -->
  <?php echo isset($styles) ? $styles : ''; ?>
</head>

<body class="parent-container container-fluid d-flex flex-column justify-content-between">
  <div>
    <!-- navbar start  -->
    <?php include 'components/header.php'; ?>
    <!-- navbar end  -->

    <!-- main content start  -->
    <main class="container">
      <?php echo isset($content) ? $content : ''; ?>
    </main>
    <!-- main content end  -->
  </div>

  <!-- footer start  -->
  <?php include 'components/footer.php'; ?>
  <!-- footer end  -->

  <!-- bootstrap js  -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
  <?php include '../shared/alerts.php'; ?>

</body>

</html>