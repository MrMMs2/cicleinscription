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
require_once($CFG->dirroot.'/mod/cicleinscription/validation.php');

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
$PAGE->set_title(format_string($cicleinscription->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_url('/mod/cicleinscription/report_inscriptions.php', array('coursemodulesid' => $cm->id));

# Cabecalho
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('inscriptionsbyorgan', 'cicleinscription'));

// Gravando log
add_to_log($course->id, 'cicleinscription', 'organsinclice_list', "organsinclice_list.php.php?coursemodulesid={$cm->id}", $cicleinscription->name, $cm->id);

// Orgaos
$sort = "o.id ASC";
$objOrgans = cicleinscription_get_organ_added_on_cicle($cm->id);

$organcount = count(cicleinscription_get_organ_added_on_cicle($cm->id));

// Criando GRID de Orgaos
echo '<br />';
echo '<table class="generaltable generalbox table table-bordered table-striped"><tr>';
echo '<th class="header" scope="col">'.get_string('organ', 'cicleinscription').'</th>';
echo '<th class="header" scope="col">'.get_string('limitvacancies', 'cicleinscription').'</th>';
echo '<th class="header" scope="col">'.get_string('jobsfilled', 'cicleinscription').'</th>';
echo '<th class="header" scope="col">'.get_string('openjobs', 'cicleinscription').'</th>';
echo '</tr>';
// percorrendo objetos da
foreach ($objOrgans as $org){
	$inscriptionsByOrgan = cicleinscription_get_qtde_enrolld_in_organ_of_cicle($org->organid, $cicleinscription->id);
	$inscriptionsByOrgan = $inscriptionsByOrgan ? $inscriptionsByOrgan : 0;
	$vlueLimitVacancies = $org->cicleorganslimitvacancies ? $org->cicleorganslimitvacancies : $org->organslimitvacancies;
	echo '<tr>';
	echo "<td>{$org->name}</td>";
	echo "<td align='center' style='color: DarkOrange'>{$vlueLimitVacancies}</td>";
	echo "<td align='center' style='color: green'>
	<div class='points'>{$inscriptionsByOrgan->qtde}</div>
	</td>";
	echo "<td align='center' style='color: blue'>".((int) $vlueLimitVacancies - (int) $inscriptionsByOrgan->qtde)."</td>"; // Calculando vagas por orgao
	echo '</tr>';
}
echo '</table>';
$strAllRecords = get_string('allrecords', 'cicleinscription');
$strRecords = get_string('records', 'cicleinscription');
echo "<br /><div style='width: 90%; margin: 0 auto; text-align: right;'><a href='$CFG->wwwroot/mod/cicleinscription/organ_report.php?coursemodulesid=$coursemodulesid&page=0&perpage=10' title='$strRecords'>{$strAllRecords}{$organcount}</a></div>";

echo $OUTPUT->footer();