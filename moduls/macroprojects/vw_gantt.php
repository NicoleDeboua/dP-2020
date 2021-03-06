<?php /* MACRO_PROJECTS viewgantt.php, v 0.1.0 2012-05-30 */
if (!defined('DP_BASE_DIR')) { die('You should not access this file directly.');}

/* Copyright (c) 2012 Region Poitou-Charentes (France)
* Author:		Henri SAULME, <henri.saulme@gmail.com>
* License:		GNU/GPL
* CHANGE LOG
* version 0.1.0
* Creation */

global $AppUI, $company_id, $dept_ids, $department, $min_view, $m, $a, $user_id, $tab;
global $m_orig, $a_orig;

$min_view = defVal($min_view, false);
$macroproject_id = intval(dPgetParam($_GET, 'macroproject_id', 0));
$user_id = intval(dPgetParam($_GET, 'user_id', $AppUI->user_id));

// sdate and edate passed as unix time stamps
$sdate = dPgetParam($_POST, 'sdate', 0);
$edate = dPgetParam($_POST, 'edate', 0);

$showInactive 		= dPgetParam($_POST,'showInactive','0');
$showLabels 		= dPgetParam($_POST,'showLabels','0');
$sortTasksByName 	= dPgetParam($_POST,'sortTasksByName','0');
$showAllGantt 		= dPgetParam($_POST,'showAllGantt','0');
$showTaskGantt 		= dPgetParam($_POST,'showTaskGantt','0');
$addPwOiD 			= dPgetParam($_POST,'add_pwoid', isset($addPwOiD) ? $addPwOiD : 0);
$m_orig = $m;
$a_orig = $a;

//if set GantChart includes user labels as captions of every GantBar
if ($showLabels!='0')   { $showLabels='1';}
if ($showInactive!='0') { $showInactive='1';}
if ($showAllGantt!='0') { $showAllGantt='1';}

if (isset($_POST['macroproFilter'])) { $AppUI->setState('MacroProjectIdxFilter',  $_POST['macroproFilter']);}
$macroproFilter = (($AppUI->getState('MacroProjectIdxFilter') !== NULL) ? $AppUI->getState('MacroProjectIdxFilter') : '-1');
$macroProjectStatus = dPgetSysVal('MacroProjectStatus');
$macroprojFilter = arrayMerge(array('-1' => 'All MacroProjects', '-2' => 'All w/o in progress', 
                               '-3' => (($AppUI->user_id == $user_id) ? 'My macroprojects' 
                                        : "User's macroprojects")), $macroProjectStatus);
if (!(empty($macroprojFilter_extra))) {	$macroprojFilter = arrayMerge($macroprojFilter, $macroprojFilter_extra); }
natsort($macroprojFilter);

$scroll_date = 1; // months to scroll
$display_option = dPgetParam($_POST, 'display_option', 'this_month');

// format dates
$df = $AppUI->getPref('SHDATEFORMAT');

if ($display_option == 'custom') {
	// custom dates
	$start_date = intval($sdate) ? new CDate($sdate) : new CDate();
	$end_date = intval($edate) ? new CDate($edate) : new CDate();
} else {
	// month
	$start_date = new CDate();
	$start_date->day = 1;
   	$end_date = new CDate($start_date);
    $end_date->addMonths($scroll_date);
}

// setup the title block
if (!@$min_view) {
	$titleBlock = new CTitleBlock('Gantt Chart', 'applet3-48.png', $m, "$m.$a");
	$titleBlock->addCrumb(('?m=' . $m), 'macroprojects list');
	$titleBlock->show();
}
?>

<script type="text/javascript" language="javascript">
var calendarField = '';

function popCalendar(field) {
	calendarField = field;
	idate = eval('document.editFrm.' + field + '.value');
	window.open('?m=public&a=calendar&dialog=1&callback=setCalendar&date=' + idate, 'calwin', 'width=250, height=220, scrollbars=no, status=no');
}

/**	@param string Input date in the format YYYYMMDD
 *	@param string Formatted date  */
function setCalendar(idate, fdate) {
	fld_date = eval('document.editFrm.' + calendarField);
	fld_fdate = eval('document.editFrm.show_' + calendarField);
	fld_date.value = idate;
	fld_fdate.value = fdate;
}

function scrollPrev() {
	f = document.editFrm;
		<?php
		$new_start = new CDate($start_date);	
		$new_start->day = 1;
		$new_end = new CDate($end_date);
		$new_start->addMonths(-$scroll_date);
		$new_end->addMonths(-$scroll_date);
		echo "f.sdate.value='".$new_start->format(FMT_TIMESTAMP_DATE)."';";
		echo "f.edate.value='".$new_end->format(FMT_TIMESTAMP_DATE)."';";
	?>
	document.editFrm.display_option.value = 'custom';
	f.submit()
}

function scrollNext() {
	f = document.editFrm;
	<?php
		$new_start = new CDate($start_date);
		$new_start->day = 1;
		$new_end = new CDate($end_date);	
		$new_start->addMonths($scroll_date);
		$new_end->addMonths($scroll_date);
		echo "f.sdate.value='" . $new_start->format(FMT_TIMESTAMP_DATE) . "';";
		echo "f.edate.value='" . $new_end->format(FMT_TIMESTAMP_DATE) . "';";
	?>
	document.editFrm.display_option.value = 'custom';
	f.submit()
}

function showThisMonth() {
	document.editFrm.display_option.value = "this_month";
	document.editFrm.submit();
}

function showFullMacroProject() {
	document.editFrm.display_option.value = "all";
	document.editFrm.submit();
}
</script>
</br>
<form name="editFrm" method="post" action="?<?php 
	foreach ($_GET as $key => $val) {	$url_query_string .= (($url_query_string) ? '&amp;' : '') . $key . '=' . $val;	}
				echo ($url_query_string);	?>">
	<input type="hidden" name="display_option" value="<?php echo $display_option;?>" />
<table class="tbl" align="center" border="0"  cellspacing="7" cellpadding="3" summary="macroprojects view gantt" width="98%">
	<tr>
		<td>
			<table align="center" border="0"  cellspacing="3" cellpadding="0" summary="select dates for graphs" width="1000">
				<tr>
					<td valign="top">
						<input type="checkbox" name="showLabels" id="showLabels" value='1' <?php echo (($showLabels==1) ? 'checked="checked"' : "");?> />
						<label for="showLabels"><?php echo $AppUI->_('Show captions');?></label>
					</td>
					<td valign="top">
						<input type="checkbox" value='1' name="showInactive" id="showInactive" <?php echo (($showInactive==1) ? 'checked="checked"' : "");?> />
						<label for="showInactive"><?php echo $AppUI->_('Show Archived');?></label>
					</td>
					<?php /*
					<td valign="top">
						<input type="checkbox" value='1' name="showAllGantt" id="showAllGantt" <?php 
							echo (($showAllGantt==1) ? 'checked="checked"' : "");?> />
						<label for="showAllGantt"><?php echo $AppUI->_('Show Tasks');?></label>
					</td>
					*/ ?>
					<td valign="top">
						<input type="checkbox" value='1' name="sortTasksByName" id="sortTasksByName" <?php echo (($sortTasksByName==1) ? 'checked="checked"' : "");?> />
						<label for="sortTasksByName"><?php echo $AppUI->_('Sort Tasks By Name');?></label>
					</td>
					<td align="right" valign="top">
						<?php echo arraySelect($macroprojFilter, 'macroproFilter', 'size="1" class="text"', $macroproFilter, true);?>
					</td>
					<td align="right">
						<input type="button" class="button" value="<?php echo $AppUI->_('submit');?>" onclick='document.editFrm.display_option.value="custom";submit();' />
					</td>
				</tr>
			</table>
			</br>
			
			<table align="center" border="0" cellpadding="3" cellspacing="0"  summary="select dates for graphs" width="98%">
					<tr>
						<td align="left" valign="top" width="20">
							<?php if ($display_option != "all") { ?>
								<a href="javascript:scrollPrev()">
									<img src="./images/prev.gif" width="16" height="16" alt="<?php echo $AppUI->_('previous');?>" border="0" />
								</a>
							<?php } ?>
						</td>
						<td align="right" width="20" nowrap="nowrap"><?php echo $AppUI->_('From');?>:
						</td>
						<td align="left" width="50" nowrap="nowrap">
							<input type="hidden" name="sdate" value="<?php 
								echo $start_date->format(FMT_TIMESTAMP_DATE);?>" />
							<input type="text" class="text" name="show_sdate" value="<?php 
								echo $start_date->format($df);?>" size="12" disabled="disabled" />
									<a href="javascript:popCalendar('sdate')">
										<img src="./images/calendar.gif" width="24" height="12" alt="" border="0" />
									</a>
						</td>						
						
						<td align="center" valign="bottom" colspan="12">
							<?php echo ("<a href='javascript:showThisMonth()'>" 
								. $AppUI->_('show this month') 
								. "</a> : <a href='javascript:showFullMacroProject()'>" 
								. $AppUI->_('show all') . "</a><br />"); 
							?>
						</td>
						<td align="right" width="20" nowrap="nowrap"><?php echo $AppUI->_('To');?>:
						</td>
						<td align="left" width="50" nowrap="nowrap">
							<input type="hidden" name="edate" value="<?php echo $end_date->format(FMT_TIMESTAMP_DATE);?>" />
							<input type="text" class="text" name="show_edate" value="<?php echo $end_date->format($df);?>" size="12" disabled="disabled" />
							<a href="javascript:popCalendar('edate')"><img src="./images/calendar.gif" width="24" height="12" alt="" border="0" /></a>
						</td>
						<td align="right" valign="top" width="20">
							<?php if ($display_option != "all") { ?>
							<a href="javascript:scrollNext()">
								<img src="./images/next.gif" width="16" height="16" alt="<?php 
								echo $AppUI->_('next');?>" border="0" />
							</a>
							<?php } ?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<table align="center" border="0" cellspacing="0" cellpadding="0" summary="show gantt" width="98%">
					<tr>
						<td>
							<?php
							$src = ("?m=macroprojects&amp;a=gantt&amp;suppressHeaders=1" 
								. (($display_option == 'all') ? '' : ('&amp;start_date=' . $start_date->format("%Y-%m-%d") 
								. '&amp;end_date=' 			. $end_date->format("%Y-%m-%d"))) 
								. "&amp;width='" 	. "+((navigator.appName=='Netscape'?window.innerWidth:document.body.offsetWidth)*0.95)" 
								. "+'&amp;showLabels=" 		. $showLabels 
								. '&amp;sortTasksByName='	. $sortTasksByName 
								. '&amp;macroproFilter='	. $macroproFilter 
								. '&amp;showInactive=' 		. $showInactive 
								. '&amp;company_id=' 		. $company_id 
								. '&amp;department=' 		. $department 
								. '&amp;dept_ids=' 			. $dept_ids 
								. '&amp;showAllGantt=' 		. $showAllGantt 
								. '&amp;user_id=' 			. $user_id 
								. '&amp;addPwOiD=' 			. $addPwOiD 
								. '&amp;m_orig=' 			. $m_orig 
								. '&amp;a_orig=' 			. $a_orig);
							echo '<script>document.write(\'<img src="' . $src . '">\')</script>';
							if (!dPcheckMem(32*1024*1024)) { ?>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo ('<span style="color: red; font-weight: bold;">' . $AppUI->_('invalid memory config') . '</span>');	} ?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
</table>
</form>
</br>