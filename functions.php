<?php
remove_action( 'wp_head', 'feed_links_extra', 3 );//移除评论Feed 
remove_action( 'wp_head', 'feed_links', 2 );//文章和评论feed  
//移除文章和评论Feed 

/**
 * 热门文章
 */
function popular_posts($num = 3, $before='<li>', $after='</li>'){
    global $wpdb;
    $sql = "SELECT comment_count,ID,post_title ";
    $sql .= "FROM $wpdb->posts ";
    $sql .= "ORDER BY comment_count DESC ";
    $sql .= "LIMIT 0 , $num";
    $hotposts = $wpdb->get_results($sql);
    $output = '';
    foreach ($hotposts as $hotpost) {
        $post_title = stripslashes($hotpost->post_title);
        $permalink = get_permalink($hotpost->ID);
        $output .= $before.'<a href="' . $permalink . '"  rel="bookmark" title="';
        $output .= $post_title . '">' . $post_title . '</a>';
        $output .= $after;
    }
    if($output==''){
        $output .= $before.'暂无...'.$after;
    }
    echo $output;
}

//支持外链缩略图
if ( function_exists('add_theme_support') )
 add_theme_support('post-thumbnails');
function catch_first_image() {global $post, $posts;$first_img = '';
    ob_start();
    ob_end_clean();
    $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
    $n = count($output[1]); 
    $rdm = mt_rand(0, $n);
    $first_img = $matches [1] [$rdm];
    if(empty($first_img)){
        $random = mt_rand(1, 10);
        echo get_bloginfo ( 'stylesheet_directory' );
        echo "<img src=".'/images/random/'.$random.'.jpg'.">";
        }
  return $first_img;
}

// 隐藏js/css附加的WP版本号
function ludou_remove_wp_version_strings( $src ) {
  global $wp_version;
  parse_str(parse_url($src, PHP_URL_QUERY), $query);
  if ( !empty($query['ver']) && $query['ver'] === $wp_version ) {
    // 用WP版本号 + 12.8来替代js/css附加的版本号
    // 既隐藏了WordPress版本号，也不会影响缓存
    // 建议把下面的 12.8 替换成其他数字，以免被别人猜出
    $src = str_replace($wp_version, $wp_version + 14.5, $src);
  }
  return $src;
}
add_filter( 'script_loader_src', 'ludou_remove_wp_version_strings' );
add_filter( 'style_loader_src', 'ludou_remove_wp_version_strings' );

// 去除加载谷歌字体
if (!function_exists('remove_wp_open_sans')){ function
remove_wp_open_sans() { wp_deregister_style( 'open-sans' );
wp_register_style( 'open-sans', false ); }
add_action('wp_enqueue_scripts', 'remove_wp_open_sans'); }

    add_filter( 'gettext_with_context', 'wpjam_disable_google_fonts', 888, 4);
    function wpjam_disable_google_fonts($translations, $text, $context, $domain ) {
        $google_fonts_contexts = array('Open Sans font: on or off','Lato font: on or off','Source Sans Pro font: on or off','Bitter font: on or off');
        if( $text == 'on' && in_array($context, $google_fonts_contexts ) ){
            $translations = 'off';
        }
        return $translations;
    }
/* 去除加载谷歌字体*/
// Remove Open Sans that WP adds from frontend
if (!function_exists('remove_wp_open_sans')) :
function remove_wp_open_sans() {
wp_deregister_style( 'open-sans' );
wp_register_style( 'open-sans', false );
}
// 前台删除Google字体CSS
add_action('wp_enqueue_scripts', 'remove_wp_open_sans');
// 后台删除Google字体CSS
add_action('admin_enqueue_scripts', 'remove_wp_open_sans');
endif;


//停用版本更新通知（Core）
// disable update
add_filter ('pre_site_transient_update_core', '__return_null');//屏蔽WP更新提示
remove_action ('load-update-core.php', 'wp_update_plugins');//屏蔽插件更新提示
add_filter ('pre_site_transient_update_plugins', '__return_null');
remove_action ('load-update-core.php', 'wp_update_themes');//屏蔽主题更新提示
add_filter ('pre_site_transient_update_themes', '__return_null');
/*

//disable update
add_filter('pre_site_transient_update_core',    create_function('$a', "return null;")); // 关闭核心提示
add_filter('pre_site_transient_update_plugins', create_function('$a', "return null;")); // 关闭插件提示
add_filter('pre_site_transient_update_themes',  create_function('$a', "return null;")); // 关闭主题提示
remove_action('admin_init', '_maybe_update_core');    // 禁止 WordPress 检查更新
remove_action('admin_init', '_maybe_update_plugins'); // 禁止 WordPress 更新插件
remove_action('admin_init', '_maybe_update_themes');  // 禁止 WordPress 更新主题
*/

/* 关闭自动保存和修订版本 */
remove_action('pre_post_update', 'wp_save_post_revision' );
add_action( 'wp_print_scripts', 'disable_autosave' );
function disable_autosave() {
wp_deregister_script('autosave');
}

/*
//解决wordpress文章ID不连续以及冗余数据问题
remove_action('pre_post_update', 'wp_save_post_revision' );
add_action( 'wp_print_scripts', 'disable_autosave' );
function disable_autosave() {
wp_deregister_script('autosave');
}

// 禁用修订版本
remove_action( 'pre_post_update' , 'wp_save_post_revision' );
function keep_id_continuous(){
    global $wpdb;
    $lastID = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_status = 'publish' OR post_status = 'draft' OR post_status = 'private' OR ( post_status = 'inherit' AND post_type = 'attachment' ) ORDER BY ID DESC LIMIT 1");
    $wpdb->query("DELETE FROM $wpdb->posts WHERE ( post_status = 'auto-draft' OR ( post_status = 'inherit' AND post_type = 'revision' ) ) AND ID > $lastID");
    $lastID++;
    $wpdb->query("ALTER TABLE $wpdb->posts AUTO_INCREMENT = $lastID");
}
// 将函数钩在新建文章、上传媒体和自定义菜单之前。
add_filter( 'load-post-new.php', 'keep_id_continuous' );
add_filter( 'load-media-new.php', 'keep_id_continuous' );
add_filter( 'load-nav-menus.php', 'keep_id_continuous' );
// 禁用自动保存，所以编辑长文章前请注意手动保存。
add_action( 'admin_print_scripts', create_function( '$a', "wp_deregister_script('autosave');" ) );
// 禁用修订版本
remove_action( 'pre_post_update' , 'wp_save_post_revision' ); 
*/

// 同时删除head和feed中的WP版本号
function ludou_remove_wp_version() {
  return '';
}
add_filter('the_generator', 'ludou_remove_wp_version');

//去除分类标志代码
    add_action( 'load-themes.php',  'no_category_base_refresh_rules');
    add_action('created_category', 'no_category_base_refresh_rules');
    add_action('edited_category', 'no_category_base_refresh_rules');
    add_action('delete_category', 'no_category_base_refresh_rules');
    function no_category_base_refresh_rules() {
        global $wp_rewrite;
        $wp_rewrite -> flush_rules();
    }
    // register_deactivation_hook(__FILE__, 'no_category_base_deactivate');
    // function no_category_base_deactivate() {
    //  remove_filter('category_rewrite_rules', 'no_category_base_rewrite_rules');
    //  // We don't want to insert our custom rules again
    //  no_category_base_refresh_rules();
    // }
    // Remove category base
    add_action('init', 'no_category_base_permastruct');
    function no_category_base_permastruct() {
        global $wp_rewrite, $wp_version;
        if (version_compare($wp_version, '3.4', '<')) {
            // For pre-3.4 support
            $wp_rewrite -> extra_permastructs['category'][0] = '%category%';
        } else {
            $wp_rewrite -> extra_permastructs['category']['struct'] = '%category%';
        }
    }
    // Add our custom category rewrite rules
    add_filter('category_rewrite_rules', 'no_category_base_rewrite_rules');
    function no_category_base_rewrite_rules($category_rewrite) {
        //var_dump($category_rewrite); // For Debugging
        $category_rewrite = array();
        $categories = get_categories(array('hide_empty' => false));
        foreach ($categories as $category) {
            $category_nicename = $category -> slug;
            if ($category -> parent == $category -> cat_ID)// recursive recursion
                $category -> parent = 0;
            elseif ($category -> parent != 0)
                $category_nicename = get_category_parents($category -> parent, false, '/', true) . $category_nicename;
            $category_rewrite['(' . $category_nicename . ')/(?:feed/)?(feed|rdf|rss|rss2|atom)/?$'] = 'index.php?category_name=$matches[1]&feed=$matches[2]';
            $category_rewrite['(' . $category_nicename . ')/page/?([0-9]{1,})/?$'] = 'index.php?category_name=$matches[1]&paged=$matches[2]';
            $category_rewrite['(' . $category_nicename . ')/?$'] = 'index.php?category_name=$matches[1]';
        }
        // Redirect support from Old Category Base
        global $wp_rewrite;
        $old_category_base = get_option('category_base') ? get_option('category_base') : 'category';
        $old_category_base = trim($old_category_base, '/');
        $category_rewrite[$old_category_base . '/(.*)$'] = 'index.php?category_redirect=$matches[1]';
        //var_dump($category_rewrite); // For Debugging
        return $category_rewrite;
    }
    // Add 'category_redirect' query variable
    add_filter('query_vars', 'no_category_base_query_vars');
    function no_category_base_query_vars($public_query_vars) {
        $public_query_vars[] = 'category_redirect';
        return $public_query_vars;
    }
    // Redirect if 'category_redirect' is set
    add_filter('request', 'no_category_base_request');
    function no_category_base_request($query_vars) {
        //print_r($query_vars); // For Debugging
        if (isset($query_vars['category_redirect'])) {
            $catlink = trailingslashit(get_option('home')) . user_trailingslashit($query_vars['category_redirect'], 'category');
            status_header(301);
            header("Location: $catlink");
            exit();
        }
        return $query_vars;
    }

//获取文章的第一张图片链接
function get_first_image() {
    global $post, $posts;
      $first_img = '';
      ob_start();
      ob_end_clean();
      $output = preg_match_all('/<img[^>]*src=\"([^\"]+)\"/i', $post->post_content, $matches);
      $first_img = $matches [1] [0];
      if(empty($first_img)){ //Defines a default image
        //$first_img = "/images/default.jpg";
      }
      return $first_img;
    }
    
// 文章内容中出现链接在新窗口打开
add_filter( 'the_content','a_blank');
function a_blank($c) {
global $post;
$s = array('/href="(.+?.(jpg|bmp|png|jepg|gif))"/i'=>'href="$1"
target="_blank"');
foreach($s as$p => $r){
$c = preg_replace($p,$r,$c);
}
return$c;
}

/** RSS 中添加全文 */
function feed_read_more($content) {
//return $content . '<p><a rel="bookmark" href="'.get_permalink().'" target="_blank">阅读全文</a></p>';
$content="";    
the_content();
}
add_filter ('the_excerpt_rss', 'feed_read_more');

/**
 * Smoothie functions and definitions
 *
 * @package Mylife
 */
#-----------------------------------------------------------------
# Define Variables
#-----------------------------------------------------------------
define( 'THEMEDIR', get_template_directory() );
define( 'THEMEURL', get_template_directory_uri() );
define( 'INC_DIR', THEMEDIR . '/inc' );
define( 'INC_URL', THEMEURL . '/inc' );
define( 'ADMIN_DIR', INC_DIR . '/admin' );
define( 'ADMIN_URL', INC_URL . '/admin' );
define( 'IMGURL', THEMEURL . '/images' );
#-----------------------------------------------------------------
# Theme Setup
#-----------------------------------------------------------------
/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 720; /* pixels */
}
if ( ! function_exists( 'mylife_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function mylife_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 */
	load_theme_textdomain( 'twenty-theme', get_template_directory() . '/languages' );
	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );
	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );
	//set_post_thumbnail_size( 960, 400, true );
	//add_image_size( 'post-thumb', 585, 180, true);
	//add_image_size( 'photo-thumb', 400, 400, true );
	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'twenty-theme' ),
	) );
	// Enable support for Post Formats.
	add_theme_support( 'post-formats', array( 'aside', 'gallery', 'video', 'quote', 'link' ) );
	// Setup the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'mylife_custom_background_args', array(
		'default-color' => '40607F',
		'default-image' => '',
	) ) );
	// Enable support for HTML5 markup.
	add_theme_support( 'html5', array( 'comment-list', 'search-form', 'comment-form', ) );
}
endif; // mylife_setup
add_action( 'after_setup_theme', 'mylife_setup' );
function mylife_infinite_scroll_render() {
	get_template_part( 'content/content', get_post_format() );
}
function mylife_scripts() {
	wp_enqueue_style( 'lightbox-style', THEMEURL . '/css/lightbox.css' );
	wp_enqueue_style( 'fontawesome-style', THEMEURL . '/css/font-awesome.min.css' );
	wp_enqueue_style( 'reset-style', THEMEURL . '/css/reset.css' );
	wp_enqueue_style( 'mylife-style', get_stylesheet_uri() );
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'infinitescroll', THEMEURL . '/js/jquery.infinitescroll.js', array('jquery'));
	wp_enqueue_script( 'imagesloaded', THEMEURL . '/js/imagesloaded.pkgd.min.js', array('jquery'));
	wp_enqueue_script( 'masonry', THEMEURL . '/js/masonry.pkgd.min.js', array('jquery', 'imagesloaded'));
	wp_enqueue_script( 'autosize', THEMEURL . '/js/jquery.autosize.js', array('jquery'));
	wp_enqueue_script( 'modernizr', THEMEURL . '/js/modernizr.custom.js');
	wp_enqueue_script( 'lightbox', THEMEURL . '/js/lightbox-2.6.min.js', array('jquery'));
	wp_enqueue_script( 'site', THEMEURL . '/js/site.js', array('jquery'), false, true);
	if ( is_singular() ) wp_enqueue_script( "comment-reply" );	
	wp_localize_script('site','twenty_ajax',array(
           'ajaxurl' => admin_url('admin-ajax.php'),
           'contact_success' => __('your email send success!', 'twenty-theme'),
           'contact_failed' => __('Failed!', 'twenty-theme')
        ));
}
add_action( 'wp_enqueue_scripts', 'mylife_scripts' );
#-----------------------------------------------------------------
# Require
#-----------------------------------------------------------------
require_once (INC_DIR . '/customizer.php');
require_once (INC_DIR . '/shortcode.php');
require_once (INC_DIR . '/template-functions.php');
require_once (ADMIN_DIR . '/post-formats/cf-post-formats.php');
//DIY
//require_once (INC_DIR . '/locale-images.php');
?>