<?php
/*
Plugin Name: Medyum Burak Günlük Burç
Plugin URI:  https://wordpress.org/plugins/medyum-burak-gunluk-burc/
Version: 1.0
Author: Medyum Burak
Author URI: https://www.medyumburak.com/
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Contributors: medyumburak
Tags: gunluk,burc,astroloji
Description: Medyum Burak  Günlük Burç Eklentisi ile güncel olarak burçları gösterir.İster widget, ister shortcode, isterseniz de fonksiyon olarak verileri kullanabilirsiniz. İkonları değiştirebilirsiniz.

Medyum Burak Gunluk Burc is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
Medyum Burak Gunluk Burc is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with Medyum Burak Gunluk Burc. If not, see {License URI}.
*/

require(dirname(__FILE__).'/inc/burc-bot-core.php');
if (class_exists('BurcBotCore')) {
    global $burcBot;
    $burcBot = new BurcBotCore(plugin_dir_path(__FILE__), plugin_dir_url(__FILE__));
    $burcBot->load();
    $burcBot->initAssets();
    $GLOBALS["burcBot"] = $burcBot;
}