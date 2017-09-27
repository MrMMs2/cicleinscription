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

$msgdel = get_string('sucessmessagedel', 'cicleinscription');

$organid = optional_param('organid', 0, PARAM_INT);
$coursemodulesid = optional_param('coursemodulesid',0, PARAM_INT);
$delete = optional_param('delete', 0, PARAM_BOOL);

if($coursemodulesid){
	$cm         = get_coursemodule_from_id('cicleinscription', $coursemodulesid, 0, false, MUST_EXIST);
	$course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
	$cicleinscription  = $DB->get_record('cicleinscription', array('id' => $cm->instance), '*', MUST_EXIST);
	
}
require_login($course, true, $cm);
// Context
$PAGE->set_title(format_string($cicleinscription->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_url('/mod/cicleinscription/organdelete.php', array('organid' => $organid));

// Gravando log
add_to_log($course->id, 'cicleinscription', 'organdelete', "organ_report.php?coursemodulesid={$cm->id}&page=0&perpage=10&organid={$organid}", $cicleinscription->name, $cm->id);

if (! $organ = $DB->get_record('ci_organ', array('id'=> $organid))) {
        error(get_string('detailnotfound', 'cicleinscription'));
}

// Removendo organ
if($delete){
	echo $OUTPUT->header();
	echo $OUTPUT->heading(get_string('exclusionregistry', 'cicleinscription'));
	echo $OUTPUT->confirm(
		get_string("msgconfirm", "cicleinscription"), "organdelete.php?organid={$organid}&delete=false&coursemodulesid={$cm->id}", "organ_report.php?coursemodulesid={$coursemodulesid}&page=0&perpage=10"
	);
	$_SESSION['coursemodulesid'] = $coursemodulesid;
	echo $OUTPUT->footer();
	
	die();
}
 
// Remove organ
$DB->delete_records('ci_organ', array('id'=>$organid));

// Recuperando variavel $coursemodulesid ;
$coursemodulesid = $_SESSION['coursemodulesid'];
unset($_SESSION['coursemodulesid']);

echo $OUTPUT->notification($msgdel, 'notifysuccess');
redirect($CFG->wwwroot."/mod/cicleinscription/organ_report.php?coursemodulesid={$coursemodulesid}&page=0&perpage=10");

