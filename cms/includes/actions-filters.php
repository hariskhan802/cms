<?php 
	
	
	function add_default_post_type() {
		register_post_type('post', [
                'name' => 'Posts',
                'singular_name' => 'Post',
                'public' => true,
                'menu_position' => 5,
                'slug' => '',
            ]
        );
        register_post_type('page', [
                'name' => 'Pages',
                'singular_name' => 'Page',
                'public' => true,
                'menu_position' => 10,
                'slug' => '',
            ]
        );

        register_taxonomy('category', 'post', [
                'name' => 'Categories',
                'singular_name' => 'Category',
                'public' => true,
                'menu_position' => 5,
                'slug' => 'category',
            ]
        );
	}
	add_action('init', 'add_default_post_type');
?>