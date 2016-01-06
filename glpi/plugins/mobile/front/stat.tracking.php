<?php
/*
 -------------------------------------------------------------------------
 GLPI - Gestionnaire Libre de Parc Informatique
 Copyright (C) 2003-2010 by the INDEPNET Development Team.

 http://indepnet.net/   http://glpi-project.org
 -------------------------------------------------------------------------

 LICENSE

 This file is part of GLPI.

 GLPI is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 GLPI is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with GLPI; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 --------------------------------------------------------------------------
 */

// ----------------------------------------------------------------------
// Original Author of file:
// Purpose of file:
// ----------------------------------------------------------------------

//http://192.168.1.118/sos-084/plugins/mobile/front/stat.graph.php?id=671&date1=2013-09-14&date2=2014-01-14&type=user

define('GLPI_ROOT', '../../..'); 
include (GLPI_ROOT . "/inc/includes.php"); 

global $LANG;

$common = new PluginMobileCommon;
$common->displayHeader($LANG['Menu'][13]." ".strtolower($LANG['stats'][47]), 'stat.php');

Session::checkRight("statistic","1");

if (empty($_REQUEST["type"])) {
   $_REQUEST["type"] = "user";
}

if (empty($_REQUEST["showgraph"])) {
   $_REQUEST["showgraph"] = 0;
}

if (empty($_REQUEST["date1"]) && empty($_REQUEST["date2"])) {
   $year = date("Y")-1;
   $_REQUEST["date1"] = date("Y-m-d",mktime(1,0,0,date("m"),date("d"),$year));
   $_REQUEST["date2"] = date("Y-m-d");
}

if (!empty($_REQUEST["date1"])
    && !empty($_REQUEST["date2"])
    && strcmp($_REQUEST["date2"],$_REQUEST["date1"]) < 0) {

   $tmp = $_REQUEST["date1"];
   $_REQUEST["date1"] = $_REQUEST["date2"];
   $_REQUEST["date2"] = $tmp;
}

if (!isset($_REQUEST["start"])) {
   $_REQUEST["start"] = 0;
}

$items =
   array($LANG['job'][4] => array('user'  => array('title' => $LANG['job'][4],
                                                  'field' => 'glpi_tickets.users_id'),
                                  'users_id_recipient'
                                          => array('title' => $LANG['common'][37],
                                                   'field' => 'glpi_tickets.users_id_recipient'),
                                  'usertitles_id'
                                          => array('title' => $LANG['users'][1],
                                                   'field' => 'glpi_usertitles.id'),
                                  'usercategories_id'
                                          => array('title' => $LANG['users'][2],
                                                   'field' => 'glpi_users.usercategories_id')),
         $LANG['common'][32] => array('ticketcategories_id'
                                                 => array('title' => $LANG['common'][36],
                                                          'field' => 'glpi_tickets.itilcategories_id'),
                                      'urgency'  => array('title' => $LANG['joblist'][29],
                                                          'field' => 'glpi_tickets.urgency'),
                                      'impact'   => array('title' => $LANG['joblist'][30],
                                                          'field' => 'glpi_tickets.impact'),
                                      'priority' => array('title' => $LANG['joblist'][2],
                                                          'field' => 'glpi_tickets.priority'),
                                      'requesttypes_id'
                                                 => array('title' => $LANG['job'][44],
                                                          'field' => 'glpi_tickets.requesttypes_id'),
                                      'ticketsolutiontypes_id'
                                                 => array('title' => $LANG['job'][48],
                                                          'field' => 'glpi_tickets.solutiontypes_id')),
         $LANG['job'][5] => array('technicien'
                                       => array('title' => $LANG['job'][6]." ".$LANG['stats'][48],
                                                'field' => 'glpi_tickets.users_id_recipient'),
                                  'technicien_followup'
                                       => array('title' => $LANG['job'][6]." ".$LANG['stats'][49],
                                                'field' => 'glpi_tickets.users_id_lastupdater'))); 
                                 

//'field' => 'glpi_followup.users_id'),                                                
//users_id_lastupdater      

//mysql_data_seek($items,0);                                          
                                                
$INSELECT = "";
foreach ($items as $label => $tab) {
   $INSELECT .= "<optgroup label=\"$label\">";
   foreach ($tab as $key => $val) {
      // Current field
      if ($key == $_REQUEST["type"]) {
         $field = $val["field"];
      }
      $INSELECT .= "<option value='$key' ".($key==$_REQUEST["type"]?"selected":"").">".$val['title'].
                   "</option>";
   }
   $INSELECT .= "</optgroup>";
}

echo "<div data-role='collapsible' data-collapsed='true'>";
echo "<h2>".$LANG['common'][27]."</h2>";

echo "<form method='get' name='form' action='".$CFG_GLPI["root_doc"]."/plugins/mobile/front/stat.tracking.php'>";
echo "<select name='type'>";
echo $INSELECT;
echo "</select><br />";

echo "<label for='date1'><b>".$LANG['search'][8]."&nbsp;:</b></label><br />";
echo "<input type='date' name='date1' id='date1' value='".$_REQUEST["date1"]."' /><br /><br />";
echo "<label for='date2'><b>".$LANG['search'][9]."&nbsp;:</b></label><br />";
echo "<input type='date' name='date2' id='date2' value='".$_REQUEST["date2"]."' /><br />";

echo $LANG['stats'][7]."&nbsp;:";
Dropdown::showYesNo('showgraph',$_REQUEST['showgraph']);
echo "<br /><br />";
echo "<input type='submit' class='button' name='submit' value='". $LANG['buttons'][7] ."' data-inline='true' data-theme='a' >";

Html::closeForm();
echo "</div>";

/*
echo "<div data-role='collapsible' data-collapsed='true'>";
echo "<h2>pager</h2>";
*/

$val = PluginMobileStat::getItems($_REQUEST["date1"],$_REQUEST["date2"],$_REQUEST["type"]);
$params = array('type'  => $_REQUEST["type"],
                'field' => $field,
                'date1' => $_REQUEST["date1"],
                'date2' => $_REQUEST["date2"],
                'start' => $_REQUEST["start"]);
/*
printPager($_REQUEST['start'],count($val),$CFG_GLPI['root_doc'].'/front/stat.tracking.php',
           "date1=".$_REQUEST["date1"]."&amp;date2=".$_REQUEST["date2"].
            "&amp;type=".$_REQUEST["type"]."&amp;showgraph=".$_REQUEST["showgraph"],
           'Stat',$params);
          
echo "</div>";*/

//echo $_REQUEST["type"];
//$itemtype = $_REQUEST["type"];
//$itemtype = "Ticket";

if (!$_REQUEST['showgraph']) {
   PluginMobileStat::show($itemtype, $_REQUEST["type"],$_REQUEST["date1"],$_REQUEST["date2"],$_REQUEST['start'],$val);
} else {
   $data=Stat::getDatas($itemtype, $_REQUEST["type"],$_REQUEST["date1"],$_REQUEST["date2"],$_REQUEST['start'],$val);
   if (isset($data['opened']) && is_array($data['opened'])) {
      foreach($data['opened'] as $key => $val){
         $newkey=html_clean($key);
         $cleandata[$newkey]=$val;
      }
      Stat::showGraph(array($LANG['stats'][5]=>$cleandata)
                     ,array('title'=>$LANG['stats'][5],
                           'showtotal' => 1,
                           'unit'      => $LANG['stats'][35],
                           'type'      => 'pie'));
   }
   if (isset($data['solved']) && is_array($data['5'])) {
      foreach($data['solved'] as $key => $val){
         $newkey=html_clean($key);
         $cleandata[$newkey]=$val;
      }
      Stat::showGraph(array($LANG['stats'][11]=>$cleandata)
                     ,array('title'    => $LANG['stats'][11],
                           'showtotal' => 1,
                           'unit'      => $LANG['stats'][35],
                           'type'      => 'pie'));
   }
}

PluginMobileStat::displayFooterNavBar("stat.tracking.php", count($val));

$common->displayFooter();

?>
