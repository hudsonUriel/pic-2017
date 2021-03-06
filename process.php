<?php
/*
# The processing stage have __ phases.
#
# First, the data is getted by cookie
# and then generates in to "data objects"
#
# Second, occurs the dynamic creation of
# SVG elements (SVG and circle)
*/

/********** | PHASE 00 | **********
********** opens classes  *********/

// opens SVG
require_once "./class/SVG.php";
require_once "./class/SVGData.php";

// starts session
session_start();

/********** | PHASE 01 | **********
*********** SVG creation **********/

// define properties
$tag_name = 'svg';
$id = 'svg_graph';
$class_name = 'svgClass';
$namespace = 'http://www.w3.org/2000/svg';
$parent = 'document.getElementById("graph")';

// creates new SVG
$svgGraph = new SVG;

$svgGraph->set_tag_name($tag_name);
$svgGraph->set_id($id);
$svgGraph->set_class_name($class_name);
$svgGraph->set_namespace($namespace);
$svgGraph->set_parent_element($parent);

/********** | PHASE 02 | **********
********* data processing *********/
// to dom use
$tag_name = 'circle';
$class_name = 'dataClassSVG';

// recieves and explodes data
$data = explode(",", $_COOKIE["graph_data"]);

//echo var_dump($data);

// recieves channels
$channels = $_COOKIE["channels"];

//echo "<p>CHANNELS = " . $channels. "</p>";

 //recieves channel size end explodes it
$channel_size = explode(",", $_COOKIE["channel_size"]);

//echo "<p>CHANNEL_SIZE = " . var_dump($channel_size) . "</p>";

// gets each channal
for($i=0; $i<$channels; $i++){
  // control variables
  $v_min = $i>0 ? $v_max : 0;
  $v_max = $v_min + $channel_size[$i];
  
  // gets each piece of data
  for($j=$v_min; $j<$v_max; $j++){
    // converts to float
    $val = floatval($data[$j]);
    
    // creates new SVGData
    $data[$j] = new SVGData;
    
    // configures data of SVGData object
    $data[$j]->set_channel($i + 1);
    $data[$j]->set_key_value($val);
    $data[$j]->set_key_y($j - $v_min);
    
    // configures identification
    $data[$j]->set_id(
        "data_" .
        $data[$j]->get_key_y() . '_' .
        $data[$j]->get_channel()
      );
    $data[$j]->set_class_name($class_name);
    
    // configures DOM
    $data[$j]->set_parent_element($svgGraph->get_id());
    $data[$j]->set_tag_name($tag_name);
    $data[$j]->set_namespace($namespace);
    
    // sends to session
    $_SESSION['dataGraph'][$j] = $data[$j]->to_dom();
    
    //echo "<p>" . $data[$j]->to_string() . "</p>";
  } 
}

/********** | PHASE 03 | **********
*******  storage and feedback *****/

// uses the session to svgGraph
$_SESSION['svgGraph'] = $svgGraph->to_dom();

// uses the session to channels
$_SESSION['channels'] = $channels;
$_SESSION['channelsSize'] = $channel_size;
$_SESSION['sample'] = $j;

// calls graph.php
header("Location: ./graph.php");
?>
