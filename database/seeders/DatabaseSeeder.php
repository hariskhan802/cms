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
                'name' => 'haris',
                'email' => 'haris@gmail.com',
                'password' => bcrypt('haris123'),
                'image' => '',
                'email_verified_at' => now(),
                'role_id' => '1',
                'is_super_admin' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        DB::table('templates')->insert([
                'title' => 'Template 1',
                'content' => 'Template 1',
                'created_at' => now(),
                'updated_at' => now(),
                
            ]);
        $cats = [[
                        'title' => 'Women',
                        'slug' => 'women',
                        'featured_image' => 'img-623ce68f571eb1648158351.jpg',
                        'description' => '',
                        'parent_id' => 0,
                        'user_id' => 1,
                        'status' => 'published',
                        'menu_order' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],[
                        'title' => 'Women`s Fashion',
                        'slug' => 'womens-fashion',
                        'featured_image' => 'img-623ce6a36746b1648158371.png',
                        'description' => '',
                        'parent_id' => 1,
                        'user_id' => 1,
                        'status' => 'published',
                        'menu_order' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],[
                        'title' => 'Pants',
                        'slug' => 'pants',
                        'featured_image' => 'img-623ce6b81b6861648158392.png',
                        'description' => '',
                        'parent_id' => 2,
                        'user_id' => 1,
                        'status' => 'published',
                        'menu_order' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],[
                        'title' => 'Shirts',
                        'slug' => 'shirts',
                        'featured_image' => 'img-623cebd33e6911648159699.png',
                        'description' => '',
                        'parent_id' => 2,
                        'user_id' => 1,
                        'status' => 'published',
                        'menu_order' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],[
                        'title' => 'Jeans Pants',
                        'slug' => 'jeans-pants',
                        'featured_image' => 'img-623cf360946d71648161632.png',
                        'description' => '',
                        'parent_id' => 3,
                        'user_id' => 1,
                        'status' => 'published',
                        'menu_order' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],[
                        'title' => 'T shirts',
                        'slug' => 't-shirts',
                        'featured_image' => 'img-623e0efba56451648234235.png',
                        'description' => '',
                        'parent_id' => 4,
                        'user_id' => 1,
                        'status' => 'published',
                        'menu_order' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],[
                        'title' => 'Dress Pants',
                        'slug' => 'dress-pants',
                        'featured_image' => 'img-623e0f3231b8c1648234290.png',
                        'description' => '',
                        'parent_id' => 3,
                        'user_id' => 1,
                        'status' => 'published',
                        'menu_order' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]];
        DB::table('categories')->insert($cats);

        DB::table('posts')->insert([
                'title' => 'Post 1',
                'slug' => 'post-1',
                'content' => 'test',
                'featured_image' => '',
                'post_status' => 'published',
                'user_id' => 1,
                'menu_order' => '0',
                'created_at' => now(),
                'updated_at' => now(),
                
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
                'comment' => 'Comment 1',
                'user_id' => 1,
                'post_id' => 1,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
                
            ]
        ]);
    }
}
