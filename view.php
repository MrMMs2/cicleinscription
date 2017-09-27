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
 * Prints a particular instance of cicleinscription
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod
 * @subpackage cicleinscription
 * @copyright  2013 CEAJUD | CNJ | Tecninys
 * @author	   Leo Santos
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/// (Replace cicleinscription with the name of your module and remove this line)

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/validation.php');
require_once($CFG->dirroot.'/mod/cicleinscription/prematriculation_form.php');

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$v = optional_param('v', 0, PARAM_BOOL);
$errormsg = get_string('errormessageadd', 'cicleinscription');
if ($id) {
    $cm         = get_coursemodule_from_id('cicleinscription', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $cicleinscription  = $DB->get_record('cicleinscription', array('id' => $cm->instance), '*', MUST_EXIST);
} else {
    error(get_string('errornotfoundidcourse', 'cicleinscription'));
}
//require_login($course, false, $cm);

// Context
$context = context_module::instance($cm->id);
$PAGE->set_context($context);
$PAGE->set_cm($cm);

add_to_log($course->id, 'cicleinscription', 'view', "view.php?id={$cm->id}", $cicleinscription->name, $cm->id);

/// Print the page header
/* $PAGE->set_pagetype('site-index');
$PAGE->set_pagelayout('frontpage'); */
$PAGE->set_url('/mod/cicleinscription/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($cicleinscription->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->requires->js(new moodle_url($CFG->wwwroot.'/mod/cicleinscription/assets/js/query-1.10.2.min.js'));
$PAGE->requires->js(new moodle_url($CFG->wwwroot.'/mod/cicleinscription/assets/js/jquery.maskedinput.js'));
$PAGE->requires->js(new moodle_url($CFG->wwwroot.'/mod/cicleinscription/assets/js/jquery.pstrength-min.1.2.js'));
$PAGE->requires->js(new moodle_url($CFG->wwwroot.'/mod/cicleinscription/assets/js/validations.js'));
$PAGE->requires->js(new moodle_url($CFG->wwwroot.'/mod/cicleinscription/assets/js/jquery-ui.js'));

# Configurações do módulo - Visivel apenas para administradores
if(is_siteadmin($USER->id) && !$v){
	redirect($CFG->wwwroot.'/mod/cicleinscription/course_prematriculation_list.php?coursemodulesid='.$cm->id);
	//cicleinscription_add_navigation($cm->id);
}else if(!$cm->visible){
	echo $OUTPUT->header();
	 // Titulo do formulario
	echo $OUTPUT->heading(get_string('registrationform', 'cicleinscription', $cicleinscription));
	echo $OUTPUT->notification(get_string('unavailable', 'cicleinscription'));
	echo html_writer::link($CFG->wwwroot, get_string('continue'), array('class'=> 'btn btn-info'));
	echo $OUTPUT->footer();
	die();
}
// Output starts here
echo $OUTPUT->header();

// Verificando o tipo de formulario que devera ser carregado
$mform = new mod_cicleinscription_prematriculation_form($CFG->wwwroot.'/mod/cicleinscription/view.php?id='.$cm->id);
switch ($cicleinscription->typeform){
	case 'opencourses':
		$mform->form_opencourses();
		break;
	case 'cicle':
		$mform->form_cicle();
		break;
}

// Iniciando formulario de inscricao
//$mform = new mod_cicleinscription_prematriculation_form($CFG->wwwroot.'/mod/cicleinscription/view.php?id='.$cm->id);

## ## ### RECEBENDO DADOS DO FORMULARIO DE INSCRICAO ## ## ##
if($mform->is_cancelled()){
	redirect($CFG->wwwroot);
}else if ($dataform = $mform->get_data()){
	// Inicializando variaveis
	$msgemailvalidation = get_string('msgemailvalidation', 'cicleinscription');
	$msgusernamevalidation = get_string('msgcpfvalidation', 'cicleinscription');
	require_once($CFG->dirroot.'/mod/cicleinscription/validation.php');

	// Validacao 1º nivel
	$strnotify = cicleinscription_validaCPF($dataform->username) ? null : $msgusernamevalidation;
	$strnotify .= validate_email($dataform->email) ? null : $msgemailvalidation;
	
	if($strnotify){
		echo $OUTPUT->notification($strnotify);
	}else {
		#echo "<pre>"; var_dump(); echo "</pre>"; die();
		# Inicializando atribustos do objeto prematricula
		$dataform->username = str_pad(preg_replace('/[^0-9]/', '', $dataform->username), 11, '0', STR_PAD_LEFT); # Retirando pontos e traços do CPF
		$password = $dataform->password;
		$dataform->password = md5($dataform->password); # Fazendo hash md5 da senha
		$dataform->cicleinscriptionid = $cicleinscription->id;
		$ci_prematriculation = 'ci_prematriculation';
		// Validando 2º Nivel
		if(cicleinscription_verify_username_blacklist($dataform->username)){	// BLACKLIST
			// Grava Usuario com status 2, envia mensagem informando que esta na blacklist e apresenta mensagem.
			$dataform->status_prematriculationid = 2;
			$dataform->note = cicleinscription_get_status_prematriculation($dataform->status_prematriculationid)->description;
			
			# Verificando se o candidato ja possui pre-matricula com esse status
			if(!cicleinscription_verify_prematriculation_by_cicle_and_status($dataform->username, $dataform->status_prematriculationid, $cicleinscription->id)){
				# Salvando registro
				if(!cicleinscription_save($ci_prematriculation, $dataform)){
					echo $OUTPUT->notification($errormsg); // Erro ao gravar dados
				}
			}
			echo $OUTPUT->notification(get_string('notitydoplicatedregisterblacklist', 'cicleinscription', $dataform));
			echo "<meta http-equiv='refresh' content='8;url='.$CFG->wwwroot.'/mod/cicleinscription/view.php?id='.$cm->id' />";
			
		}else if(!cicleinscription_verify_limit_vacancies_by_organ($dataform->organid, $cm->id, $cicleinscription->id)){ // LIMITEXCEEDED
			// Grava Usuario com status 4, envia mensagem informando que o limite de pessoas por tribunal foi excedido.
			$dataform->status_prematriculationid = 4;
			$dataform->note = cicleinscription_get_status_prematriculation($dataform->status_prematriculationid)->description;
			
			# Verificando se o candidato ja possui pre-matricula com esse status
			if(!cicleinscription_verify_prematriculation_by_cicle_and_status($dataform->username, $dataform->status_prematriculationid, $cicleinscription->id)){
				# Salvando registro
				if(!cicleinscription_save($ci_prematriculation, $dataform)){
					echo $OUTPUT->notification($errormsg); // Erro ao gravar dados
				}
			}
			$fullname = cicleinscription_get_courses_by_cicle_and_courseid($dataform->course_prematriculationid, $cicleinscription->id)->fullname;
			$objMsgLimitExceeded = new stdClass();
			$objMsgLimitExceeded->fullname = $fullname;
			$objMsgLimitExceeded-> firstname = $dataform->firstname;
			
			echo $OUTPUT->notification(get_string('notitydoplicatedregisterlimitexceeded', 'cicleinscription', $objMsgLimitExceeded));
			echo "<meta http-equiv='refresh' content='8;url='.$CFG->wwwroot.'/mod/cicleinscription/view.php?id='.$cm->id' />";
			
		}else if($objEnrolled = cicleinscription_verify_user_registered_in_course_of_cicle($dataform->username, $cicleinscription->id)){ // ENROLLED
			// Grava Usuario  com status 3, envia mensagem informando que o candidato ja esta cadastrado num curso para aquele ciclo.
			$dataform->status_prematriculationid = 3;
			$dataform->note = cicleinscription_get_status_prematriculation($dataform->status_prematriculationid)->description;
			
			if(!cicleinscription_save($ci_prematriculation, $dataform)){
				echo $OUTPUT->notification($errormsg); // Erro ao gravar dados
			}
			
			$fullname = cicleinscription_get_courses_by_cicle_and_courseid($objEnrolled->courseid, $cicleinscription->id)->fullname;
			$objMsgEnrolled = new stdClass();
			$objMsgEnrolled->fullname = $fullname;
			$objMsgEnrolled-> firstname = $dataform->firstname;
			
			echo $OUTPUT->notification(get_string('notitydoplicatedregisterenrolled', 'cicleinscription', $objMsgEnrolled));
			echo "<meta http-equiv='refresh' content='8;url='.$CFG->wwwroot.'/mod/cicleinscription/view.php?id='.$cm->id' />";
		}else{
			// Validando 3º Nivel
			// Verificando se o Usuario ja possui pre-matricula na lista de espera, ou seja, registro na pre-matricula com status 4
			$stt_waiting_list = 4;
			if($objStatus = cicleinscription_verify_prematriculation_by_cicle_and_status($dataform->username, $stt_waiting_list, $cicleinscription->id)){
				$fullname = cicleinscription_get_courses_by_cicle_and_courseid($objStatus->course_prematriculationid, $cicleinscription->id)->fullname;
				$objMsg = new stdClass();
				$objMsg->fullname = $fullname;
				$objMsg->firstname = $dataform->firstname;
				$objMsg->email = $dataform->email;
				
				echo $OUTPUT->notification(get_string('msgregisteredwaitinglist', 'cicleinscription', $objMsg));
				echo "<meta http-equiv='refresh' content='10;url='.$CFG->wwwroot.'/mod/cicleinscription/view.php?id='.$cm->id' />";
				// Encerra a inscricao para aquela pessoa
			}else {
				$dataform->status_prematriculationid = 1;
				$dataform->note = cicleinscription_get_status_prematriculation($dataform->status_prematriculationid)->description;
				
				# Verificando se o candidato ja possui pre-matricula com esse status.
				if($objPreMat = cicleinscription_verify_prematriculation_by_cicle_and_status($dataform->username, $dataform->status_prematriculationid, $cicleinscription->id)){
					$dataform->id = $objPreMat->id;		// Importante para identificar que o request sera para update
				}
				# Salvando / Alterando registro na tabela de pre-matricula
				if(!cicleinscription_save($ci_prematriculation, $dataform)){
					echo $OUTPUT->notification($errormsg); // Erro ao gravar dados
				}
					
				// Recuperando dados do perfil de Usuario e dados funcionais
				$objDataFormUser = new stdClass();
				$objDataFormUser->id = null;
				if($dataform->aim){ // Se ciclo verifica o preenchimento da matricula
					$objDataFormUser->aim = $dataform->aim;		// Matricula
				}
				if($dataform->department){ // Se ciclo verifica o preenchimento da unidade de locacao
					$objDataFormUser->department = $dataform->department;
				}
				if($dataform->phone1){ // Se ciclo verifica o preenchimento do telefone da unidade de lotacao
					$objDataFormUser->phone1 = $dataform->phone1;	
				}
				if($dataform->address){ // Se ciclo verifica o preenchimento da email funcional
					$objDataFormUser->address = $dataform->address;
				}
				$objDataFormUser->username = $dataform->username;
				$objDataFormUser->password = $dataform->password;
					
				// Validando 4º Nivel
				if($objUser = cicleinscription_verify_cadastre_user($dataform->username, $dataform->email)){
					$objDataFormUser->id = $objUser->id;		# Update username e password caso user ja exista
					$DB->update_record('user', $objDataFormUser);
				}else {
					// Novo Usuario
					$objDataFormUser->auth = 'manual';
					$objDataFormUser->email = $dataform->email;
					$objDataFormUser->firstname = $dataform->firstname;
					$objDataFormUser->lastname = $dataform->lastname;
					$objDataFormUser->city = $dataform->city;
					$objDataFormUser->country = $dataform->country;
					$objDataFormUser->confirmed = 1;
					$objDataFormUser->description = 'Apresente-se Aqui...';
					$objDataFormUser->mnethostid = 3;	// Em producao alterar para 3
					$objDataFormUser->lang = 'pt_br';
					$objDataFormUser->timecreated = strtotime('now');
				
					// Salvando usuario na tabela de usuarios moodle
					$objDataFormUser->id = cicleinscription_save('user', $objDataFormUser);
				}
					
				// Gravando dados para inscricoes de cursos abertos
				if ($cicleinscription->typeform == 'opencourses'){
					cicleinscription_generate_objParticipant_opencourse($dataform, $objDataFormUser->id);
				}
				
				// Efetuando matricula
				if(cicleinscription_enrolling_user_on_course($dataform->course_prematriculationid, $objDataFormUser->id)){
					// Matriculado! Agora, preparando email...
					
					$subject = cicleinscription_get_courses_by_cicle_and_courseid($dataform->course_prematriculationid, $cicleinscription->id)->fullname;
					
					// Verificando se e curso para vagas remanescente (lista de espera).
					list($sn_course, $sn_turma) = explode('_',  cicleinscription_get_courses_by_cicle_and_courseid($dataform->course_prematriculationid, $cicleinscription->id)->shortname);
					if(strtoupper($sn_turma) == STR_VAGAS_REMANESCENTES){
						// Enviando email de vagas remanescentes
						list($fn_course, $fn_turma) = explode('-', $subject);
						$messagetext = cicleinscription_generation_email_message_waiting_list($fn_course, $dataform->username, $password, $dataform->firstname);
						
						cicleinscription_send_email_message($dataform->email, $fn_course, $messagetext, $objDataFormUser->id);	// Executando envio
						
						$objMSGWaitingList = new stdClass();
						$objMSGWaitingList->fullname = $fn_course;
						$objMSGWaitingList->firstname = $dataform->firstname;
						$objMSGWaitingList->email = $dataform->email;
						
						
						echo $OUTPUT->notification(get_string('msgregisteredwaitinglist', 'cicleinscription', $objMSGWaitingList));
						echo "<meta http-equiv='refresh' content='10;url='.$CFG->wwwroot.'/mod/cicleinscription/view.php?id='.$cm->id' />";
					}else{
						// Enviando email de confirmacao
						$messagetext = cicleinscription_generation_email_message($subject, $dataform->username, $password, $dataform->firstname, $dataform->course_prematriculationid);	// $subject esta sendo passado como parametro por que � o nome do curso.
						cicleinscription_send_email_message($dataform->email, $subject, $messagetext, $objDataFormUser->id);
							
						echo $OUTPUT->notification(get_string('msgsucessenrollment', 'cicleinscription', $dataform), 'notifysuccess');
						echo "<meta http-equiv='refresh' content='8;url='.$CFG->wwwroot.'/mod/cicleinscription/view.php?id='.$cm->id' />";
					}
					
				}else {
					echo $OUTPUT->notification('Error');
				}
			}
		}
	}
}

### MONTANDO PAGINA ###

// Recuperando arquivo - Banner
$fs = get_file_storage();
$files = $fs->get_area_files(CONTEXTID, COMPONENT, FILE_AREA, $cicleinscription->banner, 'id ASC', false);
foreach ($files as $f) {
	echo "<div style='text-align: center; margin-bottom: 20px;'>";
	echo 	'<img src="'.PATH_BANNER.$cicleinscription->banner.$f->get_filepath().$f->get_filename().'" alt="Banner" style="box-shadow: 0 2px 5px 0 #4A4A4A; border: 1px solid #ccc; width:'.$cicleinscription->widthbanner.'; height:'.$cicleinscription->heightbanner.'" />';
	echo "</div>";
}

// Titulo do formulario
echo $OUTPUT->heading(get_string('registrationform', 'cicleinscription', $cicleinscription));

if ($cicleinscription->intro){ // Conditions to show the intro can change to look for own settings or whatever
	echo $OUTPUT->box($cicleinscription->intro, '', 'cicleinscriptionintro');
}
echo $mform->display();

// Finish the page
echo $OUTPUT->footer();

?>

<link rel="stylesheet" href="assets/css/jquery-ui.css" />
<link rel="stylesheet" href="assets/css/notification.css" />

 <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />

<!-- Conteudo do modal dialog -->
<div id="dialogCPF" title="Aviso">
	<p>CPF inv&aacute;lido! Por favor, digite seu CPF corretamente.</p>
</div>
<div id="dialogEmail" title="Aviso">
	<p>E-mail inv&aacute;lido! Por favor, digite seu e-mail corretamente.</p>
</div>
<div id="dialogFunctionalEmail" title="Aviso">
	<p>E-mail inv&aacute;lido! Por favor, digite o e-mail funcional corretamente. </p>
</div>
<div id="dialogConfirmEmail" title="Aviso">
	<p>Os campos Email e Confirmar e-mail n&atilde;o devem ter o mesmo valor.</p>
</div>

<!-- JS PAGINA de inscricao -->
<script type="text/javascript">

// Mascara e validacao dos campos
$(document).ready(
		
	function(){
		$('#cpf').mask('999.999.999-99'); // Verificar a necessidade de retirar os pontos e traço
		$('#datebirth').mask('99/99/9999'); // mascara data de nascimento
		$('.stockingunitphone').mask('(99) 9999-9999?9'); // mascara telefone
		
		$("#dialogCPF").hide();
		$("#dialogEmail").hide();
		$("#dialogFunctionalEmail").hide();
		$("#dialogConfirmEmail").hide();
		$('#id_password').pstrength(); // Senha forte
		
		$('#cpf').blur(
			function(){
				var cpf = $("#cpf").val();
				
				// validando CPF
				if(validarCPF(cpf) === false){
					chamarModal('#dialogCPF');
					$("#cpf").css("border", "1px solid #AA0000").focus();
				}else{
					$("#cpf").css("border", "1px solid #63B350");
				}
			}
		);

		$('#id_email').blur(
				function(){
					var email = $("#id_email").val();
					// validando CPF
					if(validarEmail(email) === false){
						chamarModal('#dialogEmail');
						$("#id_email").css("border", "1px solid #AA0000").focus();
					}
				}
			);
			
		// Valida email funcional
		$('.functionalemail').blur(
			function(){
				var functionalemail = $(".functionalemail").val();
				// validando CPF
				if(validarEmail(functionalemail) === false){
					chamarModal('#dialogFunctionalEmail');
					$(".functionalemail").css("border", "1px solid #AA0000").focus();
				}else{
					$(".functionalemail").css("border", "1px solid #63B350");
				}
			}
		);
		
		//Deixando o texto em Maiusculo
	   $(".toUpperCase").keyup(function() {
	        $(this).val($(this).val().toUpperCase());
	   });
	   // Deixando o texto em minusculo
	   $(".toLowerCase").keyup(function() {
	        $(this).val($(this).val().toLowerCase());
	   });

		/// Validando confirmacao de e-mail
		$('#id_confirmemail').blur(
			function(){
				if($('#id_email').val() !== $('#id_confirmemail').val()){
					chamarModal('#dialogConfirmEmail');
					$("#id_confirmemail").css("border", "1px solid #AA0000").focus();
				}else{
					$("#id_confirmemail").css("border", "1px solid #63B350");
					$("#id_email").css("border", "1px solid #63B350");
				}
			});

	// Mostrar e esconder campo deficiency do form_opencourses
	$("#fitem_id_deficiency").hide();
	 
	$("#id_deficient_yes").change(function(){
		  if($('#id_deficient_yes').is(':checked')){
			 $("#fitem_id_deficiency").show('fast');
		  }
	});// fim function
	
	$("#id_deficient_no").change(function(){
		 if($('#id_deficient_no').is(':checked')){
			 $("#fitem_id_deficiency").hide("fast");
		 }
	}); // fim function

	}
); 

function chamarModal(id){
	if(navigator.appName === "Microsoft Internet Explorer"){
		alert("Campos CPF ou e-mail não conferem. Favor verificar!");
	}else{
		$(id).dialog({
			width: 400,
			height: 100,
			modal: true,
			/*buttons: {
				"Fechar": function() {
					$( this ).dialog('close');
				}
			}*/
		});
	}
}

</script>