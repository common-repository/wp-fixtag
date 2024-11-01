<?php
if (is_admin()) { 
		/*
		Plugin Name:WP-FixTag
		Plugin URI: http://www.3eo.ir
		Version: v2.0.2
		Author:<a href="http://www.3eo.ir">3eO</a>
		Description:Automatic tagging based on post content
	*/
/*Definition*/
define( 'WFT_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . dirname( plugin_basename( __FILE__ ) ) );
define( 'WFT_PLUGIN_URL', WP_PLUGIN_URL . '/' . dirname( plugin_basename( __FILE__ ) ) );	
	
	
/******************localize*******************/
function wp_fixtag_init() { 
	//Localize plugin content
	load_plugin_textdomain('wpfixtag', false, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action('init', 'wp_fixtag_init');
/********************************************************/
/******************admin dashboard*******************/
add_action('wp_dashboard_setup','seo3_dashboard_widget');
function seo3_dashboard_widget() { 
    wp_add_dashboard_widget('seo_3eo_id', 'افزونه تگ اتوماتیک', 's_dashboard_widget');
}

function s_dashboard_widget() { 
	echo _e('dashboard_msg_before','wpfixtag').splugin_get_version();
	print '<hr>';
	echo _e('dashboard_msg_credit','wpfixtag');
	
	
	/*echo "افزونه تگ های اتوماتیک نسخه ".splugin_get_version()." فعال است . <br />با آرزوی موفقیت برای شما <br />http://www.3eo.ir<br />";*/
	echo "<hr>";
	global $wpdb;
	$tgcount=$wpdb->get_var("SELECT COUNT(*) FROM wp_3eo_tagbank");
	echo  _e('active_tags','wpfixtag').":<b>{$tgcount}</b>";
	
 }
function show_3eo_setting() { 
			add_menu_page('تنظیمات افزونه پست اتوماتیک', 'تگ اتوماتیک', 10, 's3eomenu', 's3eo_admin');
			add_submenu_page('s3eomenu', 'تنظیمات', 'تنظیمات', 10, 's3eo_admin', 's3eo_admin');
			add_submenu_page('s3eomenu','تگ به دسته بندی','تگ به دسته بندی',10,'s3eo_admin_add','s3eo_admin_add');
			add_submenu_page('s3eomenu','درباره افزونه','درباره افزونه',10,'s3eo_about','s3eo_about');
}	
/*********admin functions*******************/
function s3eo_admin() { 
	include('3eo_admin_setting.php');
}
function s3eo_about() { 
	include('3eo_about.php');
}
function s3eo_admin_add() { 
		include('s3eo_admin_add.php');
}
/*********add tag to post when publish *********/
function add_tag_to_post() { 
	global $post;
	global $wpdb;
	if(empty($p_id) || $p_id == '') { $p_id = $post->ID; }
		$get_tag_group=$wpdb->get_results('SELECT * FROM wp_3eo_tagbank',ARRAY_A);
		foreach ( $get_tag_group as $k) { 
			$idcat=explode(':',$k['cat']);
				foreach ( $idcat as $v) { 
					$llcat=get_the_category($p_id);
					foreach($llcat as $lcat) { 
						if($lcat->term_id == $v ) {  
								wp_set_post_tags($p_id,tag_replace($k['title']),true);
								
						}
					}
				}
			
		}
	if(get_option('3eo_autotag') =='1')  { 
		$getcontent=get_post($p_id);
		$autotagz=get_most_word_as_tag($getcontent->post_content);
		$result=wp_set_post_tags($p_id,$autotagz,true);
	}
	return $result;
}
/********** xmrpc function post tag ****************/
function add_tag_to_post_xmlrpc($p_id) { 
	global $post;
	global $wpdb;
	if(empty($p_id) || $p_id == '') { $p_id = $post->ID; }
		
		$get_tag_group=$wpdb->get_results('SELECT * FROM wp_3eo_tagbank',ARRAY_A);
		foreach ( $get_tag_group as $k) { 
			$idcat=explode(':',$k['cat']);
				foreach ( $idcat as $v) { 
					$llcat=get_the_category($p_id);
					foreach($llcat as $lcat) { 
						if($lcat->term_id == $v ) {  
								wp_set_post_tags($p_id,$k['title'],true);
								
						}
					}
				}
			
		}
		
	if(get_option('3eo_autotag') =='1')  { 
		$getcontent=get_post($p_id);
		$autotagz=get_most_word_as_tag($getcontent->post_content);
		$result=wp_set_post_tags($p_id,$autotagz,true);
	}
	return $result;
	
}
function add_tag_to_post_xmlrpc2($post_ID) { 
	global $wpdb;
	$postz = get_post( $post_ID,ARRAY_A );
	$p_id = $postz['ID'];
	wp_set_post_tags($p_id,'xmlrpc_post_manual',true);
}

function add_tag_to_post_xmlrpc3() { 
	global $post;
	global $wpdb;
	$p_id = $post->ID;
	wp_set_post_tags($p_id,'xmlrpc_post_savepost2',true);
}




/*******add new tag to others *****************/
function add_tag_other($tagn,$cat='') { 
	$getold=get_option('s3eo_tags_bank');
	if(empty($cat)) { 
		if(empty($getold) || $getold == '') { 
			$getold .= $tagn; } else { 
		$getold .= ",".$tagn;
			}
	} else { 
		if(empty($getold) || $getold == '') { 
			$getold .= $tagn.":".$cat; 
			} else { 
		$getold .= ",".$tagn.":".$cat;
	}
	}
	
	update_option('s3eo_tags_bank',$getold);
	return true;
}
/********** show tag bank *********/
function show_tag_bank() { 
	$getit=get_option('s3eo_tags_bank');
	$taglist=explode(',',$getit);
	return $taglist;
}
/*********** {title} replacement*************/
function tag_replace($entry) { 
	return str_replace('{title}',get_the_title($post_id),$entry);
}
/************ remove tag *********/
function remove_tag($id) { 
	global $wpdb;
	$result=$wpdb->delete('wp_3eo_tagbank',array( 'id' => $id ));
	if($result) { return true; } else { return false; }
}

function edit($keyword,$with) { 
	
	$getall=get_option('s3eo_tags_bank');
	$getall = str_replace($keyword,$with,$getall);
	update_option('s3eo_tags_bank',$getall);
	return true;
}
function tagnamebyid($id) { 
		global $wpdb;
		$res=$wpdb->get_results('SELECT `title` FROM `wp_3eo_tagbank` where `id`="'.$id.'"',ARRAY_A);
	return $res[0]['title'];
}
function getcatlistbytagid($id) { 
		global $wpdb;
		$res=$wpdb->get_results('SELECT `cat` FROM `wp_3eo_tagbank` where `id`="'.$id.'"',ARRAY_A);
		return $res[0]['cat'];
		
}
function catidintaglist($catid,$tagid) { 
	global $wpdb;
	$tagindb=$wpdb->get_results("select cat from `wp_3eo_tagbank` where `id`='".$tagid."'",ARRAY_A);
	$clist=explode(':',$tagindb[0]['cat']);
	foreach ($clist as $cid) { 
		if($cid == $catid ) { 
					$exist=1;
					break;
		} else {
					$exist=0;
				}
	}
	if($exist == 1 ) { return true; } else { return false; } 
	
}
function get_most_word_as_tag($content) { 
	
	require_once(plugin_dir_path(__FILE__).'class.autokeyword.php');
	$params['min_word_length'] = 5;  //minimum length of single words
	$params['min_word_occur'] = 2;  //minimum occur of single words

	$params['min_2words_length'] = 3;  //minimum length of words for 2 word phrases
	$params['min_2words_phrase_length'] = 10; //minimum length of 2 word phrases
	$params['min_2words_phrase_occur'] = 2; //minimum occur of 2 words phrase

	$params['min_3words_length'] = 3;  //minimum length of words for 3 word phrases
	$params['min_3words_phrase_length'] = 10; //minimum length of 3 word phrases
	$params['min_3words_phrase_occur'] = 2; //minimum occur of 3 words phrase
	$params['content']=$content;
	$keyword = new autokeyword($params, "UTF-8");
	return $keyword->get_keywords();
	
	
}
/*******************version*******************/
function splugin_get_version() {
	if ( ! function_exists( 'get_plugins' ) )
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	$plugin_folder = get_plugins( '/' . plugin_basename( dirname( __FILE__ ) ) );
	$plugin_file = basename( ( __FILE__ ) );
	return $plugin_folder[$plugin_file]['Version'];
}

function acttablecreation() { 
	global $wpdb;
	$tblname=$wpdb->prefix.'3eo_tagbank';
	$sql="CREATE TABLE IF NOT EXISTS $tblname (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` text COLLATE utf8_persian_ci NOT NULL,
  `cat` text CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci ;";
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
	
}

/***********************************/
register_activation_hook( __FILE__, 'acttablecreation' );
add_action('admin_menu','show_3eo_setting');
add_action('publish_post','add_tag_to_post');
add_action('xmlrpc_publish_post','add_tag_to_post_xmlrpc2');
//add_action('pre_post_update','add_tag_to_post_xmlrpc2');
//add_action('save_post','add_tag_to_post_xmlrpc3');






/**********user functions****************/
}
?>