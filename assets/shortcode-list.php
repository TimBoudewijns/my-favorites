<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly.
} //endif

if (! class_exists('CCC_My_Favorite_ShortCode_List')) {

  add_shortcode('ccc_my_favorite_list_menu', array('CCC_My_Favorite_ShortCode_List', 'menu'));
  add_shortcode('ccc_my_favorite_list_results', array('CCC_My_Favorite_ShortCode_List', 'results'));
  add_shortcode('ccc_my_favorite_list_custom_template', array('CCC_My_Favorite_ShortCode_List', 'custom_template'));

  class CCC_My_Favorite_ShortCode_List
  {

    public static function menu($atts)
    {
      $atts = shortcode_atts(array(
        "slug" => '',
        "text" => '',
        "style" => '',
      ), $atts);

      // slugの処理
      if ($atts['slug']) {
        $slug = trim(esc_attr($atts['slug']), '/'); // エスケープ処理を追加
      } else {
        $slug = 'favorites';
      }

      // textの処理 (エスケープを追加)
      if ($atts['text']) {
        $text = esc_html($atts['text']); // textをエスケープ
      } else {
        $text = esc_html(__('Favorites', CCCMYFAVORITE_TEXT_DOMAIN)); // デフォルトテキストもエスケープ
      }

      // styleの処理 (エスケープを追加)
      if ($atts['style'] or $atts['style'] === 0 or $atts['style'] === '0') {
        $style = esc_attr($atts['style']); // style属性をエスケープ
      } else {
        $style = 1;
      }

      $data = '<span class="ccc-favorite-post-count" data-ccc_my_favorites-menu-style="' . $style . '" style="display: none; vertical-align: baseline !important; margin-left: 6px !important;">
        <span class="num" style="
          background-color: #dc3545 !important;
          color: #ffffff !important;
          border-radius: 8px !important;
          min-width: 18px !important;
          height: 18px !important;
          display: inline-block !important;
          text-align: center !important;
          font-size: 11px !important;
          font-weight: bold !important;
          line-height: 18px !important;
          vertical-align: middle !important;
        "></span>
      </span>'; //<!-- /.ccc-favorite-post-count -->
      return $data;
    } //endfunction

    public static function results($atts)
    {
      wp_enqueue_style('ccc_my_favorite-list');
      wp_enqueue_script('ccc_my_favorite-list');

      $atts = shortcode_atts(array(
        "class" => '',
        "excerpt" => '',
        "posts_per_page" => '',
        "style" => '',
      ), $atts);

      // classの処理 (エスケープを追加)
      if ($atts['class']) {
        $class = 'class="' . esc_attr($atts['class']) . '"'; // class属性をエスケープ
      } else {
        $class = null;
      }

      if ($atts['excerpt']) {
        $excerpt = absint($atts['excerpt']); // 整数にサニタイズ
      } else {
        $excerpt = 0;
      }

      if ($atts['posts_per_page'] and ctype_digit($atts['posts_per_page'])) {
        $posts_per_page = $atts['posts_per_page'];
      } else {
        $posts_per_page = 100;
      }

      // styleの処理 (エスケープを追加)
      if ($atts['style'] or $atts['style'] === 0 or $atts['style'] === '0') {
        $style = esc_attr($atts['style']); // style属性をエスケープ
      } else {
        $style = 1;
      }

      $data = '<div id="ccc-my_favorite-list" data-ccc_my_favorites-list-style="' . $style . '" data-ccc_my_favorite-excerpt="' . $excerpt . '" data-ccc_my_favorite-posts_per_page="' . $posts_per_page . '" ' . $class . '></div>'; //<!-- /#ccc-my_favorite-list -->
      return $data;
    } //endfunction

    public static function custom_template($atts)
    {
      wp_enqueue_script('ccc_my_favorite-list');
      $atts = shortcode_atts(array(
        "class" => '',
        "style" => '',
      ), $atts);

      // classの処理 (エスケープを追加)
      if ($atts['class']) {
        $class = 'class="' . esc_attr($atts['class']) . '"'; // class属性をエスケープ
      } else {
        $class = null;
      }

      // styleの処理 (エスケープを追加)
      if ($atts['style']) {
        $style = esc_attr($atts['style']); // style属性をエスケープ
      } else {
        $style = 0;
      }

      if ($style !== 'none') {
        wp_enqueue_style('ccc_my_favorite-list');
      }

      $data = '<div id="ccc-my_favorite-list" data-ccc_my_favorites-list-style="' . $style . '" data-ccc_my_favorite-posts_per_page="custom_template" ' . $class . '></div>'; //<!-- /#ccc-my_favorite-list -->
      return $data;
    } //endfunction

  } //endclass
} //endif
