<?php

use App\Http\Middleware\AdminCheck;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

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

Route::prefix('app')->middleware(AdminCheck::class)->group(function () {
    Route::post('/create_tag', [AdminController::class, 'addTag']);
    Route::get('/get_tags', [AdminController::class, 'getTag']);
    Route::post('/edit_tag', [AdminController::class, 'editTag']);
    Route::post('/delete_tag', [AdminController::class, 'deleteTag']);
    Route::post('/upload', [AdminController::class, 'upload']);
    Route::post('/delete_image', [AdminController::class, 'deleteImage']);
    Route::post('/create_category', [AdminController::class, 'addCategory']);
    Route::get('/get_category', [AdminController::class, 'getCategory']);
    Route::post('/edit_category', [AdminController::class, 'editCategory']);
    Route::get('/get_category', [AdminController::class, 'getCategory']);
    Route::post('/delete_category', [AdminController::class, 'deleteCategory']);
    Route::post('/create_user', [AdminController::class, 'createUser']);
    Route::get('/get_users', [AdminController::class, 'getUser']);
    Route::post('/edit_user', [AdminController::class, 'editUser']);
    Route::post('/admin_login', [AdminController::class, 'adminLogin']);

    // Role Routes
    Route::post('create_role', [AdminController::class, 'addRole']);
    Route::get('get_roles', [AdminController::class, 'getRole']);
    Route::post('edit_role', [AdminController::class, 'editRole']);
    Route::post('assign_roles', [AdminController::class, 'assignRole']);

    // Blog
    Route::post('create-blog', [AdminController::class, 'createArticle']);
    Route::get('blogsdata', [AdminController::class, 'blogdata']); // Get Item Blog
    Route::post('delete_blog', [AdminController::class, 'deleteBlog']);
    Route::get('blog_single/{id}', [AdminController::class, 'SingleBlogItem']);
    Route::post('update_blog/{id}', [AdminController::class, 'updateBlog']);
});

Route::post('createArticle', [AdminController::class, 'uploadEditorImage']);
Route::get('slug', [AdminController::class, 'slug']);
Route::get('blogdata', [AdminController::class, 'blogdata']);

Route::get('/', [AdminController::class, 'index']);
Route::get('/logout', [AdminController::class, 'logout']);
// Route::any('{slug}', [AdminController::class, 'index']);
Route::any('{slug}', [AdminController::class, 'index'])->where('slug', '([A-z/0-9_\s]+)');


// Route::get('/', function () {
//     return view('welcome');
// });
// Route::any('{slug}', function () {
//     return view('welcome');
// });
