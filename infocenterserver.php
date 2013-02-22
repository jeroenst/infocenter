<?php
/**
 * server.php
 *
 * @package default
 */

// Output is JavaScript
header( 'Content-Type: text/javascript', true );


// Set the Timezone, to make sure 'date()' does not complain.
date_default_timezone_set('Europe/Brussels');

$bForce      = (empty($_REQUEST['force'])) ? 0 : 1;

$xbmc_title = "";
$xbmc_artist = "";
$xbmc_current_time = "0:00";
$xbmc_total_time = "0:00";

getXBMCData();

if ($xbmc_title == "")
{
  // Write variables to session variable (This blocks other PHP processes so keep it short...
  session_start();

  $Session_Electricity_Used = -1;
  $Session_Gas_Used = -1;
  $Session_Electricity_Usage = -1;
    
  if (isset($_SESSION['Electricity_Used']))
  {
    $Session_Electricity_Used = $_SESSION['Electricity_Used'];
    $Session_Gas_Used = $_SESSION['Gas_Used'];
    $Session_Electricity_Usage = $_SESSION['Electricity_Usage'];
  }
  
  session_write_close();

  // Get values from mysql datebase
  $mysqllink = mysql_connect(localhost, "domotica", "b-2020");
  mysql_select_db("domotica") ;
  $FirstRun=1;
  $Timeout=5;

  // Wait for a change in the database or a playing XMBC before continuing.
  while (($FirstRun==1) || (($Session_Electricity_Used == $Electricity_Used) && ($Session_Gas_Used == $Gas_Used) &&  ($Session_Electricity_Usage == $Electricity_Usage) && ($Timeout > 0) && ($xbmc_title == "") && (!$bForce)))
  {
    if ($FirstRun == 0)
    {
      sleep(5);
    }

    $mysqlresult = mysql_query('SELECT * FROM energy ORDER BY id DESC LIMIT 1;') or die(mysql_error());
    $Electricity_Used=mysql_result($mysqlresult, 0, "kwh_used1") + mysql_result($mysqlresult, 0, "kwh_used2");
    $Gas_Used=mysql_result($mysqlresult, 0, "gas_used");
    $Electricity_Usage=mysql_result($mysqlresult, 0, "watt_usage");
    $FirstRun = 0;
    $Timeout -= 1;
    getXBMCData();
  }

  // Get all the other values from the domotica database  if Gas_Used or Electricity_Used has changed, but only when XMBC is not playing...
  if (($bForce) || ($Session_Electricity_Used == $Electricity_Used) || ($Session_Gas_Used == $Gas_Used) ||  ($Session_Electricity_Usage == $Electricity_Usage) || ($xbmc_title == ""))
  {
    $mysqlresult = mysql_query("SELECT * FROM `energy` WHERE `timestamp` >= timestampadd(hour, -1, now()) LIMIT 1");
    $Electricity_Used_Hour=$Electricity_Used - (mysql_result($mysqlresult, 0, "kwh_used1") + mysql_result($mysqlresult, 0, "kwh_used2"));
    $Gas_Used_Hour=$Gas_Used - mysql_result($mysqlresult, 0, "gas_used");
    $Gas_Usage=$Gas_Used_Hour;

    $mysqlresult = mysql_query("SELECT * FROM `energy` WHERE `timestamp` >= CURDATE() LIMIT 1");
    $Electricity_Used_Today=$Electricity_Used - (mysql_result($mysqlresult, 0, "kwh_used1") + mysql_result($mysqlresult, 0, "kwh_used2"));
    $Gas_Used_Today=$Gas_Used - mysql_result($mysqlresult, 0, "gas_used");

    $mysqlresult = mysql_query("SELECT * FROM `energy` WHERE `timestamp` >= CURDATE()-7 LIMIT 1");
    $Electricity_Used_Week=$Electricity_Used - (mysql_result($mysqlresult, 0, "kwh_used1") + mysql_result($mysqlresult, 0, "kwh_used2"));
    $Gas_Used_Week=$Gas_Used - mysql_result($mysqlresult, 0, "gas_used");

    $mysqlresult = mysql_query("SELECT * FROM `energy` WHERE `timestamp` >= CURDATE()-30 LIMIT 1");
    $Electricity_Used_Month=$Electricity_Used - (mysql_result($mysqlresult, 0, "kwh_used1") + mysql_result($mysqlresult, 0, "kwh_used2"));
    $Gas_Used_Month=$Gas_Used - mysql_result($mysqlresult, 0, "gas_used");

    $mysqlresult = mysql_query("SELECT * FROM `energy` WHERE `timestamp` >= CURDATE()-365 LIMIT 1");
    $Electricity_Used_Year=$Electricity_Used - (mysql_result($mysqlresult, 0, "kwh_used1") + mysql_result($mysqlresult, 0, "kwh_used2"));
    $Gas_Used_Year=$Gas_Used - mysql_result($mysqlresult, 0, "gas_used");

    // Write variables to session variable (This blocks other PHP processes so keep it short...

    $_SESSION['Electricity_Used_Hour']=$Electricity_Used_Hour;
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
  }
  else
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

    session_write_close();
  }



  $mysqlresult = mysql_query('SELECT * FROM temperature ORDER BY id DESC LIMIT 1;');
  $Temp_Livingroom=mysql_result($mysqlresult, 0, "temp_livingroom");
  $Temp_Hal=mysql_result($mysqlresult, 0, "temp_hal");
  $Temp_Outside=mysql_result($mysqlresult, 0, "temp_outside");
  $Temp_FishTank=mysql_result($mysqlresult, 0, "temp_fishtank");
  $Temp_Bathroom=mysql_result($mysqlresult, 0, "temp_bathroom");
  $Temp_Bedroom=mysql_result($mysqlresult, 0, "temp_bedroom");

  // Free resultset
  mysql_free_result($mysqlresult);

  // Closing connection
  mysql_close($mysqllink);
}


// If XMBC Running act as Media Center Display
if (!empty($xbmc_title) && ($xbmc_title !== ""))
{
  $ostr = $ostr."Media Center<BR><BR>";
  if (!empty($xbmc_title))  $ostr = $ostr.htmlspecialchars($xbmc_title, ENT_QUOTES)."<BR><BR>";
  if (!empty($xbmc_artist)) $ostr = $ostr.htmlspecialchars($xbmc_artist, ENT_QUOTES)."<BR><BR>";
  $ostr = $ostr.$xbmc_current_time." / ".$xbmc_total_time."<BR><BR>";
  if ($xbmc_thumbnail != "") $ostr = $ostr."<IMG WIDTH=300 HEIGHT=300 SRC=\"http://xbmc.lan/image/".$xbmc_thumbnail."\"></IMG>";
}
// Else act as Domotica Info Center Display
else
{
  // Print Temperatures
  $ostr = $ostr."<TABLE BORDER=0 WIDTH=480 style=\"table-layout: fixed;\">";
  $ostr = $ostr."<TR VALIGN=bottom><TD HEIGHT=\"70\" ALIGN=CENTER>Living</TD><TD ALIGN=CENTER>Hal</TD><TD ALIGN=CENTER>Vissen</TD></TR>";
  $ostr = $ostr."<TD ALIGN=CENTER>".$Temp_Livingroom."</TD><TD ALIGN=CENTER>".$Temp_Hal."</TD><TD ALIGN=CENTER>".$Temp_Fishtank."</TD></TR>";
  $ostr = $ostr."<TR VALIGN=bottom><TD HEIGHT=\"70\" ALIGN=CENTER>Bad</TD><TD ALIGN=CENTER>Slaap</TD><TD ALIGN=CENTER>Buiten</TD></TR>";
  $ostr = $ostr."<TD ALIGN=CENTER>".$Temp_Bathroom."</TD><TD ALIGN=CENTER>".$Temp_Bedroom."</TD><TD ALIGN=CENTER>".$Temp_Outside."</TD></TR></TABLE>";


  // Print Energy Usage
  $ostr = $ostr."<BR><CENTER>Elektriciteitsverbruik</CENTER>";
  $ostr = $ostr."<TABLE BORDER=0 WIDTH=480 style=\"table-layout: fixed;\">";
  $ostr = $ostr."<TR><TD>".htmlcolorvalue($Electricity_Usage, 1000, 2000, 5000)."</TD><TD>".htmlcolorvalue($Electricity_Used_Today, 10, 20, 40)."</TD><TD>".htmlcolorvalue($Electricity_Used_Week, 69, 73, 77)."</TD><TD>".htmlcolorvalue($Electricity_Used_Month, 295, 312, 328)."</TD><TD>".htmlcolorvalue($Electricity_Used_Year, 3600, 3800, 4000)."</TD></TR></TABLE>";

  $ostr = $ostr."<BR><CENTER>Gasverbruik</CENTER>";
  $ostr = $ostr."<TABLE BORDER=0 WIDTH=480 style=\"table-layout: fixed;\"><TR><TD>".htmlcolorvalue((round(($Gas_Usage)*1000)), 800, 1200, 1600)."</TD><TD>".htmlcolorvalue(round($Gas_Used_Today), 10, 20, 40)."</TD><TD>".htmlcolorvalue(round($Gas_Used_Week), 23, 27, 31)."</TD><TD>".htmlcolorvalue(round($Gas_Used_Month), 100, 115, 130)."</TD><TD>".htmlcolorvalue(round($Gas_Used_Year), 1200, 1400, 1600)."</TD></TR></TABLE>";
}

// Write variables to session variable (This blocks other PHP processes so keep it short...

    $_SESSION['Electricity_Used_Hour']=$Electricity_Used_Hour;
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


// START OF OUTPUT JAVASCRIPT
?>
$('#loadinfo').html( <?php echo javascriptQuote($ostr); ?> );
<?php
// END OF OUTPUT JAVASCRIPT

exit;


/**
 *
 *
 * @param unknown $value
 * @param unknown $level1
 * @param unknown $level2
 * @param unknown $level3
 * @return unknown
 */
function htmlcolorvalue($value, $level1, $level2, $level3)
{
  if ($value < $level1) return $value;
  else if ($value < $level2) return "<FONT COLOR=YELLOW>".$value."</FONT>";
    else if ($value < $level3) return "<FONT COLOR=ORANGE>".$value."</FONT>";
      return "<FONT COLOR=#FF6666>".$value."</FONT>";
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
