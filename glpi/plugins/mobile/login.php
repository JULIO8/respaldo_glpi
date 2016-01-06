<?php

/*
 * @version $Id: login.php 10996 2010-03-11 18:17:19Z moyo $
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

//define('GLPI_ROOT', '../..'); 
//include (GLPI_ROOT . "/inc/includes.php");
include ("../../inc/includes.php"); 

$common = new PluginMobileCommon();

define("MOBILE_EXTRANET_ROOT", "../../plugins/mobile");

$_POST = array_map('stripslashes', $_POST);

//Do login and checks
//$user_present = 1;

if (!isset($_POST['login_name'])) {
   $_POST['login_name'] = '';
}
if (isset($_POST['login_password'])) {
   $_POST['login_password'] = Toolbox::unclean_cross_side_scripting_deep($_POST['login_password']);
} else {
   $_POST['login_password'] = '';
}

// Redirect management

$REDIRECT = "";
if (isset ($_POST['redirect']) && strlen($_POST['redirect'])>0) {
   $REDIRECT = "?redirect=" .$_POST['redirect'];
} else if (isset ($_GET['redirect']) && strlen($_GET['redirect'])>0) {
   $REDIRECT = "?redirect=" .$_GET['redirect'];
}

$auth = new Auth();

// now we can continue with the process...

if ($auth->Login($_POST['login_name'], $_POST['login_password'], (isset($_REQUEST["noAUTO"])?$_REQUEST["noAUTO"]:false))) {

Html::redirect(MOBILE_EXTRANET_ROOT . "/front/ss_menu.php?menu=maintain");	
//	Html::redirect(MOBILE_EXTRANET_ROOT . "/front/central.php$REDIRECT");
//	Html::redirect(MOBILE_EXTRANET_ROOT . "/front/central.php");

} else {	
	$common->displayHeader();
	$common->displayLoginBox($auth->getErr(), $REDIRECT);
	$common->displayFooter();
}

?>
