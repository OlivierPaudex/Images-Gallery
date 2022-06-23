<!DOCTYPE html>

  <!--
  * Plugin Name: ErrorView.php
  * Description: Display Error Messages
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
      <h1>Error</h1>
      <div>
        <p><?= htmlspecialchars($errorMessage) ?></p>
        <br>
        <p><a href=javascript:history.go(-1)>Retour</a></p>
      </div>
    </body>
  </html>