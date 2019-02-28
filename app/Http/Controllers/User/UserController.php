<?php

namespace App\Http\Controllers\User;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class UserController extends ApiController
{

    public function index()
    {
        $users = User::all();
//        return response()->json(['data' => $users], 200);
        return $this->showAll($users);
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ];
        $this->validate($request, $rules);
        $data = $request->all();
        $data['password'] = bcrypt($request->password);
        $data['verified'] = User::UNVERIFIED_USER;
        $data['verification_token'] = User::generateVerificationCode();
        $data['admin'] = User::REGULAR_USER;

        $user = User::create($data);

//        return response()->json(['data', $user], 201);
        return $this->showOne($user, 201);

    }

    public function show(User $user)
    {
//        $user = User::findOrFail($id);
//        return response()->json(['data', $user], 200);

        return $this->showOne($user);
    }

    public function update(Request $request, User $user)
    {

//        $user = User::findOrFail($id);

        $rules = [
            'email' => 'email|unique:users,email,' . $user->id,
            'password' => 'min:6|confirmed',
            'admin' => 'in:' . User::ADMIN_USER . ',' . User::REGULAR_USER,
        ];
        if ($request->has('name')){
            $user->name = $request->name;
        }
        if ($request->has('email') && $user->email != $request->email){
            $user->verified = User::UNVERIFIED_USER;
            $user->verification_token = User::generateVerificationCode();
            $user->email = $request->email;
        }
        if ($request->has('password')){
            $user->password = bcrypt($request->password);
        }
        if ($request->has('admin')){
            if (!$user->isVerified()){
                return $this->errorResponse('Only verified users can modify the admin field', 409);
            }
            $user->admin = $request->admin;
        }
        if (!$user->isDirty()){
            return $this->errorResponse('You need to specify a different value to update', 422);
        }

        $user->save();

//        return response()->json(['data', $user], 200);
        return $this->showOne($user);
    }

    public function destroy(User $user)
    {
//        $user = User::findOrFail($id);

        $user->delete();

//        return response()->json(['data', $user], 200);
        return $this->showOne($user);
    }
}
