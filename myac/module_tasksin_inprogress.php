<?php
require_once('../assets_backend/be_includes/config.php');
require_once('../Connections/connSystem.php');

if (isset($_GET['parentviewtab']))
	{
	$_SESSION['parenttabview']=preg_replace('/[^a-z\-_0-9\.:\/]/i','',mysql_escape_string(trim($_GET['parentviewtab'])));
	}

mysql_select_db($database_connSystem, $connSystem);
require_once('../assets_backend/be_includes/check_login_easy.php');

//CHECK IF I HAVE BEEN DELEGATED TASKS
$sql_delegated="SELECT usrrolename,utitle,fname,lname,wftasksdeleg_meta.idusrrole_from FROM wftasksdeleg_meta 
INNER JOIN usrrole ON wftasksdeleg_meta.idusrrole_from=usrrole.idusrrole
INNER JOIN usrac ON usrrole.idusrrole=usrac.usrrole_idusrrole
WHERE wftasksdeleg_meta.idusrrole_to=".$_SESSION['MVGitHub_iduserrole']."
AND wftasksdeleg_meta.deleg_status=1";
$res_delegated=mysql_query($sql_delegated);
$num_delegated=mysql_num_rows($res_delegated);
$fet_delegated=mysql_fetch_array($res_delegated);


if ( $num_delegated > 0 )
	{
	$qry_recepient = " (wftasks.usrrole_idusrrole=".$_SESSION['MVGitHub_iduserrole']." OR wftasks.usrrole_idusrrole=".$fet_delegated['idusrrole_from']." ".$_SESSION['idwfgroup']." ) ";
	$delegated_to_me = 1;
	} else {
	$qry_recepient = " (wftasks.usrrole_idusrrole=".$_SESSION['MVGitHub_iduserrole']." ".$_SESSION['idwfgroup'].") ";
	$delegated_to_me = 0;
	}

#	BuildNav for Dreamweaver MX v0.2
#              10-02-2002
#	Alessandro Crugnola [TMM]
#	sephiroth: alessandro@sephiroth.it
#	http://www.sephiroth.it
#	
#	Function for navigation build ::
function buildNavigation($pageNum_Recordset1,$totalPages_Recordset1,$prev_Recordset1,$next_Recordset1,$separator=" | ",$max_links=10, $show_page=true)
{
                GLOBAL $maxRows_rs_list,$totalRows_rs_list;
	$pagesArray = ""; $firstArray = ""; $lastArray = "";
	if($max_links<2)$max_links=2;
	if($pageNum_Recordset1<=$totalPages_Recordset1 && $pageNum_Recordset1>=0)
	{
		if ($pageNum_Recordset1 > ceil($max_links/2))
		{
			$fgp = $pageNum_Recordset1 - ceil($max_links/2) > 0 ? $pageNum_Recordset1 - ceil($max_links/2) : 1;
			$egp = $pageNum_Recordset1 + ceil($max_links/2);
			if ($egp >= $totalPages_Recordset1)
			{
				$egp = $totalPages_Recordset1+1;
				$fgp = $totalPages_Recordset1 - ($max_links-1) > 0 ? $totalPages_Recordset1  - ($max_links-1) : 1;
			}
		}
		else {
			$fgp = 0;
			$egp = $totalPages_Recordset1 >= $max_links ? $max_links : $totalPages_Recordset1+1;
		}
		if($totalPages_Recordset1 >= 1) {
			#	------------------------
			#	Searching for $_GET vars
			#	------------------------
			$_get_vars = '';			
			if(!empty($_GET) || !empty($HTTP_GET_VARS)){
				$_GET = empty($_GET) ? $HTTP_GET_VARS : $_GET;
				foreach ($_GET as $_get_name => $_get_value) {
					if ($_get_name != "pageNum_rs_list") {
						$_get_vars .= "&$_get_name=$_get_value";
					}
				}
			}
			$successivo = $pageNum_Recordset1+1;
			$precedente = $pageNum_Recordset1-1;
			$firstArray = ($pageNum_Recordset1 > 0) ? "<a href=\"index.php?mod=2&submod=0&ua=view&view=2&uction=view_mod&pageNum_rs_list=$precedente$_get_vars\">$prev_Recordset1</a>" :  "$prev_Recordset1";
			# ----------------------
			# page numbers
			# ----------------------
			for($a = $fgp+1; $a <= $egp; $a++){
				$theNext = $a-1;
				if($show_page)
				{
					$textLink = $a;
				} else {
					$min_l = (($a-1)*$maxRows_rs_list) + 1;
					$max_l = ($a*$maxRows_rs_list >= $totalRows_rs_list) ? $totalRows_rs_list : ($a*$maxRows_rs_list);
					$textLink = "$min_l - $max_l";
				}
				$_ss_k = floor($theNext/26);
				if ($theNext != $pageNum_Recordset1)
				{
					$pagesArray .= "<a href=\"index.php?mod=2&submod=0&ua=view&view=2&uction=view_mod&pageNum_rs_list=$theNext$_get_vars\">";
					$pagesArray .= "$textLink</a>" . ($theNext < $egp-1 ? $separator : "");
				} else {
					$pagesArray .= "$textLink"  . ($theNext < $egp-1 ? $separator : "");
				}
			}
			$theNext = $pageNum_Recordset1+1;
			$offset_end = $totalPages_Recordset1;
			$lastArray = ($pageNum_Recordset1 < $totalPages_Recordset1) ? "<a href=\"index.php?mod=2&submod=0&ua=view&view=2&uction=view_mod&pageNum_rs_list=$successivo$_get_vars\">$next_Recordset1</a>" : "$next_Recordset1";
		}
	}
	return array($firstArray,$pagesArray,$lastArray);
}

if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;

  $theValue = function_exists("mysql_escape_string") ? mysql_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

$maxRows_rs_list = 20;
$pageNum_rs_list = $_SESSION['pageNum_rs_list'];
if (isset($_GET['pageNum_rs_list'])) {
  $pageNum_rs_list = $_GET['pageNum_rs_list'];
}
$startRow_rs_list = $pageNum_rs_list * $maxRows_rs_list;

mysql_select_db($database_connSystem, $connSystem);
$query_rs_list = "SELECT idwftasks,wftasks.tktin_idtktin,wftasks.tasksubject,wftasks.timeinactual,wftasks.timedeadline,wftasks.wftskstatustypes_idwftskstatustypes,wftasks.timeactiontaken,usrrole.usrrolename as sender_role,TIMESTAMPDIFF(MINUTE, NOW(), wftasks.timedeadline) AS time_to_deadline ,usrac.utitle,usrac.lname,tktin.refnumber,tktin.senderphone,wftasks.wfactorsgroup_idwfactorsgroup,wftasks.usrrole_idusrrole,
tktin.voucher_number,
(SELECT wftasks_batch.batch_no_verbose FROM wftasks_batch
INNER JOIN tktin ON wftasks_batch.idwftasks_batch=tktin.wftasks_batch_idwftasks_batch 
WHERE tktin.idtktinPK=tktin_idtktin) as batch_number_verb
FROM wftasks 
INNER JOIN usrrole ON wftasks.sender_idusrrole=usrrole.idusrrole 
INNER JOIN usrac ON wftasks.sender_idusrac=usrac.idusrac 
INNER JOIN tktin ON wftasks.tktin_idtktin=tktin.idtktinPK 
WHERE ".$qry_recepient."  AND wftskstatustypes_idwftskstatustypes=6 
AND wftskstatusglobal_idwftskstatusglobal=2  ORDER BY ".$_SESSION['order_field']." ".$_SESSION['order_logix']."";
$query_limit_rs_list = sprintf("%s LIMIT %d, %d", $query_rs_list, $startRow_rs_list, $maxRows_rs_list);
$rs_list = mysql_query($query_limit_rs_list, $connSystem) or die(mysql_error());
$row_rs_list = mysql_fetch_assoc($rs_list);

if (isset($_GET['totalRows_rs_list'])) {
  $totalRows_rs_list = $_GET['totalRows_rs_list'];
} else {
  $all_rs_list = mysql_query($query_rs_list);
  $totalRows_rs_list = mysql_num_rows($all_rs_list);
}
$totalPages_rs_list = ceil($totalRows_rs_list/$maxRows_rs_list)-1;


?>
<div>
    <div >
    <form action="index.php" method="get" name="search_tasks" target="_top">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
    	<tr>
        	<td width="50%" class="bg_section">
			<?php echo $lbl_inprogress;?>
            <a href="#" style="text-decoration:none;" class="tooltip"><img src="../assets_backend/icons/help.gif" border="0" align="absmiddle" /><span><?php echo $msg_tip_inprogress;?></span></a>
            </td>
   	  <td width="50%" class="bg_section">
            	<?php require_once('module_tasksin_searchbox.php');?>
            </td>
        </tr>
    </table>
    </form>
    </div>

	<div style="padding:5px 0px 10px 0px">
    <?php
	if ($totalRows_rs_list > 0)
		{
	?>
<table border="0" cellpadding="2" cellspacing="0" width="100%">

	<tr>
    	<td colspan="5">
        	<table border="0" cellpadding="0" cellspacing="0">
            	<tr>
					<td class="text_small"> Tasks <?php echo ($startRow_rs_list + 1) ?> to <?php echo min($startRow_rs_list + $maxRows_rs_list, $totalRows_rs_list) ?> of <?php echo $totalRows_rs_list ?> </td>
					<td class="text_body"><?php 
# variable declaration
$prev_rs_list = "<< previous";
$next_rs_list = "next >>";
$separator = " | ";
$max_links = 10;
$pages_navigation_rs_list = buildNavigation($pageNum_rs_list,$totalPages_rs_list,$prev_rs_list,$next_rs_list,$separator,$max_links,true); 

print $pages_navigation_rs_list[0]; 
?>
				    <?php print $pages_navigation_rs_list[1]; ?> <?php print $pages_navigation_rs_list[2]; ?> </td>
			  </tr>
            </table>        </td>
    </tr>
	<tr>
		<td width="25%" class="tbl_h2">
			<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td class="tbl_h2">
					<?php echo $lbl_from;?>                    </td>
                    <td width="12px">
                    <a href="index.php?mod=2&amp;submod=0&amp;ua=view&amp;view=2&amp;uction=view_mod&amp;pageNum_rs_list=<?php echo $_SESSION['pageNum_rs_list'];?>&amp;orderlist=ascending&amp;orderwhat=from" id="button_asc"></a>                    </td>
                    <td width="12px">
                    <a href="index.php?mod=2&amp;submod=0&amp;ua=view&amp;view=2&amp;uction=view_mod&amp;pageNum_rs_list=<?php echo $_SESSION['pageNum_rs_list'];?>&amp;orderlist=descending&amp;orderwhat=from" id="button_desc"></a>                    </td>
				</tr>
			</table>		</td>
		<td width="25%" class="tbl_h2"><?php echo $lbl_subject;?></td>
		<td width="20%" class="tbl_h2">
		<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td class="tbl_h2">
					<?php echo $lbl_date;?>                    </td>
                    <td width="12px">
                    <a href="index.php?mod=2&amp;submod=0&amp;ua=view&amp;view=2&amp;uction=view_mod&amp;pageNum_rs_list=<?php echo $_SESSION['pageNum_rs_list'];?>&amp;orderlist=ascending&amp;orderwhat=datein" id="button_asc"></a>                    </td>
                    <td width="12px">
                    <a href="index.php?mod=2&amp;submod=0&amp;ua=view&amp;view=2&amp;uction=view_mod&amp;pageNum_rs_list=<?php echo $_SESSION['pageNum_rs_list'];?>&amp;orderlist=descending&amp;orderwhat=datein" id="button_desc"></a>                    </td>
				</tr>
			</table>		</td>
        <td width="15%" class="tbl_h2">
	<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td class="tbl_h2">
					<?php echo $lbl_deadline_task;?>                    </td>
                    <td width="12px">
                    <a href="index.php?mod=2&amp;submod=0&amp;ua=view&amp;view=2&amp;uction=view_mod&amp;pageNum_rs_list=<?php echo $_SESSION['pageNum_rs_list'];?>&amp;orderlist=ascending&amp;orderwhat=deadline" id="button_asc"></a>                    </td>
                    <td width="12px">
                    <a href="index.php?mod=2&amp;submod=0&amp;ua=view&amp;view=2&amp;uction=view_mod&amp;pageNum_rs_list=<?php echo $_SESSION['pageNum_rs_list'];?>&amp;orderlist=descending&amp;orderwhat=deadline" id="button_desc"></a>                    </td>
				</tr>
			</table>		</td>
        <td width="15%" class="tbl_h2">&nbsp;</td>
	</tr>  
    <?php do { ?>
      <tr <?php 
// technocurve arc 3 php mv block2/3 start
echo " style=\"background-color:$mocolor\" onMouseOver=\"this.style.backgroundColor='$mocolor3'\" onMouseOut=\"this.style.backgroundColor='$mocolor'\"";
// technocurve arc 3 php mv block2/3 end
?>>
		<td class="tbl_data" <?php if ($row_rs_list['wftskstatustypes_idwftskstatustypes']==0) { echo "style=\"font-weight:bold\""; } ?>>
        
        <?php
		//DELEGATED OUT
		if ($_SESSION['delegated']==1)
			{
			echo "<img src=\"../assets_backend/icons/icon_lock.gif\" border=\"0\" align=\"absmiddle\" style=\"padding:2px\" />";
			}
		//DELEGATED IN
		if (($delegated_to_me==1) &&  ($fet_delegated['idusrrole_from']==$row_rs_list['usrrole_idusrrole']) )
			{
			echo "<img title=\"C/O ".$fet_delegated['usrrolename']." - ".$fet_delegated['utitle']." ".$fet_delegated['fname']." ".$fet_delegated['lname']." \" src=\"../assets_backend/icons/icon_co.gif\" border=\"0\" align=\"absmiddle\" style=\"padding:2px\" />";
			}
		
		if ($row_rs_list['wfactorsgroup_idwfactorsgroup']==$_SESSION['idwfgroup_val'])
			{
			echo "<img title=\"Group Task \" src=\"../assets_backend/icons/icon_group.gif\" border=\"0\" align=\"absmiddle\" style=\"padding:2px\" />";
			}
		?>
        <a href="pop_taskview.php?task=<?php echo $row_rs_list['idwftasks']; ?>&amp;title=<?php echo $row_rs_list['tasksubject']; ?>&amp;tabview=1&amp;keepThis=true&amp;TB_iframe=true&amp;height=<?php echo $_SESSION['tb_height'];?>&amp;width=<?php echo $_SESSION['tb_width'];?>&amp;inlineId=hiddenModalContent&amp;modal=true" class="thickbox" >
        <div>
		<?php if (strlen($row_rs_list['sender_role'])>1) { echo $row_rs_list['sender_role']; } else { echo $lbl_system; } ?>
        <?php if (strlen($row_rs_list['lname'])>1) { echo "&&nbsp;<small>(".$row_rs_list['utitle']." ".$row_rs_list['lname'].")</small>"; } ?>
        </div>
        </a>        </td>
		<td class="tbl_data" <?php if ($row_rs_list['wftskstatustypes_idwftskstatustypes']==0) { echo "style=\"font-weight:bold\""; } ?>>
        <a style="color:#555555; text-decoration:none" href="pop_taskview.php?task=<?php echo $row_rs_list['idwftasks']; ?>&amp;title=<?php echo $row_rs_list['tasksubject']; ?>&amp;tabview=1&amp;keepThis=true&amp;TB_iframe=true&amp;height=<?php echo $_SESSION['tb_height'];?>&amp;width=<?php echo $_SESSION['tb_width'];?>&amp;inlineId=hiddenModalContent&amp;modal=true" class="thickbox" >
        <div>
        <?php 
		$subject=$row_rs_list['tasksubject'];
		if (strlen($subject)>40)
			{
			echo $subject."...";
			} else {
			echo $subject;
			}
		 ?>
         </div>
         </a>		</td>
		<td class="tbl_data" <?php if ($row_rs_list['wftskstatustypes_idwftskstatustypes']==0) { echo "style=\"font-weight:bold\""; } ?>>
		<a style="color:#555555; text-decoration:none" href="pop_taskview.php?task=<?php echo $row_rs_list['idwftasks']; ?>&amp;title=<?php echo $row_rs_list['tasksubject']; ?>&amp;tabview=1&amp;keepThis=true&amp;TB_iframe=true&amp;height=<?php echo $_SESSION['tb_height'];?>&amp;width=<?php echo $_SESSION['tb_width'];?>&amp;inlineId=hiddenModalContent&amp;modal=true" class="thickbox" >
        <div>
		<?php echo date("D, M d, Y H:i",strtotime($row_rs_list['timeinactual'])); ?>        </div>
        </a>		</td>
		<td class="tbl_data" <?php if ($row_rs_list['wftskstatustypes_idwftskstatustypes']==0) { echo "style=\"font-weight:bold\""; } ?>>
        <a style="color:#555555; text-decoration:none" href="pop_taskview.php?task=<?php echo $row_rs_list['idwftasks']; ?>&amp;title=<?php echo $row_rs_list['tasksubject']; ?>&amp;tabview=1&amp;keepThis=true&amp;TB_iframe=true&amp;height=<?php echo $_SESSION['tb_height'];?>&amp;width=<?php echo $_SESSION['tb_width'];?>&amp;inlineId=hiddenModalContent&amp;modal=true" class="thickbox" >
        <div>
		<?php
        echo date("D, M d, Y H:i",strtotime($row_rs_list['timedeadline'])); 
		?>
        </div>
        </a>		</td>
        <td class="tbl_data" <?php if ($row_rs_list['wftskstatustypes_idwftskstatustypes']==0) { echo "style=\"font-weight:bold\""; } ?>>&nbsp;</td>
	</tr>
<?php 
// technocurve arc 3 php mv block3/3 start
if ($mocolor == $mocolor1) {
	$mocolor = $mocolor2;
} else {
	$mocolor = $mocolor1;
}
// technocurve arc 3 php mv block3/3 end
?>    
<?php } while ($row_rs_list = mysql_fetch_assoc($rs_list)); ?>
     <tr>
    	<td colspan="5">
			<table border="0" cellpadding="0" cellspacing="0">
            	<tr>
					<td class="text_small"> Tasks <?php echo ($startRow_rs_list + 1) ?> to <?php echo min($startRow_rs_list + $maxRows_rs_list, $totalRows_rs_list) ?> of <?php echo $totalRows_rs_list ?> </td>
					<td class="text_body"><?php 
# variable declaration
$prev_rs_list = "<< previous";
$next_rs_list = "next >>";
$separator = " | ";
$max_links = 10;
$pages_navigation_rs_list = buildNavigation($pageNum_rs_list,$totalPages_rs_list,$prev_rs_list,$next_rs_list,$separator,$max_links,true); 

print $pages_navigation_rs_list[0]; 
?>
				    <?php print $pages_navigation_rs_list[1]; ?> <?php print $pages_navigation_rs_list[2]; ?> </td>
			  </tr>
            </table>        </td>
    </tr>
</table>
<?php } else { //else if no record ?>
	<div style="text-align:center; margin:45px 15px;" class="msg_warning">
    <?php echo $msg_no_record_4u; ?>
    </div>
<?php } ?>
  </div>
</div>

<?php
mysql_free_result($rs_list);

?>
