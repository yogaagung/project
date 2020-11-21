<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Tag;
use App\Models\Category;
use App\Models\Role;
use App\Models\User;
use App\Models\Blogcategory;
use App\Models\Blogtag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\TryCatch;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        // Check if login User and Admin User
        // return Auth::check();
        if (!Auth::check() && $request->path() != 'login') {
            return redirect('/login');
        }
        if (!Auth::check() && $request->path() == 'login') {
            return view('welcome');
        }
        // Logged in if Admin User
        $user = Auth::user();
        if ($user->userType == 'User') {
            return redirect('/login');
        }
        if ($request->path() == 'login') {
            return redirect('/');
        }
        return $this->checkForPermission($user, $request);
    }

    public function checkForPermission($user, $request)
    {
        $permission = json_decode($user->role->permission);
        $hasPermission = false;
        if (!$permission) return view('welcome');
        foreach ($permission as $p) {
            if ($p->name == $request->path()) {
                if ($p->read) {
                    $hasPermission = true;
                }
            }
        }
        if ($hasPermission) return view('welcome');
        return view('welcome');
        return view('404');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }

    public function addTag(Request $request)
    {
        // Validate request
        $this->validate($request, [
            'tagName' => 'required'
        ]);
        return Tag::create([
            'tagName' => $request->tagName,
        ]);
    }

    public function getTag()
    {
        return Tag::orderBy('id', 'desc')->get();
    }

    public function editTag(Request $request)
    {
        // Validate request
        $this->validate($request, [
            'tagName' => 'required',
            'id' => 'required',
        ]);
        return Tag::where('id', $request->id)->update([
            'tagName' => $request->tagName
        ]);
    }

    public function deleteTag(Request $request)
    {
        // Validate request
        $this->validate($request, [
            'id' => 'required',
        ]);
        return Tag::where('id', $request->id)->delete();
    }

    public function upload(Request $request)
    {
        // Validate request
        $this->validate($request, [
            'file' => 'required|mimes:jpeg,jpg,png'
        ]);
        $picName = time() . '.' . $request->file->extension();
        $request->file->move(public_path('uploads'), $picName);
        return $picName;
    }

    public function uploadEditorImage(Request $request)
    {
        // Validate request
        $this->validate($request, [
            'image' => 'required|mimes:jpeg,jpg,png'
        ]);
        $picName = time() . '.' . $request->image->extension();
        $request->image->move(public_path('uploads'), $picName);
        return response()->json([
            'success' => 1,
            'file' => [
                'url' => "http://127.0.0.1:8000/uploads/$picName"
            ]
        ]);
        return $picName;
    }

    public function deleteImage(Request $request)
    {
        $fileName = $request->imageName;
        $this->deleteFileFromServer($fileName, false);
        return 'Ok';
    }

    public function deleteFileFromServer($fileName, $hasFullPath = false)
    {
        if (!$hasFullPath) {
            $filePath = public_path() . '/uploads/' . $fileName;
        }
        if (file_exists($filePath)) {
            @unlink($filePath);
        }
        return;
    }

    public function addCategory(Request $request)
    {
        // Validate request
        $this->validate($request, [
            'categoryName' => 'required',
            'iconImage' => 'required',
        ]);
        return Category::create([
            'categoryName' => $request->categoryName,
            'iconImage' => $request->iconImage,
        ]);
    }

    public function getCategory()
    {
        return Category::orderBy('id', 'desc')->get();
    }

    public function editCategory(Request $request)
    {
        // Validate request
        $this->validate($request, [
            'categoryName' => 'required',
            'iconImage' => 'required',
        ]);
        return Category::where('id', $request->id)->update([
            'categoryName' => $request->categoryName,
            'iconImage' => $request->iconImage,
        ]);
    }

    public function deleteCategory(Request $request)
    {
        // Delete from server
        $this->deleteFileFromServer($request->iconImage);
        // Validate request
        $this->validate($request, [
            'id' => 'required',
        ]);
        return Category::where('id', $request->id)->delete();
    }

    public function createUser(Request $request)
    {
        $this->validate($request, [
            'fullName' => 'required',
            'email' => 'bail|required|email|unique:users',
            'password' => 'bail|required|min:6',
            'role_id' => 'required',
        ]);
        $password = bcrypt($request->password);
        $user = User::create([
            'fullName' => $request->fullName,
            'email' => $request->email,
            'password' => $password,
            'role_id' => $request->role_id,
        ]);
        return $user;
    }

    public function editUser(Request $request)
    {
        $this->validate($request, [
            'fullName' => 'required',
            'email' => "bail|required|email|unique:users,email,$request->id",
            'password' => 'min:6',
            'userType' => 'required',
        ]);
        $data = [
            'fullName' => $request->fullName,
            'email' => $request->email,
            'userType' => $request->userType,
        ];
        if ($request->password) {
            $password = bcrypt($request->password);
            $data['password'] = $password;
        }
        $user = User::where('id', $request->id)->update($data);
        return $user;
    }

    public function getUser()
    {
        return User::get();
    }

    public function adminLogin(Request $request)
    {
        // Validate request
        $this->validate($request, [
            'email' => 'bail|required|email',
            'password' => 'bail|required|min:6',
        ]);
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            if ($user->role->isAdmin == 0) {
                Auth::logout();
                return response()->json([
                    'msg' => 'Incorrect login details',
                ], 401);
            }
            return response()->json([
                'msg' => 'You are logged in',
                'user' => $user,
            ]);
        } else {
            return response()->json([
                'msg' => 'Incorrect login details',
            ], 401);
        }
    }

    // Role
    public function addRole(Request $request)
    {
        // Validate request
        $this->validate($request, [
            'roleName' => 'required',
        ]);
        return Role::create([
            'roleName' => $request->roleName
        ]);
    }

    public function editRole(Request $request)
    {
        // Validate request
        $this->validate($request, [
            'roleName' => 'required',
        ]);
        return Role::where('id', $request->id)->update([
            'roleName' => $request->roleName
        ]);
    }

    public function getRole()
    {
        return Role::all();
    }

    public function assignRole(Request $request)
    {
        // Validate request
        $this->validate($request, [
            'permission' => 'required',
            'id' => 'required',
        ]);
        return Role::where('id', $request->id)->update([
            'permission' => $request->permission
        ]);
    }

    public function slug()
    {
        $title = 'This is a nice title changed';
        return Blog::create([
            'title' => $title,
            'post' => 'some post',
            'post_excerpt' => 'aead',
            'user_id' => 1,
            'metaDescription' => 'aead',
        ]);
        return $title;
    }

    public function createArticle(Request $request)
    {
        // Validate request
        $this->validate($request, [
            'title' => 'required',
            'post' => 'required',
            'post_excerpt' => 'required',
            'metaDescription' => 'required',
            'jsonData' => 'required',
            'category_id' => 'required',
            'tag_id' => 'required',
        ]);
        $categories = $request->category_id;
        $tags = $request->tag_id;
        $blogCategories = [];
        $blogTags = [];
        DB::beginTransaction();
        try {
            $blog = Blog::create([
                'title' => $request->title,
                'slug' => $request->title,
                'post' => $request->post,
                'post_excerpt' => $request->post_excerpt,
                'user_id' => Auth::user()->id,
                'metaDescription' => $request->metaDescription,
                'jsonData' => $request->jsonData,
            ]);
            // Blog Category Insert
            foreach ($categories as $c) {
                array_push($blogCategories, ['category_id' => $c, 'blog_id' => $blog->id]);
            }
            Blogcategory::insert($blogCategories);
            // Blog Tag Insert
            foreach ($tags as $t) {
                array_push($blogTags, ['tag_id' => $t, 'blog_id' => $blog->id]);
            }
            Blogtag::insert($blogTags);
            DB::commit();
            return 'Ok';
        } catch (\Throwable $th) {
            DB::rollBack();
            return 'Not ok';
        }
    }

    public function updateBlog(Request $request, $id)
    {
        // Validate request
        $this->validate($request, [
            'title' => 'required',
            'post' => 'required',
            'post_excerpt' => 'required',
            'metaDescription' => 'required',
            'jsonData' => 'required',
            'category_id' => 'required',
            'tag_id' => 'required',
        ]);
        $categories = $request->category_id;
        $tags = $request->tag_id;
        $blogCategories = [];
        $blogTags = [];
        DB::beginTransaction();
        try {
            $blog = Blog::where('id', $id)->update([
                'title' => $request->title,
                'slug' => $request->title,
                'post' => $request->post,
                'post_excerpt' => $request->post_excerpt,
                'user_id' => Auth::user()->id,
                'metaDescription' => $request->metaDescription,
                'jsonData' => $request->jsonData,
            ]);
            // Blog Category Insert
            foreach ($categories as $c) {
                array_push($blogCategories, ['category_id' => $c, 'blog_id' => $id]);
            }
            // Delete Previous Category
            Blogcategory::where('blog_id', $id)->delete();
            Blogcategory::insert($blogCategories);
            // Blog Tag Insert
            foreach ($tags as $t) {
                array_push($blogTags, ['tag_id' => $t, 'blog_id' => $id]);
            }
            // Delete Previous Tag
            Blogtag::where('blog_id', $id)->delete();
            Blogtag::insert($blogTags);
            DB::commit();
            return 'Ok';
        } catch (\Throwable $e) {
            DB::rollBack();
            return 'Not ok';
        }
    }

    public function blogdata(Request $request)
    {
        return Blog::with(['tag', 'cat'])->orderBy('id', 'desc')->paginate($request->total);
    }

    public function deleteBlog(Request $request)
    {
        return Blog::where('id', $request->id)->delete();
    }

    public function SingleBlogItem(Request $request, $id)
    {
        return Blog::with(['tag', 'cat'])->where('id', $id)->first();
    }
}
