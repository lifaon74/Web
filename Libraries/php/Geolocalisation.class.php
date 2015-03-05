<?php

class Coords {
	public $latitude;
	public $longitude;
	
	function __construct($latitude, $longitude) {
		$this->latitude = $latitude;
		$this->longitude = $longitude;
	}
}


class Geoloc {
	function __construct() {
		$this->key = 'AIzaSyADhFLDkjlbP5MJfaalX-8e079gVtTtRbs';
	}
	
	public function getAddress($coords) {
		$url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng=' . $coords->latitude . ',' . $coords->longitude . '&sensor=true';
		
		$content = file_get_contents($url);
		$result = json_decode($content);
		
		if($result->status == 'OK') {
			$response = (object) array();
			
			$addressComponents = $result->results[0]->address_components;
			
			for($i = 0; $i < count($addressComponents); $i++) {
				switch($addressComponents[$i]->types[0]) {
					case 'street_number':
						$response->streetNumber = $addressComponents[$i]->long_name;
					break;
					case 'route':
						$response->street = $addressComponents[$i]->long_name;
					break;
					case 'locality':
						$response->city = $addressComponents[$i]->long_name;
					break;
					case 'administrative_area_level_2':
						$response->department = $addressComponents[$i]->long_name;
					break;
					case 'administrative_area_level_1':
						$response->region = $addressComponents[$i]->long_name;
					break;
					case 'country':
						$response->country = $addressComponents[$i]->long_name;
					break;
					case 'postal_code':
						$response->postalCode = $addressComponents[$i]->long_name;
					break;
				}
			}
			
			return $response;
		} else {
			return null;
		}
	}

	public function getCoordsFromAddress($address)	{
		$url = 'http://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($address) . '&sensor=true';
		
		$content = file_get_contents($url);
		$result = json_decode($content);
		
		if($result->status == 'OK') {
			$response = (object) array();
			$response->latitude = $result->results[0]->geometry->location->lat;
			$response->longitude = $result->results[0]->geometry->location->lng;
			
			return $response;
		} else {
			return null;
		}
	}
	
	function getDistance($coords1, $coords2) {
		$lat1 = 2 * M_PI * $coords1->latitude / 360;
		$lng1 = 2 * M_PI * $coords1->longitude / 360;
		
		$lat2 = 2 * M_PI * $coords2->latitude / 360;
		$lng2 = 2 * M_PI * $coords2->longitude / 360;
		
		$dlng = $lng2 - $lng1;
		
		$d = 6378000 * acos( cos($lat1) * cos($lat2) * cos($dlng) + sin($lat1) * sin($lat2));
		return $d;
	}

	
	/**
	*	$origin : adress (string) | coords
	*	$waypoints : array of waypoints
	*	$mode: 'driving' | 'walking' | 'bicycling'
	*/
	public function getPath($origin, $destination, $waypoints = [], $mode = null, $decodeCoords = true) {
		$url = 'https://maps.googleapis.com/maps/api/directions/json?';
		
		$convertToString = function($coords) {
			if(is_object($coords)) {
				return $coords->latitude . ',' . $coords->longitude;
			} else if(is_string($coords)) {
				return $coords;
			} else {
				return null;
			}
		};
		
			// origin
		if($addr = $convertToString($origin)) {
			$url .= 'origin=' . $addr;
		} else {
			trigger_error("Adress must be Coords or String");
			return null;
		}

			// destination
		if($addr = $convertToString($destination)) {
			$url .= '&destination=' . $addr;
		} else {
			trigger_error("Adress must be Coords or String");
			return null;
		}
		
			// waypoints
		if(is_array($waypoints) && count($waypoints) > 0) {
			$url .= '&waypoints=';
			
			for($i = 0; $i < count($waypoints); $i++) {
				if($i > 0) { $url .= '|'; }
				
				if($addr = $convertToString($waypoints[$i])) {
					$url .= $addr;
				} else {
					trigger_error("Adress must be Coords or String");
					return null;
				}
			}
		}
		
			// mode
		if($mode === null) {
		} else if (is_string($mode) && in_array($mode, array('driving', 'walking', 'bicycling'))) {
			$url .= '&mode=' . $mode;
		} else {
			trigger_error("Mode of transport is incorrect. Available : 'driving' | 'walking' | 'bicycling'");
			return null;
		}
		
		$url .= '&sensor=true';
		
		if($this->key) {
			//$url .= '&key=' . $this->key;
		}
		
		
		$content = file_get_contents($url);
		$result = json_decode($content);
		
		//print_r($result);
		if($result->status == 'OK') {
			$response = (object) array();
			
			$response->steps = [];
			
			$response->distance = $result->routes[0]->legs[0]->distance->value;
			$response->duration = $result->routes[0]->legs[0]->duration->value;
			
			$steps =  $result->routes[0]->legs[0]->steps;
			for($i = 0; $i < count($steps); $i++) {
			
				$step = (object) array();
				
				$step->distance = $steps[$i]->distance->value;
				$step->duration = $steps[$i]->duration->value;
				$step->origin = new Coords($steps[$i]->start_location->lat,  $steps[$i]->start_location->lng);
				$step->destination = new Coords($steps[$i]->end_location->lat,  $steps[$i]->end_location->lng);
				$step->mode = strtolower($steps[$i]->travel_mode);
				
				if($decodeCoords) {
					$step->coords = $this->_decodePolylineToArray($steps[$i]->polyline->points);
				} else {
					$step->coords = $steps[$i]->polyline->points;
				}
				
				$response->steps[] = $step;
			}
			
			return $response;
		} else {
			return null;
		}
	}
	
	private	function _decodePolylineToArray($encoded) {
		$length = strlen($encoded);
		$coordsArray = [];
		
		$index = 0;
		$lat = 0;
		$lng = 0;
	 
		$decode = function($encoded, $index, $coord) {
			$shift = 0;
			$result = 0;
			do {
				$b = ord($encoded[$index++]) - 63;
				$result |= ($b & 0x1f) << $shift;
				$shift += 5;
			} while($b >= 0x20);
			
			$dcoord = (($result & 1) ? ~($result >> 1) : ($result >> 1));
			$coord += $dcoord;
			
			return [$index, $coord];
		};
		
		while($index < $length) {
			list($index, $lat) = $decode($encoded, $index, $lat);
			list($index, $lng) = $decode($encoded, $index, $lng);
			
			$coordsArray[] = new Coords($lat * 1e-5, $lng * 1e-5);
		}

		return $coordsArray;
	}
	
}

$geoloc = new Geoloc();
//print_r($geoloc->getAddress(new Coords(46.1488451, 6.1555419)));
//print_r($geoloc->getCoordsFromAddress('189 rte de Collonges 74160 Bossey France'));
//print_r($geoloc->getPath('189 rte de Collonges 74160 Bossey France', 'Paris'));
//print_r($geoloc->getPath(new Coords(46.1488451, 6.1555419), 'Paris', [], null, false));
?>