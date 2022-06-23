<!DOCTYPE html>

  <!--
  * Plugin Name: UploadedView.php
  * Description: Display the images upload status
  * Author: Olivier Paudex
  * Author Web Site: https://www.fuyens.ch
  -->

  <!------------------------------------------------------------------------------------------------------------------->

  <html>
    <head>
      <meta charset="utf-8" />
      <title>Azure Images Gallery</title>
      <link rel="stylesheet" media="screen" type="text/css" title="CSS" href="./css/styles1.css"/>
    </head>

    <body>
      <h1>Status of the uploaded images</h1>
      <div>
        <div class="leg">Uploaded images : <?= htmlspecialchars($imagesUploaded) ?> on <?= count($_FILES['files']['tmp_name']) ?></div>

        <?php
          if(!empty($errorMessages)) {
            foreach ($errorMessages as $errorMessage) {
        ?>
              <p><?= htmlspecialchars($errorMessage) ?></p>
        <?php
            }
          }
        ?>        
      </div>
      <p><a href="index.php";>Go back</a></p>
    </body>
  </html>