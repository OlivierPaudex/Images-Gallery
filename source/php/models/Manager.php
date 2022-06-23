<?php

  /**
  * Plugin Name: Manager.php
  * Description: DB Connection
  * Author: Olivier Paudex
  * Author Web Site: https://www.fuyens.ch
  */

  namespace Fuyens\AzureImage;
  require_once 'vendor/autoload.php';

  use PDO;
  use MicrosoftAzure\Storage\Blob\BlobRestProxy;
  use AzKeyVault\Secret;

  /********************************************************************************************************************/

  class SQLManager {

    // Connexion to Database
    protected function dbConnect() {

      /* Azure SQL */
      $servername = "tcp:sqlsrv-imagesgallery-westeu-001.database.windows.net";
      $username = "azadmin";
      $password = getKeyVaultSecret('key-imagesgallery-sqldb');
      $dbname = "sqldb-imagesgallery-westeu-001";

      $bdd = new PDO("sqlsrv:server=" . $servername . ";database=" . $dbname, $username, $password);
      $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      return $bdd;
    }
  }

  /********************************************************************************************************************/
  
  class StorageManager {

    // Connexion to Azure storage account
    public function storageConnect() {
   
        // Azure String connection
        $accountName = 'stimagesgallerywesteu001';
        $accountKey = getKeyVaultSecret('key-imagesgallery-storage');

        $connectionString = "DefaultEndpointsProtocol=https;AccountName=" . $accountName . ";AccountKey=" . $accountKey;

        // Create blob client.
        $blobClient = BlobRestProxy::createBlobService($connectionString);
              
        return $blobClient;
    }
  }

  /********************************************************************************************************************/

  // Get secrets keys from Azure KeyVault
  function getKeyVaultSecret($keyName) {

    // Keyvault URL
    $url = 'https://key-imgallery-westeu-001.vault.azure.net/';

    // Get Secret from Keyvault
    $secret = new Secret($url);
    return $secret->getSecret($keyName);
  }
?>