<?php
/**
 * Plugin Name: Gestion des Magasins - Test Technique Mkone
 * Plugin URI: https://github.com/abdelkadersdn/Mkone-Gestion-Magasins
 * Description: Un plugin de gestion des magasins utilisant Champs personnalisés, type de post personnalisé, shortcode et leafletjs.com API
 * Version: 1.0.0
 * Author: Abdelkader Soudani
 * Author URI: https://abdelkader.com.tn
 * License: GPL-2.0+
 * License URI: http://gnu.org/licenses/gpl-2.0.txt
 * Text Domain: mkone-magasins-post-types
 * Domain Path: /languages
 */

//interdire l'accès directe au fichier
if(!defined('WPINC')) { 
    die; 
}


//définier les constantes
define('MKONE_VERSION', '1.0.0');
define('MKONE_DOMAIN', 'mkone-magasins-post-types');
define('MKONE_PATH', plugin_dir_path(__FILE__));


//inclure le fichier necessaire pour enregistrer le custom post type et associer les champs personalisés
require_once(MKONE_PATH. '/post-types/register.php');
require_once(MKONE_PATH. '/custom-fields/details-du-magasin.php');


add_action('init', 'mkone_register_magasin_type'); //appeler la fonction dès que wordpress initialise



//ajouter les dépendences nécessaires pour le map
function mkone_wp_enqueue_scripts_styles() {

    // Stylesheet - Register Styles et Enqueue Styles
    wp_register_style('leaflet-css', 'https://unpkg.com/leaflet@1.7.1/dist/leaflet.css', '1.7.1', true); 
    wp_enqueue_style( 'leaflet-css' );

    // Javascript - Register Scripts et Enqueue Scripts
    wp_register_script('leaflet-js', 'https://unpkg.com/leaflet@1.7.1/dist/leaflet.js', array(), '1.7.1', false);
    wp_enqueue_script('leaflet-js');
    
}

add_action('wp_enqueue_scripts', 'mkone_wp_enqueue_scripts_styles');



// Shortcode: [show_stores]
function create_show_stores_shortcode() {
    global $wpdb;
    
    $magasins = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_type = 'magasin' AND post_status = 'publish'");

    $markers = "";
    if(count($magasins)>0){
        foreach($magasins as $magasin) {
            $magasin_details = $wpdb->get_results("SELECT * FROM $wpdb->postmeta WHERE post_id = $magasin->ID");
            $magain_detail_keys = $magasin_detail_values = []; //intialiser deux arrays pour key et value
            foreach ($magasin_details as $magasin_detail) {
                array_push ($magain_detail_keys, $magasin_detail->meta_key);
                array_push ($magasin_detail_values, $magasin_detail->meta_value);
            }

            //trouver l'indice du key desirable et la valeur correspondante dans array value
            $GPS_key = array_search('Coordonnees_GPS_text', $magain_detail_keys);
            $GPS_value = $magasin_detail_values[$GPS_key];

            $nom_magasin_key = array_search('Nom_du_magasin_text', $magain_detail_keys);
            $nom_magasin_value = $magasin_detail_values[$nom_magasin_key];

            $adresse_magasin_key = array_search('Adresse_text', $magain_detail_keys);
            $adresse_magasin_value = $magasin_detail_values[$adresse_magasin_key];

            $markers .= "var marker".rand()." = L.marker([".$GPS_value."]).addTo(map).bindPopup(
            `<h5>".$nom_magasin_value."</h5><p>".$adresse_magasin_value."</p>`
            );";

        }
    }

    ob_start();
    ?>

    <div id="map" style="height: 500px"></div>    
    <script defer>
        var map = L.map('map').setView([52, -0.1], 8);
        L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1Ijoic291ZGFuaWV0IiwiYSI6ImNsMTlvZmxpcDFwejUzb3F6bTJrdTF5cjkifQ.c1jkB4fMYLc53rjxP4qrFA', {
            attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
            maxZoom: 18,
            id: 'mapbox/streets-v11',
            tileSize: 512,
            zoomOffset: -1,
            accessToken: 'pk.eyJ1Ijoic291ZGFuaWV0IiwiYSI6ImNsMTlvZmxpcDFwejUzb3F6bTJrdTF5cjkifQ.c1jkB4fMYLc53rjxP4qrFA'
        }).addTo(map);

        <?php echo $markers; ?>

       
    </script>

    <?php
    return ob_get_clean();
}
 
add_shortcode('show_stores', 'create_show_stores_shortcode');



