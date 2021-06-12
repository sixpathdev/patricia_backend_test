<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ApiResponse;
    public function show(Request $request, $id)
    {
        $user = User::where('id',$id)->first();
        if(!$user) {
            return $this->error(null, 'User does not exist', 404);
        }
        return $this->success($user, 'User data retrieved successfully');
    }

    public function update(Request $request, $id)
    {
        $user = User::where('id', $id)->first();
        if (!$user) {
            return $this->error(null, 'User does not exist', 404);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return $this->success($user, 'User data updated successfully');
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return $this->error(null, 'User does not exist', 404);
        }
        $user->delete();

        return $this->success(null, 'User removed successfully');
    }
}
