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
class mod_cicleinscription_organ_form extends moodleform {

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
		$mform->addElement('text', 'name', get_string('organ', 'cicleinscription'), array('size'=>'45'));
		if (!empty($CFG->formatstringstriptags)) {
			$mform->setType('name', PARAM_TEXT);
		} else {
			$mform->setType('name', PARAM_CLEAN);
		}
		$mform->addRule('name', null, 'required', null, 'client');
		$mform->addRule('name', get_string('maximumchars', '', 100), 'maxlength', 100, 'client');
		$mform->addHelpButton('name', 'organhelp', 'cicleinscription');
		
		// Adicionando campo titulo
		$mform->addElement('text', 'acronym', get_string('acronym', 'cicleinscription'), array('size'=>'10'));
		$mform->addRule('acronym', null, 'required', null, 'client');
		$mform->setType('acronym', PARAM_TEXT);
		$mform->addRule('acronym', get_string('maximumchars', '', 45), 'maxlength', 45, 'client');
		$mform->addHelpButton('acronym', 'acronymhelp', 'cicleinscription');
		
		// Adicionando campo Limite de vagas
		$mform->addElement('text', 'limitvacancies', get_string('limitvacancies', 'cicleinscription'), array('size'=>'10', 'value'=>2));
		$mform->addRule('limitvacancies', null, 'required', null, 'client');
		$mform->setType('limitvacancies', PARAM_INT);
		$mform->addRule('limitvacancies', get_string('maximumchars', '', 45), 'maxlength', 45, 'client');
		$mform->addHelpButton('limitvacancies', 'limitvacancieshelp', 'cicleinscription');
		
		// add standard buttons, common to all modules
		$this->add_action_buttons();
	}
}