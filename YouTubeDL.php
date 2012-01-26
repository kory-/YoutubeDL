<?php

    class YouTubeDL {

        private $url = "http://www.youtube.com/watch?v=oXMT3yXowIo";

        private $html = null;

        private $formats = array(
                0 => array("id" => 38, "icon" => 'Org', "type" => 'mp4', "desc" => 'fmt=38 ( Original, MP4 )', "color" => "000", "check" => 1),
                1 => array("id" => 37, "icon" => '1080p', "type" => 'mp4',  "desc" => 'fmt=37 ( HD1080p, MP4 )',  "color" => "C0C", "check" => 1),
                2 => array("id" => 45, "icon" => '720p',  "type" => 'WebM', "desc" => 'fmt=45 ( HD720p, WebM )',  "color" => "F00", "check" => 1),
                3 => array("id" => 22, "icon" => '720p',  "type" => 'mp4',  "desc" => 'fmt=22 ( HD720p, MP4 )',   "color" => "F00", "check" => 1),
                4 => array("id" => 44, "icon" => '480p',  "type" => 'WebM', "desc" => 'fmt=44 ( SD480p, WebM )',  "color" => "0C0", "check" => 1),
                5 => array("id" => 35, "icon" => '480p',  "type" => 'flv',  "desc" => 'fmt=35 ( SD480p, FLV )',   "color" => "0C0", "check" => 1),
                6 => array("id" => 43, "icon" => '360p',  "type" => 'WebM', "desc" => 'fmt=43 ( SD360p, WebM )',  "color" => "00F", "check" => 1),
                7 => array("id" => 34, "icon" => '360p',  "type" => 'flv',  "desc" => 'fmt=34 ( SD360p, FLV )',   "color" => "00F", "check" => 1),
                8 => array("id" => 18, "icon" => 'iPod',  "type" => 'mp4',  "desc" => 'fmt=18 ( iPod, MP4 )',     "color" => "666", "check" => 1),
                9 => array("id" =>  5, "icon" => '240p',  "type" => 'flv',  "desc" => 'fmt=5  ( OldLQ, FLV )',    "color" => "999", "check" => 1),
                //[7, {id:17, icon:'MOB',  desc:'fmt=17 ( Hmobile/3GP/MPEG4/AAC )',  color:"999",check:0}],
                //[8, {id:13, icon:'MOB',  desc:'fmt=13 ( Lmobile/3GP/H.263/AMR )',  color:"999",check:0}]
                //[5, {id: 6, icon:'OLD',desc:'fmt=6  ( OldHQ  /FLV/H.263/MP3 )',color:"999",check:1}],
            );

        public function __construct($url = null) {
            if ($url) {
                $this->setUrl($url);
            }
            $this->html = $this->getResponsText();
        }

        public function setUrl($url) {
            $this->url = $url;
        }

        public function getResponsText() {
            return mb_convert_encoding(file_get_contents($this->url), 'HTML-ENTITIES', 'auto');
        }

        public function getYouTubeDL() {

            if (preg_match_all("/\"url_encoded_fmt_stream_map\":\s?\"(.+?)\"/u", $this->html, $match)) {
                $formats = explode(',', $match[1][0]);
                foreach ($formats as $key => $format) {
                    $formatVal = explode('\\u0026', $format);
                    foreach ($formatVal as $val) {
                        $keyVal = explode('=', $val);
                        $result[$key][$keyVal[0]] = urldecode($keyVal[1]);
                    }
                }
                if ( preg_match_all("/<meta\sname=\"title\"\scontent=\"(.+?)\"/u", $this->html, $match) ) {
                    $result['title'] = preg_replace("/(\\|\/|:|\*|\?|\"|\|)/u", '_', $match[1][0]);
                }
                if ( preg_match_all("/\"video_id\":\s?\"(.+?)\"/u", $this->html, $match) ) {
                    $result['video_id'] = $match[1][0];
                }
            }

            foreach ($result as $_key => $_val) {
                $format = $this->getFormat($_val["itag"]);
                if ($_key !== "title" && $_key !== "video_id") {
                    $result[$_key]["link"] = '<a href="' . $_val["url"] . '&title=' . $result['title'] . '">' . " " . $result['title'] . "." . $format["type"] . '</a>';
                    $result[$_key]["extension"] = "." . $format["type"];
                    foreach(get_headers($_val["url"]) as $header) {
                        if (preg_match("/^Content-Length\:\s(\d+)/", $header, $match)) {
                            $result[$_key]["filesize"] = $match[1] . "byte";
                        }
                    }
                }
            }
            return $result;
        }

        public function getFormat($id) {
            foreach($this->formats as $format) {
                if ($format["id"] == $id) {
                    return $format;
                }
            }
        }

        public function getThumnail($v) {
            if (preg_match("/<link\sitemprop=\"thumbnailUrl\"\shref=\"(http\:\/\/.+)\">/", $this->html, $match)) {
                return $match[1];
            }else {
                return false;
            }
        }

        public function downloadVideo($data, $id) {
            set_time_limit(300);

            $fp = fopen($data["video_id"] . $data[$id]["extension"], "wb");
            $handle = fopen($data[$id]["url"], "rb");

            while (!feof($handle)) {
                fwrite($fp, fread($handle, 8192));
            }
        }

        public function downloadVideo2($data, $id) {
            set_time_limit(300);

            header("Content-Disposition: inline; filename=\"" . $data["title"] . $data[$id]["extension"] . "\"");
            header("Content-Length: ".$data[$id]["filesize"]);
            header("Content-Type: application/octet-stream");

            $handle = fopen($data[$id]["url"], "rb");

            $buffer = '';
              while (!feof($handle)) {
                $buffer = fread($handle, 4096);
                echo $buffer;
                flush();
              }
              fclose($handle);
        }
    }
