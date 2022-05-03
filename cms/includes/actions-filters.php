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
                'rewrite' => ['slug' => ''],
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
                'rewrite' => ['slug' => ''],
                'show_in_rest' => true,
                ]
        );

        register_taxonomy('category', ['post', 'report'], [
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

        register_taxonomy('post_tag', ['post'], [
            'labels' => [
                'name' => ___( 'Tags' ),
                'singular_name' => ___( 'Tag' )
            ],
            'hierarchical' => true,
            'show_ui' => true,
            'show_in_rest' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => [ 'slug' => 'tag' ]
        ]
        );

        register_post_type('report', [
            'labels' => [
                'name' => ___( 'Reports' ),
                'singular_name' => ___( 'Report' )
            ],
            'public' => true,
            'menu_position' => 10,
            'has_archive' => true,
            'rewrite' => ['slug' => 'report'],
            'show_in_rest' => true,
            ]
    );
	}
	add_action('init', 'add_default_post_type');
?>
