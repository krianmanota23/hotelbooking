<?php

include '../shared/connect.php';

if (isset($_POST['signin'])) {
  $email = $_POST['email'];
  $email = filter_var($email, 513); // clean input, remove malicious codes

  $pass = sha1($_POST['password']); // compute the password hash
  $pass = filter_var($pass, 513); // clean input, remove malicious codes

  $select_admins = $conn->prepare("SELECT users.id FROM users WHERE email = ? AND password = ? LIMIT 1");
  $select_admins->execute([$email, $pass]);
  $row = $select_admins->fetch(PDO::FETCH_ASSOC);

  if ($select_admins->rowCount() > 0) {
    setcookie('user_id', $row['id'], time() + 60 * 60 * 24 * 30, '/');
    header('location:index.php');
  } else {
    $warning_msg[] = 'Incorrect email or password!';
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
      <h1 class="h3 mb-3 fw-normal">Sign In</h1>
      <div class="form-floating">
        <input id="floatingInput" type="email" name="email" class="form-control" placeholder="enter username"
          maxlength="30" class="box" required oninput="this.value = this.value.replace(/\s/g, '')">
        <label for="floatingInput">Email</label>
      </div>
      <div class="form-floating">
        <input id="floatingPassword" type="password" name="password" class="form-control" placeholder="enter password"
          maxlength="30" class="box" required oninput="this.value = this.value.replace(/\s/g, '')">
        <label for="floatingPassword">Password</label>
      </div>
      <div class="form-check text-start my-3">
        <input class="form-check-input" type="checkbox" value="remember-me" id="flexCheckDefault">
        <label class="form-check-label" for="flexCheckDefault">
          Remember me
        </label>
      </div>
      <button class="btn btn-primary w-100 py-2" type="submit" name="signin">Sign in</button>
    </form>
  </main>
  <?php include '../shared/alerts.php'; ?>
</body>