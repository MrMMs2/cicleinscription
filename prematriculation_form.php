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
class mod_cicleinscription_prematriculation_form extends moodleform {

	/**
	 * Defines forms elements
	 */
	public function definition() {
	
		global $CFG;
		$mform = $this->_form;
	}
	
	/**
	 *  Formulario para cadastro de ciclo
	 * */
	public function form_cicle() {
		
		global $CFG;
		$mform = $this->_form;
		
		$coursemodulesid = optional_param('id', 0, PARAM_INT);
		# Lista de orgaos
		$organs = array();
		$dataObject = cicleinscription_get_organ_added_on_cicle($coursemodulesid);
		$organs[''] = get_string('select', 'cicleinscription');
		foreach ($dataObject as $obj){
			$organs[$obj->organid] = $obj->name;
		}
		# Lista de cursos disponiveis para o ciclo
		$courses = array();
		$courses = $this->get_courses_combobox($coursemodulesid);
		
		// Adding the "general" fieldset, where all the common settings are showed
		$mform->addElement('header', 'general', get_string('general', 'form'));
		
		// Adicionando organ
		$mform->addElement('select', 'organid', get_string('organ', 'cicleinscription'),$organs, array('style'=>'width: 300px'));
		$mform->addRule('organid', null, 'required', null, 'client');
		$mform->setType('organid', PARAM_INT);
		$mform->addHelpButton('organid', 'organhelp', 'cicleinscription');
		
		// status
		$mform->addElement('hidden', 'status_prematriculationid');
		$mform->setType('status_prematriculationid', PARAM_INT);
		
		// Adicionando course_prematriculationid
		$mform->addElement('select', 'course_prematriculationid', get_string('course'), $courses, array('style'=>'width: 300px'));
		$mform->addRule('course_prematriculationid', null, 'required', null, 'client');
		
		// cicleinscriptionid
		$mform->addElement('hidden', 'cicleinscriptionid');
		$mform->setType('cicleinscriptionid', PARAM_INT);
		
		// Adicionando momento de cadastro
		$mform->addElement('hidden', 'timeenrollment', strtotime('now'));
		$mform->setType('timeenrollment', PARAM_INT);
		
		// note
		$mform->addElement('hidden', 'note');
		$mform->setType('note', PARAM_TEXT);
		
		# Dados pessoais
		$mform->addElement('header', 'general', get_string('personaldata', 'cicleinscription'));
		
		// Adicionando username
		$mform->addElement('text', 'username', get_string('username', 'cicleinscription'), array('style'=>'width: 300px', 'id'=>'cpf'));
		$mform->addRule('username', null, 'required', null, 'client');
		
		// Adicionando password
		$mform->addElement('passwordunmask', 'password', get_string('password'), array('style'=>'width: 300px'));
		$mform->addHelpButton('password', 'password');
		$mform->addRule('password', null, 'required', null, 'client');
		
		// Adicionando firstname
		$mform->addElement('text', 'firstname', get_string('firstname'), array('class'=>'toUpperCase', 'style'=>'width: 300px'));
		$mform->addRule('firstname', null, 'required', null, 'client');
		
		// Adicionando lastname
		$mform->addElement('text', 'lastname', get_string('lastname'), array('class'=>'toUpperCase', 'style'=>'width: 300px'));
		$mform->addRule('lastname', null, 'required', null, 'client');
		
		// Adicionando email
		$mform->addElement('text', 'email', get_string('email', 'cicleinscription'), array('class'=> 'toLowerCase', 'style'=>'width: 300px'));
		$mform->addRule('email', null, 'required', null, 'client');
		
		// Adicionando confirmacao de email
		$mform->addElement('text', 'confirmemail', get_string('confirmemail', 'cicleinscription'), array('class'=> 'toLowerCase', 'style'=>'width: 300px'));
		$mform->addRule('confirmemail', null, 'required', null, 'client');
		
		// Adicionando city
		$mform->addElement('text', 'city', get_string('city'), array('style'=>'width: 300px', 'value'=>'Brasilia/DF'));
		$mform->addRule('city', null, 'required', null, 'client');
		
		// Adicionando country
		$mform->addElement('select', 'country', get_string('country'), array('BR'=>'Brasil'));
		
		// Adding the "general" fieldset, where all the common settings are showed
		$mform->addElement('header', 'general', get_string('functionaldata', 'cicleinscription'));
		
		// Adicionando departamento
		$mform->addElement('text', 'department', get_string('stockingunit', 'cicleinscription'), array('class'=>'toUpperCase', 'style'=>'width: 300px'));
		$mform->addRule('department', null, 'required', null, 'client');
		
		// Adicionando telefone unidade de lotacao
		$mform->addElement('text', 'phone1', get_string('stockingunitphone', 'cicleinscription'), array('class'=>'stockingunitphone', 'style'=>'width: 300px'));
		$mform->addRule('phone1', null, 'required', null, 'client');
		
		// Adicionando aim (matricula)
		$mform->addElement('text', 'aim', get_string('matriculation', 'cicleinscription'), array('class'=>'toUpperCase', 'style'=>'width: 300px'));
		$mform->addRule('aim', null, 'required', null, 'client');
		
		// Adicionando email funcional
		$mform->addElement('text', 'address', get_string('functionalemail', 'cicleinscription'), array('class'=>'functionalemail toLowerCase', 'style'=>'width: 300px'));
		$mform->addRule('address', null, 'required', null, 'client');
		
		// Recuperando conteudo do termo de responsabilidade
		$cicleinscription = cicleinscription_get_term_responsability($coursemodulesid);
		$titleterm = $cicleinscription->titleterm ?  $cicleinscription->titleterm : get_string('term', 'cicleinscription');
		# Dados pessoais
		$mform->addElement('header', 'general', $titleterm);
		$mform->addElement('html', "<div style='overflow: auto; max-height:400px; background-color: #FFF; padding:10px; border: 1px solid #EAEAEA; border-radius: 5px;'>".$cicleinscription->descriptionterm.'</div>');
		
		# termresponse
		$mform->addElement('checkbox', 'termresponse', get_string('agree', 'cicleinscription'));
		$mform->addRule('termresponse', null, 'required', null, 'client');
		
		// adicionando botoes
		$submitlabel = get_string('saveinscription', 'cicleinscription');
		$buttonarray = array();
		$buttonarray[] = &$mform->createElement('submit', 'submitbutton', $submitlabel);
		$buttonarray[] = &$mform->createElement('cancel');
		$mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
		$mform->closeHeaderBefore('buttonar');
	}
	
	
	/**
	 * Formulario para cursos abertos
	 * */
	public function form_opencourses() {
		global $CFG;
		$mform = $this->_form;
		
		$coursemodulesid = optional_param('id', 0, PARAM_INT);
		# Lista de orgaos
		$organs = array();
		$dataObject = cicleinscription_get_organ_added_on_cicle($coursemodulesid);
		$organs[''] = get_string('select', 'cicleinscription');
		foreach ($dataObject as $obj){
			$organs[$obj->organid] = $obj->name;
		}
		# Lista de cursos disponiveis para o ciclo
		$courses = array();
		$courses = $this->get_courses_combobox($coursemodulesid);
		
		// Adding the "general" fieldset, where all the common settings are showed
		$mform->addElement('header', 'general', get_string('general', 'form'));
		
		// Adicionando organ
		$mform->addElement('select', 'organid', get_string('organ', 'cicleinscription'),$organs, array('style'=>'width: 300px'));
		$mform->addRule('organid', null, 'required', null, 'client');
		$mform->setType('organid', PARAM_INT);
		$mform->addHelpButton('organid', 'organhelp', 'cicleinscription');
		
		// status
		$mform->addElement('hidden', 'status_prematriculationid');
		$mform->setType('status_prematriculationid', PARAM_INT);
		
		// Adicionando course_prematriculationid
		$mform->addElement('select', 'course_prematriculationid', get_string('course'), $courses, array('style'=>'width: 300px'));
		$mform->addRule('course_prematriculationid', null, 'required', null, 'client');
		
		// cicleinscriptionid
		$mform->addElement('hidden', 'cicleinscriptionid');
		$mform->setType('cicleinscriptionid', PARAM_INT);
		
		// Adicionando momento de cadastro
		$mform->addElement('hidden', 'timeenrollment', strtotime('now'));
		$mform->setType('timeenrollment', PARAM_INT);
		
		// note
		$mform->addElement('hidden', 'note');
		$mform->setType('note', PARAM_TEXT);
		
		# Dados pessoais
		$mform->addElement('header', 'general', get_string('personaldata', 'cicleinscription'));
		
		// Adicionando username
		$mform->addElement('text', 'username', get_string('username', 'cicleinscription'), array('style'=>'width: 300px', 'id'=>'cpf'));
		$mform->addRule('username', null, 'required', null, 'client');
		
		// Adicionando password
		$mform->addElement('passwordunmask', 'password', get_string('password'), array('style'=>'width: 300px'));
		$mform->addHelpButton('password', 'password');
		$mform->addRule('password', null, 'required', null, 'client');
		
		// Adicionando firstname
		$mform->addElement('text', 'firstname', get_string('firstname'), array('class'=> 'toUpperCase', 'style'=>'width: 300px'));
		$mform->addRule('firstname', null, 'required', null, 'client');
		
		// Adicionando lastname
		$mform->addElement('text', 'lastname', get_string('lastname'), array('class'=> 'toUpperCase', 'style'=>'width: 300px'));
		$mform->addRule('lastname', null, 'required', null, 'client');
		
		// Adicionando email
		$mform->addElement('text', 'email', get_string('email', 'cicleinscription'), array('class'=> 'toLowerCase', 'style'=>'width: 300px'));
		$mform->addRule('email', null, 'required', null, 'client');
		
		// Adicionando confirmação de email
		$mform->addElement('text', 'confirmemail', get_string('confirmemail', 'cicleinscription'), array('class'=> 'toLowerCase', 'style'=>'width: 300px'));
		$mform->addRule('confirmemail', null, 'required', null, 'client');
		
		// Adicionando data de nascimento
		$mform->addElement('text', 'datebirth', get_string('datebirth','cicleinscription'), array('style'=>'width: 300px','id'=>'datebirth'));
		$mform->addRule('datebirth', null, 'required', null, 'client');
		
		// Adicionando civilstate
		$civilstate = array();
		$civilstate[] =& $mform->createElement('radio', 'civilstate','', get_string('unmarried','cicleinscription'), 'unmarried');
		$civilstate[] =& $mform->createElement('radio', 'civilstate', '', get_string('married','cicleinscription'), 'married');
		$civilstate[] =& $mform->createElement('radio', 'civilstate','', get_string('widower','cicleinscription'), 'widower');
		$civilstate[] =& $mform->createElement('radio', 'civilstate','', get_string('divorced','cicleinscription'), 'divorced');
		$mform->addGroup($civilstate, 'civilstate', get_string('civilstate','cicleinscription'), array(' '), false);
		$mform->setDefault('civilstate', 'unmarried');
		
		// Adicionando civilstate
		$sex = array();
		$sex[] =& $mform->createElement('radio', 'sex','', get_string('masculine','cicleinscription'), 'masculine');
		$sex[] =& $mform->createElement('radio', 'sex', '', get_string('female','cicleinscription'), 'female');
		$mform->addGroup($sex, 'sex', get_string('sex','cicleinscription'), array(' '), false);
		$mform->setDefault('sex', 'masculine');
		
		// Adicionando campo race
		$race = array();
		$race[] =& $mform->createElement('radio', 'race','', get_string('white','cicleinscription'), 'white');
		$race[] =& $mform->createElement('radio', 'race','', get_string('yellow','cicleinscription'), 'yellow');
		$race[] =& $mform->createElement('radio', 'race','', get_string('black','cicleinscription'), 'black');
		$race[] =& $mform->createElement('radio', 'race','', get_string('brown','cicleinscription'), 'brown');
		$race[] =& $mform->createElement('radio', 'race','', get_string('indigenous','cicleinscription'), 'indigenous');
		$race[] =& $mform->createElement('radio', 'race','', get_string('unreported','cicleinscription'), 'unreported');
		$mform->addGroup($race, 'race', get_string('race','cicleinscription'), array(' '), false);
		$mform->setDefault('race', 'white');
		
		
		// Adicionando campo rolefamily
		$rolefamily = array();
		$rolefamily[] =& $mform->createElement('radio', 'rolefamily','', get_string('householder','cicleinscription'), 'householder');
		$rolefamily[] =& $mform->createElement('radio', 'rolefamily','', get_string('composedincome','cicleinscription'), 'composedincome');
		$rolefamily[] =& $mform->createElement('radio', 'rolefamily','', get_string('dependent','cicleinscription'), 'dependent');
		$mform->addGroup($rolefamily, 'householder', get_string('rolefamily','cicleinscription'), array(' '), false);
		$mform->setDefault('rolefamily', 'householder');
		
		// Adicionando campo deficient
		$deficient = array();
		$deficient[] =& $mform->createElement('radio', 'deficient','', get_string('yes'), 'yes');
		$deficient[] =& $mform->createElement('radio', 'deficient','', get_string('no'), 'no');
		$mform->addGroup($deficient, 'deficient', get_string('deficient','cicleinscription'), array(' '), false);
		$mform->setDefault('deficient', 'no');
		
		// Adicionando campo deficiency
		$mform->addElement('text', 'deficiency', get_string('deficiency','cicleinscription'), array('style'=>'width: 300px'));
		
		// Adicionando campo incomefamily
		$income = array(
				''=> get_string('select', 'cicleinscription'),
				'semRenda'=>'Sem renda',
				'ate2Mil'=>'Até 2 mil', 
				'2Mila3Mil'=>'2 mil a 3 mil', 
				'3Mila4Mil' => '3 mil a 4 mil', 
				'4mila7mil'=>'4 mil a 7 mil', 
				'AcimaDe7Mil'=>'Acima de 7 mil'
				);
		$mform->addElement('select', 'incomefamily', get_string('incomefamily','cicleinscription'), $income , array('style'=>'width: 300px'));
		$mform->addRule('incomefamily', null, 'required', null, 'client');
		
		// Adicionando campo schooling
		$schooling = array(
				''=> get_string('select', 'cicleinscription'),
				'fundamental'=>'Ensino Fundamental',
				'medio'=>'Através Ensino Médio',
				'superior'=>'Ensino Superior',
				'pos-graduacao'=>'Pós-Graudação',
				'mestrado'=>'Mestrado',
				'Doutorado'=>'Doutorado',
				'pos-doutorado'=>'Pós-Doutorado'
				);
		$mform->addElement('select', 'schooling', get_string('schooling','cicleinscription'), $schooling , array('style'=>'width: 300px'));
		$mform->addRule('schooling', null, 'required', null, 'client');
		
		// Adicionando campo howdid
		$howdid = array(
				''=> get_string('select', 'cicleinscription'),
				'portalcnj'=>'Através do Portal CNJ',
				'facebook'=>'Através do Facebook',
				'twitter'=>'Através do Twitter',
				'email'=>'Através do E-mail',
				'indicacao'=>'Por indicação de amigos',
				'outros'=>'Por outro meio'
				);
		$mform->addElement('select', 'howdid', get_string('howdid','cicleinscription'), $howdid , array('style'=>'width: 300px'));
		$mform->addRule('howdid', null, 'required', null, 'client');
		
		// Adicionando campo region
		$region = array(
				''=> get_string('select', 'cicleinscription'),
				'norte'=>'Região Norte',
				'nordeste'=>'Região Nordeste',
				'sudeste'=>'Região Sudeste',
				'sul'=>'Região Sul',
				'centro-oeste'=>'Região Centro-Oeste',
				);
		
		$mform->addElement('select', 'region', get_string('region','cicleinscription'), $region , array('style'=>'width: 300px'));
		$mform->addRule('region', null, 'required', null, 'client');
		
		
		// Adicionando campo estado
		$state = array(
				''=> get_string('select', 'cicleinscription'),
				'AC'=>'Acre',
				'AL'=>'Alagoas',
				'AP'=>'Amapá',
				'AM'=>'Amazonas',
				'BA'=>'Bahia',
				'CE'=>'Ceará',
				'DF'=>'Distrito Federal',
				'ES'=>'Espírito Santo',
				'GO'=>'Goiás',
				'MA'=>'Maranhão',
				'MT'=>'Mato Grosso',
				'MS'=>'Mato Grosso do Sul',
				'MG'=>'Minas Gerais',
				'PR'=>'Paraná',
				'PB'=>'Paraíba',
				'PA'=>'Pará',
				'PE'=>'Pernambuco',
				'PI'=>'Piauí',
				'RJ'=>'Rio de Janeiro',
				'RN'=>'Rio Grande do Norte',
				'RS'=>'Rio Grande do Sul',
				'RO'=>'Rondonia',
				'RR'=>'Roraima',
				'SC'=>'Santa Catarina',
				'SE'=>'Sergipe',
				'SP'=>'São Paulo',
				'TO'=>'Tocantins',
		);
		
		$mform->addElement('select', 'state', get_string('state','cicleinscription'), $state , array('style'=>'width: 300px'));
		$mform->addRule('state', null, 'required', null, 'client');
		
		
		// Adicionando city
		$mform->addElement('text', 'city', get_string('city'), array('style'=>'width: 300px', 'value'=>'Brasilia/DF'));
		$mform->addRule('city', null, 'required', null, 'client');
		
		// Adicionando country
		$mform->addElement('select', 'country', get_string('country'), array('BR'=>'Brasil'));

		// Recuperando conteudo do termo de responsabilidade
		$cicleinscription = cicleinscription_get_term_responsability($coursemodulesid);
		$titleterm = $cicleinscription->titleterm ?  $cicleinscription->titleterm : get_string('term', 'cicleinscription');
		
		# Termo de responsabilidade
		$mform->addElement('header', 'general', $titleterm);
		$mform->addElement('html', "<div style='overflow: auto; max-height:400px; background-color: #FFF; padding:10px; border: 1px solid #EAEAEA; border-radius: 5px;'>".$cicleinscription->descriptionterm.'</div>');
		
		# termresponse
		$mform->addElement('checkbox', 'termresponse', get_string('agree', 'cicleinscription'));
		$mform->addRule('termresponse', null, 'required', null, 'client');
		
		// adicionando botões
		$submitlabel = get_string('saveinscription', 'cicleinscription');
		$buttonarray = array();
		$buttonarray[] = &$mform->createElement('submit', 'submitbutton', $submitlabel);
		$buttonarray[] = &$mform->createElement('cancel');
		$mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
		$mform->closeHeaderBefore('buttonar');
		
	}
	
	/**	
	 * Metodo responsavel por realizar logica de apresentacao dos cursos na combobox. 
	 * 	REGRA: 	A Combo deve apresentar apenas o nome do curso, sendo que o id da turma dever� ser dinamico, ou seja
	 *			quando a turma atingir o numero maximo de inscritos, as inscricoes dever�o ser feitas para outra turma do 
	 *			curso que esta sendo apresentado. 
	 *
	 * @param	$coursemodulesid
	 * @author	Leo Santos
	 * @return	Array $course
	 */
	
	public function get_courses_combobox($coursemodulesid){
		$dataObject = cicleinscription_get_courses_added_on_cicle($coursemodulesid);	# Recuperando turmas do ciclo
		
		$courses[''] = get_string('select', 'cicleinscription');
		$ncourse = null;
		foreach ($dataObject as $obj){
			
			list($sn_course, $sn_turma) = explode('_',  $obj->shortname);
			list($fn_course, $fn_turma) = explode('-',  $obj->fullname);	# Retirando identifica��o da turma
			
			# Verificando quantidade de inscritos
			$number_enrolled = cicleinscription_get_number_enrolled_course($obj->courseid);
			$maxnumberstudents = $obj->numberstudents ? $obj->numberstudents : DEFAULT_NUMBER_OF_STUDENTS_BY_COURSE;
			
			if ($number_enrolled < $maxnumberstudents && $ncourse !== $sn_course){
				$courses[$obj->courseid] = $fn_course;
				
				$ncourse = $sn_course;
			}
		}
		return $courses;
	}
}