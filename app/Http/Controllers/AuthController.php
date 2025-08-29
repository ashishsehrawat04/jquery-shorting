<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {

        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $user = User::where('email', $request->email)->first();



        // if (!$user || !Hash::check($request->password, $user->password)) {
        //     return response()->json([
        //         'status'  => false,
        //         'message' => 'Invalid email or password',
        //     ], 401);
        // }


        // $token = $user->createToken('auth_token')->plainTextToken;

        session()->flash('success', 'you are loged in ');


        return redirect('dashboard');
    }

    public function users_Data(Request $request)
    {

        return view('users');

    }

    public function dashboard(Request $request)
    {
        return view('dashboard');
    }

    public function get_users(Request $request)
    {
        return response()->json([
            'status' => true,
            'users' => User::select('id', 'name', 'email')->get()
        ]);

    }

    public function destroy($id)
    {
        $user = User::find($id);

        if ($user) {
            $user->delete();
            return response()->json(['status' => true, 'message' => 'User deleted successfully']);
        } else {
            return response()->json(['status' => false, 'message' => 'User not found']);
        }
    }

    public function update_user(Request $request, $id)
    {
        $user = User::find($id);

        if(!$user){
            return response()->json(['status'=> false,'message'=> 'data not found']);
        }
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->save();
        return response()->json(['status' => true, 'users' => User::select('id', 'name', 'email')->where('id',$id)->first()]);

    }


}
