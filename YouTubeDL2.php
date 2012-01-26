<?php
// 動画のURL
$url = "http://www.youtube.com/watch?v=3Mp8RBnMydc";
 
// 拡張子のリスト
$extension = array("5" => ".flv", "18" => ".mp4", "22" => ".mp4", "34" => ".flv", "35" => ".flv", "37" => ".mp4", "38" => ".mp4");
 
// HTMLファイルの取得
$html = file_get_contents($url);
 
// HTMLからタイトルを獲得する
preg_match("/<meta name=\"title\" content=\"([^\"]*)\">/s", $html, $matches);
$title = $matches[1];
 
// 動画URLの取得
preg_match("/url_encoded_fmt_stream_map=([^\"]*)/i", $html, $matches);
$dataset = explode("%2C", $matches[1]);
for ($i = 0; $i < count($dataset); $i++) {
    preg_match("/^url%3D(.*)/i", $dataset[$i], $matches);
    if (count($matches) != 0) {
        $flvs = explode("&quality=", urldecode(urldecode($matches[1])));
        preg_match("/&itag=(\d*)/i", $flvs[0], $itags);
 
        $video_url = $flvs[0];
        $itag = $itags[1];
 
        // 動画をDL
        if (isset($extension[$itag])) {
            echo "title : $title\n";
            echo "itag  : $itag\n";
            echo "start  download.\n";
 
            $fp = fopen(mb_convert_encoding($title,"sjis","auto")."_fmt" . $itag . $extension[$itag], "wb");
            $handle = fopen($video_url, "rb");
            while (!feof($handle)) fwrite($fp, fread($handle, 8192));
            echo "finish download.\n\n";
        }
    }
}

