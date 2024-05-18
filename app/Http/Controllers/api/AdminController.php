<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;



class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::paginate(12);
        return $users;
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        return $user;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return response()->json(['message' => 'deleted successfuly']);
    }

    public function getAdmins()
    {
        $admins = User::where('role', 1)->get();
        return $admins;
    }

    public function makeAdmin(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            // 'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            // 'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'email' => 'required',
            'password' => 'required',
        ]);

        $admin = new User();

        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->password = $request->password;
        $admin->role = 1;

        $admin->save();

        return response()->json(['message' => 'admin added successfuly']);
    }
}
