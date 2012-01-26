<?php
    require_once "YouTube.php";

    $cnt = 0;

    // Country Code
    $hl = $_GET["hl"] ? $_GET["hl"] : "JP";

    // Feed Code
    $fe = $_GET["fe"] ? $_GET["fe"] : "most_recent";

    // Display Mode
    $dm = $_GET["dm"] == "list" ? $_GET["dm"] : "xml";

    $youtube = new YouTube($hl, $fe);

    $xml = $youtube->getXmlList($youtube->getUrl());
    foreach ($xml->entry as $key => $entry) {
        $list[$cnt]["thumnail"] = $youtube->getThumnail($entry->id, 2);
        $list[$cnt]["title"] = $entry->title;
        $list[$cnt]["publish"] = $entry->published;
        $cnt++;
    }

    // make change mode link
    $link = '<a href= "' . "?hl=" . $hl . "&fe=" . $fe . "&dm=" . ($dm=="xml" ? "list" : "xml") . '">' . ($dm=="xml" ? "list" : "xml") . '</a>';

?>

<!DOCTYPE HTML>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title></title>
</head>
<body>
    <div>change mode link : <?php echo $link ?><div><br>
    <?php 
        if ($dm == "list") {
            foreach($list as $_val){
               echo "<ul>";
               echo "<li>" . $_val["title"] . "</li>";
               echo "<li>" . $_val["publish"] . "</li>";
               echo '<li><img src="' . $_val["thumnail"] . '"/></li>';
               echo "</ul>";
            }
        } 
    ?>
    <?php if ($dm == "xml") {print'<pre>'; print_r($xml); print'</pre>';} ?>
</body>
</html>
