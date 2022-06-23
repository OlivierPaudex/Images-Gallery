<?php

  /**
  * Plugin Name: FileManager.php
  * Description: Manage output from $_FILES input form 
  * Author: Olivier Paudex
  * Author Web Site: https://www.fuyens.ch
  */

  namespace Fuyens\AzureImage;

  /********************************************************************************************************************/

  class FileManager {

    public $name;
    public $type;
    public $tmp_name;
    public $error;
    public $size;

    // Init
    public function __construct ($index) {
      $image = array_column($_FILES['files'], $index);
      list($name, $type, $tmp_name, $error, $size) = $image;
      $this->name = $name;
      $this->type = $type;
      $this->tmp_name = $tmp_name;
      $this->error = $error;
      $this->size = $size;
    }

    /**************************************************************************************************************************/

    // Get name
    public function getName() {
      return $this->name;
    }

    /**************************************************************************************************************************/

    // Get full path name (temporary name)
    public function getTempName() {
      return $this->tmp_name;
    }

    /**************************************************************************************************************************/

    // Get size
    public function getSize() {
      return $this->size;
    }

    /**************************************************************************************************************************/

    // Get type
    public function getType() {
      return $this->type;
    }

    /**************************************************************************************************************************/

    // Check if file is an image
    public function isImage() {
      return getimagesize($this->tmp_name);
    }

    /**************************************************************************************************************************/

    // Check if file exists
    public function fileExists($container) {
      return file_exists($container . $this->name);
    }

    /**************************************************************************************************************************/

    // Check error
    public function getError() {
      if ($this->error) {
        switch ($this->error) {
        case UPLOAD_ERR_OK:
          return 'There is no error, the file "' . $this->name . '" uploaded with success.';
        case UPLOAD_ERR_INI_SIZE:
          return 'The uploaded file "' . $this->name . '" exceeds the upload_max_filesize directive in php.ini.';
        case UPLOAD_ERR_FORM_SIZE:
          return 'The uploaded file "' . $this->name . '" exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.';
        case UPLOAD_ERR_PARTIAL:
          return 'The uploaded file "' . $this->name . '" was only partially uploaded.';
        case UPLOAD_ERR_NO_FILE:
          return 'No file was uploaded !';
        case UPLOAD_ERR_NO_TMP_DIR:
          return 'Missing a temporary folder !';
        case UPLOAD_ERR_CANT_WRITE:
          return 'Failed to write file "' . $this->name . '" to disk.';
        case UPLOAD_ERR_EXTENSION:
          return 'The uploaded file "' . $this->name . '" was stopped by extension.';
        default:
          return 'Unknown upload error !';
        }
      }
      return false;
    }
  }
?>