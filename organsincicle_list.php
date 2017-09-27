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
$delete = optional_param('delete', 0, PARAM_BOOL);
$del_cicleorganid = optional_param('cicleorganid', 0, PARAM_INT);
$strchange = get_string('change', 'cicleinscription');
$strupdate = get_string('update', 'cicleinscription');
$strdel = get_string('del','cicleinscription');
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
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_url('/mod/cicleinscription/organsincicle_list.php', array('coursemodulesid' => $cm->id));

# Cabecalho
$site = get_site();
$PAGE->set_title("$site->shortname: $cicleinscription->name");
$PAGE->set_heading(format_string($course->fullname));
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('listorganscicle', 'cicleinscription', $cicleinscription));

// Gravando log
add_to_log($course->id, 'cicleinscription', 'organsinclice_list', "organsinclice_list.php.php?coursemodulesid={$cm->id}", $cicleinscription->name, $cm->id);

// PROCESSAMENTO DA PAGINA - Alterando numero limite de vagas
if (isset($_POST['cicleorganid'])){
	global $DB;
	$cicleorganid = $_POST['cicleorganid'];
	$limitvacancies = $_POST['limitvacancies'.$cicleorganid];

	$objData = new stdClass();
	$objData->id = $cicleorganid;
	$objData->limitvacancies = $limitvacancies;

	cicleinscription_save('ci_cicleorgan', $objData);

	/*  echo $OUTPUT->notification($sucessmsg, 'notifysuccess');
	 redirect($CFG->wwwroot."/mod/cicleinscription/organsinclice_list.php?coursemodulesid=".$cm->id); */
}
// EXCLUS�O DE ITEM
if($delete && $del_cicleorganid){
	try {
		// Remove cicleorgan
		$DB->delete_records('ci_cicleorgan', array('id'=>$del_cicleorganid));
		echo $OUTPUT->notification(get_string('msgremoveitemsucess', 'cicleinscription'), 'notifysuccess');
		redirect($CFG->wwwroot."/mod/cicleinscription/organsincicle_list.php?coursemodulesid=".$cm->id);
	} catch (Exception $e) {
		$OUTPUT->notification($e->getMessage());
	}
}

// Orgaos
$sort = "o.id ASC";
$objOrgans = cicleinscription_get_organ_added_on_cicle($cm->id);

$organcount = count(cicleinscription_get_organ_added_on_cicle($cm->id));

echo "<a href='{$CFG->wwwroot}/mod/cicleinscription/cicleorgan.php?coursemodulesid={$coursemodulesid}'> <img src='{$OUTPUT->pix_url('t/add')}' alt='Novo Registro' style='float: left; margin-right: 5px;' />".get_string('add', 'cicleinscription')." </a> ";
// Criando GRID de Orgaos
echo '<br />';
echo '<table width="77%" border="0" cellspacing="2" cellpadding="4" class="generaltable generalbox boxaligncenter"><tr>';
echo '<th class="header" scope="col">'.get_string('organplural', 'cicleinscription').'</th>';
echo '<th class="header" scope="col">'.get_string('limitvacancies', 'cicleinscription').'</th>';
echo '<th class="header" scope="col">'.get_string('actions', 'cicleinscription').'</th>';
echo '</tr>';
// percorrendo objetos da
foreach ($objOrgans as $org){
	$vlueLimitVacancies = $org->cicleorganslimitvacancies ? $org->cicleorganslimitvacancies : $org->organslimitvacancies;
	echo '<tr>';
	echo "<td>{$org->name}</td>";
	echo "<td align='center'>
	<div class='points'>
	<form method='post' action='#' class='quizsavegradesform'>
	<fieldset class='invisiblefieldset' style='display: block;'>
	<input type='hidden' name='cicleorganid' value='{$org->cicleorganid}'/>
	<input type='text' name='limitvacancies{$org->cicleorganid}' size='5' value='{$vlueLimitVacancies}' />
	<input type='submit' class='pointssubmitbutton' value='{$strchange}' />
	</fieldset>
	</form>
	</div>
	</td>";
	echo "<td align='center'>
	<a class='action-icon' href='organ.php?organid={$org->organid}&update=true&coursemodulesid={$cm->id}' >
	<img class='smallicon' src='{$OUTPUT->pix_url('t/edit')}' alt='{$strupdate}' />
	</a>
	<a class='action-icon' href='organsincicle_list.php?cicleorganid={$org->cicleorganid}&delete=true&coursemodulesid={$cm->id}' >
	<img class='smallicon' src='{$OUTPUT->pix_url('t/delete')}' alt='{$strdel}' />
	</a>
	</td>";
	echo '</tr>';
}
echo '</table>';
$strAllRecords = get_string('allrecords', 'cicleinscription');
$strRecords = get_string('records', 'cicleinscription');
echo "<br /><div style='width: 77%; margin: 0 auto; text-align: right;'><a href='$CFG->wwwroot/mod/cicleinscription/organ_report.php?coursemodulesid=$coursemodulesid&page=0&perpage=10' title='$strRecords'>{$strAllRecords}{$organcount}</a></div>";

echo $OUTPUT->footer();