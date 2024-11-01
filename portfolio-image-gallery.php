<?php

/*
  Plugin Name: Simple Portfolio Gallery
  Plugin URI: http://wordpress.org/
  Description: Wordpress portfolio image gallery
  Author: Tauhidul Alam
  Version: 0.1
  Author URI: http://wordpress.org/
  License: GPLv2 or later
 */

/*  © Copyright 2014 

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define("PIG_BASE_DIR", dirname(__FILE__) . '/');
define("PIG_BASE_URL", plugins_url("/portfolio-image-gallery/"));

class wordpress_portfolio_image_gallery {

    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'portfolio_enqueue_scripts'));
        add_action('init', array($this, 'portfolio_admin_section'));
        add_action('add_meta_boxes', array($this, 'portfolio_add_meta_box'));
        add_action('save_post', array($this, 'portfolio_save_meta_box_data'));
        add_shortcode('portfolio-image-gallery', array($this, 'portfolio_gallery'));
        add_action('wp_enqueue_scripts', array($this, 'portfolio_scripts_styles'));
        add_action('init', array($this, 'portfolio_show_modal'));
    }

    function portfolio_scripts_styles() {
        wp_enqueue_script('jquery');
        wp_enqueue_script('portfolio-dlmenu', PIG_BASE_URL . 'js/jquery.dlmenu.js');
        wp_enqueue_script('portfolio-modernizer-custom', PIG_BASE_URL . '/js/modernizr.custom.js');
        wp_enqueue_script('portfolio-script', PIG_BASE_URL . 'js/script.js');
        wp_enqueue_script('portfolio-easing', PIG_BASE_URL . 'js/easing.js');
        wp_enqueue_script('portfolio-jquery-cycle', PIG_BASE_URL . 'js/jquery.cycle.all.js');
        wp_enqueue_script('portfolio-slitslider', PIG_BASE_URL . 'js/jquery.slitslider.js');
        wp_enqueue_script('portfolio-jquery-ba-cond', PIG_BASE_URL . 'js/jquery.ba-cond.min.js');
        wp_enqueue_script('portfolio-jcarousel', PIG_BASE_URL . 'js/jquery.jcarousel.min.js');
        wp_enqueue_script('portfolio-bootstrap', PIG_BASE_URL . 'bootstrap/js/bootstrap.min.js', array('jquery'));
        wp_enqueue_script('portfolio-modernizer', PIG_BASE_URL . 'js/modernizr.custom.79639.js', array('jquery'), '20140318', true);
        wp_enqueue_style('portfolio-font', 'http://fonts.googleapis.com/css?family=Lato');
        wp_enqueue_style('portfolio-font-awesome', PIG_BASE_URL . 'font-awesome/css/font-awesome.min.css');
        wp_enqueue_style('portfolio-bootstrap', PIG_BASE_URL . 'bootstrap/css/bootstrap.css');
        wp_enqueue_style('portfolio-skin', PIG_BASE_URL . 'css/skin.css');
        wp_enqueue_style('portfolio-modal', PIG_BASE_URL . 'css/modal.css');
        wp_enqueue_style('portfolio-style', PIG_BASE_URL . 'css/style.css');
    }

    function portfolio_enqueue_scripts() {
        wp_enqueue_script('jquery');
    }

    function portfolio_show_modal() {
        if (isset($_REQUEST['task']) && $_REQUEST['task'] == 'show-portfolio') {
            $portfolioid = $_REQUEST['portfolio'];
            $content_post = get_post($portfolioid);
            $feat_image_url = wp_get_attachment_url(get_post_thumbnail_id($portfolioid));
            $title = $content_post->post_title;
            $content = $content_post->post_content;
            $liveProject = get_post_meta($portfolioid, '_live_project_meta_value_key', true);
            if($content == false){
                $content = 'No description available';
            }
            $data['url'] = $feat_image_url;
            $data['title'] = $title;
            $data['content'] = substr($content, 0 , 600);
            $data['live_url'] = $liveProject;
            echo json_encode($data);
            die;
        }
    }

    function portfolio_gallery() {
        ob_start();
        include(PIG_BASE_DIR . "tpls/portfolio-gallery.php");
        $data = ob_get_clean();
        return $data;
    }

    function portfolio_add_meta_box() {
        add_meta_box(
                'portfolio_meta_id', __('Portfolio options', 'portfolio-image-gallery'), array($this, 'portfolio_meta_box_callback'), 'portfolios-gallery', 'normal', 'high');
    }

    function portfolio_meta_box_callback($post) {
        wp_nonce_field('portfolio_meta_box', 'portfolio_meta_box_nonce');

        $value = get_post_meta($post->ID, '_live_project_meta_value_key', true);

        echo '<label for="portfolio_live_url">';
        _e('Live project url', 'portfolio-image-gallery');
        echo '</label> ';
        echo '<input type="text" id="portfolio_live_url" name="portfolio_live_url" value="' . esc_attr($value) . '" size="65" /><br><br>';
        
    }

    function portfolio_save_meta_box_data($post_id) {

        if (!isset($_POST['portfolio_meta_box_nonce'])) {
            return;
        }

        if (!wp_verify_nonce($_POST['portfolio_meta_box_nonce'], 'portfolio_meta_box')) {
            return;
        }

        $my_data = sanitize_text_field($_POST['portfolio_live_url']);

        update_post_meta($post_id, '_live_project_meta_value_key', $my_data);
    }

    function portfolio_admin_section() {
        $labels = array(
            'name' => _x('Portfolios', 'Post Type General Name', 'portfolio-image-gallery'),
            'singular_name' => _x('Portfolio', 'Post Type Singular Name', 'portfolio-image-gallery'),
            'menu_name' => __('Portfolios', 'portfolio-image-gallery'),
            'parent_item_colon' => __('Parent Portfolio', 'portfolio-image-gallery'),
            'all_items' => __('All Portfolios', 'portfolio-image-gallery'),
            'view_item' => __('View Portfolio', 'portfolio-image-gallery'),
            'add_new_item' => __('Add New Portfolio', 'portfolio-image-gallery'),
            'add_new' => __('Add New', 'portfolio-image-gallery'),
            'edit_item' => __('Edit Portfolio', 'portfolio-image-gallery'),
            'update_item' => __('Update Portfolio', 'portfolio-image-gallery'),
            'search_items' => __('Search Portfolio', 'portfolio-image-gallery'),
            'not_found' => __('Not Found', 'portfolio-image-gallery'),
            'not_found_in_trash' => __('Not found in Trash', 'portfolio-image-gallery'),
        );

        $args = array(
            'label' => __('portfolios', 'portfolio-image-gallery'),
            'description' => __('Portfolio news and reviews', 'portfolio-image-gallery'),
            'labels' => $labels,
            'supports' => array('title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields',),
            'taxonomies' => array('genres'),
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_nav_menus' => true,
            'show_in_admin_bar' => true,
            'menu_position' => 5,
            'can_export' => true,
            'has_archive' => true,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'capability_type' => 'page',
        );

        register_post_type('portfolios-gallery', $args);
    }

}

new wordpress_portfolio_image_gallery();

