<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class permetionController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        return view('admin.permission.index');
    }
    public function adminPermission()
    {
        $permissions  = Permission::all();
        return view('admin.permission.adminPermission',compact("permissions"));

    }
}
