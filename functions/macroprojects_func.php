<?php /* FUNCTIONS $Id: macroprojects_func.php 5872 2009-04-25 00:09:56Z merlinyoda $ */
if (!defined('DP_BASE_DIR')) { die('You should not access this file directly.'); }

// project statii
$pstatus = dPgetSysVal('MacroProjectStatus');
$ptype = dPgetSysVal('MacroProjectType');
$ppriority_name = dPgetSysVal('MacroProjectPriority');
$ppriority_color = dPgetSysVal('MacroProjectPriorityColor');
$priority = array();
foreach ($ppriority_name as $key => $val) { $priority[$key]['name'] = $val; }
foreach ($ppriority_color as $key => $val) { $priority[$key]['color'] = $val; }

// kept for reference
$priority = array(
	-1 => array('name' => 'low', 	'color' => '#E5F7FF' ),
	0  => array('name' => 'normal', 'color' => '#CCFFCA' ),
	1  => array('name' => 'high', 	'color' => '#FFDCB3' ),
	2  => array('name' => 'immediate', 'color' => '#FF887C')
);

?>
