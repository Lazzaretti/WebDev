<?php
//TODO:
//IT
$browsers = array("chrome", "firefox", "ie", "edge", "safari", "opera");
$data["browsers"] = json_decode(file_get_contents("https://raw.githubusercontent.com/Fyrd/caniuse/master/fulldata-json/data-2.0.json"), true);
$helper_config["log"] = true;
$helper_config["intent-function"] = true;
include("helper/google-assistant_api-ai_helper.php");

//--------------------------------------------------------------------------------------------------

function getFeature($feat){
	global $data;
	$data["data"][$feat] = json_decode(file_get_contents("https://raw.githubusercontent.com/Fyrd/caniuse/master/features-json/" . $feat . ".json"), true);
}

//--------------------------------------------------------------------------------------------------

function How_much_Feature(){
	global $data, $helper;
	$feat = $helper["parameters"]["features"];
	getFeature($feat);
	
	// unknown feature
	if($data["data"][$feat]["usage_perc_y"] == null){
		if($helper["locale"] == "de-DE"){
			simple_response("Es tut mir leid, ich kenne dieses Feature nicht.");
		} else {
			simple_response("Sorry, I don't know this feature");
		}
		
	// known feature
	} else {
		if($helper["locale"] == "de-DE"){
			simple_response($data["data"][$feat]["usage_perc_y"] . "% der Computer können " . $feat . " nutzen.");
		} else {
			simple_response($data["data"][$feat]["usage_perc_y"] . "% of the computers can use " . $feat);
		}
	}
	
	// call-to-action
	if($helper["locale"] == "de-DE"){
			simple_response("Kann ich dir noch etwas anderes helfen?");
	} else {
			simple_response("Can I help you with something else?");
	}
}

//--------------------------------------------------------------------------------------------------

function How_much_Browser(){
	global $data, $helper;
	
	// all browser versions
	if($helper["parameters"]["number"] == null){
		
		// calculating
		$length = count($data["browsers"]["agents"][$helper["parameters"]["browser"]]["version_list"]);
		$percent = 0;
		for($i=0; $i < $length; $i++){
			$percent += $data["browsers"]["agents"][$helper["parameters"]["browser"]]["version_list"][$i]["global_usage"];
		}
		$percent = round($percent);
		
		// unknown browser
		if ($percent = 0){
			if($helper["locale"] == "de-DE"){
				simple_response("Es tut mir leid, ich kenne diesen Browser nicht.");
			} else {
				simple_response("Sorry, I don't know this browser");
			}
			
		// known browser
		} else {
			if($helper["locale"] == "de-DE"){
				simple_response($percent . "% der Welt nutzen " . $helper["parameters"]["browser"]);
				simple_response("Kann ich dir noch etwas anderes helfen?");
			} else {
				simple_response($percent . "% of the world are using " . $helper["parameters"]["browser"]);
				simple_response("Can I help you with something else?");
			}
		}
		
	// specific browser version
	} else {
		$percent = $data["browsers"]["agents"][$helper["parameters"]["browser"]]["usage_global"][$helper["parameters"]["number"]];
		
		// unknown browser
		if ($percent = null){
			if($helper["locale"] == "de-DE"){
				simple_response("Es tut mir leid, ich kenne diesen Browser nicht.");
			} else {
				simple_response("Sorry, I don't know this browser");
			}
			
		// known browser
		} else {
			if($helper["locale"] == "de-DE"){
				simple_response(round($percent) . "% der Welt nutzen " . $helper["parameters"]["browser"] . " " . $helper["parameters"]["number"]);
			} else {
				simple_response(round($percent) . "% of the world are using " . $helper["parameters"]["browser"] . " " . $helper["parameters"]["number"]);
			}
	}
	
	// call-to-action
	if($helper["locale"] == "de-DE"){
			simple_response("Kann ich dir noch etwas anderes helfen?");
	} else {
			simple_response("Can I help you with something else?");
	}
}

//--------------------------------------------------------------------------------------------------

function Can_I_Use(){
	global $data, $helper;
	
	// actual browser version
	if($helper["parameters"]["number"] == null){
		
		// actual browser version supports feature
		if(browser($helper["parameters"]["browser"], $helper["parameters"]["features"])){
			if($helper["locale"] == "de-DE"){
				simple_response("Ja, du kannst " . $helper["parameters"]["features"] . " in der aktuellen Version von " . $helper["parameters"]["browser"] . " nutzen.");
			} else {
				simple_response("Yes, you can use " . $helper["parameters"]["features"] . " in the actual version of " . $helper["parameters"]["browser"]);
			}
			
		// actual browser version doesn't support the feature
		} else {
			if($helper["locale"] == "de-DE"){
				simple_response("Nein, du kannst " . $helper["parameters"]["features"] . " nicht in der aktuellen Version von " . $helper["parameters"]["browser"] . " nutzen.");
			} else {
				simple_response("No, you can't use " . $helper["parameters"]["features"] . " in the actual version of " . $helper["parameters"]["browser"]);
			}
		}
	
	// specific browser version
	} else {
		
		// browser version supports feature
		if(browser($helper["parameters"]["browser"], $helper["parameters"]["features"], $helper["parameters"]["number"])){
			if($helper["locale"] == "de-DE"){
				simple_response("Ja, du kannst " . $helper["parameters"]["features"] . " in " . $helper["parameters"]["browser"] . " " . $helper["parameters"]["number"] . " nutzen.");
			} else {
				simple_response("Yes, you can use " . $helper["parameters"]["features"] . " in " . $helper["parameters"]["browser"] . " " . $helper["parameters"]["number"]);
			}
			
		// browser version doesn't support the feature
		} else {
			if($helper["locale"] == "de-DE"){
				simple_response("Nein, du kannst " . $helper["parameters"]["features"] . " in " . $helper["parameters"]["browser"] . " " . $helper["parameters"]["number"] . " nicht nutzen.");
			} else {
				simple_response("No, you can't use " . $helper["parameters"]["features"] . " in " . $helper["parameters"]["browser"] . " " . $helper["parameters"]["number"]);
			}
		}
	}
	
	// call-to-action
	if($helper["locale"] == "de-DE"){
		simple_response("Kann ich dir noch etwas anderes helfen?");
	} else {
		simple_response("Can I help you with something else?");
	}
}

//--------------------------------------------------------------------------------------------------

function Which(){
	global $data, $browsers, $helper;
	$browser_amount = count($browsers);
	$j = 0;
	for($i = 0; $i < $browser_amount; $i++){
		if(browser($browsers[$i], $helper["parameters"]["features"])){
			$browserresult[$j] = $browsers[$i];
			$j++;
		}
	}
	$count = count($browserresult);
	if($count > 1){
		$browsertext = "";
		for($i = 0; $i < $count - 2; $i++){
			$browsertext .= $browserresult[$i] . ", ";
		}
		if($helper["locale"] == "de-DE"){
			$browsertext .= $browserresult[$count - 2] . " und ";
		} else {
			$browsertext .= $browserresult[$count - 2] . " and ";
		}
		$browsertext .= $browserresult[$count - 1];
	} else {
		$browsertext = $browserresult[0];
	}
	
	// 
	if($browsertext == ""){
		
	// 
	} else {
		if($helper["locale"] == "de-DE"){
			simple_response("Die neuste Version von " . $browsertext . " können " . $helper["parameters"]["features"] . " nutzen.");
		} else {
			simple_response("The newest version of " . $browsertext . " can use " . $helper["parameters"]["features"]);
		}
	}
	
	
	// call-to-action
	if($helper["locale"] == "de-DE"){
		simple_response("Kann ich dir noch etwas anderes helfen?");
	} else {
		simple_response("Can I help you with something else?");
	}
}

//--------------------------------------------------------------------------------------------------

function What(){
	global $data, $helper;
	getFeature($helper["parameters"]["features"]);
	if($data["data"][$helper["parameters"]["features"]]["description"] == null){
		if($helper["locale"] == "de-DE"){
			simple_response("Es tut mir leid, ich kenne dieses Feature nicht.");
		} else {
			simple_response("Sorry, I don't know this feature");
		}
	} else {	
		if($helper["locale"] == "de-DE"){
			simple_response("Die Beschreibung gibt es leider nur auf englisch.");
			simple_response("Kann ich dir noch etwas anderes helfen?");
		} else {
			simple_response($data["data"][$helper["parameters"]["features"]]["description"]);
			simple_response("Can I help you with something else?");
		}
	}
}

//--------------------------------------------------------------------------------------------------

function browser($browser, $feat, $number){
	global $data, $helper;
	getFeature($feat);
	if($number == null){
		$number = $data["browsers"]["agents"][$browser]["current_version"];
	}
	$raw = $data["data"][$feat]["stats"][$browser][$number];
	$split = explode("", $raw);
	if($split[0] == null){
		$result = $raw;
	} else {
		$result = $split[0];
	}
	if($result == "y"){
		return true;
	} else {
		return false;
	}
}

function test(){
	
}