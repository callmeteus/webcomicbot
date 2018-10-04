<?php 
	function random_pic($includes = "*.*") {
	    $files 		= glob("images/partials" . $includes, GLOB_BRACE);
	    $file 		= array_rand($files);
	    return $files[$file];
	}

	function my_dir(){
	    $base_dir 	= __DIR__;

	    $doc_root 	= preg_replace("!${_SERVER['SCRIPT_NAME']}$!", '', $_SERVER['SCRIPT_FILENAME']);

		// server protocol
		$protocol 	= empty($_SERVER['HTTPS']) ? 'http' : 'https';

		// domain name
		$domain 	= $_SERVER['SERVER_NAME'];

		// base url
		$base_url 	= preg_replace("!^${doc_root}!", '', $base_dir);

		// server port
		$port 		= $_SERVER['SERVER_PORT'];
		$disp_port 	= ($protocol == 'http' && $port == 80 || $protocol == 'https' && $port == 443) ? '' : ":$port";

		// put em all together to get the complete base URL
		$url 		= "${protocol}://${domain}${disp_port}${base_url}";

		return $url;
	}

	$url 				= my_dir();
	$isMaking 			= isset($_POST["res0"]);

	$res0 				= $isMaking ? $_POST["res0"] : random_pic("/0/*.png");
	$res1 				= $isMaking ? $_POST["res1"] : random_pic("/{1,2}/*.png");
	$res2 				= $isMaking ? $_POST["res2"] : random_pic("/{1,2}/*.png");
	$res3 				= $isMaking ? $_POST["res3"] : random_pic("/3/*.png");

	$name 				= strlen($_GET["q"]) < 1 ? md5($res0 . $res1 . $res2 . $res3) : $_GET["q"];
	$file 				= "images/final/{$name}.png";

	$isFinal 			= file_exists($file);

	$completeUrl 		= $url;

	if ($isFinal) {
		$completeUrl 	.= "/" . $name;

		$img 			= imagecreatefrompng($file);
		$height 		= imagesy($img);
		$width 			= imagesx($img);
	}
?>
<html>
<head>
	<title>Webcomicbot</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<meta property="og:locale" content="en_US">
	<meta property="og:url" content="<?=$completeUrl?>">
	<meta property="og:title" content="<?=($isFinal) ? "Random generated comic" : "Webcomicbot"?>">
	<meta property="og:description" content="A bot that create random Webcomicname comics">
	<meta property="og:site_name" content="Webcomicbot">

	<meta property="og:type" content="website">

	<?php if ($isFinal) { ?>
		<meta property="og:image" content="<?=$url . "/" . $file?>">
		<meta property="og:image:type" content="image/png">
		<meta property="og:image:width" content="<?=$width?>">
		<meta property="og:image:height" content="<?=$height?>">
	<?php } ?>

	<style type="text/css">
		.img-display {
			display: inline-block;
			background-repeat: no-repeat;
			background-position: center;
			background-size: contain;
		}

		.img-display:after {
			content: "";
			display: block;
			padding-bottom: 100%;
		}

		@media screen and (max-width: 768px) {
			.col-4 {
				display: block;
				width: 100%;
				flex: none;
				max-width: 100%;
			}
		}

		body {
			overflow-x: hidden;
		}
	</style>
</head>
<body>
	<div class="container py-3">
		<h3>Webcomicbot</h3>

		<div class="row text-center">
			<?php
				

				if ($isFinal)
					echo "<img class='w-100' style='object-fit:contain' src='{$file}' />";
				else
				if ($isMaking) {
					if (!$isFinal) {
						$height 	= 500;
						$width 		= 1280;

						$handler 	= imagecreatetruecolor(1280, 500);
						imagefill($handler, 0, 0, 0xffffff);

						$img0 		= imagecreatefrompng($res0);
						$img1 		= imagecreatefrompng($res1);
						$img2 		= imagecreatefrompng($res2);
						$img3 		= imagecreatefrompng($res3);

						imagecopymerge($handler, $img0, 0, 10, 0, 0, 1280, 75, 100);
						imagecopymerge($handler, $img1, 0, 90, 0, 0, 415, 400, 100);
						imagecopymerge($handler, $img2, 430, 90, 0, 0, 415, 400, 100);
						imagecopymerge($handler, $img3, 846, 90, 0, 0, 415, 400, 100);

						imagepng($handler, $file);

						echo("<script>location.href='{$url}/{$name}'</script>");
						exit;
					}

					echo "<img class='w-100' style='object-fit:contain' height='{$height}' width='{$width}' src='{$file}' />";
				} else {
					echo "<img height='75' src='{$url}/{$res0}' /><br/>";
					echo "<div class='img-display col-4' style='background-image:url({$url}/{$res1})'></div>";
					echo "<div class='img-display col-4' style='background-image:url({$url}/{$res2})'></div>";
					echo "<div class='img-display col-4' style='background-image:url({$url}/{$res3})'></div>";
				}
			?>
		</div>

		<form method="post" class="mt-3">
			<input type="hidden" value="<?=$res0?>" name="res0" />
			<input type="hidden" value="<?=$res1?>" name="res1" />
			<input type="hidden" value="<?=$res2?>" name="res2" />
			<input type="hidden" value="<?=$res3?>" name="res3" />

			<?php if ($isFinal) { ?>
				<div class="btn-group w-100 mb-1">
					<a href="https://www.facebook.com/sharer.php?caption=Webcomicbot&description=Random Webcomicname generator&u=<?=$completeUrl?>&picture=<?=$url . "/" . $file?>
		" target="blank" class="btn w-50 btn-primary">Share</a>
					<a class="btn w-50 btn-success" href="<?=$url . "/" . $file?>" download>Save</a>
				</div>
			<?php } ?>

			<div class="btn-group w-100">
				<a href="<?=$url?>" class="btn btn-primary w-50">Make a new one</a>
				<button type="submit" class="btn btn-success w-50">Make it unique / Save</a>
			</div>
		</form>

		<hr/>

		<footer class="text-muted text-right">
			Bot created by <a href="https://github.com/theprometeus" target="_blank">Matheus Giovani</a>
		</footer>
	</div>
</body>