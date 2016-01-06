<?php
/*
 * @version $Id: stat.item.php 10661 2010-02-19 18:11:54Z moyo $
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

define('GLPI_ROOT', '../../..'); 
include (GLPI_ROOT . "/inc/includes.php"); 

$common = new PluginMobileCommon;
$common->displayHeader($LANG['Menu'][13], 'stat.php');

Session::checkRight("statistic","1");

if (isset($_GET["date1"])) {
   $_POST["date1"] = $_GET["date1"];
}
if (isset($_GET["date2"])) {
   $_POST["date2"] = $_GET["date2"];
}

if (empty($_POST["date1"]) && empty($_POST["date2"])) {
   $year = date("Y")-1;
   $_POST["date1"] = date("Y-m-d",mktime(1,0,0,date("m"),date("d"),$year));
   $_POST["date2"] = date("Y-m-d");
}

if (!empty($_POST["date1"])
    && !empty($_POST["date2"])
    && strcmp($_POST["date2"],$_POST["date1"]) < 0) {

   $tmp = $_POST["date1"];
   $_POST["date1"] = $_POST["date2"];
   $_POST["date2"] = $tmp;
}

if (!isset($_GET["start"])) {
   $_GET["start"] = 0;
}


PluginMobileStat::showDateSelector('stat.item.php');

PluginMobileStat::showItems($_SERVER['PHP_SELF'],$_POST["date1"],$_POST["date2"],$_GET['start']);

//PluginMobileStat::displayFooterNavBar("stat.item.php", count($val));

$common->displayFooter();

?>
