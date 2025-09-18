<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_FILES['file'])) {
 
    $file = $_FILES['file'];
 

 $picture_name = "temp_photo";
 
    $_SESSION['picture_name'] = '';
    $_SESSION['picture_name'] = $picture_name;
    
    // remove temp picture if exists
    $uploadFile = "customer/images/".$picture_name.".jpg";
    file_exists($old_filename)? unlink($old_filename):'';
 
  $old_filename = "customer/images/".$_SESSION['picture_name'].".jpg";
    if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
      
      // File uploaded successfully
      echo 'File uploaded successfully.';
    } else {
      // Error uploading file
      echo 'Error uploading file.';
    }
  } else {
    // No file sent
    echo 'No file sent.';
  }
} else {
  // Invalid request method
  echo 'Invalid request method.';
}
?>
