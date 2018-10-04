<?php 
	$dir 			= "images/partials/";

	function cropImage($image, $index) {
		$width 		= imagesx($image);
		$height 	= imagesy($image);

		$index--;
		$x 			= ($index * 415) + 14 + $index;

		return imagecrop($image, ["x" => $x, "y" => 80, "width" => 415, "height" => 400]);
	}

	function doCrop($url) {
		global $dir;

		$image 		= str_replace(["_540.png", "_500.png"], "_1280.png", $url);

		$name 		= basename($image);
		$name 		= str_replace(["tumblr_", "_1280", ".png", ".jpg"], "", $name);

		echo "<strong>{$name}</strong><br/>{$image}<br/>";

		if (file_exists("{$dir}/0/{$name}.png")) {
			echo "This comic already exists.";
			return false;
		}

		if (stripos($image, ".jpg") !== false)
			$handler 	= imagecreatefromjpeg($image);
		else
			$handler	= imagecreatefrompng($image);

		$width 		= imagesx($handler);

		if ($width != 1280) {
			echo "Invalid width.";
			return false;
		}

		$crop0 		= imagecrop($handler, ["x" => 0, "y" => "0", "width" => 1280, "height" => 75]);
		$crop1 		= cropImage($handler, 1);
		$crop2 		= cropImage($handler, 2);
		$crop3 		= cropImage($handler, 3);

		$res0 		= imagepng($crop0, "{$dir}/0/{$name}.png");
		$res1 		= imagepng($crop1, "{$dir}/1/{$name}.png");
		$res2 		= imagepng($crop2, "{$dir}/2/{$name}.png");
		$res3 		= imagepng($crop3, "{$dir}/3/{$name}.png");

		imagedestroy($handler);

		echo "<img src='{$dir}/0/{$name}.png' /><br/>";
		echo "<img src='{$dir}/1/{$name}.png' /> ";
		echo "<img src='{$dir}/2/{$name}.png' /> ";
		echo "<img src='{$dir}/3/{$name}.png' />";
	}

	if (!isset($_GET["url"])) {
		$url 			= "http://webcomicname.com/rss";
		$data 			= file_get_contents($url);
		$xml 			= simplexml_load_string($data);

		foreach($xml->channel->item as $item) {
			$dom 		= @new DOMDocument();

			libxml_use_internal_errors(true);

			$dom->loadHTML($item->description);
			$xpath 		= @new DOMXPath($dom);

			doCrop($xpath->evaluate("string(//img/@src)"));

			echo "<hr/>";
		}
	} else
		doCrop($_GET["url"]);