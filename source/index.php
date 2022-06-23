<?php
  require('./php/controllers/Controller.php');

  // Set the azure container for the images
  $containerName = 'images';

  try {
    if (isset($_GET['action'])) {
      switch ($_GET['action']) {
      case "uploadImages":
        uploadImages($containerName);
        break;
      case "deleteImage":
        if (isset($_GET['imageName'])) {
          deleteImage($containerName, $_GET['imageName']);
        }
        break;
      case "downloadImage":
        if (isset($_GET['imageName'])) {
          downloadImage($containerName, $_GET['imageName']);
        }
        break;
      }
    }
    else {
      displayImages($containerName);
    }
  }
  catch(Exception $e) {
   $errorMessage = $e->getMessage();
   require('./php/views/ErrorView.php');
  }
?>