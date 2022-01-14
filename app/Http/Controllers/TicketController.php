<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json') {
            $tickets = Ticket::paginate(5)->toArray();

            if ($acceptHeader === 'application/json') {
                $response = [
                    "total_count" => $tickets["total"],
                    "limit" => $tickets["per_page"],
                    "pagination" => [
                        "next_page" => $tickets["next_page_url"],
                        "current_page" => $tickets["current_page"]
                    ],
                    "data" => $tickets["data"],
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
            $ticket = Ticket::find($id)->with(['station', 'schedule', 'user'])->get();

            if (!$ticket) {
                abort(404);
            }

            return response()->json($ticket, 200);
        } else {
            return response('Not Acceptable!', 406);
        }
    }

    public function store()
    {
        $acceptHeader = request()->header('Accept');

        if ($acceptHeader === 'application/json') {
            $contentTypeHeader = request()->header('Content-Type');

            if ($contentTypeHeader === 'multipart/form-data; boundary=<calculated when request is sent>') {
                $attr = request()->all();
                $attr['slug'] = Str::slug(request('nama'));

                $validationRules = [
                    "nama" => 'required|min:5',
                    "slug" => 'required|min:5',
                    "kelas" => 'required|min:5',
                    'station_id' => 'required|exists:stations,id'
                ];

                $validator = Validator::make($attr, $validationRules);

                if ($validator->fails()) {
                    return response()->json($validator->errors(), 400);
                }
                $ticket = Ticket::create($attr);

                return response()->json($ticket, 200);
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
                $input['slug'] = Str::slug(request('nama'));

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

                $ticket = Ticket::where(['slug' => $slug])->firstOrFail();

                if (!$ticket) {
                    abort(404);
                }

                $ticket->fill($input);
                $ticket->save();

                return response()->json($ticket, 200);
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
            $ticket = Ticket::where(['slug' => $slug])->firstOrFail();

            if (!$ticket) {
                abort(404);
            }

            $ticket->delete();

            $message = ['message' => 'delete successfully', 'train_slug' => $slug];
            return response()->json($message, 200);
        } else {
            return response('Not Acceptable!', 406);
        }
    }
}
