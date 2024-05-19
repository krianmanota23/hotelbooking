<?php

include '../shared/connect.php';

if (isset($_COOKIE['user_id'])) {
  $user_id = $_COOKIE['user_id'];
} else {
  header('location:login.php');
}

$sql_user = $conn->prepare("SELECT * FROM users WHERE id = ?");
$sql_user->execute([$user_id]);
$user = $sql_user->fetch(PDO::FETCH_ASSOC);

if (isset($_POST['update'])) {
  $password = $_POST['password'];
  $password = filter_var($password, 513);

  $confirm_password = $_POST['confirm-password'];
  $confirm_password = filter_var($confirm_password, 513);
  if ($password != $confirm_password) {
    $error_msg[] = 'Passwords must match!';
  } else {
    $password = sha1($password);
    $sql_update_password = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $sql_update_password->execute([$password, $user_id]);
    $success_msg[] = 'Username updated!';
    header('location:bookings.php');
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>

  <!-- font awesome cdn link  -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
  <!-- bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <!-- custom css file link  -->
  <link rel="stylesheet" href="../css/login.css">
</head>

<body class="d-flex align-items-center py-4 bg-body-tertiary">
  <main class="form-signin w-100 m-auto">
    <form data-bitwarden-watching="1" action="" method="POST">
      <h1 class="h3 mb-3 fw-normal">Hi, <?= $user["first_name"] ?></h1>
      <p>Please update your password</p>
      <div class="form-floating">
        <input id="floatingPassword" type="text" name="email" class="form-control" placeholder="enter password"
          maxlength="20" class="box" required disabled value="<?= $user['email'] ?>">
        <label for="floatingPassword">Email</label>
      </div>
      <div class="form-floating">
        <input id="floatingPassword" type="password" name="password" class="form-control" placeholder="enter password"
          maxlength="20" class="box" required oninput="this.value = this.value.replace(/\s/g, '')">
        <label for="floatingPassword">Password</label>
      </div>
      <div class="form-floating">
        <input id="floatingPassword" type="password" name="confirm-password" class="form-control"
          placeholder="enter password" maxlength="20" class="box" required
          oninput="this.value = this.value.replace(/\s/g, '')">
        <label for="floatingPassword">Confirm password</label>
      </div>
      <button class="btn btn-primary w-100 py-2" type="submit" name="update">Update</button>
    </form>
  </main>
  <?php include '../shared/alerts.php'; ?>
</body>