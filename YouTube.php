<?php

    class YouTube {

        // url
        private $url     = "http://gdata.youtube.com/feeds/api/standardfeeds/";
        private $thumUrl = "http://img.youtube.com/vi/";

        //feed_url
        private $feedUrl = array(
            "top_rated"         => "top_rated",
            "top_favorites"     => "top_favorites",
            "most_viewed"       => "most_viewed",
            "most_recent"       => "most_recent",
            "most_discussed"    => "most_discussed",
            "most_linked"       => "most_linked",
            "most_responded"    => "most_responded",
            "recently_featured" => "recently_featured",
            "watch_on_mobile"   => "watch_on_mobile",
            );

        public function __construct($region = "US", $feed = null) {
            $this->setRegion($region);
            if ($feed != null) {
                $this->setFeed($feed);
            }
        }

        public function setRegion($region) {
            $this->url .= $region . "/";
        }

        public function setFeed($feed = "top_rated") {
            $this->url .= $this->feedUrl[$feed];
        }
        
        public function getUrl() {
            return $this->url;
        }

        public function getXmlList($url) {
            try {
                libxml_use_internal_errors(true);
                $ret = @simplexml_load_file($url);
                if (!$ret) {
                    foreach (libxml_get_errors() as $error) {

                        throw new Exception($error->message);
                    }
                }
            }catch(Exception $e){
                echo $e->getMessage();
                exit;
            }
            
            return $ret;
        }

        public function getThumnail($id, $ImageNo = "default") {
            $id = trim($id, "http://gdata.youtube.com/feeds/api/videos/");
            return $this->thumUrl . $id . "/" . $ImageNo . ".jpg";
        }

    }


