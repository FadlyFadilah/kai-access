<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use JD\Cloudder\Facades\Cloudder;

class userController extends Controller
{
    public function index(Request $request)
    {
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json') {
            $users = User::paginate(5)->toArray();

            if ($acceptHeader === 'application/json') {
                $response = [
                    "total_count" => $users["total"],
                    "limit" => $users["per_page"],
                    "pagination" => [
                        "next_page" => $users["next_page_url"],
                        "current_page" => $users["current_page"]
                    ],
                    "data" => $users["data"],
                ];
                return response()->json($response, 200);
            }
        } else {
            return response('Not Acceptable!', 406);
        }
    }

    public function show($id)
    {
        $acceptHeader = request()->header('Accept');

        if ($acceptHeader === 'application/json') {

            $user = User::find($id)->with('orders')->first();

            if (!$user) {
                abort(404);
            }

            if ($user->picture == null) {
                return response()->json($user, 200);
            }
            $gambar = Cloudder::show($user->picture);

            return response()->json([
                "data" => $user,
                "picture" => $gambar
            ], 200);
        } else {
            return response('Not Acceptable!', 406);
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'nama' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ]);

        $input = $request->all();

        $validation = [
            'nama' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ];

        $validator = Validator::make($input, $validation);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $file = $request->file('picture');
        $file_url = "kai-access.test/defaultimage.png";
        if ($file) {
            $dateTime = date('Ymd_his');
            $newName = 'user_' . $dateTime;
            $cloudder = Cloudder::upload($request->file('picture')->getRealPath(), $newName);
            $uploadResult = $cloudder->getResult();
            $file_url = $uploadResult["url"];
        }

        $user = new User();
        $user->nama = $request->input('nama');
        $user->email = $request->input('email');
        $passwordP = $request->input('password');
        $user->picture = $newName;
        $user->password = app('hash')->make($passwordP);
        $user->save();

        return response()->json([
            "data" => $user,
            "picture_URL" => $file_url
        ], 200);
    }

    public function update($id)
    {
        $acceptHeader = request()->header('Accept');

        if ($acceptHeader === 'application/json') {
            $contentTypeHeader = request()->header('Content-Type');

            if ($contentTypeHeader === 'application/x-www-form-urlencoded') {
                $user = User::find($id);

                $user->nama = request()->input('nama');
                $user->email = request()->input('email');
                $passwordP = request()->input('password');
                $user->password = app('hash')->make($passwordP);
                $user->save();

                return response()->json($user, 200);
            } else {
                return response('Unsupported Media Type', 415);
            }
        } else {
            return response('Not Acceptable!', 406);
        }
    }

    public function destroy($id)
    {
        $acceptHeader = request()->header('Accept');

        if ($acceptHeader === 'application/json') {
            $user = user::find($id)->firstOrFail();
            if ($user->picture == null) {
                $user->delete();

                $message = ['message' => 'delete successfully', 'user_id' => $id];
                return response()->json($message, 200);
            }

            $destroy = Cloudder::destroyImage($user->picture);
            if (!$user) {
                abort(404);
            }

            $user->delete();

            $message = ['message' => 'delete successfully', 'user_id' => $id];
            return response()->json($message, 200);
        } else {
            return response('Not Acceptable!', 406);
        }
    }

    public function updatePicture($id)
    {
        $acceptHeader = request()->header('Accept');

        if ($acceptHeader === 'application/json') {
            $contentTypeHeader = request()->header('Content-Type');

            if ($contentTypeHeader === 'multipart/form-data; boundary=<calculated when request is sent>') {
                $users = User::where(["id" => $id])->firstOrFail();

                $file = request()->file('picture');
                if ($file) {
                    if ($users->picture == null) {
                        $dateTime = date('Ymd_his');
                        $newName = 'user_' . $dateTime;
                        $cloudder = Cloudder::upload(request()->file('picture')->getRealPath(), $newName);
                        $uploadResult = $cloudder->getResult();
                        $file_url = $uploadResult["url"];

                        $users->fill([
                            "nama" => $users->nama,
                            "email" => $users->email,
                            "password" => $users->password,
                            "picture" => $newName,
                        ]);
                        $users->save();
                        return response()->json([
                            "picture_URL" => $file_url
                        ], 200);
                    }
                    Cloudder::destroyImage($users->picture);

                    $dateTime = date('Ymd_his');
                    $newName = 'user_' . $dateTime;
                    $cloudder = Cloudder::upload(request()->file('picture')->getRealPath(), $newName);
                    $uploadResult = $cloudder->getResult();
                    $file_url = $uploadResult["url"];
                } else {
                    $newName = $users->picture;
                }

                $users->fill([
                    "nama" => $users->nama,
                    "email" => $users->email,
                    "password" => $users->password,
                    "picture" => $newName,
                ]);
                $users->save();
                return response()->json([
                    "picture_URL" => $file_url
                ], 200);
            } else {
                return response('Unsupported Media Type', 415);
            }
        } else {
            return response('Not Acceptable!', 406);
        }
    }
}