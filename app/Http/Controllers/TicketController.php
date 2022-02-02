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
            $ticket = Ticket::find($id)->with(['station', 'schedule'])->get();

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

            if ($contentTypeHeader === 'application/x-www-form-urlencoded') {
                $attr = request()->all();

                $validationRules = [
                    "namaLengkap" => 'required|min:5',
                    "tujuan" => 'required|min:5',
                    "harga" => 'required|min:3',
                    "schedule_id" => 'required|exists:schedules,id',
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

    public function update($id)
    {
        $acceptHeader = request()->header('Accept');

        if ($acceptHeader === 'application/json') {
            $contentTypeHeader = request()->header('Content-Type');

            if ($contentTypeHeader === 'application/x-www-form-urlencoded') {
                $input = request()->all();

                $validationRules = [
                    "namaLengkap" => 'required|min:5',
                    "tujuan" => 'required|min:5',
                    "harga" => 'required|min:3',
                    "schedule_id" => 'required|exists:schedules,id',
                    'station_id' => 'required|exists:stations,id'
                ];

                $validator = Validator::make($input, $validationRules);

                if ($validator->fails()) {
                    return response()->json($validator->errors(), 400);
                }

                $ticket = Ticket::where(['id' => $id])->firstOrFail();

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

    public function destroy($id)
    {
        $acceptHeader = request()->header('Accept');

        if ($acceptHeader === 'application/json') {
            $ticket = Ticket::where(['id' => $id])->firstOrFail();

            if (!$ticket) {
                abort(404);
            }

            $ticket->delete();

            $message = ['message' => 'delete successfully', 'ticket_id' => $id];
            return response()->json($message, 200);
        } else {
            return response('Not Acceptable!', 406);
        }
    }
}
