<!DOCTYPE html>

  <!--
  * Plugin Name: ImagesView.php
  * Description: Display All Images
  * Author: Olivier Paudex
  * Author Web Site: https://www.fuyens.ch
  -->

  <!------------------------------------------------------------------------------------------------------------------->

  <html>
    <head>
      <meta charset="utf-8" />
      <title>Azure Images Gallery</title>
      <link rel="stylesheet" media="screen" type="text/css" title="CSS" href="./css/styles1.css"/>
      <link rel="stylesheet" href="./awesome/css/fontawesome.min.css">
      <link rel="stylesheet" href="./awesome/css/solid.min.css">
      <link rel="stylesheet" href="./awesome/css/regular.min.css">

    </head>

    <body>
      <h1>My picture gallery</h1>

      <form action="index.php?action=uploadImages" method="post" enctype="multipart/form-data">    
        <div class="dropzone">
          <input type="file" name="files[]" id="files" onchange="javascript:form.submit()" multiple autofocus>
          <label for="files"><i class="fa-solid fa-upload"></i>Upload one or multiple files</label>
        </div>
      </form>

      <div class="container">
        <?php

          // Loop through all images
          while ($image = $images->fetch()) {
            try {
        ?>

              <div class="item">
                <div class="icons">

                  <!-- Display the delete and download icons buttons -->
                  <div class="icon"><a href="index.php?action=deleteImage&imageName=<?= htmlspecialchars($image->getName()) ?>"><i class="fa-solid fa-trash-can"></i></a></div>
                  <div class="icon"><a href="index.php?action=downloadImage&imageName=<?= htmlspecialchars($image->getName()) ?>"><i class="fa-solid fa-download"></i></a></div>
                </div>

                <!-- Display the base64 encoded image from Azure blob container -->
                <div class="img" style="background-image: url('<?= htmlspecialchars($image->getBlobContent($containerName)) ?>')"></div>
                
                <!-- Display the name and the date of the image -->
                <div class="leg"><?= htmlspecialchars($image->getName()) ?></div>
                <div class="date"><?= htmlspecialchars($image->getDate()) ?></div>
              </div>
            
        <?php
            }

            // If error, display a big-eye instead of the image
            catch(Exception $e) {
              $icon = "<i class='fa-regular fa-eye-slash'></i>";
        ?>

                <div class="img"><?= $icon ?></div>
                <div class="leg"><?= htmlspecialchars($image->getName()) ?></div>
                <div class="date"><?= htmlspecialchars($image->getDate()) ?></div>
              </div>

        <?php
            }
          }
          $images->closeCursor();
        ?>
      </div>
    </body>
  </html>