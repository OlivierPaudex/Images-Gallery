<?php

  /**
  * Plugin Name: Controller.php
  * Description: All functions
  * Author: Olivier Paudex
  * Author Web Site: https://www.fuyens.ch
  */

  require_once('./php/models/ImageManager.php');
  require_once('./php/models/FileManager.php');
  require_once('./php/models/BlobManager.php');
  use \Fuyens\AzureImage\ImageManager;
  use \Fuyens\AzureImage\FileManager;
  use \Fuyens\AzureImage\BlobManager;

  /********************************************************************************************************************/

  function displayImages($containerName) {

    // Get all images from Database
    $imageManager = new ImageManager();
    $images = $imageManager->getImages();

    // Fetch each image with a custom class
    $class = '\Fuyens\AzureImage\BlobContent';
    $images->setFetchMode(PDO::FETCH_CLASS, $class);

    require('./php/views/ImagesView.php');
  }

  /********************************************************************************************************************/

  function deleteImage($containerName, $filename) {

    // Storage connexion
    $blobManager = new BlobManager();
    $blobClient = $blobManager->storageConnect();    

    // Delete blob file
    $blobManager->deleteBlob($blobClient, $containerName, $filename);

    // Delete row from Azure SQL Database
    $imageManager = new ImageManager();
    $images = $imageManager->deleteImage($filename);

    // Go back to index.php and refresh
    ob_start();
    header('Location: ./index.php');
    ob_end_flush();
  }

  /********************************************************************************************************************/

  function downloadImage($containerName, $filename) {

    // Storage connexion
    $blobManager = new BlobManager();
    $blobClient = $blobManager->storageConnect();

    // Download blob file
    $blobManager->downloadBlob($blobClient, $containerName, $filename);
  }

  /********************************************************************************************************************/

  function uploadImages($containerName) {
    
    // Set the images counter
    $imagesUploaded = 0;

    // Check if not limited by php.ini
    if (!is_countable($_FILES['files']['tmp_name'])) {
      throw new Exception ('The total size of files exceeds the post_max_size directive in php.ini.');
    }
    
    // Storage connexion
    $blobManager = new BlobManager();
    $blobClient = $blobManager->storageConnect();

    // Create blobs container if necessary
    if (!$blobManager->createContainer($blobClient, $containerName, $errorMessage)) {
      throw new Exception ($errorMessage['errorMessage']);
    }
    
    // Loop through all images to upload
    for ($index = 0; $index < count($_FILES['files']['tmp_name']); $index++) {
      try {
        $fileManager = new FileManager($index);
        $imageManager = new ImageManager();

        // Check if file has errors
        if ($fileManager->getError()) throw new Exception ($fileManager->getError());
       
        // Check if file is an image
        if (!$fileManager->isImage()) throw new Exception ('The file "' . $fileManager->getName() . '" is not an image.');

        // Check if file extension is allowed
        $type = $fileManager->getType();
        if ($type != 'image/jpg' && $type != 'image/jpeg' && $type != 'image/png' && $type != 'image/gif') {
          throw new Exception ('The file type of "' . $fileManager->getName() . '" is not allowed.');
        }

        // Check if blob file already exists
        if ($blobManager->blobExists($blobClient, $containerName, $fileManager->getName())) {
          throw new Exception ('The file "' . $fileManager->getName() . '" already exists.');
        }

        // Upload blob file to Azure container
        $filename = $fileManager->getName();
        $content = fopen($fileManager->getTempName(), "r");
        $mimeType = $fileManager->getType();

        if (!$blobManager->uploadBlob($blobClient, $containerName, $filename, $content, $mimeType, $errorMessage)) {
          throw new Exception ($errorMessage['errorMessage']);
        }

        // Save blob file to a database
        $fileUrl = $blobManager->getBlobUrl($blobClient, $containerName, $fileManager->getName(), $errorMessage);
        if (!$fileUrl) throw new Exception ($errorMessage['errorMessage']);

        if (!$imageManager->SaveImage($fileManager->getName(), $fileUrl)) {
          throw new Exception ('The file "' . $fileManager->getName() . '" was not saved into the database.');
        }
      }
      catch (Exception $e) {
        $errorMessages[] = $e->getMessage();
        continue;
      }
      $imagesUploaded++;
    }
    require('./php/views/UploadView.php');
  }
?>