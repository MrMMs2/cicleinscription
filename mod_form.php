<?php

// This file is part of Moodle - http://moodle.org/
//
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
 * The main cicleinscription configuration form
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package    mod
 * @subpackage cicleinscription
 * @copyright  2013 CEAJUD | CNJ | Tecninys
 * @author	   Leo Santos
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');
include_once 'lib/moodlelib.php';
/**
 * Module instance settings form
 */
class mod_cicleinscription_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {

        $mform = $this->_form;

        //-------------------------------------------------------------------------------
        // Adding the "general" fieldset, where all the common settings are showed
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field
        $mform->addElement('text', 'name', get_string('cicleinscriptionname', 'cicleinscription'), array('size'=>'64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEAN);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 80), 'maxlength', 80, 'client');
        $mform->addHelpButton('name', 'cicleinscriptionname', 'cicleinscription');
        
        $mform->addElement('select', 'typeform', get_string('typeform', 'cicleinscription'), array('cicle' => 'Ciclo de Capacitação', 'opencourses' => 'Cursos Abertos'));
        
        // Adding the standard "intro" and "introformat" fields
        $this->add_intro_editor();
        
        // Adicionando momento de cadastro
        $mform->addElement('hidden', 'datetimecreation', strtotime('now'));
        $mform->setType('datetimecreation', PARAM_INT);
        
        // Adicionando campo oculto course
        $mform->addElement('hidden', 'course', optional_param('course', 0, PARAM_INT));
        $mform->setType('course', PARAM_INT);
        //------------------------------------------------------------------------------- Resp: Léo Santos
        // Adding fieldset settings banner
        $mform->addElement('header', 'bannersettingsfieldset', get_string('bannersettingsfieldset', 'cicleinscription'));
        $mform->addElement('filemanager', 'banner', get_string('filebanner', 'cicleinscription'), null,
        		array('maxbytes' => 1, 'accepted_types' => array('.jpg', '.png','jpeg', '.gif')));
        $mform->addHelpButton('banner', 'bannerbuttonhelp', 'cicleinscription');
        
        // Adding field widthbanner
        $mform->addElement('text', 'widthbanner', get_string('widthbanner', 'cicleinscription'), array('maxlength'=> 5, 'size' => 10));
        $mform->setType('widthbanner', PARAM_INT);
        $mform->addRule('widthbanner', get_string('maximumchars', '', 5), 'maxlength', 5, 'client');
		
        // Adding field widthbanner
        $mform->addElement('text', 'heightbanner', get_string('heightbanner', 'cicleinscription'), array('maxlength'=> 5, 'size' => 10));
        $mform->setType('heightbanner', PARAM_INT);
        $mform->addRule('heightbanner', get_string('maximumchars', '', 5), 'maxlength', 5, 'client');
        
        // Adding new fieldset for terms
        $mform->addElement('header', 'cicleinscription', get_string('addtermofcommitment', 'cicleinscription'));
		
        // Adding field nameterm
        $mform->addElement('text', 'nameterm', get_string('termofcommitmentname', 'cicleinscription'), array('maxlength'=> 100, 'size' => 40));
        $mform->setType('nameterm', PARAM_TEXT);
        $mform->addHelpButton('nameterm', 'termofcommitmentnamehelp', 'cicleinscription');
        $mform->addRule('nameterm', null, 'required', null, 'client');
        $mform->addRule('nameterm', get_string('maximumchars', '', 100), 'maxlength', 100, 'client');
        
        // Adding field nameterm
        $mform->addElement('text', 'titleterm', get_string('termofcommitmenttitle', 'cicleinscription'), array('maxlength'=> 100, 'size' => 40));
        $mform->setType('titleterm', PARAM_TEXT);
        $mform->addHelpButton('titleterm', 'termofcommitmenttitlehelp', 'cicleinscription');
        $mform->addRule('titleterm', get_string('maximumchars', '', 100), 'maxlength', 100, 'client');
        
        // Adding campo description
        $mform->addElement('htmleditor', 'descriptionterm',get_string('termofcommitmentdescription', 'cicleinscription'), array('cols'=>'85', 'rows'=>'15', 'wrap'=>'virtual'));
        $mform->setType('descriptionterm', PARAM_RAW);
        $mform->addRule('descriptionterm', null, 'required', null, 'client');
        $mform->addHelpButton('descriptionterm', 'termofcommitmentdescriptionhelp', 'cicleinscription');
        
        // Exemplo de como adicionar uma label
        // $mform->addElement('static', 'label2', 'cicleinscriptionsetting2', 'Your cicleinscription fields go here. Replace me!');

        //-------------------------------------------------------------------------------
        // add standard elements, common to all modules
        $this->standard_coursemodule_elements();
        //-------------------------------------------------------------------------------
        // add standard buttons, common to all modules
        $this->add_action_buttons();
    }
    
}
