<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php
if (isset($success_msg)) {
   foreach ($success_msg as $success_msg) {
      echo '<script>Swal.fire("' . $success_msg . '", "" ,"success");</script>';
   }
   $success_msg = [];
}

if (isset($warning_msg)) {
   foreach ($warning_msg as $warning_msg) {
      echo '<script>Swal.fire("' . $warning_msg . '", "" ,"warning");</script>';
   }
   $warning_msg = [];
}

if (isset($info_msg)) {
   foreach ($info_msg as $info_msg) {
      echo '<script>Swal.fire("' . $info_msg . '", "" ,"info");</script>';
   }
   $info_msg = [];
}

if (isset($error_msg)) {
   foreach ($error_msg as $error_msg) {
      echo '<script>Swal.fire("' . $error_msg . '", "" ,"error");</script>';
   }
   $error_msg = [];
}

?>