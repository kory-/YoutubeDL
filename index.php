<?php
    require_once "YouTubeDL.php";

    $url = "http://www.youtube.com/watch?v=86MA3fGxJno";

    if ($_POST["url"]) {
        if (preg_match("/http:\/\/www\.youtube\.com\/watch\?v\=(\w+)/", $_POST["url"])) {
            $url = $_GET["url"];
        }
    }
    $youtube = new YouTubeDL($url);

    $result = $youtube->getYouTubeDL();
    $thum = $youtube->getThumnail($result["video_id"]);
    
    switch ($_POST["mode"]) {
        case "dl":
            $youtube->downloadVideo2($result, $_POST["fmt"]);
            break;
        case "sd":
            $youtube->downloadVideo($result, $_POST["fmt"]);
            break;
        default:
            break;
    }
?>


<!DOCTYPE HTML>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>youtube</title>
</head>
<body>
	<?php print'<pre>'; print_r($result); print'</pre>';?>
	<img src="<?php echo $thum;?>">
    <form action="" method="post">
    	<table>
        <tr><th>url:</th><td><input type="text" name="url" id="" /></td>
        <tr><th>mode:</th><td>download<input type="radio" name="mode" value="dl" />
        server download<input type="radio" name="mode" value="sd" /></td>
        <tr><th>format:</th><td><input type="text" name="fmt" id="" /></td>
    	</table>
    <input type="submit" value="send" />
    </form>
</body>
</html>
