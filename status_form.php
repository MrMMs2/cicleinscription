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
class mod_cicleinscription_status_form extends moodleform {

	/**
	 * Defines forms elements
	 */
	public function definition() {
		global $CFG;
		$mform = $this->_form;
		
		// Adding the "general" fieldset, where all the common settings are showed
		$mform->addElement('header', 'general', get_string('general', 'form'));
		
		$mform->addElement('hidden', 'id');
		$mform->setType('id', PARAM_RAW);
		
		// Adding the standard "name" field
		$mform->addElement('text', 'name', get_string('status', 'cicleinscription'), array('size'=>'45'));
		if (!empty($CFG->formatstringstriptags)) {
			$mform->setType('name', PARAM_TEXT);
		} else {
			$mform->setType('name', PARAM_CLEAN);
		}
		$mform->addRule('name', null, 'required', null, 'client');
		$mform->addRule('name', get_string('maximumchars', '', 100), 'maxlength', 100, 'client');
		$mform->addHelpButton('name', 'statushelp', 'cicleinscription');
		
		// Adicionando campo description
		$mform->addElement('htmleditor', 'description', get_string('description', 'cicleinscription'), array('cols'=>'85', 'rows'=>'10', 'wrap'=>'virtual'));
		$mform->addRule('description', null, 'required', null, 'client');
		$mform->setType('description', PARAM_TEXT);
		$mform->addRule('description', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
		$mform->addHelpButton('description', 'descriptionhelp', 'cicleinscription');
		
		// Adicionando emailmessage
		$mform->addElement('htmleditor', 'emailmessage', get_string('emailmessage', 'cicleinscription'), array('cols'=>'85', 'rows'=>'20', 'wrap'=>'virtual'));
		$mform->setType('descriptionterm', PARAM_RAW);
		$mform->addRule('emailmessage', null, 'required', null, 'client');
		$mform->addRule('emailmessage', get_string('maximumchars', '', 5000), 'maxlength', 5000, 'client');
		$mform->addHelpButton('emailmessage', 'emailmessagehelp', 'cicleinscription');
		
		// add standard buttons, common to all modules
		$this->add_action_buttons();
	}
	
	/**
	 * Metodo responsavel por desabilitar um elemento
	 * @param	$attribute
	 * @return	void
	 * @access	public
	 * @author	Leo Santos
	 */
	public function desableElement($attribute){
		global $CFG;
		$mform = $this->_form;
		$element = $mform->getElement($attribute);
		$element->setAttributes(array('name'=>$attribute,'readonly'=>'readonly'));
	}
}