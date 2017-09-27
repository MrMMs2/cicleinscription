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
* @author		Léo Renis Santos <leo.santos@cnj.jus.br>
* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

require('../../config.php');
require_once($CFG->dirroot.'/mod/cicleinscription/lib.php');
require_once($CFG->dirroot.'/mod/cicleinscription/organ_form.php');

// Verifica se o usuario esta logado

$errormsg = get_string('errormessageadd', 'cicleinscription');
$sucessmsg = get_string('sucessmessageadd', 'cicleinscription');

$coursemodulesid = optional_param('coursemodulesid',0, PARAM_INT);
$organid = optional_param('organid', 0, PARAM_INT);
$update = optional_param('update', 0, PARAM_BOOL);

if($coursemodulesid && $_GET){
	$cm         = get_coursemodule_from_id('cicleinscription', $coursemodulesid, 0, false, MUST_EXIST);
	$course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
	$cicleinscription  = $DB->get_record('cicleinscription', array('id' => $cm->instance), '*', MUST_EXIST);
	
	$_SESSION['coursemodulesid'] = $coursemodulesid;	#gravando sessao para redirecionar pagina
}

require_login($course, true, $cm);

$mform  = new mod_cicleinscription_organ_form($CFG->wwwroot.'/mod/cicleinscription/organ.php?organid='.$organid.'&coursemodulesid='.$cm->id);

// Edit
if ($organid && $update) {
	$organ = $DB->get_record('ci_organ', array('id'=>$organid));
	$mform->set_data($organ);
}

// Gravando log
add_to_log($course->id, 'cicleinscription', 'organ', "organ.php?coursemodulesid={$cm->id}", $cicleinscription->name, $cm->id);

$PAGE->set_url('/mod/cicleinscription/organ.php', array('coursemodulesid' => $cm->id));
//$PAGE->set_context($context);
$PAGE->set_title(get_string('titleterm', 'cicleinscription'));
$PAGE->set_cacheable(false);
$PAGE->navbar->add($cm->name.' - '.get_string('addorupdateorgan', 'cicleinscription'));

# Imprimindo cabeçalho da pagina
echo $OUTPUT->header();

// Validando requisiçao do form
if($mform->is_cancelled()){
	redirect($CFG->wwwroot);
}else if ($form_data = $mform->get_data()){
	//echo "<pre>"; var_dump($form_data); echo "</pre>";die();
	$result = cicleinscription_save('ci_organ', $form_data);
	if ($result){
		echo $OUTPUT->notification($sucessmsg, 'notifysuccess');
		$idCmRedirect = $_SESSION['coursemodulesid']; # Recuperando sessao
		unset($_SESSION['coursemodulesid']);
		redirect($CFG->wwwroot."/mod/cicleinscription/organ_report.php?coursemodulesid=".$idCmRedirect.'&page=0&perpage=20');
	}else{
		echo $OUTPUT->notification($errormsg);
	}
}


echo $OUTPUT->heading(get_string('addorupdateorgan', 'cicleinscription'));
echo $mform->display();
echo $OUTPUT->footer();

