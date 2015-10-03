<?php

if (!class_exists('slideshow_lite')) {
	class slideshow_lite extends GalleryPlugin {
		
		function slideshow_lite() {
			//if (empty($this -> plugin_file)) {
			//	$this -> plugin_file = plugin_basename(basename(dirname(dirname(__FILE__))) . DS . 'wp-mailinglist.php');
			//}
			
			$this -> initialize_classes();
				
			if (!is_multisite() || (is_multisite() && $this -> is_plugin_active($this -> plugin_file))) {					
				if (!$this -> ci_serial_valid()) {									
					$this -> add_filter('slideshow_sections', 'lite_sections', 10, 1);
					$this -> sections = apply_filters('slideshow_sections', (object) $this -> sections);		
					$this -> add_action('slideshow_admin_menu', 'lite_admin_menu', 10, 1);
					$this -> add_action('admin_bar_menu', 'lite_admin_bar_menu', 999, 1);
					$this -> add_filter('slideshow_gallery_validation', 'lite_gallery_validation', 10, 2); 
					$this -> add_filter('slideshow_slide_validation', 'lite_slide_validation', 10, 2);
				}
			}
		}
		
		function lite_sections($sections = null) {
			$sections = (object) $sections;
			$sections -> lite_upgrade = "slideshow-lite-upgrade";			
			return $sections;
		}
		
		function lite_admin_menu($menus = null) {
			add_submenu_page($this -> sections -> welcome, __('Upgrade to PRO', $this -> plugin_name), __('Upgrade to PRO', $this -> plugin_name), 'slideshow_welcome', $this -> sections -> lite_upgrade, array($this, 'lite_upgrade'));
		}
		
		function lite_upgrade() {
			$this -> render('lite-upgrade', false, true, 'admin');
		}
		
		function lite_admin_bar_menu($wp_admin_bar = null) {
			global $wp_admin_bar, $blog_id;

			if (is_multisite()) {				
				if (is_network_admin()) {
					return;
				}
			}
			
			if (!current_user_can('slideshow_welcome')) {
				return;
			}
		
			$args = array(
				'id'		=>	'slideshowlite',
				'title'		=>	'<i class="fa fa-picture-o fa-fw"></i> ' . __('Slideshow LITE', $this -> plugin_name),
				'href'		=>	admin_url('admin.php?page=' . $this -> sections -> lite_upgrade),
				'meta'		=>	array('class' => 'slideshow-lite'),
			);
			
			$wp_admin_bar -> add_node($args);
			
			$galleries_count = $this -> Gallery -> count();
			$galleries_percentage = (($galleries_count / 1) * 100);
			$gallerieslimit_title = sprintf(__('%s of 1 (%s&#37;) galleries used', $this -> plugin_name), $galleries_count, $galleries_percentage);
			
			$args = array(
				'id'		=>	'slideshowlite_gallerieslimit',
				'title'		=>	$gallerieslimit_title,
				'parent'	=>	'slideshowlite',
				'href'		=>	false,
				'meta'		=>	array('class' => 'slideshow-lite-gallerieslimit'),
			);
			
			$wp_admin_bar -> add_node($args);
			
			$slides_count = $this -> Slide -> count();
			$slides_percentage = (($slides_count / 20) * 100);
			$slideslimit_title = sprintf(__('%s of 20 (%s&#37;) slides used', $this -> plugin_name), $slides_count, $slides_percentage);
			
			$args = array(
				'id'		=>	'slideshowlite_slideslimit',
				'title'		=>	$slideslimit_title,
				'parent'	=>	'slideshowlite',
				'href'		=>	false,
				'meta'		=>	array('class' => 'slideshow-lite-slideslimit'),
			);
			
			$wp_admin_bar -> add_node($args);
			
			$args = array(
				'id'		=>	'slideshowlite_submitserial',
				'title'		=>	'<i class="fa fa-key"></i> ' . __('Submit Serial Key', $this -> plugin_name),
				'parent'	=>	'slideshowlite',
				'href'		=>	admin_url('admin.php?page=' . $this -> sections -> submitserial),
				'meta'		=>	array('class' => 'slideshow-lite-submitserial', 'onclick' => "jQuery.colorbox({href:ajaxurl + \"?action=slideshow_serialkey\"}); return false;"),
			);
			
			$wp_admin_bar -> add_node($args);
			
			$args = array(
				'id'		=>	'slideshowlite_upgrade',
				'title'		=>	'<i class="fa fa-check"></i> ' . __('Upgrade to PRO now!', $this -> plugin_name),
				'parent'	=>	'slideshowlite',
				'href'		=>	admin_url('admin.php?page=' . $this -> sections -> lite_upgrade),
				'meta'		=>	array('class' => 'slideshow-lite-upgrade'),
			);
			
			$wp_admin_bar -> add_node($args);
		}
		
		function lite_gallery_validation($errors = null, $data = null) {
			$slideshow_lite_gallerylimit = 1;
			
			if (!empty($slideshow_lite_gallerylimit) && $slideshow_lite_gallerylimit > 0) {
				$galleries_count = $this -> Gallery -> count();
				
				if (empty($data -> id) && $galleries_count >= $slideshow_lite_gallerylimit) {
					$error = sprintf(__('Gallery limit of %s has been reached, you can %s for unlimited.', $this -> plugin_name), $slideshow_lite_gallerylimit, '<a href="' . admin_url('admin.php?page=' . $this -> sections -> lite_upgrade) . '">Upgrade to PRO</a>');
					$errors['limit'] = $error;
					$this -> render_err($error, false, false);
				}
			}
			
			return $errors;
		}
		
		function lite_slide_validation($errors = null, $data = null) {
			$slideshow_lite_slidelimit = 20;
			
			if (!empty($slideshow_lite_slidelimit) && $slideshow_lite_slidelimit > 0) {
				$slides_count = $this -> Slide -> count();
				
				if (empty($data -> id) && $slides_count >= $slideshow_lite_slidelimit) {
					$error = sprintf(__('Slides limit of %s has been reached, you can %s for unlimited.', $this -> plugin_name), $slideshow_lite_slidelimit, '<a href="' . admin_url('admin.php?page=' . $this -> sections -> lite_upgrade) . '">Upgrade to PRO</a>');
					$errors['limit'] = $error;
					$this -> render_err($error, false, false);
				}
			}
			
			return $errors;
		}
	}
	
	add_action('plugins_loaded', 'load_slideshow_lite');
	
	function load_slideshow_lite() {
		$slideshow_lite = new slideshow_lite();
		return $slideshow_lite;
	}
}

?>