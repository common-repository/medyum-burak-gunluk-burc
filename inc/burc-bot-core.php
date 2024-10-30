<?php

if (!class_exists('BurcBotCore')) {
    class BurcBotCore {
        public $plugin_path = '';
        public $plugin_url = '';
        public $api_endpoints = array(
            array(
                "name" => "Koç",
                "slug" => "koc",
                "icon" => '',
            ),
            array(
                "name" => "İkizler",
                "slug" => "ikizler",
                "icon" => "",
            ),
            array(
                "name" => "Boğa",
                "slug" => "boga",
                "icon" => "",
            ),
            array(
                "name" => "Yengeç",
                "slug" => "yengec",
                "icon" => "",
            ),
            array(
                "name" => "Aslan",
                "slug" => "aslan",
                "icon" => "",
            ),
            array(
                "name" => "Başak",
                "slug" => "basak",
                "icon" => "",
            ),
            array(
                "name" => "Terazi",
                "slug" => "terazi",
                "icon" => "",
            ),
            array(
                "name" => "Akrep",
                "slug" => "akrep",
                "icon" => "",
            ),
            array(
                "name" => "Yay",
                "slug" => "yay",
                "icon" => "",
            ),
            array(
                "name" => "Oğlak",
                "slug" => "oglak",
                "icon" => "",
            ),
            array(
                "name" => "Kova",
                "slug" => "kova",
                "icon" => "",
            ),
            array(
                "name" => "Balık",
                "slug" => "balik",
                "icon" => "",
            ),
        );
        public $api_endpoint_url = "https://www.mynet.com/kadin/burclar-astroloji/{name}-burcu-gunluk-yorumu.html";
        function __construct($_plugin_path, $_api_endpoints)
        {
            $this->plugin_path = $_plugin_path;
            $this->plugin_url = $_api_endpoints;
        }
        public function load() {
            add_action('init', function() {
                foreach (glob($this->plugin_path."/inc/*.php") as $filename)
                {
                    include($filename);
                }
            });
            add_action('widgets_init', function() {
                foreach (glob($this->plugin_path."/lib/*.php") as $filename)
                {
                    include($filename);
                }
                foreach (glob($this->plugin_path."/widgets/*.php") as $filename)
                {
                    include($filename);
                    register_widget(str_replace(".php", "", str_replace($this->plugin_path."/widgets/", "", $filename)));
                }
            });
            add_action('init', function() {
                if ($_GET) {
                    if (isset($_GET["load_burc"])) 
                    {
                        $this->load_data();
                        die("");
                    }
                }
            });
            add_action('init', function() {
                $burcBotFields = array(
                    array(
                        'title' => 'Give credits to author',
                        'name' => 'give_credits',
                        'type' => 'select',
                        'default_value' => '',
                        'options' => array(
                            array("key" => "", "text" => "Yes"),
                            array("key" => "-1", "text" => "No"),
                        )
                    ),
                    array(
                        'title' => 'Short Code',
                        'name' => 'short_code',
                        'type' => 'input',
                        'default_value' => 'gunluk_burc',
                    ),
                    array(
                        'title' => 'Style Templates',
                        'name' => 'css_templates',
                        'type' => 'textarea-only',
                        'default_value' => '',
                    ),
                );
                $exts = $this->get_points();
                foreach($exts as $ext) {
                    $burcBotFields[] = array(
                        "title" => $ext["name"]." Icon",
                        'name' => $ext["slug"],
                        'type' => 'input',
                        'default_value' => $ext["icon"],
                    );
                }
                $burcBotSettingsCl = new burcSettingsModule(
                    true, 
                    "Medyum Burak Günlük Burç", 
                    "medyum_burak_gunluk_burc", 
                    "administrator", 
                    "medyum_burak_gunluk_burc_settings", 
                    $burcBotFields
                );
                $GLOBALS["burcBotSettings"] = $burcBotSettings;
                global $burcBotSettings;
                $burcBotSettings = $burcBotSettingsCl->load();
                add_shortcode($burcBotSettings["short_code"], array($this, 'render_shortcode'));
            });
        }
        public function copyright() {
            echo '<div class="burc_copyright">'.
            '<a href="https://www.medyumburak.com/" title="Medyum Burak Günlük Burç">Medyum Burak Günlük Burç</a>'.
            '</div>';
        }
        public function render_shortcode() {
            global $burcBotSettings;
            $output = $this->render($burcBotSettings);
        }
        public function render($options) {
            $data = $this->get_data();
            echo '<div class="burc_container">'."\n";
            foreach($data as $item) {
                echo '<div class="burc_item">'."\n";
                echo '<a href="javascript:void(0);" data-slug="'.$item["slug"].'">'."\n";
                echo '<img src="'.$options[$item["slug"]].'" alt="'.$item["name"].'" />'."\n";
                echo "<span>".$item["name"].'</span>';
                echo "</a>\n";
                echo "</div>\n";
            }
            if ($options["give_credits"] != "-1") {
                $this->copyright();
            }
            echo "</div>\n";
        }
        public function find_value($arr, $col, $val) {
            foreach($arr as $r) {
                if ($r[$col] == $val) {
                    return $r;
                }
            }
            return false;
        }
        public function get_points() {
            $filtered_points = array();
            foreach($this->api_endpoints as $point) {
                $point["icon"] = $this->plugin_url."assets/img/".$point["slug"].".svg";
                $filtered_points[] = $point;
            }
            return $filtered_points;
        }
        public function load_data() {
            $slug = $_REQUEST["load_burc"];
            $data = $this->get_data();
            foreach($data as $row) {
                if ($row["slug"] == $slug) {
                    echo '<div class="burc_content">';
                    echo $row["content"];
                    echo '</div>';
                    die("");
                }
            }
            return false;
        }
        public function get_data() {
			if ( false === ( $ready_data = get_transient( 'burc_widget_data' ) )) {
				$ready_data = array();
                foreach($this->api_endpoints as $endpoint) {
                    $api_url = str_replace("{name}", strtolower($this->get_non_tr_str($endpoint["name"])), $this->api_endpoint_url);
                    $response = wp_remote_get($api_url);
                    $content = wp_remote_retrieve_body( $response );
                    if ($content != false) {
                        $dom = new simple_html_dom();
                        $dom->load($content);
                        $content = $dom->find(".detail-content-inner", 0)->outertext;
                    }
                    else {
                        $content = "";
                    }
                    $ready_data[] = array(
                        "name" => $endpoint["name"],
                        "slug" => $endpoint["slug"],
                        "content" => $content,
                        "api_url" => $api_url
                    );
                }
				set_transient( 'burc_widget_data', $ready_data, 12 * HOUR_IN_SECONDS );
			}
            return $ready_data;
        }
        public function get_non_tr_str($str) {
            $before = array('ğ', 'ü', 'ş', 'ö', 'ç', "ı", "İ");
            $after   = array('g', 'u', 's', 'o', 'c', "i", "i");
            $clean = str_replace($before, $after, $str);
            return $clean;
        }
        public function initAssets() {
            // Lets not make another assets request for the page. Just put it as inline style.
            add_action('wp_enqueue_scripts', function() {
                wp_register_style('magnific_popup_style', $this->plugin_url.'assets/css/jquery.magnific-popup.min.css');
                wp_enqueue_style('magnific_popup_style');
                wp_register_style('medyum_burak_gunluk_burc_style', $this->plugin_url.'assets/css/medyum-burak-gunluk-burc.css');
                wp_enqueue_style('medyum_burak_gunluk_burc_style');
            });
            add_action('wp_enqueue_scripts', function() {
                wp_register_script('magnific_popup_script', $this->plugin_url.'assets/js/jquery.magnific-popup.min.js');
                wp_enqueue_script('magnific_popup_script');
                wp_register_script('medyum_burak_gunluk_burc_script', $this->plugin_url.'assets/js/medyum-burak-gunluk-burc.js');
                wp_enqueue_script('medyum_burak_gunluk_burc_script');
            }, PHP_INT_MAX, 2);
        }
    }
}