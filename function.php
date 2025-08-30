<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}
require( CCCMYFAVORITE_PLUGIN_PATH .'/assets/list.php' );
require( CCCMYFAVORITE_PLUGIN_PATH .'/addons/ccc-post_thumbnail/ccc-post_thumbnail.php' );



class CCC_My_Favorite {

  const CCC_MY_FAVORITE_POST_IDS = 'ccc_my_favorite_post_ids';
  const CCC_MY_TRAINING_SESSIONS = 'ccc_my_training_sessions';
  const CCC_MY_TRAINING_DRILLS = 'ccc_my_training_drills';

  /*** Initial execution ***/
  public function __construct() {
    add_action( 'wp_enqueue_scripts', array( $this, 'jquery_check' ) );
    add_action( 'wp_enqueue_scripts', array( $this, 'select_styles' ) );
    add_action( 'wp_enqueue_scripts', array( $this, 'select_scripts' ) );
    add_action( 'wp_enqueue_scripts', array( $this, 'training_styles' ) );
    add_action( 'wp_enqueue_scripts', array( $this, 'training_scripts' ) );
    add_action( 'wp_ajax_ccc_my_favorite-update-action', array( $this, 'usermeta_my_favorite_update') );
    add_action( 'wp_ajax_ccc_my_favorite-get-action', array( $this, 'usermeta_my_favorite_get') );

    add_action( 'wp_enqueue_scripts', array( $this, 'list_styles' ) );
    add_action( 'wp_enqueue_scripts', array( $this, 'list_scripts' ) );
    add_action( 'wp_ajax_ccc_my_favorite-list-action', array( $this, 'list_posts_action' ) );
    add_action( 'wp_ajax_nopriv_ccc_my_favorite-list-action', array( $this, 'list_posts_action' ) );
    
    // Training AJAX actions
    add_action( 'wp_ajax_ccc_my_training-save-action', array( $this, 'save_training_session') );
    add_action( 'wp_ajax_ccc_my_training-get-action', array( $this, 'get_training_sessions') );
    add_action( 'wp_ajax_ccc_my_training-delete-action', array( $this, 'delete_training_session') );
    add_action( 'wp_ajax_ccc_add_drill_to_training', array( $this, 'add_drill_to_training') );
    add_action( 'wp_ajax_ccc_get_drill_trainings', array( $this, 'get_drill_trainings') );
    add_action( 'wp_ajax_ccc_remove_drill_from_training', array( $this, 'remove_drill_from_training') );
    add_action( 'wp_ajax_get_posts_by_ids', array( $this, 'get_posts_by_ids') );
    add_action( 'wp_ajax_nopriv_get_posts_by_ids', array( $this, 'get_posts_by_ids') );
  } //endfunction

  public function jquery_check() {
    wp_enqueue_script('jquery');
  } //endfunction

  public function select_styles() {
    wp_enqueue_style( 'ccc_my_favorite-select', CCCMYFAVORITE_PLUGIN_URL.'/assets/select.css', array(), CCCMYFAVORITE_PLUGIN_VERSION, 'all');
  } //endfunction
  
  public function training_styles() {
    wp_enqueue_style( 'ccc_my_training-modern', CCCMYFAVORITE_PLUGIN_URL.'/assets/training-modern.css', array(), CCCMYFAVORITE_PLUGIN_VERSION, 'all');
  } //endfunction

  public function select_scripts() {
    $handle = 'ccc_my_favorite-select';
    $file = 'select.js';
    wp_register_script( $handle, CCCMYFAVORITE_PLUGIN_URL.'/assets/'.$file, array( 'jquery' ), CCCMYFAVORITE_PLUGIN_VERSION, true );
    wp_enqueue_script( $handle );
    $action_update = 'ccc_my_favorite-update-action';
    wp_localize_script( $handle, 'CCC_MY_FAVORITE_UPDATE',
                       array(
                         'api'    => admin_url( 'admin-ajax.php' ),
                         'action' => $action_update,
                         'nonce'  => wp_create_nonce( $action_update ),
                         'user_logged_in' => is_user_logged_in()
                       )
                      );
    $action_get = 'ccc_my_favorite-get-action';
    wp_localize_script( $handle, 'CCC_MY_FAVORITE_GET',
                       array(
                         'api'    => admin_url( 'admin-ajax.php' ),
                         'action' => $action_get,
                         'nonce'  => wp_create_nonce( $action_get )
                       )
                      );
  } //endfunction
  
  public function training_scripts() {
    // Enqueue modal script
    $modal_handle = 'ccc_my_training-modal';
    wp_register_script( $modal_handle, CCCMYFAVORITE_PLUGIN_URL.'/assets/training-modal.js', array( 'jquery' ), CCCMYFAVORITE_PLUGIN_VERSION, true );
    wp_enqueue_script( $modal_handle );
    
    // Enqueue gallery script
    $gallery_handle = 'ccc_my_training-gallery';
    wp_register_script( $gallery_handle, CCCMYFAVORITE_PLUGIN_URL.'/assets/training-gallery.js', array( 'jquery' ), CCCMYFAVORITE_PLUGIN_VERSION, true );
    wp_enqueue_script( $gallery_handle );
    
    // Localize scripts for training functionality
    wp_localize_script( $modal_handle, 'CCC_MY_TRAINING',
      array(
        'api' => admin_url( 'admin-ajax.php' ),
        'save_action' => 'ccc_my_training-save-action',
        'save_nonce' => wp_create_nonce( 'ccc_my_training-save-action' ),
        'get_action' => 'ccc_my_training-get-action',
        'get_nonce' => wp_create_nonce( 'ccc_my_training-get-action' ),
        'delete_action' => 'ccc_my_training-delete-action',
        'delete_nonce' => wp_create_nonce( 'ccc_my_training-delete-action' ),
        'add_drill_nonce' => wp_create_nonce( 'ccc_add_drill_to_training' ),
        'get_drill_nonce' => wp_create_nonce( 'ccc_get_drill_trainings' ),
        'remove_drill_nonce' => wp_create_nonce( 'ccc_remove_drill_from_training' )
      )
    );
    
    // Also localize for gallery script
    wp_localize_script( $gallery_handle, 'CCC_MY_TRAINING',
      array(
        'api' => admin_url( 'admin-ajax.php' ),
        'save_action' => 'ccc_my_training-save-action',
        'save_nonce' => wp_create_nonce( 'ccc_my_training-save-action' ),
        'get_action' => 'ccc_my_training-get-action',
        'get_nonce' => wp_create_nonce( 'ccc_my_training-get-action' ),
        'delete_action' => 'ccc_my_training-delete-action',
        'delete_nonce' => wp_create_nonce( 'ccc_my_training-delete-action' ),
        'add_drill_nonce' => wp_create_nonce( 'ccc_add_drill_to_training' ),
        'get_drill_nonce' => wp_create_nonce( 'ccc_get_drill_trainings' ),
        'remove_drill_nonce' => wp_create_nonce( 'ccc_remove_drill_from_training' )
      )
    );
  } //endfunction

  /*** お気に入りの投稿をユーザーメタ（usermeta）に追加 ***/
  public function usermeta_my_favorite_update() {
    if( check_ajax_referer( $_POST['action'], 'nonce', false ) ) {
      /* 保存された値でメタデータを更新する（もしくはまだそのフィールドが存在しなければ新規作成する）ための関数 */
      update_user_meta( wp_get_current_user()->ID, self::CCC_MY_FAVORITE_POST_IDS, sanitize_text_field( $_POST['post_ids'] ) );
      $data = get_user_meta( wp_get_current_user()->ID, self::CCC_MY_FAVORITE_POST_IDS, true );
    } else {
      //status_header( '403' );
      $data = null;
    }
    echo $data;
    /* スクリプト終了時のメッセージを削除（注意：admin-ajax.phpの仕様でwp_die('0');があるためレスポンスの値に「0」が含まれる）*/
    die(); //メッセージは無しで現在のスクリプトを終了する（メッセージは空にする）
  } //endfunction

  /*** ユーザーメタに保存されたお気に入りの投稿を取得 ***/
  public function usermeta_my_favorite_get() {
    if( check_ajax_referer( $_POST['action'], 'nonce', false ) ) {
      $data = get_user_meta( wp_get_current_user()->ID, self::CCC_MY_FAVORITE_POST_IDS, true );
    } else {
      //status_header( '403' );
      $data = null;
    }
    echo $data;
    /* スクリプト終了時のメッセージを削除（注意：admin-ajax.phpの仕様でwp_die('0');があるためレスポンスの値に「0」が含まれる）*/
    die(); //メッセージは無しで現在のスクリプトを終了する（メッセージは空にする）
  } //endfunction

  public function list_styles() {
    wp_register_style( 'ccc_my_favorite-list', CCCMYFAVORITE_PLUGIN_URL.'/assets/list.css', array(), CCCMYFAVORITE_PLUGIN_VERSION, 'all');
  } //endfunction

  public function list_scripts() {
      $handle = 'ccc_my_favorite-list';
      $file = 'list.js';
      wp_register_script( $handle, CCCMYFAVORITE_PLUGIN_URL.'/assets/'.$file, array( 'jquery' ), CCCMYFAVORITE_PLUGIN_VERSION, true );
      $action = 'ccc_my_favorite-list-action';
      wp_localize_script( $handle, 'CCC_MY_FAVORITE_LIST',
                         array(
                           'api'    => admin_url( 'admin-ajax.php' ),
                           'action' => $action,
                           'nonce'  => wp_create_nonce( $action )
                         )
                        );
  } //endfunction

  public function list_posts_action() {
    if( check_ajax_referer( $_POST['action'], 'nonce', false ) ) {
      $data = CCC_My_Favorite_List::action();
    } else {
      //status_header( '403' );
      $data = 'Forbidden';
    }
    echo $data;
    die();
  } //endfunction

  /*** Save training session ***/
  public function save_training_session() {
    if( check_ajax_referer( $_POST['action'], 'nonce', false ) ) {
      $user_id = wp_get_current_user()->ID;
      $sessions = get_user_meta( $user_id, self::CCC_MY_TRAINING_SESSIONS, true );
      
      if( !is_array($sessions) ) {
        $sessions = array();
      }
      
      $session_data = array(
        'id' => uniqid('training_'),
        'name' => sanitize_text_field( $_POST['session_name'] ),
        'date' => sanitize_text_field( $_POST['session_date'] ),
        'created' => current_time('mysql')
      );
      
      $sessions[] = $session_data;
      update_user_meta( $user_id, self::CCC_MY_TRAINING_SESSIONS, $sessions );
      
      wp_send_json_success( $session_data );
    } else {
      wp_send_json_error( 'Forbidden' );
    }
    die();
  }
  
  /*** Get all training sessions with drills ***/
  public function get_training_sessions() {
    if( check_ajax_referer( $_POST['action'], 'nonce', false ) ) {
      $user_id = wp_get_current_user()->ID;
      $sessions = get_user_meta( $user_id, self::CCC_MY_TRAINING_SESSIONS, true );
      $drills = get_user_meta( $user_id, self::CCC_MY_TRAINING_DRILLS, true );
      
      if( !is_array($sessions) ) {
        $sessions = array();
      }
      
      if( !is_array($drills) ) {
        $drills = array();
      }
      
      // Add drill count to each session
      foreach($sessions as &$session) {
        $session['drill_count'] = 0;
        $session['drills'] = array();
        foreach($drills as $drill) {
          if($drill['training_id'] === $session['id']) {
            $session['drill_count']++;
            $session['drills'][] = $drill['post_id'];
          }
        }
      }
      
      // Get unassigned drills
      $unassigned = array();
      foreach($drills as $drill) {
        if(empty($drill['training_id']) || $drill['training_id'] === 'none') {
          $unassigned[] = $drill['post_id'];
        }
      }
      
      wp_send_json_success( array(
        'sessions' => $sessions,
        'unassigned' => $unassigned
      ) );
    } else {
      wp_send_json_error( 'Forbidden' );
    }
    die();
  }
  
  /*** Delete training session ***/
  public function delete_training_session() {
    if( check_ajax_referer( $_POST['action'], 'nonce', false ) ) {
      $user_id = wp_get_current_user()->ID;
      $session_id = sanitize_text_field( $_POST['session_id'] );
      $sessions = get_user_meta( $user_id, self::CCC_MY_TRAINING_SESSIONS, true );
      
      if( is_array($sessions) ) {
        $sessions = array_filter($sessions, function($session) use ($session_id) {
          return $session['id'] !== $session_id;
        });
        
        update_user_meta( $user_id, self::CCC_MY_TRAINING_SESSIONS, array_values($sessions) );
        
        // Mark all drills from this training as unassigned
        $drills = get_user_meta( $user_id, self::CCC_MY_TRAINING_DRILLS, true );
        if( is_array($drills) ) {
          foreach($drills as &$drill) {
            if($drill['training_id'] === $session_id) {
              $drill['training_id'] = 'none';
            }
          }
          update_user_meta( $user_id, self::CCC_MY_TRAINING_DRILLS, $drills );
        }
        
        wp_send_json_success();
      } else {
        wp_send_json_error( 'No sessions found' );
      }
    } else {
      wp_send_json_error( 'Forbidden' );
    }
    die();
  }
  
  /*** Add drill to training ***/
  public function add_drill_to_training() {
    if( check_ajax_referer( $_POST['action'], 'nonce', false ) ) {
      $user_id = wp_get_current_user()->ID;
      $post_id = intval( $_POST['post_id'] );
      $training_id = sanitize_text_field( $_POST['training_id'] );
      
      $drills = get_user_meta( $user_id, self::CCC_MY_TRAINING_DRILLS, true );
      
      if( !is_array($drills) ) {
        $drills = array();
      }
      
      // Check if drill already exists and update or add new
      $found = false;
      foreach($drills as &$drill) {
        if($drill['post_id'] == $post_id) {
          $drill['training_id'] = $training_id;
          $drill['updated'] = current_time('mysql');
          $found = true;
          break;
        }
      }
      
      if(!$found) {
        $drills[] = array(
          'post_id' => $post_id,
          'training_id' => $training_id,
          'added' => current_time('mysql'),
          'updated' => current_time('mysql')
        );
      }
      
      update_user_meta( $user_id, self::CCC_MY_TRAINING_DRILLS, $drills );
      
      // Also add to favorites for backward compatibility
      $favorites = get_user_meta( $user_id, self::CCC_MY_FAVORITE_POST_IDS, true );
      if(!empty($favorites)) {
        $fav_array = explode(',', $favorites);
        if(!in_array($post_id, $fav_array)) {
          $fav_array[] = $post_id;
          update_user_meta( $user_id, self::CCC_MY_FAVORITE_POST_IDS, implode(',', $fav_array) );
        }
      } else {
        update_user_meta( $user_id, self::CCC_MY_FAVORITE_POST_IDS, $post_id );
      }
      
      wp_send_json_success();
    } else {
      wp_send_json_error( 'Forbidden' );
    }
    die();
  }
  
  /*** Get trainings for a specific drill ***/
  public function get_drill_trainings() {
    if( check_ajax_referer( $_POST['action'], 'nonce', false ) ) {
      $user_id = wp_get_current_user()->ID;
      $post_id = intval( $_POST['post_id'] );
      
      $drills = get_user_meta( $user_id, self::CCC_MY_TRAINING_DRILLS, true );
      $sessions = get_user_meta( $user_id, self::CCC_MY_TRAINING_SESSIONS, true );
      
      if( !is_array($drills) ) {
        $drills = array();
      }
      
      if( !is_array($sessions) ) {
        $sessions = array();
      }
      
      $training_ids = array();
      foreach($drills as $drill) {
        if($drill['post_id'] == $post_id && !empty($drill['training_id']) && $drill['training_id'] !== 'none') {
          $training_ids[] = $drill['training_id'];
        }
      }
      
      wp_send_json_success( array(
        'training_ids' => $training_ids,
        'sessions' => $sessions
      ) );
    } else {
      wp_send_json_error( 'Forbidden' );
    }
    die();
  }
  
  /*** Remove drill from training ***/
  public function remove_drill_from_training() {
    if( check_ajax_referer( $_POST['action'], 'nonce', false ) ) {
      $user_id = wp_get_current_user()->ID;
      $post_id = intval( $_POST['post_id'] );
      $training_id = sanitize_text_field( $_POST['training_id'] );
      
      $drills = get_user_meta( $user_id, self::CCC_MY_TRAINING_DRILLS, true );
      
      if( is_array($drills) ) {
        foreach($drills as $key => &$drill) {
          if($drill['post_id'] == $post_id && $drill['training_id'] == $training_id) {
            $drill['training_id'] = 'none'; // Mark as unassigned instead of deleting
            break;
          }
        }
        
        update_user_meta( $user_id, self::CCC_MY_TRAINING_DRILLS, $drills );
      }
      
      wp_send_json_success();
    } else {
      wp_send_json_error( 'Forbidden' );
    }
    die();
  }
  
  /*** Get posts by IDs for gallery display ***/
  public function get_posts_by_ids() {
    if( !isset($_POST['post_ids']) ) {
      wp_send_json_error( 'No post IDs provided' );
      die();
    }
    
    $post_ids = explode(',', sanitize_text_field($_POST['post_ids']));
    $post_ids = array_map('intval', $post_ids);
    $post_ids = array_filter($post_ids); // Remove empty values
    
    $posts = array();
    foreach($post_ids as $post_id) {
      $post = get_post($post_id);
      if($post) {
        $thumbnail_url = get_the_post_thumbnail_url($post->ID, 'medium');
        if(!$thumbnail_url) {
          $thumbnail_url = 'https://via.placeholder.com/300x200?text=No+Image';
        }
        
        $posts[] = array(
          'id' => $post->ID,
          'title' => $post->post_title,
          'excerpt' => wp_trim_words($post->post_excerpt ?: $post->post_content, 20),
          'permalink' => get_permalink($post->ID),
          'thumbnail' => $thumbnail_url
        );
      }
    }
    
    wp_send_json_success($posts);
    die();
  }

} //endclass