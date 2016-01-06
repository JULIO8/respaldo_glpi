<?php
/*
 * @version $Id: data.class.php 781 2013-07-17 14:17:11Z tsmr $
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

class PluginDatainjectionData {

   private $injectionDatas;


   function __construct() {
      $injectionDatas = array();
   }


   /**
    * @param $newData
   **/
   function addToDatas($newData) {
      $this->injectionDatas[] = $newData;
   }


   function getDatas() {
      return $this->injectionDatas;
   }


   /**
    * @param $line_id
   **/
   function getDataAtLine($line_id) {

      if (count($this->injectionDatas) >= $line_id) {
         return $this->injectionDatas[$line_id][0];
      }
      return array();
   }

}
?>