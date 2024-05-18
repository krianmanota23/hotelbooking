<?php
// connect to database
include '../components/connect.php';

if(isset($_POST['submit'])){

    
    $username = $_POST['user_name'];
    $username = filter_var($username, FILTER_SANITIZE_STRING); 
    $firstname = $_POST['first_name'];
    $firstname = filter_var($firstname, FILTER_SANITIZE_STRING); 
    $lastname = $_POST['last_name'];
    $lastname = filter_var($lastname, FILTER_SANITIZE_STRING);
    $email = $_POST['email'];
    $email = filter_var($email, FILTER_SANITIZE_STRING); 
    $phonenumber = $_POST['phone_number'];
    $phonenumber = filter_var($phonenumber, FILTER_SANITIZE_STRING);  
    $pass = sha1($_POST['pass']);
    $pass = filter_var($pass, FILTER_SANITIZE_STRING); 
    $c_pass = sha1($_POST['c_pass']);
    $c_pass = filter_var($c_pass, FILTER_SANITIZE_STRING);   
 
    $select_admins = $conn->prepare("SELECT * FROM `Users` WHERE username = ? OR email = ?");
    $select_admins->execute([$username, $email]);
 
    if($select_admins->rowCount() > 0){
       $warning_msg[] = 'Username or email is already taken!';
    }else{
       if($pass != $c_pass){
          $warning_msg[] = 'Password not matched!';
       }else{
          $insert_admin = $conn->prepare("INSERT INTO `Users`(username,first_name, last_name, email, phone_number, password) VALUES(?,?,?,?,?,?)");
          
    $insert_admin->execute([$username, $firstname, $lastname, $email, $phonenumber, $c_pass]);
          $success_msg[] = 'Registered successfully!';
       }
    }
 
 }

// set the value of content that will be displayed in the body section
$content = <<<HTML
<section class="form-container">

<form action="" method="POST">
   <h3>register new</h3>
   <input type="text" name="user_name" placeholder="enter username" maxlength="20" class="box" required oninput="this.value = this.value.replace(/\s/g, '')">
   <input type="text" name="first_name" placeholder="enter firstname" maxlength="20" class="box" required oninput="this.value = this.value.replace(/\s/g, '')">
   <input type="text" name="last_name" placeholder="enter lastname" maxlength="20" class="box" required oninput="this.value = this.value.replace(/\s/g, '')">
   <input type="text" name="email" placeholder="enter email" maxlength="20" class="box" required oninput="this.value = this.value.replace(/\s/g, '')">
   <input type="text" name="phone_number" placeholder="enter phone number" maxlength="20" class="box" required oninput="this.value = this.value.replace(/\s/g, '')">
    <input type="password" name="pass" placeholder="enter password" maxlength="20" class="box" required oninput="this.value = this.value.replace(/\s/g, '')">
   <input type="password" name="c_pass" placeholder="confirm password" maxlength="20" class="box" required oninput="this.value = this.value.replace(/\s/g, '')">
   <input type="submit" value="register now" name="submit" class="btn">
</form>

</section>
HTML;

// include the admin base template
include '../components/admin_template.php';
?>