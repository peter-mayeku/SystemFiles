<?php
require_once('../assets_backend/be_includes/config.php');
require_once('../assets_backend/be_includes/check_login.php');

if (!isset($_SESSION['tabview_con'])) //set default tab
	{
	$_SESSION['tabview_con'] = 1;
	}

if ( (isset($_SESSION['tabview_con'])) && ($_SESSION['tabview_con']!=1) && ($_SESSION['tabview_con']!=2) && ($_SESSION['tabview_con']!=3) ) //set default tab
	{
	$_SESSION['tabview_con'] = 1;
	}

if (isset($_GET['tabview']))
	{
	$_SESSION['tabview_con'] = mysql_escape_string(trim($_GET['tabview']));
	}
//ensure there is a valid session
if ( (isset($_SESSION['tabview_con'])) && (($_SESSION['tabview_con']==1) || ($_SESSION['tabview_con']==2)  || ($_SESSION['tabview_con']==3) || ($_SESSION['tabview_con']==4) ) )
	{
	//check the selected user
	if (isset($_GET['team_member']))
		{
		$_SESSION['myteam_member']=trim($_GET['team_member']);
		}
	
	//get the staff member details
	$sql_memberdetails="SELECT idusrac,utitle,usrrole_idusrrole,fname,lname FROM usrac WHERE usrrole_idusrrole=".$_SESSION['myteam_member']." AND usrteam_idusrteam=".$_SESSION['MVGitHub_idacteam']." LIMIT 1";
	$res_memberdetails=mysql_query($sql_memberdetails);
	$num_memberdetails=mysql_num_rows($res_memberdetails);
	$fet_memberdetails=mysql_fetch_array($res_memberdetails);
	//echo $sql_memberdetails;
	if ($num_memberdetails < 1)
		{
		echo "<span class=\"msg_warning\">".$msg_warn_contactadmin."</span>";
		
		} else {
		//else go ahead and give OK to display the content
		$display_content="OK";
		
		}
	
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../assets_backend/css/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../scripts/jquery.js"></script>

<!--<script type="text/javascript" src="../scripts/jquery-1.7.2.min.js"></script>-->
<script type="text/javascript" src="../scripts/jquery-ui.min.js"></script>
<script type="text/javascript" src="../scripts/jquery-ui-timepicker-addon_.js"></script>

<link href="../thickbox/original_thickbox.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../thickbox/thickbox_be.js"></script>
<script type="text/javascript" src="../scripts/jquery.autocomplete.js"></script>

<title>My Team Task</title>
<script language="javascript">
function getAJAXHTTPREQ() { //fuction to return the xml http object
		var xmlhttp=false;	
		try{
			xmlhttp=new XMLHttpRequest();
		}
		catch(e)	{		
			try{			
				xmlhttp= new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch(e){
				try{
				xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
				}
				catch(e1){
					xmlhttp=false;
				}
			}
		}
		 	
		return xmlhttp;
    }

//select all check boxes or disselect
function selectAll(state) {
for (i = 0; i < document.form.elements.length; i++) {
var checkbox = document.form.elements[i];
checkbox.checked = state;
}
}

//checkbox mass list validate
function checkbox_tasks() {
if (document.form.sel_action.value=="")
	{
	alert ('Please select the action you wish to perform');
	document.form.sel_action.focus();
	return false;
	}
	
	if (document.form.sel_action.value=="transfer")
	{
	return confirm ('Are you sure you want to Transfer the selected tasks?');
	}

return true;
}

//autocomplete the staff 
$().ready(function() {
	$("#recepient_alt").autocomplete("findrole_alt_transfer_pop.php", {
		width: 410,
		matchContains: true,
		//mustMatch: true
		//minChars: 0,
		//multiple: true,
		//highlight: true,
		//multipleSeparator: ",",
		selectFirst: true
	});
});



</script>
<script type="text/javascript">
//search page 
function hidetxt()
	{
		if (document.findusr.recepient_alt.value=="Search a User by Name or Role")
			{
			document.findusr.recepient_alt.value='';
			}
	return true;
	}

function showtxt()
	{
		if (document.findusr.recepient_alt.value=='')
			{
			document.findusr.recepient_alt.value='Search a User by Name or Role';
			}
	return true;
	}
</script>
<script type="text/javascript" src="../uilock/jquery.uilock.js"></script>
<script language="javascript">
//Preloader for Batch Processing
			$(document).ready(function() {
				//$('#lock').click(function(){
				$('#button_transfer').click(function(){
				
					// To lock user interface interactions
					// Optinal: put html on top of the lock section,
					// like animated loading gif
					
					//$.uiLock('some html and <a href="#" onclick="$.uiUnlock();">unlock</a>');
				$.uiLock('<center class=msg_ok_overlay>Transferring... Please wait...</center>');
					
				});
				
				
				// To unlock user interface interactions
				//$.uiUnlock();

			});
</script>	
</head>
<body>
<div>
<div class="tbl_sh">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
    	<td width="100%" >
		<div>
        <?php
		if ( (isset($display_content)) && ($display_content=="OK" ))
			{
			echo $fet_memberdetails['utitle']." ".$fet_memberdetails['fname']." ".$fet_memberdetails['lname'];
			}
		?>
		</div>
		</td>
		<td align="right">
		<a href="#" onClick="parent.tb_remove();" id="button_closewin"></a>
		</td>
	</tr>
</table>
</div>
<div>
	<div>
    	<div class="tab_area">
            <span class="tab<?php if ($_SESSION['tabview_con']==1){ echo "_on"; } ?>"><a href="<?php $_SERVER['PHP_SELF'];?>?tabview=1"><?php echo $lbl_new;?></a></span>
            <span class="tab<?php if ($_SESSION['tabview_con']==2){ echo "_on"; } ?>"><a href="<?php $_SERVER['PHP_SELF'];?>?tabview=2"><?php echo $lbl_overdue;?></a></span>
            <span class="tab<?php if ($_SESSION['tabview_con']==3){ echo "_on"; } ?>"><a href="<?php $_SERVER['PHP_SELF'];?>?tabview=3"><?php echo $lbl_inprogress;?></a></span>
        </div>
	</div>
    <div>
    <?php
	if ( (isset($display_content)) && ($display_content=="OK" ))
			{
			if (isset($_SESSION['tabview_con']))
				{
					if ($_SESSION['tabview_con']==1)
						{
						require_once('pop_myteam_tasks_new.php');
						}
					if ($_SESSION['tabview_con']==2)
						{
						require_once('pop_myteam_tasks_overdue.php');
						}
					if ($_SESSION['tabview_con']==3)
						{
						require_once('pop_myteam_tasks_inprogress.php');
						}
					if ($_SESSION['tabview_con']==4)
						{
						require_once('pop_myteam_tasks_transfer.php');
						}
				}
			}
	?>
    </div>
</div>
</div>
</body>
</html>