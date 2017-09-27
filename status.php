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
 * Form organ for cicleinscription
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
require_once($CFG->dirroot.'/mod/cicleinscription/status_form.php');

// Verifica se o usuario esta logado
$errormsg = get_string('errormessageadd', 'cicleinscription');
$sucessmsg = get_string('sucessmessageadd', 'cicleinscription');

$coursemodulesid = optional_param('coursemodulesid',0, PARAM_INT);
$statusid = optional_param('statusid', 0, PARAM_INT);
$update = optional_param('update', 0, PARAM_BOOL);

if($coursemodulesid && $_GET){
	$cm         = get_coursemodule_from_id('cicleinscription', $coursemodulesid, 0, false, MUST_EXIST);
	$course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
	$cicleinscription  = $DB->get_record('cicleinscription', array('id' => $cm->instance), '*', MUST_EXIST);

	$_SESSION['coursemodulesid'] = $coursemodulesid;	#gravando sessao para redirecionar pagina

}
require_login($course, true, $cm);
// Context
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_url('/mod/cicleinscription/status.php', array('statusid' => $statusid));

$mform  = new mod_cicleinscription_status_form($CFG->wwwroot."/mod/cicleinscription/status.php?statusid={$statusid}&coursemodulesid={$cm->id}");

# Imprimindo cabecalho da pagina
echo $OUTPUT->header();

// Verifica se e uma requisicao de update
if ($statusid && $update) {
	$status = $DB->get_record('ci_status_prematriculation', array('id'=>$statusid));
	$mform->desableElement('name');
	$mform->set_data($status);
}elseif (!$mform->is_submitted() && count(cicleinscription_get_itemsTable('ci_status_prematriculation')) >= QTDE_MAX_STATUS ){
	echo $OUTPUT->notification(get_string('maxlimitexceeded', 'cicleinscription'));
	redirect($CFG->wwwroot."/mod/cicleinscription/status_report.php?coursemodulesid=".$cm->id.'&page=0&perpage=10');
}
// Gravando log
add_to_log($course->id, 'cicleinscription', 'status', "organ.php?coursemodulesid={$cm->id}", $cicleinscription->name, $cm->id);

$PAGE->set_url('/mod/cicleinscription/status.php', array('coursemodulesid' => $cm->id));
$PAGE->set_title(get_string('titleterm', 'cicleinscription'));
$PAGE->set_cacheable(false);


// Validando requisicao do form
if($mform->is_cancelled()){
	redirect($CFG->wwwroot);
}else if ($form_data = $mform->get_data()){

	$result = cicleinscription_save('ci_status_prematriculation', $form_data);
	if ($result){
		echo $OUTPUT->notification($sucessmsg, 'notifysuccess');
		$idCmRedirect = $_SESSION['coursemodulesid']; # Recuperando sessao
		unset($_SESSION['coursemodulesid']);
		redirect($CFG->wwwroot."/mod/cicleinscription/status_report.php?coursemodulesid=".$idCmRedirect.'&page=0&perpage=10');
	}
	else
	{
		echo $OUTPUT->notification($errormsg);
	}
}

echo $OUTPUT->heading(get_string('addorupdatestatus', 'cicleinscription'));
echo $mform->display();
echo $OUTPUT->footer();
