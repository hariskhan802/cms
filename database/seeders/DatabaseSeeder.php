<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        DB::table('users')->insert([
            [
                'user_login' => 'haris',
                'user_pass' => bcrypt('haris123'),
                'user_nicename' => 'haris',
                'user_email' => 'haris@abc.com',
                'user_url' => 'http://localhost/wordpress',
                'is_super_admin' => '1',
                'user_registered' => now(),
                'user_activation_key' => '',
                'user_status' => 1,
                'display_name' => 'haris',
            ],
            [
                'user_login' => 'ali',
                'user_pass' => bcrypt('ali123'),
                'user_nicename' => 'ali',
                'user_email' => 'ali@abc.com',
                'user_url' => 'http://localhost/wordpress',
                'is_super_admin' => '1',
                'user_registered' => now(),
                'user_activation_key' => '',
                'user_status' => 1,
                'display_name' => 'ali',
            ],
        ]);
        DB::table('templates')->insert([
                'title' => 'Template 1',
                'content' => 'Template 1',
                'created_at' => now(),
                'updated_at' => now(),
                
            ]);
        
        $termID = DB::table('terms')->insertGetId([
            'name' => 'Women',
            'slug' => 'women',
        ]);
        DB::table('term_taxonomy')->insert([
            'term_id' => $termID,
            'taxonomy' => 'category',
            'description' => 'test content',
        
        ]);
        // DB::table('posts')->insert([
        //     [
        //         'title' => 'Post 1',
        //         'slug' => 'post-1',
        //         'content' => 'test',
        //         'featured_image' => '',
        //         'post_status' => 'published',
        //         'post_type' => 'post',
        //         'user_id' => 1,
        //         'menu_order' => '0',
        //         'created_at' => now(),
        //         'updated_at' => now(),
                
        //     ],
        //     [
        //         'title' => 'Post 2',
        //         'slug' => 'post-2',
        //         'content' => 'test',
        //         'featured_image' => '',
        //         'post_status' => 'published',
        //         'post_type' => 'post',
        //         'user_id' => 1,
        //         'menu_order' => '0',
        //         'created_at' => now(),
        //         'updated_at' => now(),
                
        //     ],
        //     [
        //         'title' => 'Page 1',
        //         'slug' => 'page-1',
        //         'content' => 'test',
        //         'featured_image' => '',
        //         'post_status' => 'published',
        //         'post_type' => 'page',
        //         'user_id' => 1,
        //         'menu_order' => '0',
        //         'created_at' => now(),
        //         'updated_at' => now(),
                
        //     ],
        //     [
        //         'title' => 'Page 2',
        //         'slug' => 'page-2',
        //         'content' => 'test',
        //         'featured_image' => '',
        //         'post_status' => 'published',
        //         'post_type' => 'page',
        //         'user_id' => 1,
        //         'menu_order' => '0',
        //         'created_at' => now(),
        //         'updated_at' => now(),
                
        //     ],
        // ]);
        /* $postArr = [];
        for ($i=1; $i <= 5000; $i++) { 
            # code...
            $postArr [] = [
                'post_title' => 'Post '.$i,
                    'post_name' => 'post-'.$i,
                    'post_content' => 'test',
                    'post_excerpt' => 'test',
                    'post_password' => '',
                    'post_mime_type' => '',
                    'pinged' => '',
                    'to_ping' => '',
                    'guid' => '',
                    'post_content_filtered' => '',
                    'post_status' => 'published',
                    'post_type' => 'page',
                    'post_author' => 1,
                    'post_date' => now(),
                    'post_date_gmt' => now(),
                    'post_modified' => now(),
                    'post_modified_gmt' => now(),
            ];
        } */
        $postArr [] = [
            'post_title' => 'Post 1',
            'post_name' => 'post-1',
            'post_content' => 'test',
            'post_excerpt' => 'test',
            'post_password' => '',
            'post_mime_type' => '',
            'pinged' => '',
            'to_ping' => '',
            'guid' => '',
            'post_content_filtered' => '',
            'post_status' => 'publish',
            'post_type' => 'post',
            'post_author' => 1,
            'post_date' => now(),
            'post_date_gmt' => now(),
            'post_modified' => now(),
            'post_modified_gmt' => now(),
            
        ];
        $postArr [] = [
                    'post_title' => 'Page 1',
                    'post_name' => 'page-1',
                    'post_content' => 'test',
                    'post_excerpt' => 'test',
                    'post_password' => '',
                    'post_mime_type' => '',
                    'pinged' => '',
                    'to_ping' => '',
                    'guid' => '',
                    'post_content_filtered' => '',
                    'post_status' => 'publish',
                    'post_type' => 'page',
                    'post_author' => 1,
                    'post_date' => now(),
                    'post_date_gmt' => now(),
                    'post_modified' => now(),
                    'post_modified_gmt' => now(),
                    
                ];
        DB::table('posts')->insert($postArr);
        
        DB::table('postmeta')->insert([
            [
                'post_id' => 2,
                'meta_key' => '__template_id',
                'meta_value' => 1,
                
            ],
            [
                'post_id' => 2,
                'meta_key' => '__featured_image',
                'meta_value' => 'img-624e02d87153d1649279704.jpg',
                
            ],
            [
                'post_id' => 1,
                'meta_key' => '__featured_image',
                'meta_value' => '',
                
            ],
        ]);
        $roles = [
            [
                'role' => 'Administrator',
                'permissions' => 'a:21:{i:0;s:14:"view_dashboard";i:1;s:12:"create_posts";i:2;s:12:"delete_posts";i:3;s:10:"edit_posts";i:4;s:10:"list_posts";i:5;s:16:"manage_own_posts";i:6;s:12:"create_pages";i:7;s:12:"delete_pages";i:8;s:10:"edit_pages";i:9;s:10:"list_pages";i:10;s:16:"manage_own_pages";i:11;s:15:"delete_comments";i:12;s:13:"edit_comments";i:13;s:13:"list_comments";i:14;s:19:"manage_own_comments";i:15;s:12:"create_users";i:16;s:12:"delete_users";i:17;s:10:"edit_users";i:18;s:10:"list_users";i:19;s:13:"list_settings";i:20;s:13:"edit_settings";}',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role' => 'Subscriber',
                'permissions' => 'a:3:{i:0;s:14:"view_dashboard";i:1;s:16:"manage_own_posts";i:2;s:19:"manage_own_comments";}',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        DB::table('roles')->insert($roles);


        DB::table('comments')->insert([
            [
                'comment_post_ID' => '1',
                'comment_author' => 1,
                'comment_author_email' => 'haris@abc.com',
                'comment_author_url' => 'http://localhost/cms',
                'comment_author_IP' => '',
                'comment_date_gmt' => now(),
                'comment_content' => 'Hello comment 1',
                'comment_approved' => 0,
                'comment_agent' => '',
                'user_id' => 1,
                
            ],
            [
                'comment_post_ID' => '1',
                'comment_author' => 1,
                'comment_author_email' => 'haris@abc.com',
                'comment_author_url' => 'http://localhost/cms',
                'comment_author_IP' => '',
                'comment_date_gmt' => now(),
                'comment_content' => 'Hello comment 2',
                'comment_approved' => 0,
                'comment_agent' => '',
                'user_id' => 1,
                
            ],
        ]);

        DB::table('options')->insert([
            [
                'option_name' => 'site_title',
                'option_value' => 'My Site',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'option_name' => 'tagline',
                'option_value' => 'test',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'option_name' => 'site_address_url',
                'option_value' => 'http://localhost/cms',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'option_name' => 'administration_email_address',
                'option_value' => 'alex@abc.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'option_name' => 'membership',
                'option_value' => 'on',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'option_name' => 'new_user_default_role',
                'option_value' => '2',
                'created_at' => now(),
                'updated_at' => now(),
            ],


            [
                'option_name' => 'is_admin_panel_date_custom',
                'option_value' => 'false',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'option_name' => 'admin_panel_date_format',
                'option_value' => 'F j, Y',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'option_name' => 'admin_panel_custom_date_format',
                'option_value' => 'Y-m-d',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            


            [
                'option_name' => 'is_admin_panel_time_custom',
                'option_value' => 'false',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'option_name' => 'admin_panel_time_format',
                'option_value' => 'g:i a',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'option_name' => 'admin_panel_custom_time_format',
                'option_value' => 'g:i a',
                'created_at' => now(),
                'updated_at' => now(),
            ],




            [
                'option_name' => 'is_website_date_custom',
                'option_value' => 'false',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'option_name' => 'website_date_format',
                'option_value' => 'F j, Y',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'option_name' => 'website_custom_date_format',
                'option_value' => 'Y-m-d',
                'created_at' => now(),
                'updated_at' => now(),
            ],




            [
                'option_name' => 'is_website_time_custom',
                'option_value' => 'false',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'option_name' => 'website_time_format',
                'option_value' => 'g:i a',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'option_name' => 'website_custom_time_format',
                'option_value' => 'g:i a',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
