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

$coursemodulesid = optional_param('coursemodulesid',0, PARAM_INT);
$update = optional_param('update', 0, PARAM_BOOL);

if ($coursemodulesid) {
	$cm         = get_coursemodule_from_id('cicleinscription', $coursemodulesid, 0, false, MUST_EXIST);
	$course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
	$cicleinscription  = $DB->get_record('cicleinscription', array('id' => $cm->instance), '*', MUST_EXIST);
} else {
	error(get_string('errornotfoundidcourse', 'cicleinscription'));
}

require_login($course, true, $cm);

// Gravando log
add_to_log($course->id, 'cicleinscription', 'extractionrecords', "blacklistdeleteexceeded.php?coursemodulesid={$cm->id}", $cicleinscription->name, $cm->id);

// Context
$PAGE->set_title(format_string($cicleinscription->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_url('/mod/cicleinscription/blacklistdeleteexceeded.php', array('coursemodulesid' => $cm->id));

# Imprimindo cabecalho da pagina
echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('removeexceededpenalties', 'cicleinscription'));

if($update){
	$obj = cicleinscription_get_count_blacklistexceeded();
	if($obj->total <= 0){
		echo "<div class='alert alert-info'><strong>ATENÇÃO! </strong>Não há registros na lista de penalidades com a data de entrada inferior a 1 ano. </div>";
	}else{
		echo $OUTPUT->confirm(
			get_string("msgconfirmremoveblacklist", "cicleinscription", $obj), "blacklistdeleteexceeded.php?coursemodulesid={$cm->id}&update=false", "course_prematriculation_list.php?coursemodulesid={$cm->id}"
		);
	}
	
	echo $OUTPUT->footer();
	
	die();
}

$objexceededs = cicleinscription_get_blacklistexceeded();
$i = 1;

$objBlacklist = new stdClass();
$objBlacklist->id = 0;
$objBlacklist->datetimeoutputblacklist = strtotime('now');
$objBlacklist->statusblacklist = 'n';

foreach($objexceededs as $obj){
	
	// Remove blaklist
	$objBlacklist->id = $obj->id;
	
	if($DB->update_record('ci_blacklist', $objBlacklist)){
		echo "<div class='alert alert-success'><strong>{$i}. Sucesso! </strong>Atualização realizada com sucesso para o participante com CPF n° <strong>".$obj->username." </strong> que estava registrado na lista de penalidades há {$obj->difference} ano(s). </div>";
	}else{
		echo "<div class='alert alert-danger'><strong>ERRO! </strong>Não foi possível completar a operação para o participante com CPF n° <strong>".$obj->username." </strong> que ainda está registrado na lista de penalidades. </div>";
	}
	$i ++;
}
echo $OUTPUT->footer();