<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

if( ! class_exists( 'CCC_My_Training_ShortCode' ) ) {

  add_shortcode('ccc_my_training_save_button', array('CCC_My_Training_ShortCode', 'save_button') );
  add_shortcode('ccc_my_training_sessions_list', array('CCC_My_Training_ShortCode', 'sessions_list') );
  add_shortcode('ccc_my_training_gallery', array('CCC_My_Training_ShortCode', 'training_gallery') );

  class CCC_My_Training_ShortCode {

    public static function save_button($atts) {
      $atts = shortcode_atts(array(
        "text" => 'Save Training Session',
        "class" => '',
      ), $atts);
      
      $text = esc_html($atts['text']);
      $class = esc_attr($atts['class']);
      
      $data = '<button class="ccc-save-training-session ' . $class . '">' . $text . '</button>';
      return $data;
    }

    public static function sessions_list($atts) {
      $atts = shortcode_atts(array(
        "title" => 'My Training Sessions',
        "show_search" => 'true',
        "show_filter" => 'true',
        "class" => '',
      ), $atts);
      
      $title = esc_html($atts['title']);
      $show_search = $atts['show_search'] === 'true';
      $show_filter = $atts['show_filter'] === 'true';
      $class = esc_attr($atts['class']);
      
      $html = '<div class="ccc-training-container ' . $class . '">';
      
      // Header with title and save button
      $html .= '<div class="ccc-training-header">';
      $html .= '<h2>' . $title . '</h2>';
      $html .= '<button class="ccc-save-training-session">Save Current Selection</button>';
      $html .= '</div>';
      
      // Search and filter controls
      if ($show_search || $show_filter) {
        $html .= '<div class="ccc-training-controls">';
        
        if ($show_search) {
          $html .= '<input type="text" id="ccc-session-search" placeholder="Search training sessions...">';
        }
        
        if ($show_filter) {
          $html .= '<select id="ccc-session-filter">';
          $html .= '<option value="">All sessions</option>';
          $html .= '<option value="week">This week</option>';
          $html .= '<option value="month">This month</option>';
          $html .= '<option value="custom">Custom date</option>';
          $html .= '</select>';
        }
        
        $html .= '</div>';
      }
      
      // Sessions list container
      $html .= '<div id="ccc-training-sessions-list">';
      $html .= '<div class="ccc-no-sessions">Training sessions will be loaded here...</div>';
      $html .= '</div>';
      
      $html .= '</div>';
      
      return $html;
    }
    
    public static function training_gallery($atts) {
      // Enqueue required styles and scripts
      wp_enqueue_style('ccc_my_training-modern');
      wp_enqueue_script('ccc_my_training-gallery');
      
      // Also enqueue modal for logged-in users
      if (is_user_logged_in()) {
        wp_enqueue_script('ccc_my_training-modal');
      }
      
      $atts = shortcode_atts(array(
        "title" => 'My Training Sessions',
        "show_search" => 'true',
        "show_filter" => 'true',
        "class" => '',
      ), $atts);
      
      $title = esc_html($atts['title']);
      $show_search = $atts['show_search'] === 'true';
      $show_filter = $atts['show_filter'] === 'true';
      $class = esc_attr($atts['class']);
      
      $html = '<div class="ccc-training-gallery-container ' . $class . '">';
      
      // Header
      $html .= '<div class="ccc-gallery-header">';
      $html .= '<h2>' . $title . '</h2>';
      $html .= '</div>';
      
      // Controls
      if ($show_search || $show_filter) {
        $html .= '<div class="ccc-gallery-controls">';
        
        if ($show_search) {
          $html .= '<input type="text" id="ccc-training-search" placeholder="Search training sessions...">';
        }
        
        if ($show_filter) {
          $html .= '<select id="ccc-training-filter">';
          $html .= '<option value="">All trainings</option>';
          $html .= '<option value="week">This week</option>';
          $html .= '<option value="month">This month</option>';
          $html .= '</select>';
        }
        
        $html .= '</div>';
      }
      
      // Gallery container
      $html .= '<div id="ccc-training-gallery">';
      $html .= '<div class="ccc-no-trainings">Loading training sessions...</div>';
      $html .= '</div>';
      
      $html .= '</div>';
      
      return $html;
    }

  }
}