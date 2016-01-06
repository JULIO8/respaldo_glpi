<?php
/*
 * @version $Id: knowbaseiteminjection.class.php 770 2013-07-16 13:48:20Z yllen $
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

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

class PluginDatainjectionKnowbaseItemInjection extends KnowbaseItem
                                               implements PluginDatainjectionInjectionInterface {


   static function getTable() {

      $parenttype = get_parent_class();
      return $parenttype::getTable();

   }


   function isPrimaryType() {
      return true;
   }


   function connectedTo() {
      return array();
   }


   /**
    * @see plugins/datainjection/inc/PluginDatainjectionInjectionInterface::getOptions()
   **/
   function getOptions($primary_type='') {

      $tab                 = Search::getOptions(get_parent_class($this));

      $tab[5]['checktype'] = 'datetime';

      //Remove some options because some fields cannot be imported
      $blacklist     = PluginDatainjectionCommonInjectionLib::getBlacklistedOptions(get_parent_class($this));
      $notimportable = array();

      $options['ignore_fields'] = array_merge($blacklist, $notimportable);

      $options['displaytype']   = array("dropdown"       => array(4),
                                        "date"           => array(5),
                                        "multiline_text" => array(6,7),
                                        "bool"           => array(8, 9, 86),
                                        "user"           => array(70));

      return PluginDatainjectionCommonInjectionLib::addToSearchOptions($tab, $options, $this);
   }


   /**
    * @see plugins/datainjection/inc/PluginDatainjectionInjectionInterface::addOrUpdateObject()
   **/
   function addOrUpdateObject($values=array(), $options=array()) {

      $lib = new PluginDatainjectionCommonInjectionLib($this, $values, $options);
      $lib->processAddOrUpdate();
      return $lib->getInjectionResults();
   }

}
?>