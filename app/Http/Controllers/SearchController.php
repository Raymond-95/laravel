<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class SearchController extends Controller
{
    function parseFloat($ptString) { 
                if (strlen($ptString) == 0) { 
                        return false; 
                } 

                $pString = str_replace(" ", "", $ptString); 

                if (substr_count($pString, ",") > 1) 
                    $pString = str_replace(",", "", $pString); 

                if (substr_count($pString, ".") > 1) 
                    $pString = str_replace(".", "", $pString); 

                $pregResult = array(); 

                $commaset = strpos($pString,','); 
                if ($commaset === false) {$commaset = -1;} 

                $pointset = strpos($pString,'.'); 
                if ($pointset === false) {$pointset = -1;} 

                $pregResultA = array(); 
                $pregResultB = array(); 

                if ($pointset < $commaset) { 
                    preg_match('#(([-]?[0-9]+(\.[0-9])?)+(,[0-9]+)?)#', $pString, $pregResultA); 
                } 
                preg_match('#(([-]?[0-9]+(,[0-9])?)+(\.[0-9]+)?)#', $pString, $pregResultB); 
                if ((isset($pregResultA[0]) && (!isset($pregResultB[0]) 
                        || strstr($preResultA[0],$pregResultB[0]) == 0 
                        || !$pointset))) { 
                    $numberString = $pregResultA[0]; 
                    $numberString = str_replace('.','',$numberString); 
                    $numberString = str_replace(',','.',$numberString); 
                } 
                elseif (isset($pregResultB[0]) && (!isset($pregResultA[0]) 
                        || strstr($pregResultB[0],$preResultA[0]) == 0 
                        || !$commaset)) { 
                    $numberString = $pregResultB[0]; 
                    $numberString = str_replace(',','',$numberString); 
                } 
                else { 
                    return false; 
                } 
                $result = (float)$numberString; 
                return $result; 
    }   

    function xrange($start, $limit, $step = 1) {
        if ($start < $limit) {
            if ($step <= 0) {
                throw new LogicException('Step must be +ve');
            }

            for ($i = $start; $i <= $limit; $i += $step) {
                yield $i;
            }
        } else {
            if ($step >= 0) {
                throw new LogicException('Step must be -ve');
            }

            for ($i = $start; $i >= $limit; $i += $step) {
                yield $i;
            }
        }
    }

    function modf($x) {
        $m = fmod($x, 1);
        return [$m, $x - $m];
    }


    function getPathLength($lat1,$lng1,$lat2,$lng2)
    {
        $R = '6371000'; //# radius of earth in m
        $lat1rads = deg2rad($lat1);
        $lat2rads = deg2rad($lat2);
        $deltaLat = deg2rad(($lat2 - $lat1));
        $deltaLng = deg2rad(($lng2 - $lng1));
        $a = sin($deltaLat/2) * sin($deltaLat/2) + cos($lat1rads) * cos($lat2rads) * sin($deltaLng/2) * sin($deltaLng/2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $d = $R * $c;
        return $d;
    }

    function getDestinationLatLong($lat,$lng,$azimuth,$distance){

        $R = 6378.1; //#Radius of the Earth in km
        $brng = deg2rad($azimuth); #Bearing is degrees converted to radians.
        $d = $distance / 1000; #Distance m converted to km
        $lat1 = deg2rad($lat); #Current dd lat point converted to radians
        $lon1 = deg2rad($lng); #Current dd long point converted to radians
        $lat2 = asin(sin($lat1) * cos($d/$R) + cos($lat1)* sin($d/$R)* cos($brng));
        $lon2 = $lon1 + atan2(sin($brng) * sin($d/$R)* cos($lat1), cos($d/$R)- sin($lat1)* sin($lat2));
        #convert back to degrees
        $lat2 = rad2deg($lat2);
        $lon2 = rad2deg($lon2);

        return [$lat2, $lon2];  
    }

    function calculateBearing($lat1,$lng1,$lat2,$lng2){
       // '''calculates the azimuth in degrees from start point to end point'''
        $startLat = deg2rad($lat1);
        $startLong = deg2rad($lng1);
        $endLat = deg2rad($lat2);
        $endLong = deg2rad($lng2);
        $dLong = $endLong - $startLong;
        $dPhi = log(tan($endLat / 2 + pi() / 4) / tan($startLat / 2 + pi() / 4));
        if (abs($dLong) > pi()) {
            if ($dLong > 0) {
                $dLong = -(2 * pi() - $dLong);
            } else {
                $dLong = 2 * pi() + $dLong;
            }
        }
        $bearing = (rad2deg(atan2($dLong, $dPhi)) + 360) % 360;
        return $bearing;
    }

    function main($interval, $azimuth, $lat1, $lng1, $lat2, $lng2) 
    {
        $d = $this->getPathLength($lat1, $lng1, $lat2, $lng2);
        $rapydUnpack = $this->modf($d / $interval);
        $remainder = $rapydUnpack[0];
        $dist = $rapydUnpack[1];
        $counter = $this->parseFloat($interval);
        $coords = [];
        array_push($coords, [ $lat1, $lng1 ]);

        $xRange = $this->xrange(0, intval($dist));

        foreach ($xRange as $rapydIndex => $value) 
        {
        //print $value;
            $distance =$value;
            $coord = $this->getDestinationLatLong($lat1, $lng1, $azimuth, $counter);
            $counter = $counter + $this->parseFloat($interval);
            array_push($coords, $coord);
        }
        array_push($coords, [ $lat2, $lng2]);

        return $coords;
    }

    function GetDrivingDistance($lat1, $long1, $lat2, $long2)
    {
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$lat1.",".$long1."&destinations=".$lat2.",".$long2."&mode=driving&language=pl-PL";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        curl_close($ch);
        $response_a = json_decode($response, true);
        $dist = $response_a['rows'][0]['elements'][0]['distance']['text'];

        return $dist;
    }

    function calculateDistance($coords, $source, $destination){

        $limit = 3;
        $current = 0;

        foreach ($coords as $coord) 
        {

            $distance = $this->GetDrivingDistance($coord[0], $coord[1], $source, $destination);

            echo $distance;

            if ($current == 0){
                $current = $distance;
            }elseif ($distance <= $current){
               $current = $distance;
            }elseif ($distance > $current){

                if($distance <= $limit){

                    $url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.$coord[0].','.$coord[1].'&sensor=false';
                    $json = file_get_contents($url);
                    $data = json_decode($json);
                    $address = $data->results[0]->formatted_address;
                    return [$address, $distance];
                }
                else{
                    return "false";
                }
            }
        }
    }

    public function getCoordinate($location){
        $address = $location; // Google HQ
        $prepAddr = str_replace(' ','+',$address);
        $geocode=file_get_contents('https://maps.google.com/maps/api/geocode/json?address='.$prepAddr.'&sensor=false');
        $output= json_decode($geocode);
        $latitude = $output->results[0]->geometry->location->lat;
        $longitude = $output->results[0]->geometry->location->lng;

        return [$latitude, $longitude];  
    }

    function search(Request $request){

        #point interval in meters
        $interval = 200;

        // $geocoordinate = $this->getCoordinate($request->source);
        // $searched_source_lat = $geocoordinate[0];
        // $searched_source_lng = $geocoordinate[1];

        // $geocoordinate = $this->getCoordinate($request->destination);
        // $searched_destination_lat = $geocoordinate[0];
        // $searched_destination_lng = $geocoordinate[1];

        $searched_source_lat = 2.926348;
        $searched_source_lng = 101.636219;

        $searched_destination_lat = 2.927729;
        $searched_destination_lng = 101.642036;

        #start point
        $source_lat = 2.926348;
        $source_lng = 101.636219;
        #end point
        $destination_lat = 2.927729;
        $destination_lng = 101.642036;

        $azimuth = $this->calculateBearing($source_lat,$source_lng,$destination_lat,$destination_lng);
        //print $azimuth;
        $coords = $this->main($interval,$azimuth,$source_lat,$source_lng,$destination_lat,$destination_lng);

        return $coords;

        // $result = $this->calculateDistance($coords, $searched_source_lat, $searched_source_lng);

        // if ($result == "false"){
        //     return "false";
        // }
        // else{
        //     $status = $this->calculateDistance(array_reverse($coords), $searched_destination_lat, $searched_destination_lng);

        //     if ($result == "false"){
        //         return "false";
        //     }
        //     else{
        //         return $result;
        //     }
        // }
    }
}
