<?php
require_once('../../assets_backend/be_includes/config.php');

if (isset($_GET['display']))
	{
	$_SESSION['display_type']=trim($_GET['display']);
	} else {
	$_SESSION['display_type']="graph";
	}

if ((isset($_GET['timestart'])) && ($_GET['timestart']!=''))
	{
	$_SESSION['timestart']=preg_replace('/[^a-z\-_0-9\.:\/\s]/i','',mysql_escape_string(trim($_GET['timestart'])));
	} 
	
if (!isset($_SESSION['timestart']))
	{
	$error_1 = "<div class=\"msg_warning\">Start Date is Missing</div>";
	}
	
		
if ((isset($_GET['timestop'])) && ($_GET['timestop']!=''))
	{
	$_SESSION['timestop']=preg_replace('/[^a-z\-_0-9\.:\/\s]/i','',mysql_escape_string(trim($_GET['timestop'])));
	} 
	
	if (!isset($_SESSION['timestop'])) {
	$error_2="<div class=\"msg_warning\">Ending Date is Missing</div>";
	}
	
	
if (isset($_GET['reportname']))
	{
	$_SESSION['reportname']=trim($_GET['reportname']);
	}

if (isset($_GET['exportid']))
	{
	$_SESSION['exportid']=mysql_real_escape_string(trim($_GET['exportid']));
	} else {
	$_SESSION['exportid']=0;
	}

if($_SESSION['exportid']==1)
	{
	header("Content-type: application/octet-stream");
	header("Content-Disposition: attachment; filename=complaints_resolution_compliance_global.xls");
	header("Pragma: no-cache");
	header("Expires: 0");
	print "$header\n$data";	
	}

?>	
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $_SESSION['reportname'];?></title>
<?php if (!isset($_GET['exportid'])) { ?>
<link href="../css_report_print.css" rel="stylesheet" type="text/css" media="print" />
<link href="../css_report.css" rel="stylesheet" type="text/css" />
<?php } ?>
 <script type="text/javascript">
  
  //Function to hide the loading div
  function loadingDivHide()
  {
      document.getElementById("loading_div").style.display = "none";
      document.getElementById("content_area_div").style.display = "";
  }
  
  </script>
<style type="text/css">
  <!--
     .loaderClass
     {
        position: absolute;
        top: 0px;
        left: 0px;
        z-index: 999999;
        text-align: center;
        width: 100%;
        height: 200px
     }
  //-->
  </style>
</head>
<body>

<div style="padding:10px">

    <div class="text_body_vlarge"><?php echo $_SESSION['MVGitHub_acteam'];?></div>
    <div class="text_body_large"><?php echo $_SESSION['reportname'];?></div>
	<div class="rpt_period">
    	<table cellpadding="0" cellspacing="0" width="100%">
        	<tr>
            	<td width="50%">
	        		Report Period : <span ><?php echo date("F jS, Y ",strtotime($_SESSION['timestart']))." to ".date("F jS, Y ",strtotime($_SESSION['timestop'])); ?> </span>
                </td>
                <?php if (!isset($_GET['exportid'])) { ?>
                <td width="50%" align="right">
                	<span><a class="text_body" href="<?php $_SERVER['PHP_SELF'];?>?exportid=1">Export to Excel</a></span>
                </td>
                <?php } ?>
        	</tr>
       	</table>     
   	</div>

    <?php
	//fix the lables dependin on the time span selected
	$month_start=date("F",strtotime($_SESSION['timestart']));
	$month_stop=date("F",strtotime($_SESSION['timestop']));
	//check the span between the timestart and timestop if within the same month but lesser than the full month
	$month_start_lastday=date("t",strtotime($_SESSION['timestart']));
	$month_stop_lastday=date("t",strtotime($_SESSION['timestop']));
	$last_day=date("d",strtotime($_SESSION['timestop']));
	
	if (($month_start==$month_stop) && ($last_day!=$month_stop_lastday) )
		{
		$less_month=1;
		} else {
		$less_month=0;
		}
	?>

<div style="padding:10px 5px">
<table border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td width="55%">
    <table border="0" cellpadding="2" cellspacing="0">
	   	<tr>
        	<td class="tbl_h">
            Region
            </td>
        	<td class="tbl_h">
            B/F
            </td>
            <td class="tbl_h">
            <small>Total Recorded <?php if ($less_month==1) { echo "in Period"; } else { if ($month_stop==$month_start) { echo $month_start; } else { echo $month_start."-".$month_stop; } } ?></small>
            </td>
            <td class="tbl_h">
            <small>B/F + Recorded <?php if ($less_month==1) { echo "in Period"; } else { if ($month_stop==$month_start) { echo $month_start; } else { echo $month_start."-".$month_stop; } }?></small>
            </td>
            <td class="tbl_h">
            <small>[2] Total Recorded <?php if ($less_month==1) { echo "in Period"; } else { if ($month_stop==$month_start) { echo $month_start; } else { echo "(".$month_start."-".$month_stop.")"; } }?> &amp; Closed within TAT</small>
            </td>
            <td class="tbl_h">
            <small>[2] Total Recorded <?php if ($less_month==1) { echo "in Period"; } else { if ($month_stop==$month_start) { echo $month_start; } else { echo "(".$month_start."-".$month_stop.")"; } }?> &amp; Closed Beyond TAT</small>
            </td>
            <td class="tbl_h">
            <small>[2] Total Recorded <?php if ($less_month==1) { echo "this Period"; } else { if ($month_stop==$month_start) { echo $month_start; } else { echo "(".$month_start."-".$month_stop.")"; } } ?> &amp; Closed</small>
            </td>
            <td class="tbl_h">
            Balance C/F
            </td>
        </tr>
          <?php
				//the report per region
				$sql="SELECT idusrteamzone,userteamzonename FROM usrteamzone 
				WHERE usrteam_idusrteam=".$_SESSION['MVGitHub_idacteam']." ";
				$res=mysql_query($sql);
				$num=mysql_num_rows($res);
				$fet=mysql_fetch_array($res);

				if ($num > 0)
					{
		  			$totrec=0;	
					$totpen=0;
					$totcwtat=0;
					$totcbtat=0;
					$totclosed=0;
					$totcf=0;
					$totbf=0;
					
					 do {
					?>
					<tr>
					<td class="tbl_data"><?php echo $fet['userteamzonename']; ?></td>
                    <td class="tbl_data">
   						<?php 
						$sql_bf="SELECT count(*) as BF FROM tktin 
						WHERE date(tktin.timereported) < '".$_SESSION['timestart']."'
						AND tktstatus_idtktstatus<4
						AND usrteamzone_idusrteamzone=".$fet['idusrteamzone']."";
						//echo $sql_bf;
						$res_bf=mysql_query($sql_bf);
						$fet_bf=mysql_fetch_array($res_bf);
						echo number_format($fet_bf['BF'],0); 
						$col1[]=number_format($fet_bf['BF'],0); 
						
						$totbf=$totbf+$fet_bf['BF'];
						
						?>
                    </td>
					<td class="tbl_data">
						<?php 
                        /*$sql_totrec="SELECT count(*) as totrec FROM tktin 
                        WHERE ( (date(tktin.timereported) = '".$_SESSION['timestart']."') OR (date(tktin.timereported) > '".$_SESSION['timestart']."') )
                        AND ( (date(tktin.timereported) = '".$_SESSION['timestop']."') OR (date(tktin.timereported) < '".$_SESSION['timestop']."') ) 
                        AND usrteamzone_idusrteamzone=".$fet['idusrteamzone']."";
*/
						 $sql_totrec="SELECT count(*) as totrec FROM tktin 
                        WHERE (date(tktin.timereported) >= '".$_SESSION['timestart']."') 
                        AND (date(tktin.timereported) <= '".$_SESSION['timestop']."') 
                        AND usrteamzone_idusrteamzone=".$fet['idusrteamzone']."";

                        $res_totrec=mysql_query($sql_totrec);
                        $fet_totrec=mysql_fetch_array($res_totrec);
                        echo number_format($fet_totrec['totrec'],0); 
						$col2[]=number_format($fet_totrec['totrec'],0); ;
						
						$totrec=$totrec+$fet_totrec['totrec'];
						
                        ?>
                    </td>
                    <td class="tbl_data">
						<?php  
						$thisPpending=$fet_bf['BF']+$fet_totrec['totrec'];
						echo number_format($thisPpending,0);
						$col3[]=$thisPpending;
						
						$totpen=$totpen+$thisPpending;
						
						?>
                    </td>
					<td class="tbl_data"><?php 
                        /*$sql_wtat="SELECT count(*) as withinTAT FROM tktin 
                        INNER JOIN tktcategory ON tktin.tktcategory_idtktcategory=tktcategory.idtktcategory 
                        WHERE TIME_TO_SEC(TIMEDIFF(tktin.timeclosed,tktin.timereported)) <= tktcategory.tat 
                        AND ( (date(tktin.timereported) = '".$_SESSION['timestart']."') OR (date(tktin.timereported) > '".$_SESSION['timestart']."') )
                        AND ( (date(tktin.timereported) = '".$_SESSION['timestop']."') OR (date(tktin.timereported) < '".$_SESSION['timestop']."') ) 
						AND (tktstatus_idtktstatus=4 OR tktstatus_idtktstatus=5)
                        AND usrteamzone_idusrteamzone=".$fet['idusrteamzone']."";*/

						$sql_wtat="SELECT count(*) as withinTAT FROM tktin 
                        INNER JOIN tktcategory ON tktin.tktcategory_idtktcategory=tktcategory.idtktcategory 
                        WHERE TIME_TO_SEC(TIMEDIFF(tktin.timeclosed,tktin.timereported)) <= tktcategory.tat 
                        AND (date(tktin.timereported) >= '".$_SESSION['timestart']."')
                        AND (date(tktin.timereported) <= '".$_SESSION['timestop']."') 
						AND tktstatus_idtktstatus>3
                        AND usrteamzone_idusrteamzone=".$fet['idusrteamzone']."";
                        $res_wtat=mysql_query($sql_wtat);
                        $fet_wtat=mysql_fetch_array($res_wtat);
                        
						//Percentage Pending
						$perc_raw_wtat=($fet_wtat['withinTAT']/$thisPpending)*100;
						$perc_fin_wtat = number_format($perc_raw_wtat,2);						
						
						$fet_wtat_fin=number_format($fet_wtat['withinTAT'],0);
						
						echo $fet_wtat_fin."   <span style=\"color:#009900;font-size:10px\">(".$perc_fin_wtat."%)</span>"; 
						$col4[]=$fet_wtat_fin;
						
						//$totcwtat=$totcwtat+$fet_wtat_fin;
						$totcwtat=$totcwtat+$fet_wtat['withinTAT'];
                        ?>                    </td>
				  <td class="tbl_data">
						<?php 
                        $sql_btat="SELECT count(*) as beyondTAT FROM tktin 
                        INNER JOIN tktcategory ON tktin.tktcategory_idtktcategory=tktcategory.idtktcategory
                        WHERE TIME_TO_SEC(TIMEDIFF(tktin.timeclosed,tktin.timereported)) > tktcategory.tat 
                        AND date(tktin.timereported) >= '".$_SESSION['timestart']."'
                        AND date(tktin.timereported) <= '".$_SESSION['timestop']."' 
                        AND tktstatus_idtktstatus>3
                        AND usrteamzone_idusrteamzone=".$fet['idusrteamzone']."";

                        $res_btat=mysql_query($sql_btat);
                        $fet_btat=mysql_fetch_array($res_btat);

						//Percentage Pending
						$perc_raw_btat=($fet_btat['beyondTAT']/$thisPpending)*100;
						$perc_fin_btat = number_format($perc_raw_btat,2);						
						
						$fet_btat_fin=number_format($fet_btat['beyondTAT'],0);
						
						echo $fet_btat_fin."   <span style=\"color:#ff0000;font-size:10px\">(".$perc_fin_btat."%)</span>"; 
						$col5[]=$fet_btat_fin;
						
						$totcbtat=$totcbtat+$fet_btat['beyondTAT'];
                        ?>
                    </td>
                    <td class="tbl_data">
						<?php 
						/*$sql_totclosed="SELECT count(*) as totclosed FROM tktin 
                        WHERE ( (date(tktin.timereported) = '".$_SESSION['timestart']."') OR (date(tktin.timereported) > '".$_SESSION['timestart']."') )
                        AND ( (date(tktin.timereported) = '".$_SESSION['timestop']."') OR (date(tktin.timereported) < '".$_SESSION['timestop']."') ) 
                        AND (tktstatus_idtktstatus=4 OR tktstatus_idtktstatus=5)
                        AND usrteamzone_idusrteamzone=".$fet['idusrteamzone']."";*/
						$sql_totclosed="SELECT count(*) as totclosed FROM tktin 
                        WHERE (date(tktin.timereported) >= '".$_SESSION['timestart']."')
                        AND (date(tktin.timereported) <= '".$_SESSION['timestop']."')
                        AND tktstatus_idtktstatus>3
                        AND usrteamzone_idusrteamzone=".$fet['idusrteamzone']."";
                        $res_totclosed=mysql_query($sql_totclosed);
                        $fet_totclosed=mysql_fetch_array($res_totclosed);

						//Percentage Pending
						$perc_raw_cld=($fet_totclosed['totclosed']/$thisPpending)*100;
						$perc_fin_cld = number_format($perc_raw_cld,2);						
						
						$totclsd_fin=number_format($fet_totclosed['totclosed'],0);
						
						echo $totclsd_fin."   <span style=\"color:#ff6600;font-size:10px\">(".$perc_fin_cld."%)</span>"; 
						$col6[]=$totclsd_fin;
						
//						$totclosed=$totclosed+$totclsd_fin;
						$totclosed=$totclosed+$fet_totclosed['totclosed'];
						?>
                   	</td>
					<td class="tbl_data">
						<?php 
                        $DateCF=date('Y-m-d', strtotime($_SESSION['timestop']. ' + 1 days'));
                        
                        $sql_cf="SELECT count(*) as CF FROM tktin 
                        WHERE tktin.usrteamzone_idusrteamzone=".$fet['idusrteamzone']." 
                        AND date(tktin.timereported) < '".$DateCF."' 
                        AND tktstatus_idtktstatus<3
                        AND usrteamzone_idusrteamzone=".$fet['idusrteamzone']."";
                        $res_cf=mysql_query($sql_cf);
                        $fet_cf=mysql_fetch_array($res_cf);

						//Percentage CF
						$perc_raw_cf=($fet_cf['CF']/$thisPpending)*100;
						$perc_fin_cf = number_format($perc_raw_cf,2);						
						
						$totcf_fin=number_format($fet_cf['CF'],0);
						
						echo $totcf_fin."   <span style=\"color:#ff6600;font-size:10px\">(".$perc_fin_cf."%)</span>"; 
						$col7[]=$totcf_fin;
						
						$totcf=$totcf+$fet_cf['CF'];
                        ?>
                    </td>
					</tr>
					<?php
		
					 } while ($fet=mysql_fetch_array($res));					
					?>
         <tr>
        	<td class="tbl_h" style="background-color:#66FFFF; color:#000000;">
            Total
            </td>
        	<td class="tbl_h" style="background-color:#66FFFF; color:#000000;">
            	<?php echo number_format($totbf,0); ?>
            </td>
        	<td class="tbl_h" style="background-color:#66FFFF; color:#000000;">
            	<?php echo number_format($totrec,0); ?>
            </td>
        	<td class="tbl_h" style="background-color:#66FFFF; color:#000000;">
            	<?php echo number_format($totpen,0); ?>
            </td>
        	<td class="tbl_h" style="background-color:#66FFFF; color:#000000;">
            	<?php echo number_format($totcwtat,0); ?>
            </td>
        	<td class="tbl_h" style="background-color:#66FFFF; color:#000000;">
            	<?php echo number_format($totcbtat,0); ?>
            </td>
        	<td class="tbl_h" style="background-color:#66FFFF; color:#000000;">
            	<?php echo number_format($totclosed,0); ?>
            </td>
        	<td class="tbl_h" style="background-color:#66FFFF; color:#000000;">
            	<?php echo number_format($totcf,0); ?>
            </td>
        </tr>      
        
    </table>   

    </td>
    <td width="5%">
    </td>
    <td width="40%" valign="top">
    	 <table width="100%" border="0" cellpadding="2" cellspacing="0" style="border:1px solid #999999">
        <tr>
            <td width="30%" class="tbl_h">Legend Title</td>
            <td width="70%" class="tbl_h" style="color:#cccccc">-</td>
        </tr>
        <tr>
            <td class="tbl_data">TAT</td>
            <td class="tbl_data">Turn Around Time<small> [ As per the Service Charter ]</small></td>
        </tr>
        <tr>
            <td class="tbl_data">C/F</td>
            <td class="tbl_data">Complaints Carried Forward</td>
        </tr>
        <tr>
            <td colspan="2" style="padding:15px 0px 10px 5px" bgcolor="#FBFBFB">
                <div class="text_small"><small>[ 1 ]</small> Tickets Closed includes Resolved as well as Invalidated Tickets</div>
                <div class="text_small"><small>[ 2 ] In this table, tickets are listed as Closed even if they were closed after the selected period, as long as they were originally received in the selected period</small></div>
            </td>
        </tr>
    </table>    
    </td>
    </tr>
    </table>    
     
    <?php    

	
	} else {
	
	echo "<span class=\"msg_warning\">Regions not found - Please contact Admin</span>";
	
	} //if regions exist

    ?>
    </div>
</div>
</body>
</html>
