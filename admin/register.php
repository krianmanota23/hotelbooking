<?php
// connect to database
include '../shared/connect.php';

if (isset($_POST['signup'])) {


   $username = $_POST['username'];
   $username = filter_var($username, 513);
   $firstname = $_POST['first_name'];
   $firstname = filter_var($firstname, 513);
   $lastname = $_POST['last_name'];
   $lastname = filter_var($lastname, 513);
   $email = $_POST['email'];
   $email = filter_var($email, 513);
   $phonenumber = $_POST['phone_number'];
   $phonenumber = filter_var($phonenumber, 513);
   $pass = sha1($_POST['password']);
   $pass = filter_var($pass, 513);
   $c_pass = sha1($_POST['confirm_password']);
   $c_pass = filter_var($c_pass, 513);

   $select_admins = $conn->prepare("SELECT * FROM `Users` WHERE username = ? OR email = ?");
   $select_admins->execute([$username, $email]);

   if ($select_admins->rowCount() > 0) {
      $warning_msg[] = 'Username or email is already taken!';
   } else {
      if ($pass != $c_pass) {
         $warning_msg[] = 'Password not matched!';
      } else {
         $insert_admin = $conn->prepare("INSERT INTO `Users`(username,first_name, last_name, email, is_admin, phone_number, password) VALUES(?,?,?,?,?,?,?)");

         $insert_admin->execute([$username, $firstname, $lastname, $email, true, $phonenumber, $c_pass]);
         $success_msg[] = 'Registered successfully!';
      }
   }

}

// set the value of content that will be displayed in the body section
$content = <<<HTML
   <main class="form-sign-up m-auto col-6 mt-3">
      <h1 class="h3 mb-3 fw-normal">Create admin</h1>
      <form data-bitwarden-watching="1" action="" method="POST">
            <div class="form-floating mb-1">
               <input id="username" type="text" name="username" class="form-control" placeholder=""
                  maxlength="20" class="box" required>
               <label for="username">Username</label>
            </div>
         <div class="form-floating mb-1">
               <input id="email" type="email" name="email" class="form-control" placeholder=""
                  maxlength="20" class="box" required>
               <label for="email">Email</label>
            </div>
            <div class="form-floating mb-1">
         <input id="contact_number" type="text" name="phone_number" class="form-control" placeholder=""
            maxlength="20" class="box" required>
         <label for="contact_number">Contact number</label>
      </div>
            <div class="form-floating mb-1">
               <input id="first_name" type="text" name="first_name" class="form-control" placeholder=""
                  maxlength="20" class="box" required>
               <label for="first_name">First name</label>
            </div>
            <div class="form-floating mb-1">
               <input id="last_name" type="text" name="last_name" class="form-control" placeholder=""
                  maxlength="20" class="box" required>
               <label for="last_name">Last name</label>
            </div>
            <div class="form-floating mb-1">
               <input id="password" type="password" name="password" class="form-control" placeholder=""
                  maxlength="20" class="box" required>
               <label for="password">Password</label>
            </div>
            <div class="form-floating mb-3">
               <input id="confrm_password" type="password" name="confirm_password" class="form-control" placeholder=""
                  maxlength="20" class="box" required>
               <label for="confrm_password">Confirm password</label>
            </div>
         <button class="btn btn-primary w-100 py-2" type="submit" name="signup">Register</button>
      </form>
   </main>
</section>
HTML;

// include the admin base template
include 'template.php';
?>