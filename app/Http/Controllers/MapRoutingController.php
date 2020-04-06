<?php

namespace App\Http\Controllers;

use App\Conversation;
use App\DriverLocation;
use App\Location;
use App\Message;
use DateTime;
use Illuminate\Http\Request;
use App\User;
use JWTAuth;
use JWTAuthException;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Util\Json;

class MapRoutingController extends Controller
{
    public function findPassengers(Request $request)
    {
        $date = new DateTime;
        $date->modify('-15 seconds');
        $formatted_date = $date->format('Y-m-d H:i:s');
        $passengers = Location::where('updated_at', '>=', $formatted_date)->where('user_id','!=',$request->user_id)->get();

        $selectedPassengers = [];
        //get trips that are very close to user tripData
        $tripData = $request->tripData;
        $ss_lat = $tripData['start']['lat'];
        $ss_lng = $tripData['start']['lng'];
        $dd_lat = $tripData['destination']['lat'];
        $dd_lng = $tripData['destination']['lng'];

        $maxDistance = 250; //meters

        foreach ($passengers as $passenger) {

            $trip = new \stdClass();
            $trip->s_lat = $ss_lat;
            $trip->s_lng = $ss_lng;
            $trip->d_lat = $dd_lat;
            $trip->d_lng = $dd_lng;
            $s_lat = $passenger->s_lat;
            $s_lng = $passenger->s_lng;
            $d_lat = $passenger->d_lat;
            $d_lng = $passenger->d_lng;

            //start mai aproape  de trip start, dest mai aproape de trip dest
            //start si dest sa fie in paralelipipedul determinat de trip
            //start si dest sa fie mai aproape de X de dreapta determinata de trip start si trip dest
            array_push($selectedPassengers, $passenger);
            if (
                self::inAreaOfTrip($trip->s_lat, $trip->s_lng, $trip->d_lat, $trip->d_lng, $s_lat, $s_lng) &&
                self::inAreaOfTrip($trip->s_lat, $trip->s_lng, $trip->d_lat, $trip->d_lng, $d_lat, $d_lng) &&
                (self::distanceBetweenPP($trip->s_lat, $trip->s_lng, $s_lat, $s_lng) < self::distanceBetweenPP($trip->s_lat, $trip->s_lng, $d_lat, $d_lng)) &&
                (self::distanceBetweenPP($trip->d_lat, $trip->d_lng, $d_lat, $d_lng) < self::distanceBetweenPP($trip->d_lat, $trip->d_lng, $s_lat, $s_lng)) &&
                (self::distanceBetweenLP($trip->s_lat, $trip->s_lng, $trip->d_lat, $trip->d_lng, $s_lat, $s_lng) <= $maxDistance) &&
                (self::distanceBetweenLP($trip->s_lat, $trip->s_lng, $trip->d_lat, $trip->d_lng, $d_lat, $d_lng) <= $maxDistance)
            ) {
                $passenger->name = User::find($passenger->user_id)->name;
                array_push($selectedPassengers, $passenger);
            }
        }

        return response()->json($selectedPassengers, 200);
    }

    static function inAreaOfTrip($lat1, $lng1, $lat2, $lng2, $lat3, $lng3)
    {
        $minLat = $lat1 < $lat2 ? $lat1 : $lat2;
        $maxLat = $lat1 > $lat2 ? $lat1 : $lat2;
        $minLng = $lng1 < $lng2 ? $lng1 : $lng2;
        $maxLng = $lng1 > $lng2 ? $lng1 : $lng2;

        if ($minLat <= $lat3 && $lat3 <= $maxLat && $minLng <= $lng3 && $lng3 <= $maxLng)
            return true;

        return false;
    }

    static function distanceBetweenPP($lat1, $lng1, $lat2, $lng2)
    {
        $earth_radius = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * asin(sqrt($a));
        $d = $earth_radius * $c;

        return $d * 1000;
    }

    static function distanceBetweenLP($lat1, $lng1, $lat2, $lng2, $lat3, $lng3)
    {
        $XX = $lat2 - $lat1;
        $YY = $lng2 - $lng1;
        $ShortestLength = (($XX * ($lat3 - $lat1)) + ($YY * ($lng3 - $lng1))) / (($XX * $XX) + ($YY * $YY));
        $lat4 = $lat1 + $XX * $ShortestLength;
        $lng4 = $lng1 + $YY * $ShortestLength;

        return self::distanceBetweenPP($lat3, $lng3, $lat4, $lng4);
    }

    public function notifyPassengers(Request $request)
    {
        $passengers = json_encode($request->selectedPassengers);

        $conversation = new Conversation();
        $conversation->driver_id = $request->driver_id;
        $conversation->passengers = $passengers;
        $conversation->save();

        foreach ($request->selectedPassengers as $passenger) {
            $location = Location::find($passenger);
            $location->accepted = 1;
            $location->conversation_id = $conversation->id;
            $location->save();
        }

        return response()->json($conversation, 200);
    }

    public function savePassengerLocation(Request $request)
    {
        $tripData = $request->tripData;
        $location = null;

        if (count(Location::where('user_id', $request->userID)->get()) == 0)
            $location = new Location();
        else {
            $location = Location::where('user_id', $request->userID)->get()[0];
            if($request->first == 1) {
                $location->accepted = 0;
                $location->conversation_id = 0;
            }
        }


        $location->user_id = $request->userID;
        $location->s_lat = $tripData['start']['lat'];
        $location->s_lng = $tripData['start']['lng'];
        $location->d_lat = $tripData['destination']['lat'];
        $location->d_lng = $tripData['destination']['lng'];

        if ($location->save())
            return response()->json(['success' => true, 'accepted' => $location->accepted, 'conversation_id' => $location->conversation_id ], 200);


        return response()->json(['error' => 'There was an error saving the location'], 500);
    }

    public function sendMessage(Request $request){
        $message = new Message();

        $message->is_driver = $request->is_driver;
        $message->sender_name = $request->sender_name;
        $message->conversation_id = $request->conversation_id;
        $message->text = $request->text;

        if($message->save())
            return response()->json(['success' => true], 200);

        return response()->json(['error' => 'There was an error saving the message'], 500);
    }

    public function getMessages(Request $request){
        $messages = Message::where('conversation_id', $request->conversation_id)->orderBy('created_at','ASC')->get();
        $conversation = Conversation::find($request->conversation_id);

        if($request->is_driver == 1) {

            if(count(DriverLocation::where('driver_id',$conversation->driver_id)->get()) == 0){
                $driverLocation = new DriverLocation();
                $driverLocation->driver_id = $conversation->driver_id;
                $driverLocation->lat = $request->lat;
                $driverLocation->lng = $request->lng;
                $driverLocation->save();
            } else {
                $driverLocation = DriverLocation::where('driver_id',$conversation->driver_id)->get()[0];
                $driverLocation->lat = $request->lat;
                $driverLocation->lng = $request->lng;
                $driverLocation->save();
            }

            $passengers = json_decode($conversation->passengers);
            $passengerLocations = [];
            foreach ($passengers as $passenger) {
                $result = Location::where('user_id', $passenger)->get()[0];
                $result->name = User::find($result->user_id)->name;
                array_push($passengerLocations, $result);
            }
            return response()->json(['messages' => $messages, 'passengers' => $passengerLocations], 200);
        } else {
            $driver_location = DriverLocation::where('driver_id',$conversation->driver_id)->get()[0];
            return response()->json(['messages' => $messages, 'driver_location' => $driver_location], 200);
        }
    }
}
