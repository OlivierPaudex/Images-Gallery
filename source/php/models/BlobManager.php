<?php

  /**
  * Plugin Name: BlobManager.php
  * Description: Upload file to Azure Blob Service
  * Author: Olivier Paudex
  * Author Web Site: https://www.fuyens.ch
  */

  namespace Fuyens\AzureImage;
  require_once 'vendor/autoload.php';
  require_once('./php/models/Manager.php');

  //use MicrosoftAzure\Storage\Blob\BlobRestProxy;
  use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
  use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
  use MicrosoftAzure\Storage\Blob\Models\CreateBlockBlobOptions;
  use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
  use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;

  /********************************************************************************************************************/

  Class BlobContent Extends StorageManager {

    // Database fields
    public $ID;
    public $Name;
    public $URL;
    public $Creation_Date;

    // Get ID from database
    public function getID() {
      return $this->ID;
    }

    // Get Name from database
    public function getName() {
      return $this->Name;
    }

    // Get Url from database
    public function getUrl() {
      return $this->URL;
    }

    // Get Date from database
    public function getDate() {
      return $this->Creation_Date;
    }

    // Get blob content from Azure storage
    public function getBlobContent($containerName) {

      // Storage connexion
      $blobClient = $this->storageConnect();
    
      // Get blob file and mime property
      $blob = $blobClient->getBlob($containerName, $this->Name);
      $properties = $blobClient->getBlobProperties($containerName, $this->Name);
      $mimeType = $properties->getProperties()->getContentType();

      // Get base64 encoded blob file stream
      $stream = stream_get_contents($blob->getContentStream());
      $fileEncode = base64_encode($stream);

      // Return the source stream
      $src = 'data: '. $mimeType . ';base64,' . $fileEncode;
      return $src;
    }
  }

  /********************************************************************************************************************/

  Class BlobManager Extends StorageManager {

    // Create container if not found
    public function createContainer($blobClient, $containerName, &$errorMessage) {
      try {

        // Container already exists
        $blobClient->getContainerProperties($containerName);
        return true;
      }
      catch(ServiceException $e) {
        try {
          
          // Container not found (404)
          if ($e->getCode() == 404) {
            $createContainerOptions = new CreateContainerOptions();
            $createContainerOptions->setPublicAccess(PublicAccessType::CONTAINER_AND_BLOBS);
            $blobClient->createContainer($containerName, $createContainerOptions);
            return true;
          }
        }
        catch(ServiceException $e) {
          $errorMessage = array('errorMessage'=>$e->getMessage(), 'Code'=>$e->getCode());
          return false;
        }
      }
    }

    /**************************************************************************************************************************/

    // Return cache time for one day in seconds
    public function getCacheTimeByMimeType($mimeType) {
      $mimeType = strtolower($mimeType);

      $types = array(
          "image/bmp" => 86400,
          "image/gif" => 86400,
          "image/jpeg" => 86400,
          "image/png" => 86400,
      );

      // return value
      if(array_key_exists($mimeType, $types)) {
        return $types[$mimeType];
      }

      return false;
    }

    /**************************************************************************************************************************/

    // Upload blob file to container
    public function uploadBlob($blobClient, $containerName, $filename, $content, $mimeType, &$errorMessage) {
      try {
        // Set options
        $options = new CreateBlockBlobOptions();
        $options->setContentType($mimeType);

        // Set cache control time
        if ($mimeType) {
          $cacheTime = $this->getCacheTimeByMimeType($mimeType);
          
          if ($cacheTime) {
            $options->setCacheControl("public, max-age=" . $cacheTime);
          }
        }

        // Create blob file 
        $blobClient->createBlockBlob($containerName, $filename, $content, $options);
        return true;
      }
      catch(ServiceException $e) {
        $errorMessage = array('errorMessage'=>$e->getMessage(), 'Code'=>$e->getCode());
        return false;
      }
    }

    /**************************************************************************************************************************/

    // Download blob from container
    public function downloadBlob($blobClient, $containerName, $filename) {

      // Change header before downloading the blob
      $blob = $blobClient->getBlob($containerName, $filename);
      $properties = $blobClient->getBlobProperties($containerName, $filename);
      $size = $properties->getProperties()->getContentLength();
      $mime = $properties->getProperties()->getContentType();

      header("Content-Type: $mime");
      header("Content-Length: $size");
      header('Content-Disposition: attachment; filename="' . $filename . '"');
      fpassthru($blob->getContentStream());
    }

    /**************************************************************************************************************************/

    // Check if blob file already exists in a container
    public function blobExists($blobClient, $containerName, $filename) {
      try {
        $blobClient->getBlob($containerName, $filename);
        return true;
      }
      catch(ServiceException $e) {
        return false;
      }
    }

    /**************************************************************************************************************************/
    
    // Get Url from Blob files
    public function getBlobUrl($blobClient, $containerName, $filename, &$errorMessage) {
      try {

        // Search for blob file into container and return Url
        if (!empty($filename)) {        
          $listBlobsOptions = new ListBlobsOptions();
          $listBlobsOptions->setPrefix($filename);
          $blobList = $blobClient->listBlobs($containerName, $listBlobsOptions);
          $blobs = $blobList->getBlobs();

          foreach($blobs as $blob) {
            return $blob->getUrl();
          }
        }
      }
      catch(ServiceException $e) {
        $errorMessage = array('errorMessage'=>$e->getMessage(), 'Code'=>$e->getCode());
        return false;
      }
    }

    /**************************************************************************************************************************/

    // Delete blob file from container
    public function deleteBlob($blobClient, $containerName, $filename) {
      $blobClient->deleteBlob($containerName, $filename);
    }
  }
?>