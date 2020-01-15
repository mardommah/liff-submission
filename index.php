<?php
require_once 'vendor/autoload.php';
require_once "./random_string.php";

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;

$connectionString = "DefaultEndpointsProtocol=https;AccountName=lineblob2;AccountKey=0APqhXQc2PJMXl68NYMPvfbIc27PjvY1zgHePxOGxd7F7/PLAvh+F2JDARC9a6Y0gr04zfg+gaJBq8fVRmlh+g==;EndpointSuffix=core.windows.net";
$containerName = "lineblob";
// Create blob client.
$blobClient = BlobRestProxy::createBlobService($connectionString);
if (isset($_POST['submit'])) {
	$fileToUpload = strtolower($_FILES["fileToUpload"]["name"]);
	$content = fopen($_FILES["fileToUpload"]["tmp_name"], "r");
	// echo fread($content, filesize($fileToUpload));
	$blobClient->createBlockBlob($containerName, $fileToUpload, $content);
	header("Location: index.php");
}
$listBlobsOptions = new ListBlobsOptions();
$listBlobsOptions->setPrefix("");
$result = $blobClient->listBlobs($containerName, $listBlobsOptions);
?>

<!DOCTYPE html>
<html>
 <head>
 <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0>
    <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Image Analyzer App</title>
  </head>
<body>
	<main role="main" class="container">
    		<div class="starter-template"> <br>
        		<h1>Image Analyzer</h1>
			</div>
		<div class="mt-4 mb-2">
			<form class="d-flex justify-content-lefr" action="index.php" method="post" enctype="multipart/form-data">
				<input type="file" name="fileToUpload" accept=".jpeg,.jpg,.png" required="">
				<input type="submit" name="submit" value="Upload">
			</form>
		</div>
		<br>
		<br>
		<table class='table table-hover'>
			<thead>
				<tr>
					<th>File Name</th>
					<th>File URL</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				<?php
				do {
					foreach ($result->getBlobs() as $blob)
					{
						?>
						<tr>
							<td><?php echo $blob->getName() ?></td>
							<td><?php echo $blob->getUrl() ?></td>
							<td>
								<form action="analisa.php" method="post">
									<input type="hidden" name="url" value="<?php echo $blob->getUrl()?>">
									<input type="submit" name="submit" value="Analyze!" class="btn btn-primary">
								</form>
							</td>
						</tr>
						<?php
					}
					$listBlobsOptions->setContinuationToken($result->getContinuationToken());
				} while($result->getContinuationToken());
				?>
			</tbody>
		</table>

		<div class="buttongroup">
        <div class="buttonrow">
            <button id="openwindowbutton">Open Window</button>
            <button id="closewindowbutton">Close Window</button>
        </div>
        <div class="buttonrow">
            <button id="getaccesstoken">Get Access Token</button>
            <button id="getprofilebutton">Get Profile</button>
            <button id="sendmessagebutton">Send Message</button>
        </div>
    </div>

    <div id="accesstokendata">
        <h2>Access Token</h2>
        <a href="#" onclick="toggleAccessToken()">Close Access Token</a>
        <table border="1">
            <tr>
                <th>accessToken</th>
                <td id="accesstokenfield"></td>
            </tr>
        </table>
    </div>

    <div id="profileinfo">
        <h2>Profile</h2>
        <a href="#" onclick="toggleProfileData()">Close Profile</a>
        <div id="profilepicturediv">
        </div>
        <table border="1">
            <tr>
                <th>userId</th>
                <td id="useridprofilefield"></td>
            </tr>
            <tr>
                <th>displayName</th>
                <td id="displaynamefield"></td>
            </tr>
            <tr>
                <th>statusMessage</th>
                <td id="statusmessagefield"></td>
            </tr>
        </table>
    </div>

    <div id="liffdata">
        <h2>LIFF Data</h2>
        <table border="1">
            <tr>
                <th>language</th>
                <td id="languagefield"></td>
            </tr>
            <tr>
                <th>context.viewType</th>
                <td id="viewtypefield"></td>
            </tr>
            <tr>
                <th>context.userId</th>
                <td id="useridfield"></td>
            </tr>
            <tr>
                <th>context.utouId</th>
                <td id="utouidfield"></td>
            </tr>
            <tr>
                <th>context.roomId</th>
                <td id="roomidfield"></td>
            </tr>
            <tr>
                <th>context.groupId</th>
                <td id="groupidfield"></td>
            </tr>
        </table>
    </div>

	</div>

<!-- Placed at the end of the document so the pages load faster -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery-slim.min.js"><\/script>')</script>
   <!--  <script src="https://getbootstrap.com/docs/4.0/assets/js/vendor/popper.min.js"></script>
    <script src="https://getbootstrap.com/docs/4.0/dist/js/bootstrap.min.js"></script> -->
	<link rel="stylesheet" type="text/css" href="style.css">
	<script src="liff-starter.js"></script>
  </body>
</html>
