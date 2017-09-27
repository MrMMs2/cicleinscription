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
// Verifica se o usuario esta logado

$msgdel = get_string('msgremoveitemsucess', 'cicleinscription');

$blacklistid = optional_param('blacklistid', 0, PARAM_INT);
$coursemodulesid = optional_param('coursemodulesid',0, PARAM_INT);
$delete = optional_param('delete', 0, PARAM_BOOL);

if($coursemodulesid){
	$cm         = get_coursemodule_from_id('cicleinscription', $coursemodulesid, 0, false, MUST_EXIST);
	$course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
	$cicleinscription  = $DB->get_record('cicleinscription', array('id' => $cm->instance), '*', MUST_EXIST);

}
require_login($course, true, $cm);
$PAGE->set_url('/mod/cicleinscription/blacklistdelete.php', array('blacklistid' => $blacklistid));
// Gravando log
add_to_log($course->id, 'cicleinscription', 'blacklistdelete', "blacklistdelete.php?coursemodulesid={$cm->id}&page=0&perpage=10&blacklistid={$blacklistid}", $cicleinscription->name, $cm->id);

if (! $blacklist = $DB->get_record('ci_blacklist', array('id'=> $blacklistid))) {
        error(get_string('detailnotfound', 'cicleinscription'));
}

// Removendo blacklist
if($delete){
	echo $OUTPUT->header();
	echo $OUTPUT->heading(get_string('exclusionregistry', 'cicleinscription'));
	echo $OUTPUT->confirm(
		get_string("msgconfirm", "cicleinscription"), "blacklistdelete.php?blacklistid={$blacklistid}&coursemodulesid={$cm->id}&delete=false", "blacklist_report.php?coursemodulesid={$coursemodulesid}&page=0&perpage=10"
	);
	$_SESSION['coursemodulesid'] = $coursemodulesid;
	echo $OUTPUT->footer();
	
	die();
}
 
// Remove blaklist
$objBlacklist = new stdClass();
$objBlacklist->id = $blacklistid;
$objBlacklist->datetimeoutputblacklist = strtotime('now');
$objBlacklist->statusblacklist = 'n';

cicleinscription_save('ci_blacklist', $objBlacklist);

// Recuperando variavel $coursemodulesid ;
$coursemodulesid = $_SESSION['coursemodulesid'];
unset($_SESSION['coursemodulesid']);

echo $OUTPUT->notification($msgdel, 'notifysuccess');
redirect($CFG->wwwroot."/mod/cicleinscription/blacklist_report.php?coursemodulesid={$coursemodulesid}&page=0&perpage=20");

