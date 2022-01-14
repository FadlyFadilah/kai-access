<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PublicOrderController extends Controller
{
    public function index(Request $request)
    {
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json') {
            $orders = Order::paginate(5)->toArray();

            if ($acceptHeader === 'application/json') {
                $response = [
                    "total_count" => $orders["total"],
                    "limit" => $orders["per_page"],
                    "pagination" => [
                        "next_page" => $orders["next_page_url"],
                        "current_page" => $orders["current_page"]
                    ],
                    "data" => $orders["data"],
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
            $order = Order::find($id)->with(['ticket', 'user'])->get();

            if (!$order) {
                abort(404);
            }

            return response()->json($order, 200);
        } else {
            return response('Not Acceptable!', 406);
        }
    }

    public function store($id)
    {
        $acceptHeader = request()->header('Accept');

        if ($acceptHeader === 'application/json') {
            $contentTypeHeader = request()->header('Content-Type');

            if ($contentTypeHeader === 'multipart/form-data; boundary=<calculated when request is sent>') {
                $ticket = Ticket::find($id)->firstOrFails();
                $attr = request()->all();
                $attr['user_id'] = request()->input('user_id');
                $attr['ticket_id'] = request()->input('ticket_id');
                $attr['harga'] = $ticket['harga'];

                $order = Order::create($attr);

                return response()->json($order, 200);
            } else {
                return response('Unsupported Media Type', 415);
            }
        } else {
            return response('Not Acceptable!', 406);
        }
    }

    public function update($slug)
    {
        $acceptHeader = request()->header('Accept');

        if ($acceptHeader === 'application/json') {
            $contentTypeHeader = request()->header('Content-Type');

            if ($contentTypeHeader === 'multipart/form-data; boundary=<calculated when request is sent>') {
                $input = request()->all();

                $validationRules = [
                    "nama" => 'required|min:5',
                    "slug" => 'required|min:5',
                    "kelas" => 'required|min:5',
                    'station_id' => 'required|exists:stations,id'
                ];

                $validator = Validator::make($input, $validationRules);

                if ($validator->fails()) {
                    return response()->json($validator->errors(), 400);
                }

                $order = Order::where(['slug' => $slug])->firstOrFail();

                if (!$order) {
                    abort(404);
                }

                $order->fill($input);
                $order->save();

                return response()->json($order, 200);
            } else {
                return response('Unsupported Media Type', 415);
            }
        } else {
            return response('Not Acceptable!', 406);
        }
    }

    public function destroy($slug)
    {
        $acceptHeader = request()->header('Accept');

        if ($acceptHeader === 'application/json') {
            $order = Order::where(['slug' => $slug])->firstOrFail();

            if (!$order) {
                abort(404);
            }

            $order->delete();

            $message = ['message' => 'delete successfully', 'train_slug' => $slug];
            return response()->json($message, 200);
        } else {
            return response('Not Acceptable!', 406);
        }
    }
}
