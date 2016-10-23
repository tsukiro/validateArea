<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Polygon;
use App\Point;
use Auth;
use Illuminate\Support\Facades\Input;
use Log;
use Uuid;

class PolygonController extends Controller
{
    /**
     * Register New Area
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      try{
        $coordinates_string = Input::get("points");
        $coordinates = json_decode($coordinates_string);

        $polygon = new Polygon;
        $polygon->name = Uuid::generate();
        $polygon->user_id = Auth::id();

        $polygon->save();

        foreach ($coordinates as $coord) {
          $point = new Point;
          $point->longitude = (string)$coord->lng;
          $point->latitude = (string)$coord->lat;
          $point->polygon_id = (string)$polygon->id;
          $point->save();
        }

        $json = (object) [
          'status' => 200,
          'response' => "/polygon",
          'msg' => "Polygon Created"
          ];
        return response(json_encode($json), 200)->header('Content-Type', 'application/json');
      }catch(\Exception $e){
        $json = (object) [
          'status' => 200,
          'response' => "/polygon",
          'msg' => "Error " . $e->getMessage()
          ];
        return response(json_encode($json), 200)->header('Content-Type', 'application/json');
      }
    }

}