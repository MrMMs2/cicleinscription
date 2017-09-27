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
 * @author		L�o Renis Santos <leo.santos@cnj.jus.br>
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
class mod_cicleinscription_blacklist_form extends moodleform {

	/**
	 * Defines forms elements
	 */
	public function definition() {
		global $CFG;
		$mform = $this->_form;
		
		// Adding the "general" fieldset, where all the common settings are showed
		$mform->addElement('header', 'general', get_string('general', 'form'));
		
		$coursemodulesid = optional_param('coursemodulesid', 0, PARAM_INT);
		
		$mform->addElement('hidden', 'id');
		$mform->setType('id', PARAM_RAW);
		$mform->addElement('hidden', 'userid', null, array('id'=>'userid'));
		$mform->setType('userid', PARAM_RAW);
		
		// Recuperando lista de cursos para a combo
		# Lista de orgaos
		$courses = array();
		//$dataObject = cicleinscription_get_courses_all();
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
		$mform->addRule('username', null, 'required', null, 'client');
		$mform->addRule('username', get_string('maximumchars', '', 100), 'maxlength', 100, 'client');
		#$mform->addHelpButton('username', 'organhelp', 'cicleinscription');
		
		$mform->addElement('select', 'courseid',get_string('course'), $courses, array('style'=>'width: 41%'));
		$mform->addRule('courseid', null, 'required', null, 'client');
		
		// Adicionando momento de cadastro
		$mform->addElement('hidden', 'datetimeinput', strtotime('now'));
		$mform->setType('datetimeinput', PARAM_INT);
		
		// Adicionando campo tipo de entrada
		$mform->addElement('select', 'inputtype', get_string('inputtype','cicleinscription'), array('manual'=>'Manual'), array('style'=>'width: 41%'));
		$mform->addRule('inputtype', null, 'required', null, 'client');
		
		// Adicionando campo tipo de entrada
		$mform->addElement('select', 'statusblacklist', get_string('statusblacklist','cicleinscription'), array('s'=>'Ativo'), array('style'=>'width: 41%'));
		$mform->addRule('statusblacklist', null, 'required', null, 'client');
		
		# Name - Esse campo so vai apresentar o nome da pessoa na tela
		$mform->addElement('text', 'name', get_string('name'), array('size'=> '35', 'readonly'=>'readonly', 'id'=>'name', 'style'=>'width: 41%'));
		$mform->setType('name', PARAM_TEXT);
		
		// add standard buttons, common to all modules
		$this->add_action_buttons();
	}
}