<?php
/*
 * @version $Id: dropdownSelectModel.php 793 2014-03-27 09:25:21Z orthagh $
 LICENSE

 This file is part of the datainjection plugin.

 Datainjection plugin is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 Datainjection plugin is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with datainjection. If not, see <http://www.gnu.org/licenses/>.
 --------------------------------------------------------------------------
 @package   datainjection
 @author    the datainjection plugin team
 @copyright Copyright (c) 2010-2013 Datainjection plugin team
 @license   GPLv2+
            http://www.gnu.org/licenses/gpl.txt
 @link      https://forge.indepnet.net/projects/datainjection
 @link      http://www.glpi-project.org/
 @since     2009
 ---------------------------------------------------------------------- */

// Direct access to file
if (strpos($_SERVER['PHP_SELF'],"dropdownSelectModel.php")) {
   include ('../../../inc/includes.php');
   header("Content-Type: text/html; charset=UTF-8");
   Html::header_nocache();
}

Session::checkCentralAccess();

if (isset($_SESSION['datainjection']['models_id'])
      && $_SESSION['datainjection']['models_id']!=$_POST['models_id']) {
   PluginDatainjectionModel::cleanSessionVariables();
}

$_SESSION['datainjection']['step'] = PluginDatainjectionClientInjection::STEP_UPLOAD;
$model = new PluginDatainjectionModel();

if (($_POST['models_id'] > 0)
    && $model->can($_POST['models_id'], READ)) {
   PluginDatainjectionInfo::showAdditionalInformationsForm($model);
}

?>