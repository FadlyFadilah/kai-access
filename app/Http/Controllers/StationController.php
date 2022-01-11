<?php

namespace App\Http\Controllers;

use App\Models\Station;

class StationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function index()
    {
        $stations = Station::OrderBy("id", "DESC")->paginate(10);

        $outPut = [
            "message" => "stations",
            "result" => $stations
        ];
        return response()->json($stations, 200);
    }

    public function show($id)
    {
        $station = Station::find($id);
        if (!$station) {
            abort(404);
        }

        return response()->json($station, 200);
    }
}
