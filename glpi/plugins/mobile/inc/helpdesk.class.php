<?php
/*
 -------------------------------------------------------------------------
 GLPI - Gestionnaire Libre de Parc Informatique
 Copyright (C) 2003-2010 by the INDEPNET Development Team.

 http://indepnet.net/   http://glpi-project.org
 -------------------------------------------------------------------------

 LICENSE
Inventaire
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
// Original Author of file: MickaelH - IPEOS I-Solutions - www.ipeos.com
// Purpose of file: This class displays the form to create a new ticket
// ----------------------------------------------------------------------

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

class PluginMobileHelpdesk {

   public static function show($ID,$from_helpdesk) {
      global $LANG,$CFG_GLPI,$DB;

//$logged = $_SESSION['glpiID'];

if(!isset($_REQUEST['session'])) {
$IDO = Session::getLoginUserID();
}
else {
$IDO = $_REQUEST['session'];
}

$logged = $ID;

/*
echo $logged."logged2 ";
echo Session::getLoginUserID()."session ";
*/
  
if(isset($_REQUEST['id'])) {		
	$ID = $_REQUEST['id'];
	$_SESSION["glpiID"] = $_REQUEST['id'];

$query = "SELECT `profiles_id` AS id
FROM `glpi_profiles_users`
WHERE `users_id` = ".$IDO."
ORDER BY `glpi_profiles_users`.`profiles_id` DESC";   

$result = $DB->query($query);
$cont = $DB->numrows($result);
$profile = $DB->fetch_assoc($result);

	}

else {
	$ID = $_SESSION['glpiID'];
	$_SESSION['glpiID'] = $_SESSION['glpiID'];
	
$query = "SELECT `profiles_id` AS id
FROM `glpi_profiles_users`
WHERE `users_id` = ".$IDO."
ORDER BY `glpi_profiles_users`.`profiles_id` DESC";   

$result = $DB->query($query);
$cont = $DB->numrows($result);
$profile = $DB->fetch_assoc($result);	
	
		}	
/*		
echo $ID."id ";
echo $_SESSION['glpiID']."glpiid ";
echo $_SESSION['logged']."logged ";
echo $logged."logged2 ";
*/

      if (!Session::haveRight("ticket",CREATE)) {
          return false;
      }

       if (Session::haveRight('validate_ticket',1)) {
     //	      if (Session::haveRightsOr('ticketvalidation', array(TicketValidation::VALIDATEREQUEST, TicketValidation::VALIDATEINCIDENT))) {
          $opt=array();
          $opt['reset']  = 'reset';
          $opt['field'][0]      = 55; // validation status
          $opt['searchtype'][0] = 'equals';
          $opt['contains'][0]   = 'waiting';
          $opt['link'][0]        = 'AND';

          $opt['field'][1]      = 59; // validation aprobator
          $opt['searchtype'][1] = 'equals';
          $opt['contains'][1]   = Session::getLoginUserID();
          //$opt['contains'][1]   = $ID;       	
          $opt['link'][1]        = 'AND';


          $url_validate=$CFG_GLPI["root_doc"]."/front/ticket.php?".Toolbox::append_params($opt,'&amp;');

           if (TicketValidation::getNumberTicketsToValidate(Session::getLoginUserID()) >0) {      	   
             //if (TicketValidation::getNumberTicketsToValidate( $ID >0) {
             echo "<a href='$url_validate' title=\"".$LANG['validation'][15]."\"
                      alt=\"".$LANG['validation'][15]."\">".$LANG['validation'][33]."</a><br><br>";
          }
       }

// Stevenes Donato

//email user

		$query = "SELECT  gu.id, ge.email, gu.`firstname`, gu.`realname`, gu.`name`
                 FROM `glpi_users` gu, glpi_useremails ge
                 WHERE gu.`id` = '$ID'
					  AND ge.users_id = gu.id ";                 
                 
       $result=$DB->query($query);       
       $email = $DB->result($result,0,"email");
  
       $user_name = $DB->result($result,0,"firstname")." ";
       $user_sname = $DB->result($result,0,"realname");
           
 //categories 
       
  		$query_cat = "SELECT id, completename
							FROM `glpi_itilcategories` 
							WHERE `is_helpdeskvisible` = 1";                 
                 
      $result_cat = $DB->query($query_cat);
      $itilcategories_id = $DB->fetch_assoc($result_cat); 
      
              
		$sql_user = "
		SELECT DISTINCT glpi_users.`id` AS id , glpi_users.`firstname` AS name, glpi_users.`realname` AS sname
		FROM `glpi_users`
		WHERE glpi_users.is_deleted = 0
		ORDER BY `glpi_users`.`firstname` ASC ";
		
		$result_user = $DB->query($sql_user);
		$user = $DB->fetch_assoc($result_user);


       // Get saved data from a back system
       
       $use_email_notification = 1;
       if ($email=="") {
          $use_email_notification=0;
       }
       $itemtype = 0;      
       $items_id="";
       $content="";
       $title="";
       //$itilcategories_id = 0;
       $urgency = 3;

       if (isset($_SESSION["helpdeskSaved"]["use_email_notification"])) {
          $use_email_notification = stripslashes($_SESSION["helpdeskSaved"]["use_email_notification"]);
       }
       if (isset($_SESSION["helpdeskSaved"]["email"])) {
          $email = stripslashes($_SESSION["helpdeskSaved"]["user_email"]);
       }
       if (isset($_SESSION["helpdeskSaved"]["itemtype"])) {
          $itemtype = stripslashes($_SESSION["helpdeskSaved"]["itemtype"]);
       }
       if (isset($_SESSION["helpdeskSaved"]["items_id"])) {
          $items_id = stripslashes($_SESSION["helpdeskSaved"]["items_id"]);
       }
       if (isset($_SESSION["helpdeskSaved"]["content"])) {
          $content = cleanPostForTextArea($_SESSION["helpdeskSaved"]["content"]);
       }
       if (isset($_SESSION["helpdeskSaved"]["name"])) {
          $title = stripslashes($_SESSION["helpdeskSaved"]["name"]);
       }
       if (isset($_SESSION["helpdeskSaved"]["itilcategories_id"])) {
          //$itilcategories_id = stripslashes($_SESSION["helpdeskSaved"]["itilcategories_id"]);
          $itilcategories_id = $_SESSION["helpdeskSaved"]["itilcategories_id"];
       }
       if (isset($_SESSION["helpdeskSaved"]["urgency"])) {
          $urgency = stripslashes($_SESSION["helpdeskSaved"]["urgency"]);
       }

       unset($_SESSION["helpdeskSaved"]);

       echo "<form method='post' name=\"helpdeskform\" action=\"".$CFG_GLPI["root_doc"]."/plugins/mobile/front/tracking.injector.php?ido=".$IDO."\" enctype=\"multipart/form-data\">";
       echo "<input type='hidden' name='_from_helpdesk' value='$from_helpdesk'>";              
                                        
       if ($CFG_GLPI['urgency_mask']==(1<<3)) {
       	
  // Dont show dropdown if only 1 value enabled
                    
          echo "<input type='hidden' name='urgency' value='3'>";
       }
       echo "<input type='hidden' name='entities_id' value='".$_SESSION["glpiactive_entity"]."'>";
       echo "<div class='force_left input_right'><table class='tab_cadre'>";

       echo "<tr><th colspan='1'>".$LANG['job'][11]."&nbsp;: </th></tr>";
		 echo "<tr><th colspan='1'>";
       if (Session::isMultiEntitiesMode()) {
          echo "&nbsp;(".Dropdown::getDropdownName("glpi_entities",$_SESSION["glpiactive_entity"]).")";
       }
       echo "</th></tr>";
                   
   //Requerente   

if($cont != "1" && $profile != "1") {   
   
          echo "<tr class='tab_bg_1'>";
          echo "<td>".$LANG['job'][4]."&nbsp;: </td></tr>";
          
          echo "<tr class='tab_bg_1'>";
          echo "<td >";
       // Dropdown::show('ITILCategories', array('value' => $user,'condition'=>'is_deleted=0'));    
        //Dropdown::showFromArray("user_id", $user);


echo "

<script>
function getComboA(sel) {
    var id = sel.options[sel.selectedIndex].value; 
    //var name = sel.options[sel.selectedIndex].id;
    //document.getElementById('user').innerHTML = id;
    window.location.assign('".$CFG_GLPI['root_doc']."/plugins/mobile/front/helpdesk.php?id='+id+'&session=".$IDO."');    
}
</script> ";

	echo "<select id='sel_user' onchange='getComboA(this)'>";
   echo '<option value=" "> -- Selecione -- </option>';

	while($user = $DB->fetch_array($result_user))
	{
		echo  "<option value=".$user['id']." id= \"". $user['name']." ".$user['sname']." \">". $user['name']." ". $user['sname'] ."</option>";
	
	}
		echo "</select>";		     
      echo "</td></tr>";

	   echo "<tr class='tab_bg_1'>";
		echo "<td>".$LANG['job'][4]."&nbsp;: </td></tr>";
		
		echo "<tr class='center tab_bg_1'>";
		echo "<td >".$user_name.$user_sname."</td></tr>";

		echo "<input type='hidden' name='logged' value='".$logged."'>";
 }                  
                        
       if ($CFG_GLPI['urgency_mask']!=(1<<3)) {
       	
          echo "<tr class='tab_bg_1'>";
          echo "<td>".$LANG['joblist'][29]."&nbsp;: </td></tr>";
			 echo "<tr class='tab_bg_1'>";          
          echo "<td>";
          //Ticket::dropdownUrgency("urgency",$urgency);
          Ticket::dropdownUrgency(array('value' => $values["urgency"]));
          echo "</td></tr>";
       }
   
       if (NotificationTargetTicket::isAuthorMailingActivatedForHelpdesk()) {
          echo "<tr class='tab_bg_1'>";
          echo "<td>".$LANG['help'][8]."&nbsp;:</td></tr>";
          echo "<tr class='tab_bg_1'>";
          echo "<td >";
          Dropdown::showYesNo('use_email_notification',$use_email_notification);
          echo "</td></tr>";

          echo "<tr class='tab_bg_1'>";
          echo "<td>".$LANG['plugin_mobile']["email"]."&nbsp;:</td></tr>";
          echo "<tr class='tab_bg_1'>";
          echo "<td ><input type='text' id='user_email' name='user_email' value=\"$email\" size='40' onchange=\"use_email_notification.value='1'\">";
          echo "</td></tr>";
       }

       if ($_SESSION["glpiactiveprofile"]["helpdesk_hardware"]!=0) {
          echo "<tr class='tab_bg_1'>";
          echo "<td>".$LANG['help'][24]."&nbsp;: </td></tr>";
          echo "<tr class='tab_bg_1'>";
          echo "<td >";
          Item_Ticket::dropdownMyDevices(Session::getLoginUserID(),$_SESSION["glpiactive_entity"]);
          //Ticket::dropdownMyDevices($ID,$_SESSION["glpiactive_entity"]);
			 echo "</td></tr>";	      
         
       }

//Stevenes Donato

       echo "<tr class='tab_bg_1'>";
       echo "<td class='force_left'>".$LANG['common'][36]."&nbsp;:</td></tr>";
       
       echo "<tr class='tab_bg_1'><td>";
       //Dropdown::show('TicketCategory', array('value' => $itilcategories_id,'condition'=>'`is_helpdeskvisible`=1')); 
       //Dropdown::show('ITILCategory', array('value' => $itilcategories_id,'condition'=>'`is_helpdeskvisible`=1'));
       //Dropdown::getDropdownName("glpi_itilcategories", $this->fields["itilcategories_id"]);           
       //Dropdown::showFromArray("completename", $itilcategories_id);

   echo "<script>
		function getCat(sel) {
		    //var x = document.getElementById('sel_cat').value;
		    var cat = sel.options[sel.selectedIndex].value; 
		    //document.getElementById('demo').innerHTML = 'Categoria: ' + cat;
		    //document.getElementById('demo').innerHTML = '<input type=\'text\' name=\'itilcategories_id\' value=\' +cat+ \'/>';
		    document.getElementById('categoria').value = cat;		   
		}
		</script>";   
        

	echo "<select id='sel_cat' onchange='getCat(this)'>";
   echo '<option value=" "> -- '. $LANG['dropdown'][35] .'-- </option>';

	while($cat = $DB->fetch_array($result_cat))
	{
		echo  "<option value=".$cat['id']." id= \"". $cat['completename']." \">". $cat['completename']." </option>";
	
	}
		echo "</select>"."\n";	 		                    
       
       echo "</td></tr>";
       
   	echo "<tr><td>";
		//echo '<label for="itilcategories_id" id="demo"></label>';
		//echo "<span id='demo'></span>";
		echo "<input id='categoria' type='hidden' name='itilcategories_id' value='' />";    
		echo "</td></tr>";

       echo "<tr class='tab_bg_1'>";
       echo "<td>".$LANG['common'][57]."&nbsp;:</td></tr>";
  
       echo "<tr class='tab_bg_1'>";
       echo "<td ><input type='text' maxlength='250' size='50' name='name' value=\"$title\" required ></td></tr>";

       echo "<tr class='tab_bg_1'>";
       echo "<td>". $LANG['joblist'][6]."&nbsp;:</td></tr>";  

       echo "<tr class='tab_bg_1'>";
       echo "<td  colspan='1'><textarea name='content' cols='78' rows='14' required >$content</textarea>";
       echo "</td></tr>";

       echo "<tr class='tab_bg_1'><td>".$LANG['document'][2]." (".Document::getMaxUploadSize().")&nbsp;:";
/*
 * we hide the picture (aide.png) to prevent the form openning in other window,
 * outside the mobile plugin layout.
       echo "<img src=\"".$CFG_GLPI["root_doc"]."/pics/aide.png\" class='pointer' alt=\"".
              $LANG['central'][7]."\" onclick=\"window.open('".$CFG_GLPI["root_doc"].
              "/front/documenttype.list.php','Help','scrollbars=1,resizable=1,width=1000,height=800')\">";
*/


echo "<input type='hidden' name='ido' value='".$IDO."'>";
?>

<script>
function getID() {
    var ido = document.getElementById('ido').value; 
    document.getElementById('session').innerHTML = "$_SESSION['logged']="+ido;
    //window.location.assign('".$CFG_GLPI['root_doc']."/plugins/mobile/front/helpdesk.php?id='+id+'&session=".$IDO."');    
}
</script>
<div id="session" style="display:none;"></div>

<?php
       echo "</td></tr>";
       
       echo "<tr class='tab_bg_1'>";
       echo "<td><input type='file' class='ui-input-text ui-body-null ui-corner-all ui-shadow-inset ui-body-c' name='filename' value=\"\" size='25'></td></tr>";

       echo "<tr class='tab_bg_1'>";
       echo "<td colspan='1' class='center'>";
       echo "<input type='submit' value=\"".$LANG['help'][14]."\" class='submit' >";
       echo "</td></tr>";

       echo "</table></div>"; //</form>";
    
       Html::closeForm();
    }

}
