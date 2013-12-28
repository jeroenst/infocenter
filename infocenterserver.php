<?php
/**
 * server.php
 *
 * @package default
 */

// Output is JavaScript
header( 'Content-Type: text/javascript', true );

//session_start();

// Set the Timezone, to make sure 'date()' does not complain.
//date_default_timezone_set('Europe/Brussels');

$bWait = (empty($_REQUEST['wait'])) ? 0 : 1;

$xbmc_title = "";
$xbmc_artist = "";
$xbmc_current_time = "0:00";
$xbmc_total_time = "0:00";

//getXBMCData();

if ($xbmc_title == "")
{
/*  $Session_Electricity_Used = -1;
  $Session_Gas_Used = -1;
  $Session_Electricity_Usage = -1;
    
  if (isset($_SESSION['Electricity_Used']))
  {
    $Session_Electricity_Used = $_SESSION['Electricity_Used'];
    $Session_Gas_Used = $_SESSION['Gas_Used'];
    $Session_Electricity_Usage = $_SESSION['Electricity_Usage'];
  }
  else
  {
    $bForce=1;
  }*/
 if (bWait)
 {
   $time = 0;
   $smartmeterfiletime = 0;
   $temperaturefiletime = 0;
   do
   {
     sleep (1);
     $time = time() - 1;
     $smartmeterfiletime = filectime("/tmp/smartmeter.xml");
     $temperaturefiletime = filectime("/tmp/temperature.xml");
   } while (($smartmeterfiletime < $time) && ($temperaturefiletime < $time));
   
 }

  // Get values from xml file
  $smartmeterxml = 0;
  if (file_exists('/tmp/smartmeter.xml')) 
  {
    $smartmeterxml = simplexml_load_file('/tmp/smartmeter.xml');
  } 

  $stemperaturexml = 0;
  if (file_exists('/tmp/temperature.xml')) 
  {
    $temperaturexml = simplexml_load_file('/tmp/temperature.xml');
  } 

  
} 

// If XMBC Running act as Media Center Display
if (!empty($xbmc_title) && ($xbmc_title !== ""))
{
  $ostr = "Media Center<BR><BR>";
  if (!empty($xbmc_title))  $ostr = $ostr.htmlspecialchars($xbmc_title, ENT_QUOTES)."<BR><BR>";
  if (!empty($xbmc_artist)) $ostr = $ostr.htmlspecialchars($xbmc_artist, ENT_QUOTES)."<BR><BR>";
  $ostr = $ostr.$xbmc_current_time." / ".$xbmc_total_time."<BR><BR>";
  if ($xbmc_thumbnail != "") $ostr = $ostr."<IMG WIDTH=300 HEIGHT=300 SRC=\"http://xbmc.lan/image/".$xbmc_thumbnail."\"></IMG>";
}
// Else act as Domotica Info Center Display
else
{
  // Print Temperatures
  $ostr = "";
  $ostr = $ostr."</b><TABLE BORDER=0 WIDTH=470 CELLPADDING=0px ALIGN=CENTER CELLSPACING=0px style=\"table-layout: fixed; white-space: nowrap;\">";
  $ostr = $ostr."<TR VALIGN=bottom  HEIGHT=\"30\"><TD ALIGN=CENTER>Zithoek</TD><TD ALIGN=CENTER>Hal</TD><TD ALIGN=CENTER>Garage</TD></TR>";
  $ostr = $ostr."<TR VALIGN=top HEIGHT=\"30\"><TD ALIGN=CENTER>".htmlcolorvalue($temperaturexml->livingroom,10,12,15,21,23,25)."</TD><TD ALIGN=CENTER>".htmlcolorvalue($temperaturexml->hal,10,11,12,19,20,21)."</TD><TD ALIGN=CENTER>".htmlcolorvalue($temperaturexml->garage,0,5,10,20,22,24)."</TD></TR>";
  $ostr = $ostr."<TR VALIGN=bottom  HEIGHT=\"30\"><TD ALIGN=CENTER>Bad</TD><TD ALIGN=CENTER>Slaap</TD><TD ALIGN=CENTER>Zolder</TD></TR>";
  $ostr = $ostr."<TR VALIGN=top HEIGHT=\"30\"><TD ALIGN=CENTER>".htmlcolorvalue($temperaturexml->bathroom,10,12,15,21,23,25)."</TD><TD ALIGN=CENTER>".htmlcolorvalue($temperaturexml->bedroom,8,10,14,19,20,21)."</TD><TD ALIGN=CENTER>".$temperaturexml->attic."</TD></TR>";
  $ostr = $ostr."<TR VALIGN=bottom><TD HEIGHT=\"30\" ALIGN=CENTER>Vissen</TD><TD ALIGN=CENTER>Vijver</TD><TD ALIGN=CENTER>Buiten</TD></TR>";
  $ostr = $ostr."<TR VALIGN=top HEIGHT=\"30\"><TD ALIGN=CENTER>".htmlcolorvalue($temperaturexml->fishtank,22,23,24,25,26,27)."</TD><TD ALIGN=CENTER>".$temperaturexml->outside_pond."</TD><TD ALIGN=CENTER>".$temperaturexml->outside."</TD></TR>";
  $ostr = $ostr."<TR VALIGN=bottom><TD HEIGHT=\"30\" ALIGN=CENTER>Koeling</TD><TD ALIGN=CENTER>Vriezer</TD><TD ALIGN=CENTER>Cv</TD></TR>";
  $ostr = $ostr."<TR VALIGN=top HEIGHT=30><TD ALIGN=CENTER>".htmlcolorvalue($temperaturexml->fridge,0,1,2,6,7,8)."</TD><TD ALIGN=CENTER>".htmlcolorvalue($temperaturexml->freezer,-25,-23,-21,-18,-15,-10)."</TD><TD ALIGN=CENTER>".$temperaturexml->central_heater_water_out."</TD></TR></TABLE>";


  // Print Energy Usage
  $ostr = $ostr."Elektriciteitsverbruik";
  $ostr = $ostr."<TABLE BORDER=0 WIDTH=470 ALIGN=CENTER CELLPADDING=0 CELLSPACING=0 style=\"table-layout: fixed; white-space: nowrap;\">";
  $ostr = $ostr."<TR VALIGN=top HEIGHT=30><TD>".htmlcolorvalue($smartmeterxml->Electricity_Usage, -1, -1, -1, 1000, 2000, 5000)."</TD><TD>".htmlcolorvalue(round($smartmeterxml->Electricity_Used_Today), -1, -1, -1, 10, 20, 40)."</TD><TD>".htmlcolorvalue(round($smartmeterxml->Electricity_Used_Week), 0, 0, 0, 69, 73, 77)."</TD><TD>".htmlcolorvalue(round($smartmeterxml->Electricity_Used_Month), 0, 0, 0, 295, 312, 328)."</TD><TD>".htmlcolorvalue(round($smartmeterxml->Electricity_Used_Year), 0, 0, 0, 3600, 3800, 4000)."</TD></TR></TABLE>";

  $ostr = $ostr."Gasverbruik";
  $ostr = $ostr."<TABLE BORDER=0 WIDTH=470 ALIGN=CENTER CELLPADDING=0 CELLSPACING=0 style=\"table-layout: fixed; white-space: nowrap;\">";
  $ostr = $ostr."<TR VALIGN=top HEIGHT=30><TD>".htmlcolorvalue($smartmeterxml->Gas_Usage, -1, -1, -1, 800, 1200, 1600)."</TD><TD>".htmlcolorvalue(round($smartmeterxml->Gas_Used_Today,1), -1, -1, -1, 10, 20, 40)."</TD><TD>".htmlcolorvalue(round($smartmeterxml->Gas_Used_Week), 0, 0, 0, 23, 27, 31)."</TD><TD>".htmlcolorvalue(round($smartmeterxml->Gas_Used_Month), 0, 0, 0, 100, 115, 130)."</TD><TD>".htmlcolorvalue(round($smartmeterxml->Gas_Used_Year), 0, 0, 0, 1200, 1400, 1600)."</TD></TR></TABLE><b>";

//  $noisefile=parse_ini_file('/tmp/noisedata');
  

//  $ostr = $ostr."Geluid";
//  $ostr = $ostr."<TABLE BORDER=0 WIDTH=470 ALIGN=CENTER CELLPADDING=0 CELLSPACING=0 style=\"table-layout: fixed; white-space: nowrap;\">";
//  $ostr = $ostr."<TR VALIGN=top HEIGHT=30><TD>E ".htmlcolorvalue(round($noisefile[eindhovenairport]), -1, -1, -1, 60, 70, 80)."</TD><TD>O ".htmlcolorvalue(round($noisefile[oerle]), -1, -1, -1, 60, 70, 80)."</TD><TD>K ".htmlcolorvalue(round($noisefile[knegsel]), -1, -1, -1, 60, 70, 80)."</TD><TD>D ".htmlcolorvalue(round($noisefile[duizel]), -1, -1, -1, 60, 70, 80)."</TD></TR></TABLE>";

//  $Planning_Difference=match("Saldo_Total_Cashflow_Year_Difference","/tmp/externaldata.dat");
//  $Planning_Difference_Percentage=match("Saldo_Total_Cashflow_Year_Difference_Percentage","/tmp/externaldata.dat");
//  $ostr = $ostr."Planning";
//  $ostr = $ostr."<TABLE BORDER=0 WIDTH=480 CELLPADDING=0 CELLSPACING=0 ALIGN=CENTER style=\"table-layout: fixed; white-space: nowrap;\">";
//  $ostr = $ostr."<TR><TD>".htmlcolorvalue(round($Planning_Difference), 500, 200, 0)."</TD><TD>".htmlcolorvalue(round($Planning_Difference_Percentage), 10, 5, 0)."</TD><TD>".htmlcolorvalue(round(0), 23, 27, 31)."</TD><TD>".htmlcolorvalue(round(0), 100, 115, 130)."</TD><TD>".htmlcolorvalue(round(0), 1200, 1400, 1600)."</TD></TR></TABLE>";
}

// Write variables to session variable (This blocks other PHP processes so keep it short...
/*$_SESSION['Electricity_Used_Hour']=$Electricity_Used_Hour;
$_SESSION['Gas_Used_Hour']=$Gas_Used_Hour;
$_SESSION['Gas_Usage']=$Gas_Usage;

$_SESSION['Electricity_Used_Today']=$Electricity_Used_Today;
$_SESSION['Gas_Used_Today']=$Gas_Used_Today;

$_SESSION['Electricity_Used_Week']=$Electricity_Used_Week;
$_SESSION['Gas_Used_Week']=$Gas_Used_Week;

$_SESSION['Electricity_Used_Month']=$Electricity_Used_Month;
$_SESSION['Gas_Used_Month']=$Gas_Used_Month;

$_SESSION['Electricity_Used_Year']=$Electricity_Used_Year;
$_SESSION['Gas_Used_Year']=$Gas_Used_Year;

$_SESSION['Electricity_Usage']=$Electricity_Usage;
$_SESSION['Electricity_Used']=$Electricity_Used;
$_SESSION['Gas_Used']=$Gas_Used;


session_write_close();

*/
// START OF OUTPUT JAVASCRIPT
?>
$('#loadinfo').html( <?php echo javascriptQuote($ostr); ?> );
<?php
// END OF OUTPUT JAVASCRIPT

exit;

function htmlcolorvalue($value, $levellowcritical, $levellowwarning, $levellownotice, $levelhighnotice, $levelhighwarning, $levelhighcritical)
{
  if (!is_numeric($value)) return $value;
  if ($value < $levellowcritical) return "<FONT COLOR=#FF6666>".$value."</FONT>";
  if ($value < $levellowwarning) return "<FONT COLOR=ORANGE>".$value."</FONT>";
  if ($value < $levellownotice) return "<FONT COLOR=YELLOW>".$value."</FONT>";
  if ($value > $levelhighcritical) return "<FONT COLOR=FF6666>".$value."</FONT>";
  if ($value > $levelhighwarning) return "<FONT COLOR=ORANGE>".$value."</FONT>";
  if ($value > $levelhighnotice) return "<FONT COLOR=YELLOW>".$value."</FONT>";
  return $value;
}

/**
 *
 *
 * @param unknown $mInput - The string you want to pass to Javascript
 * @return string - The quoted string, quotes have been added around it.
 */
function javascriptQuote($mInput)
{
  $sResult = 'null';
  if ( is_array($mInput) )
  {
    $aVals = array();
    foreach ( $mInput as $mVal )
    {
      array_push( $aVals, javascriptQuote($mVal) );
    }
    $sResult = '[' . implode(',', $aVals) . ']';
  }
  else
  {
    $sResult = '';
    $iLen = mb_strlen($mInput);
    for ($iPos = 0; $iPos < $iLen; $iPos++)
    {
      $sChar = mb_substr($mInput, $iPos, 1);
      if ( preg_match('/^[\w -\.:,]$/', $sChar) )
      {
        $sResult .= $sChar;
      }
      else
      {
        $sResult .= sprintf("\x%02X", ord($sChar));
      }
    }
    $sResult = "'".$sResult."'";
  }
  return $sResult;
}


/**
 *
 *
 * @param unknown $needle
 * @param unknown $file
 * @return unknown
 */
function match($needle, $file)
{
  $ret = false;
  $lines = file($file);

  foreach ( $lines as $line )
  {
    list($key, $val) = explode(':', $line);
    $ret = $key==$needle ? $val : false;
    if ( $ret ) break;
  }
  return $ret;
}

function nulltodash($input)
{
  if (empty($input)) return "---";
  return $input;
}


/**
 * Reads data from XBMC and puts info to global variables
 */
function getXBMCData()
{
  $url = "http://xbmc/jsonrpc";

  $content = '{"jsonrpc":"2.0","method":"Player.GetActivePlayers","id":"0"}';

  $curl = curl_init($url);

  curl_setopt($curl, CURLOPT_HEADER, false);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
  curl_setopt($curl, CURLOPT_TIMEOUT, 2); //timeout in seconds
  $json_response = curl_exec($curl);
  $bnba = $json_response;

  $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
  $json_decoded=json_decode($json_response, true);
  $xbmc_active_player = $json_decoded["result"]["0"]["playerid"];

  if ( $status == 200 )
  {
    $json_decoded=json_decode($json_response, true);
    if ( empty($json_decoded["error"]) )
    {
      $xbmc_active_player = $json_decoded["result"]["0"]["playerid"];

      if (!empty($xbmc_active_player))
      {
        $content = '{"jsonrpc":"2.0","method":"Player.GetItem","id":"0","params":{"playerid":'.$xbmc_active_player.',"properties":["artist","title","thumbnail"]}}';

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

        $json_response = curl_exec($curl);
        $bnb = $json_response;

        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ( $status == 200 )
        {
          $json_decoded=json_decode($json_response, true);
          if ( empty($json_decoded["error"]) )
          {
            $xbmc_title = $json_decoded["result"]["item"]["title"];
            if (is_array($xbmc_title)) $xbmc_title="";
            $xbmc_artist = $json_decoded["result"]["item"]["artist"][0];
            if (is_array($xbmc_artist)) $xbmc_artist="";
            $xbmc_thumbnail = str_replace("%", "%25" , $json_decoded["result"]["item"]["thumbnail"]);

            if ($xbmc_title == "") $xbmc_title=$json_decoded["result"]["item"]["label"];;
            if (is_array($xbmc_artist)) $xbmc_artist="";
          }
        }

        if (!empty($xbmc_title) && $xbmc_title !== "")
        {
          curl_close($curl);

          $content = '{"jsonrpc":"2.0","method":"Player.GetProperties","id":"1","params":{"playerid":'.$xbmc_active_player.',"properties":["time","totaltime"]}}';

          $curl = curl_init($url);
          curl_setopt($curl, CURLOPT_HEADER, false);
          curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
          curl_setopt($curl, CURLOPT_POST, true);
          curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

          $json_response = curl_exec($curl);

          $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
          if ( $status == 200 )
          {
            $json_decoded=json_decode($json_response, true);
            if ( empty($json_decoded[error]) )
            {
              if ($json_decoded["result"]["totaltime"]["hours"] == 0)
              {
                $xbmc_current_time = sprintf("%02d:%02d", $json_decoded["result"]["time"]["minutes"], $json_decoded["result"]["time"]["seconds"]);
                $xbmc_total_time =   sprintf("%02d:%02d", $json_decoded["result"]["totaltime"]["minutes"], $json_decoded["result"]["totaltime"]["seconds"]);
              }
              else
              {
                $xbmc_current_time = sprintf("%02d:%02d:%02d", $json_decoded["result"]["time"]["hours"], $json_decoded["result"]["time"]["minutes"], $json_decoded["result"]["time"]["seconds"]);
                $xbmc_total_time =   sprintf("%02d:%02d:%02d", $json_decoded["result"]["totaltime"]["hours"], $json_decoded["result"]["totaltime"]["minutes"], $json_decoded["result"]["totaltime"]["seconds"]);
              }
            }
          }

          curl_close($curl);
        }
      }
    }
  }
}
