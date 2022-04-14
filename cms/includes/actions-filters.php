<?php 
	
	
	function add_default_post_type() {
		register_post_type([
                'name' => 'Posts',
                'singular_name' => 'Post',
                'public' => true,
                'menu_position' => 5,
                'slug' => '',
                'post_type' => 'post',
            ]
        );
        register_post_type([
                'name' => 'Pages',
                'singular_name' => 'Page',
                'public' => true,
                'menu_position' => 10,
                'slug' => '',
                'post_type' => 'page',
            ]
        );
	}
	add_action('init', 'add_default_post_type');
?>