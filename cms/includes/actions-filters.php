<?php 
	
	
	function add_default_post_type() {
		register_post_type('post', [
                
                'labels' => [
                    'name' => ___( 'Posts' ),
                    'singular_name' => ___( 'Post' )
                ],
                'public' => true,
                'menu_position' => 5,
                'has_archive' => true,
                'rewrite' => ['slug' => 'movies'],
                'show_in_rest' => true,
            ]
        );
        register_post_type('page', [
                'labels' => [
                    'name' => ___( 'Pages' ),
                    'singular_name' => ___( 'Page' )
                ],
                'public' => true,
                'menu_position' => 10,
                'has_archive' => true,
                'rewrite' => ['slug' => 'movies'],
                'show_in_rest' => true,
                ]
        );

        register_taxonomy('category', ['post'], [
                'labels' => [
                    'name' => ___( 'Categories' ),
                    'singular_name' => ___( 'Category' )
                ],
                'hierarchical' => true,
                'show_ui' => true,
                'show_in_rest' => true,
                'show_admin_column' => true,
                'query_var' => true,
                'rewrite' => [ 'slug' => 'category' ]
            ]
        );
	}
	add_action('init', 'add_default_post_type');
?>
