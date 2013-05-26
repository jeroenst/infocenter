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
date_default_timezone_set('Europe/Brussels');

$bForce = (empty($_REQUEST['wait'])) ? 1 : 0;

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

  // Get values from mysql datebase
  $mysqllink = mysql_connect(localhost, "domotica", "b-2020");
  mysql_select_db("domotica");
  $FirstRun=1;
  $Timeout=60;


  // Wait for a change in the database or a playing XMBC before continuing.
//  while (($Session_Electricity_Used == $Electricity_Used) && ($Session_Gas_Used == $Gas_Used) &&  ($Session_Electricity_Usage == $Electricity_Usage) && ($Timeout > 0) && ($xbmc_title == ""))  
  $FirstRun = 1;
  while (($FirstRun) || (($Timeout != 0) && ($Session_Electricity_Usage == $Electricity_Usage)))
  {
    $mysqlresult = mysql_query('SELECT * FROM energy  WHERE `timestamp` >= timestampadd(minute, -1, now()) ORDER BY id DESC LIMIT 1;');
    if (($mysqlresult) && (mysql_num_rows($mysqlresult)))
    {
    $Electricity_Used=mysql_result($mysqlresult, 0, "kwh_used1") + mysql_result($mysqlresult, 0, "kwh_used2");
    $Gas_Used=mysql_result($mysqlresult, 0, "gas_used");
    $Electricity_Usage=mysql_result($mysqlresult, 0, "watt_usage");
    $FirstRun = 0;
    $Timeout -= 1;

    }
    else
    {
      $Timeout = 0;
    }
    getXBMCData();

    if ($bForce == 1) 
    {
      $Timeout = 0;
    }
    else
    {
      sleep(5);
    }

    $FirstRun = 0;
  }

//echo __LINE__."\n"; 
  if ($Electricity_Used != "")
  {
  // Get all the other values from the domotica database  if Gas_Used or Electricity_Used has changed, but only when XMBC is not playing...
//  if (($bForce) || ($Session_Electricity_Used != $Electricity_Used) || ($Session_Gas_Used != $Gas_Used) || ($xbmc_title != ""))
  //{
    $mysqlresult = mysql_query("SELECT * FROM `energy` WHERE `timestamp` >= timestampadd(hour, -1, now()) LIMIT 1");
    $Electricity_Used_Hour=$Electricity_Used - (mysql_result($mysqlresult, 0, "kwh_used1") + mysql_result($mysqlresult, 0, "kwh_used2"));
    $Gas_Used_Hour=$Gas_Used - mysql_result($mysqlresult, 0, "gas_used");
    $Gas_Usage=$Gas_Used_Hour;


    $mysqlresult = mysql_query("SELECT * FROM `energy` WHERE `timestamp` >= CURDATE() LIMIT 1");
    $Electricity_Used_Today=$Electricity_Used - (mysql_result($mysqlresult, 0, "kwh_used1") + mysql_result($mysqlresult, 0, "kwh_used2"));
    $Gas_Used_Today=$Gas_Used - mysql_result($mysqlresult, 0, "gas_used");

    $mysqlresult = mysql_query("SELECT * FROM `energy` WHERE `timestamp` >= DATE_SUB(CURDATE(),INTERVAL 7 DAY) LIMIT 1");
    $Electricity_Used_Week=$Electricity_Used - (mysql_result($mysqlresult, 0, "kwh_used1") + mysql_result($mysqlresult, 0, "kwh_used2"));
    $Gas_Used_Week=$Gas_Used - mysql_result($mysqlresult, 0, "gas_used");

    $mysqlresult = mysql_query("SELECT * FROM `energy` WHERE `timestamp` >= DATE_SUB(CURDATE(),INTERVAL 30 DAY) LIMIT 1");
    $Electricity_Used_Month=$Electricity_Used - (mysql_result($mysqlresult, 0, "kwh_used1") + mysql_result($mysqlresult, 0, "kwh_used2"));
    $Gas_Used_Month=$Gas_Used - mysql_result($mysqlresult, 0, "gas_used");

    $mysqlresult = mysql_query("SELECT * FROM `energy` WHERE `timestamp` >= DATE_SUB(CURDATE(),INTERVAL 365 DAY) LIMIT 1");
    $Electricity_Used_Year=$Electricity_Used - (mysql_result($mysqlresult, 0, "kwh_used1") + mysql_result($mysqlresult, 0, "kwh_used2"));
    $Gas_Used_Year=$Gas_Used - mysql_result($mysqlresult, 0, "gas_used");
   }
/*  else
  {
    $Electricity_Used_Hour = $_SESSION['Electricity_Used_Hour'];
    $Gas_Used_Hour = $_SESSION['Gas_Used_Hour'];
    $Gas_Usage = $_SESSION['Gas_Usage'];

    $Electricity_Used_Today = $_SESSION['Electricity_Used_Today'];
    $Gas_Used_Today = $_SESSION['Gas_Used_Today'];

    $Electricity_Used_Week = $_SESSION['Electricity_Used_Week'];
    $Gas_Used_Week = $_SESSION['Gas_Used_Week'];

    $Electricity_Used_Month = $_SESSION['Electricity_Used_Month'];
    $Gas_Used_Month = $_SESSION['Gas_Used_Month'];

    $Electricity_Used_Year = $_SESSION['Electricity_Used_Year'];
    $Gas_Used_Year = $_SESSION['Gas_Used_Year'];

  }
  }
  else
  {
    $Electricity_Used = 0;
    $Gas_Used = 0;
    $Electricity_Usage = 0;
      
    $Electricity_Used_Hour = 0;
    $Gas_Used_Hour = 0;
    $Gas_Usage = 0;

    $Electricity_Used_Today = 0;
    $Gas_Used_Today = 0;

    $Electricity_Used_Week = 0;
    $Gas_Used_Week = 0;

    $Electricity_Used_Month = 0;
    $Gas_Used_Month = 0;

    $Electricity_Used_Year = 0;
    $Gas_Used_Year = 0;
  }*/


  $mysqlresult = mysql_query('SELECT * FROM temperature  WHERE `timestamp` >= timestampadd(minute, -5, now()) ORDER BY id DESC LIMIT 1;');
  if (($mysqlresult) && (mysql_num_rows($mysqlresult)))
  {
    $Temp_Livingroom=nulltodash(mysql_result($mysqlresult, 0, "livingroom"));
    $Temp_Hal=nulltodash(mysql_result($mysqlresult, 0, "hal"));
    $Temp_Outside=nulltodash(mysql_result($mysqlresult, 0, "outside"));
    $Temp_FishTank=nulltodash(mysql_result($mysqlresult, 0, "fishtank"));
    $Temp_Bathroom=nulltodash(mysql_result($mysqlresult, 0, "bathroom"));
    $Temp_Bedroom=nulltodash(mysql_result($mysqlresult, 0, "bedroom"));
    $Temp_Woodburner=nulltodash(mysql_result($mysqlresult, 0, "woodburner"));
    $Temp_Outside_Pond=nulltodash(mysql_result($mysqlresult, 0, "outside_pond"));
    $Temp_Central_Heater_Water_In=nulltodash(mysql_result($mysqlresult, 0, "central_heater_water_in"));
    $Temp_Central_Heater_Water_Out=nulltodash(mysql_result($mysqlresult, 0, "central_heater_water_out"));
    $Temp_Freezer=nulltodash(mysql_result($mysqlresult, 0, "freezer"));
    $Temp_Garage=nulltodash(mysql_result($mysqlresult, 0, "garage"));
    $Temp_Fridge=nulltodash(mysql_result($mysqlresult, 0, "fridge"));
    $Temp_Attic=nulltodash(mysql_result($mysqlresult, 0, "attic"));
  }
  else
  {
    $Temp_Livingroom="---";
    $Temp_Hal="---";
    $Temp_Outside="---";
    $Temp_FishTank="---";
    $Temp_Bathroom="---";
    $Temp_Bedroom="---";
    $Temp_Woodburner="---";
    $Temp_Outside_Pond="---";
    $Temp_Central_Heater_Water_In="---";
    $Temp_Central_Heater_Water_Out="---";
    $Temp_Freezer="---";
    $Temp_Garage="---";
    $Temp_Fridge="---";
    $Temp_Attic="---";
  }
  
  
  
  // Free resultset
  mysql_free_result($mysqlresult);

  // Closing connection
  mysql_close($mysqllink);
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
  $ostr = $ostr."<TABLE BORDER=0 WIDTH=470 CELLPADDING=0px CELLSPACING=0px ALIGN=LEFT style=\"table-layout: fixed; white-space: nowrap;\">";
  $ostr = $ostr."<TR VALIGN=bottom  HEIGHT=\"60\"><TD ALIGN=CENTER>Zithoek</TD><TD ALIGN=CENTER>Hal</TD><TD ALIGN=CENTER>Garage</TD></TR>";
  $ostr = $ostr."<TR VALIGN=top HEIGHT=\"30\"><TD ALIGN=CENTER>".htmlcolorvalue($Temp_Livingroom,10,12,15,21,23,25)."</TD><TD ALIGN=CENTER>".$Temp_Hal."</TD><TD ALIGN=CENTER>".$Temp_Garage."</TD></TR>";
  $ostr = $ostr."<TR VALIGN=bottom  HEIGHT=\"60\"><TD ALIGN=CENTER>Bad</TD><TD ALIGN=CENTER>Slaap</TD><TD ALIGN=CENTER>Zolder</TD></TR>";
  $ostr = $ostr."<TR VALIGN=top HEIGHT=\"30\"><TD ALIGN=CENTER>".htmlcolorvalue($Temp_Bathroom,10,12,15,21,23,25)."</TD><TD ALIGN=CENTER>".$Temp_Bedroom."</TD><TD ALIGN=CENTER>".$Temp_Attic."</TD></TR>";
  $ostr = $ostr."<TR VALIGN=bottom><TD HEIGHT=\"60\" ALIGN=CENTER>Vissen</TD><TD ALIGN=CENTER>Vijver</TD><TD ALIGN=CENTER>Buiten</TD></TR>";
  $ostr = $ostr."<TR VALIGN=top HEIGHT=\"30\"><TD ALIGN=CENTER>".htmlcolorvalue($Temp_FishTank,20,22,24,28,30,33)."</TD><TD ALIGN=CENTER>".$Temp_Outside_Pond."</TD><TD ALIGN=CENTER>".$Temp_Outside."</TD></TR>";
  $ostr = $ostr."<TR VALIGN=bottom><TD HEIGHT=\"60\" ALIGN=CENTER>Koeling</TD><TD ALIGN=CENTER>Vriezer</TD><TD ALIGN=CENTER>Cv</TD></TR>";
  $ostr = $ostr."<TR VALIGN=top HEIGHT=70><TD ALIGN=CENTER>".htmlcolorvalue($Temp_Fridge,0,1,2,5,6,7)."</TD><TD ALIGN=CENTER>".htmlcolorvalue($Temp_Freezer,-35,-28,-25,-18,-15,-10)."</TD><TD ALIGN=CENTER>".$Temp_Central_Heater_Water_Out."</TD></TR></TABLE>";


  // Print Energy Usage
  $ostr = $ostr."Elektriciteitsverbruik";
  $ostr = $ostr."<TABLE BORDER=0 WIDTH=470 CELLPADDING=0 CELLSPACING=0 ALIGN=LEFT style=\"table-layout: fixed; white-space: nowrap;\">";
  $ostr = $ostr."<TR VALIGN=top HEIGHT=30><TD>".htmlcolorvalue($Electricity_Usage, -1, -1, -1, 1000, 2000, 5000)."</TD><TD>".htmlcolorvalue($Electricity_Used_Today, -1, -1, -1, 10, 20, 40)."</TD><TD>".htmlcolorvalue($Electricity_Used_Week, 0, 0, 0, 69, 73, 77)."</TD><TD>".htmlcolorvalue($Electricity_Used_Month, 0, 0, 0, 295, 312, 328)."</TD><TD>".htmlcolorvalue($Electricity_Used_Year, 0, 0, 0, 3600, 3800, 4000)."</TD></TR></TABLE>";

  $ostr = $ostr."<BR>Gasverbruik";
  $ostr = $ostr."<TABLE BORDER=0 WIDTH=470 CELLPADDING=0 CELLSPACING=0 ALIGN=LEFT style=\"table-layout: fixed; white-space: nowrap;\">";
  $ostr = $ostr."<TR VALIGN=top HEIGHT=70><TD>".htmlcolorvalue((round(($Gas_Usage)*1000)), -1, -1, -1, 800, 1200, 1600)."</TD><TD>".htmlcolorvalue(round($Gas_Used_Today,1), -1, -1, -1, 10, 20, 40)."</TD><TD>".htmlcolorvalue(round($Gas_Used_Week), 0, 0, 0, 23, 27, 31)."</TD><TD>".htmlcolorvalue(round($Gas_Used_Month), 0, 0, 0, 100, 115, 130)."</TD><TD>".htmlcolorvalue(round($Gas_Used_Year), 0, 0, 0, 1200, 1400, 1600)."</TD></TR></TABLE>";

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
  if ($value <= $levellowcritical) return "<FONT COLOR=#FF6666>".$value."</FONT>";
  if ($value <= $levellowwarning) return "<FONT COLOR=ORANGE>".$value."</FONT>";
  if ($value <= $levellownotice) return "<FONT COLOR=YELLOW>".$value."</FONT>";
  if ($value >= $levelhighcritical) return "<FONT COLOR=FF6666>".$value."</FONT>";
  if ($value >= $levelhighwarning) return "<FONT COLOR=ORANGE>".$value."</FONT>";
  if ($value >= $levelhighnotice) return "<FONT COLOR=#YELLOW>".$value."</FONT>";
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
