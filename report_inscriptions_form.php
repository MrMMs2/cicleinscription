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
 * Form organ_form for cicleinscription
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

if (!defined('MOODLE_INTERNAL')) {
	die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir.'/formslib.php');
require_once($CFG->dirroot.'/mod/cicleinscription/lib.php');
/**
 * Module instance settings form
*/
class mod_cicleinscription_report_inscriptions_form extends moodleform {

	/**
	 * Defines forms elements
	 */
	public function definition() {
		global $CFG;
		$mform = $this->_form;

		// Adding the "general" fieldset, where all the common settings are showed
		$mform->addElement('header', 'newfilter', get_string('newfilter', 'filters'));
		
		$coursemodulesid = optional_param('coursemodulesid', 0, PARAM_INT);
		
		// campo oculto
		$mform->addElement('hidden', 'cicleinscriptionid');
		$mform->setType('cicleinscriptionid', PARAM_RAW);
		
		# Lista de orgaos
		$organs = array();
		$dataObject = cicleinscription_get_organ_added_on_cicle($coursemodulesid);
		$organs[''] = get_string('select', 'cicleinscription');
		foreach ($dataObject as $obj){
			$organs[$obj->organid] = $obj->name;
		}

		// Recuperando lista de cursos para a combo
		# Lista de cursos
		$courses = array();
		$dataObject = cicleinscription_get_courses_added_on_cicle($coursemodulesid);
		$courses[''] = get_string('select', 'cicleinscription');
		foreach ($dataObject as $obj){
			$courses[$obj->cid] = $obj->fullname;
		}

		$mform->addElement('html', "<div id='error_message' style='text-align: center;' class='error'></div>");
		// Adding the standard "username" field
		$mform->addElement('text', 'username', get_string('username', 'cicleinscription'), array('size'=>'35', 'id'=>'username','style'=>'width: 41%'));
		if (!empty($CFG->formatstringstriptags)) {
			$mform->setType('username', PARAM_TEXT);
		} else {
			$mform->setType('username', PARAM_CLEAN);
		}
		
		$mform->addRule('username', get_string('maximumchars', '', 100), 'maxlength', 100, 'client');
		#$mform->addHelpButton('username', 'organhelp', 'cicleinscription');
		
		// Adicionando organ
		$mform->addElement('select', 'organid', get_string('organ', 'cicleinscription'),$organs, array('style'=>'width: 41%'));
		$mform->addHelpButton('organid', 'organhelp', 'cicleinscription');

		$mform->addElement('select', 'courseid',get_string('course'), $courses, array('style'=>'width: 41%'));


		// Add button
		$mform->addElement('submit', 'addfilter', get_string('addfilter','filters'));
		$coursemodulesid = optional_param('coursemodulesid', 0, PARAM_INT);
		$removefilter = get_string('removeall','filters');
		$mform->addElement('html', "<a href='{$CFG->wwwroot}/mod/cicleinscription/report_inscriptions.php?coursemodulesid={$coursemodulesid}&page=0&perpage=20'>{$removefilter}</a>");
	}
}