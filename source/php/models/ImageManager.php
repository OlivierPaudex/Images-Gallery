<?php

  /**
  * Plugin Name: ImageManager.php
  * Description: Manage Images from Database
  * Author: Olivier Paudex
  * Author Web Site: https://www.fuyens.ch
  */

  namespace Fuyens\AzureImage;
  require_once('./php/models/Manager.php');

  /********************************************************************************************************************/

  Class ImageManager Extends SQLManager {
    
    public function getImages() {
      $bdd = $this->dbConnect();

      /* Azure SQL */
      $sql = $bdd->prepare("SELECT ID, Name, URL, FORMAT(Creation_Date AT TIME ZONE 'UTC' AT TIME ZONE 'Central Europe Standard Time', 'dd MMMM yyyy - HH:mm:ss', 'fr-FR')
                            AS Creation_Date FROM images ORDER BY Creation_Date DESC");
      $sql->execute();
      return $sql;
    }

    /**************************************************************************************************************************/

    public function getImage($id) {
      $bdd = $this->dbConnect();

      /* Get image from ID */
      $sql = $bdd->prepare("SELECT ID, Name, URL, FORMAT(Creation_Date AT TIME ZONE 'UTC' AT TIME ZONE 'Central Europe Standard Time', 'dd MMMM yyyy - HH:mm:ss', 'fr-FR')
                            AS Creation_Date FROM images WHERE ID=? ORDER BY Creation_Date DESC");
      $sql->execute([$id]);
      return $sql;
    }

    /**************************************************************************************************************************/

    public function saveImage($filename, $fileUrl) {
      $bdd = $this->dbConnect();

      // Date should be always inserted into US style "Y/m/d H:i:s to be compatible with Azure SQL Database"
      $sql = $bdd->prepare('INSERT INTO images(Name, URL, Creation_Date) VALUES(?, ?, ?)');
      $sql->execute(array($filename, $fileUrl, date('Y/m/d H:i:s')));
      return $sql;
    }

    /**************************************************************************************************************************/

    public function deleteImage($filename) {
      $bdd = $this->dbConnect();

      $sql = $bdd->prepare('DELETE FROM images WHERE Name=?');
      $sql->execute([$filename]);
      return $sql;
    }
  }
?>