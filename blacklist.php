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
require_once($CFG->dirroot.'/mod/cicleinscription/blacklist_form.php');

$errormsg = get_string('errormessageadd', 'cicleinscription');
$errormsgduplicatedata = get_string('errormsgduplicatedata', 'cicleinscription');
$sucessmsg = get_string('sucessmessageadd', 'cicleinscription');

$coursemodulesid = optional_param('coursemodulesid',0, PARAM_INT);
$blacklistid = optional_param('blacklistid', 0, PARAM_INT);
$update = optional_param('update', 0, PARAM_BOOL);

if($coursemodulesid && $_GET){
	$cm         = get_coursemodule_from_id('cicleinscription', $coursemodulesid, 0, false, MUST_EXIST);
	$course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
	$cicleinscription  = $DB->get_record('cicleinscription', array('id' => $cm->instance), '*', MUST_EXIST);

	$_SESSION['coursemodulesid'] = $coursemodulesid;	#gravando sessao para redirecionar pagina

	// Gravando log
	add_to_log($course->id, 'cicleinscription', 'blacklist', "blacklist.php?coursemodulesid={$cm->id}", $cicleinscription->name, $cm->id);
}

require_login($course, true, $cm);
// Context
$PAGE->set_title(format_string($cicleinscription->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_url('/mod/cicleinscription/blacklist.php', array('coursemodulesid' => $cm->id));

$mform  = new mod_cicleinscription_blacklist_form($CFG->wwwroot.'/mod/cicleinscription/blacklist.php?coursemodulesid='.$cm->id);

# Imprimindo cabecalho da pagina
echo $OUTPUT->header();

if ($blacklistid && $update) {
	$blacklist = $DB->get_record('ci_blacklist', array('id'=>$blacklistid));
	$mform->set_data($blacklist);
}

$PAGE->set_title(get_string('titleterm', 'cicleinscription'));
$PAGE->set_cacheable(false);
$PAGE->requires->js(new moodle_url($CFG->wwwroot.'/mod/cicleinscription/assets/js/query-1.10.2.min.js'));
$PAGE->requires->js(new moodle_url($CFG->wwwroot.'/mod/cicleinscription/assets/js/jquery.maskedinput.js'));

// Validando requisicao do form
if($mform->is_cancelled()){
	redirect($CFG->wwwroot);
}else if ($form_data = $mform->get_data()){
	//echo "<pre>"; var_dump($form_data); echo "</pre>";die();
	$form_data->username =  preg_replace('#[^0-9]#', '', $form_data->username); # Retirando pontos e tracos do CPF
	
	// Verifica se registro ja existe
	$idCmRedirect = $_SESSION['coursemodulesid']; # Recuperando sessao
	unset($_SESSION['coursemodulesid']);
	
	if (!cicleinscription_verify_username_blacklist($form_data->username)) {
		// save
		$result = cicleinscription_save('ci_blacklist', $form_data);
		// if sucess
		if ($result){
			echo $OUTPUT->notification($sucessmsg, 'notifysuccess');
			redirect($CFG->wwwroot."/mod/cicleinscription/blacklist.php?coursemodulesid=".$idCmRedirect);
			#redirect($CFG->wwwroot."/mod/cicleinscription/blacklist.php?coursemodulesid=".$idCmRedirect);
		}else	// if error
		{
			echo $OUTPUT->notification($errormsg);
			redirect($CFG->wwwroot."/mod/cicleinscription/blacklist.php?coursemodulesid=".$idCmRedirect);
		}
	}else{
		echo $OUTPUT->notification($errormsgduplicatedata);
		redirect($CFG->wwwroot."/mod/cicleinscription/blacklist.php?coursemodulesid=".$idCmRedirect);
	}
}
	

echo $OUTPUT->heading(get_string('addorupdateblacklist', 'cicleinscription'));
echo $mform->display();
echo $OUTPUT->footer();

?>
<script type="text/javascript">
$(document).ready(
	function(){
		// Adicionando mascara no campo username
		$('#username').mask('999.999.999-99');
		// Iniciando requisicao ajax
		$('#username').blur(
			function(){
				if($('#username').val() === ""){
					$('#username').css("border", "1px solid red");
					return false;
				}else{
					// Passando tipo por parametro para a pagina ajax
					 $.ajax({
			              type: 'post',
			              url: '<?php echo $CFG->wwwroot; ?>/mod/cicleinscription/blacklist_ajax.php',
			              dataType : "json",
			              async : false,
			              data: {
			            	  username : $("#username").val()
			       	   },
			              success: function(data){
				              if(!data){
					             $('#error_message').text("CPF invalido ou nao pode ser localizado na base de dados.");
					             $('#error_message').css("padding-bottom", "5px").show();
					             $("#username").val('').css("border", "1px solid red");
					             $("#name").val('');
					              
				              }else{
				            	  $("#name").val(data.firstname + ' ' + data.lastname);
				            	  $("#userid").val(data.id);
				            	  $('#error_message').hide();
				            	  $("#username").css("border", "1px solid #6EB462");
				              }
			              }
			           });
	              }
		})
	});
</script>
