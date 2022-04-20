<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\{
    AuthController,
    DashboardController,
    PostController,
    CategoryController,
    PageController,
    CommentController,
    UserController,
    RoleController,
    SettingController,
    TemplateController,
};
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', function(){
    // echo bcrypt('haris123');
    echo request()->route()->parameter('id');

    echo '<img src="'.get_user_image('img-623cde3a98177164815621.jpg').'" />';
});

Route::fallback(function () {
    return redirect("/");
});

Route::group(['prefix' => 'admin', 'middleware' => ['AdminCheck']], function() {
    
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');


    // Auth Routes
    Route::match(['get', 'post'], 'login', [AuthController::class, 'login'])->name('admin-login');
    Route::get('logout', [AuthController::class, 'logout'])->name('admin-logout');
    Route::match(['get', 'post'], 'profile', [AuthController::class, 'profile'])->name('profile');


    // Post Type Routes
    Route::get('posts', [PostController::class, 'index'])->name('posts');
    Route::match(['get', 'post'], 'post/add', [PostController::class, 'add'])->name('add-post');
    
    Route::match(['get', 'post'], 'post/edit/{id}', [PostController::class, 'edit'])->name('edit-post');
    Route::match(['get', 'post'], 'post/delete/{id?}', [PostController::class, 'delete'])->name('delete-post');
    Route::get('post
        /restore/{id?}', [PostController::class, 'restore'])->name('restore-post');


    // Category Routes
    Route::get('categories', [CategoryController::class, 'index'])->name('categories');
    Route::post('category/add', [CategoryController::class, 'add'])->name('add-category');
    Route::match(['get', 'post'], 'category/edit/{id}', [CategoryController::class, 'edit'])->name('edit-category');
    Route::match(['get', 'post'], 'category/delete/{id?}', [CategoryController::class, 'delete'])->name('delete-category');


    // Page Routes
    Route::get('pages', [PageController::class, 'index'])->name('pages');
    Route::post('page/add', [PageController::class, 'add'])->name('add-page');
    Route::match(['get', 'post'], 'page/edit/{id}', [PageController::class, 'edit'])->name('edit-page');
    Route::match(['get', 'post'], 'page/delete/{id?}', [PageController::class, 'delete'])->name('delete-page');
    Route::get('page/restore/{id?}', [PageController::class, 'restore'])->name('restore-page');

    // Template Routes
    Route::get('templates', [TemplateController::class, 'index'])->name('templates');
    Route::post('template/add', [TemplateController::class, 'add'])->name('add-template');
    Route::match(['get', 'post'], 'template/edit/{id}', [TemplateController::class, 'edit'])->name('edit-template');
    Route::match(['get', 'post'], 'template/delete/{id?}', [TemplateController::class, 'delete'])->name('delete-template');
    Route::get('template/restore/{id?}', [TemplateController::class, 'restore'])->name('restore-template');


    // Comment Routes
    Route::get('comments', [CommentController::class, 'index'])->name('comments');
    Route::match(['get', 'post'], 'comment/edit/{id}', [CommentController::class, 'edit'])->name('edit-comment');
    Route::match(['get', 'post'], 'comment/delete/{id?}', [CommentController::class, 'delete'])->name('delete-comment');
    Route::get('comment/restore/{id?}', [CommentController::class, 'restore'])->name('restore-comment');

    // User Routes
    Route::get('users', [UserController::class, 'index'])->name('users');
    Route::post('user/add', [UserController::class, 'add'])->name('add-user');
    Route::match(['get', 'post'], 'user/edit/{id}', [UserController::class, 'edit'])->name('edit-user');
    Route::match(['get', 'post'], 'user/delete/{id?}', [UserController::class, 'delete'])->name('delete-user');


    // Role Routes
    Route::get('roles', [RoleController::class, 'index'])->name('roles');
    Route::post('role/add', [RoleController::class, 'add'])->name('add-role');
    Route::match(['get', 'post'], 'role/edit/{id}', [RoleController::class, 'edit'])->name('edit-role');
    Route::match(['get', 'post'], 'role/delete/{id?}', [RoleController::class, 'delete'])->name('delete-role');
    

    // Settings Routes
    Route::match(['get', 'post'], 'general-settings', [SettingController::class, 'general_settings'])->name('general-settings');
    Route::match(['get', 'post'], 'roles-settings', [SettingController::class, 'roles_settings'])->name('roles-settings');

    
    
    

    
    // Route::get('category/restore/{id?}', [CategoryController::class, 'restore'])->name('restore-category');

    

});