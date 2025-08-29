<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Message;

class AuthController extends Controller
{


public function login(Request $request)
{
    $request->validate([
        'email'    => 'required|email',
        'password' => 'required|min:6',
    ]);

    // Try to authenticate user using Auth::attempt
    if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
        // Regenerate session for security
        $request->session()->regenerate();

        session()->flash('success', 'You are logged in');
        return redirect('dashboard');
    }

     session()->flash('error', 'invalid credentials');
        return redirect('/');
   
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


    public function signupSubmit(Request $request){


          $request->validate([
            'name'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|min:6', 
            ]);

            $new  = new User();
            $new->name = $request->name;
            $new->email = $request->email;
            $new->password =  Hash::make($request->password); 
            $new->save();
            return redirect('/')
            ->with('success', 'You are successfully registered');


    }


    public function chat(Request $request){
    
            $users = User::where('id', '!=', auth()->id())->select('name','email','id')->paginate(10); // 10 per page
            return view('chat', compact('users'));

    }


    public function getMessages($userId)
    {
         $authId = Auth::id();

        $messages = Message::where(function ($q) use ($authId, $userId) {
                $q->where('sender_id', $authId)->where('receiver_id', $userId);
            })
            ->orWhere(function ($q) use ($authId, $userId) {
                $q->where('sender_id', $userId)->where('receiver_id', $authId);
            })
            ->orderBy('created_at', 'asc')
            ->select('message')
            ->limit(20)
            ->get();

        return response()->json($messages);
    }



     

    public function send_messages(Request $request){

  
         $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'text'    => 'required|string|max:10000',
        ]);

        $msg = Message::create([
            'sender_id'   => Auth::id(),
            'receiver_id' => $request->user_id,
            'message'     => $request->text,
            'is_read'     => 0,
        ]);

        return response()->json($msg);
            

    }



    


}
