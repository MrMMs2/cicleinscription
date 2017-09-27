<?php
// This file is part of cicleinscription - http://cnj.jus.br/eadcnj
// Extension moodle for Cicle of Inscriptions
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Form term_form for cicleinscription
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod
 * @subpackage cicleinscription
 * @copyright  2013 CEAJUD - Sector CNJ
 * @author		Leo Renis Santos <leo.santos@cnj.jus.br>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->dirroot.'/mod/cicleinscription/lib.php');

// Recuperando parametros
$coursemodulesid = optional_param('coursemodulesid', 0, PARAM_INT); // course_module ID, or
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 0, PARAM_INT);
$strorganupdate = get_string('update', 'cicleinscription');
$sucessmsg = get_string('sucessmessageadd', 'cicleinscription');

$url = new moodle_url('/mod/cicleinscription/cicleorgan.php', array('coursemodulesid'=>$coursemodulesid, 'page' => $page, 'perpage' => $perpage));

if ($coursemodulesid) {
	$cm         = get_coursemodule_from_id('cicleinscription', $coursemodulesid, 0, false, MUST_EXIST);
	$course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
	$cicleinscription  = $DB->get_record('cicleinscription', array('id' => $cm->instance), '*', MUST_EXIST);
	
} else {
	error(get_string('errornotfoundidcourse', 'cicleinscription'));
}
require_login($course, true, $cm);
// Context
$context = context_module::instance($cm->id);
$PAGE->set_context($context);
$PAGE->set_cm($cm);
$PAGE->set_title(format_string($cicleinscription->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_url('/mod/cicleinscription/cicleorgan.php', array('coursemodulesid' => $cm->id));

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('addorgantocicle', 'cicleinscription'));
// Gravando log
add_to_log($course->id, 'cicleinscription', 'cicleorgan', "cicleorgan.php?coursemodulesid={$cm->id}", $cicleinscription->name, $cm->id);

// PROCESSAMENTO DA PAGINA
if (isset($_POST['organs'])){
	global $DB;
	$arrOrgans = $_POST['organs'];
	
	$objCicleOrgan = new stdClass();
	$objCicleOrgan->id = '';
	$objCicleOrgan->organid = null;	# Sera preenchido a cada iteracao do foreach
	$objCicleOrgan->cicleinscriptionid = $cicleinscription->id;
	$objCicleOrgan->coursemodulesid = $cm->id;
	$objCicleOrgan->limitvacancies = null;
	 foreach($arrOrgans as $keyOrgan => $valueOrgan) {
	 	$objCicleOrgan->organid = $valueOrgan; 
		$DB->insert_record('ci_cicleorgan', $objCicleOrgan);
	 }
	echo $OUTPUT->notification($sucessmsg, 'notifysuccess');
	redirect($CFG->wwwroot."/mod/cicleinscription/organsincicle_list.php?coursemodulesid=".$cm->id);
}

// Orgaos
$sort = "o.id ASC";
$objOrgans = cicleinscription_get_organ_available_for_cicle($cicleinscription->id);

 $organcount = count(cicleinscription_get_organ_available_for_cicle($cicleinscription->id));
/* 
if (!$perpage){
	$perpage = ITEMS_PER_PAGE;
	$page = 0;
} 
echo $OUTPUT->paging_bar($organcount, $page, $perpage, $url);*/

// Criando GRID de Orgaos
echo '<br />';
echo '<form id="page-admin-course-category" action="#" method="post">';
echo '<div style="width:90%; max-height:500px; overflow: auto;">';
echo '<table width="90%" border="0" cellspacing="2" cellpadding="4" class="generalbox boxaligncenter"><tr>';
echo '<th class="header" scope="col">'.get_string('organplural', 'cicleinscription').'</th>';
echo '<th class="header" scope="col">'.get_string('update', 'cicleinscription').'</th>';
echo '<th class="header" scope="col">'.get_string('select', 'cicleinscription').'</th>';
echo '</tr>';
// percorrendo objetos da 
foreach ($objOrgans as $org){
	echo '<tr>';
		echo "<td>{$org->name}</td>";
		echo "<td align='center'>
				<a href=\"organ.php?organid=".$org->id."&update=true&coursemodulesid={$cm->id} \">"." 
					<img src=\"" . $OUTPUT->pix_url('t/edit') . "\" alt=\".$strorganupdate.\" />";
				"</a>
			  </td>";
		echo "<td align='center'><input type='checkbox' name='organs[]' value='{$org->id}' /></td>";
	echo '</tr>';
}
echo '</table>';
echo '</div>';
$strAllRecords = get_string('allrecords', 'cicleinscription');
$strRecords = get_string('records', 'cicleinscription');
echo "<br /><div style='width: 77%; margin: 0 auto; text-align: right;'><a href='$CFG->wwwroot/mod/cicleinscription/organ_report.php?coursemodulesid=$coursemodulesid&page=0&perpage=10' title='$strRecords'>{$strAllRecords}{$organcount}</a></div>";
$valueButton = get_string('addorgantocicle', 'cicleinscription').": ". $cm->name;
echo "<div class='buttons'> <input type='submit' name='add' value='$valueButton' ></div>";
echo'</form>';
echo '<br />';

echo $OUTPUT->footer();