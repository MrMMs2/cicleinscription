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
require_once($CFG->dirroot.'/mod/cicleinscription/blacklist_report_form.php');

$PAGE->requires->js(new moodle_url($CFG->wwwroot.'/mod/cicleinscription/assets/js/query-1.10.2.min.js'));
$PAGE->requires->js(new moodle_url($CFG->wwwroot.'/mod/cicleinscription/assets/js/jquery.maskedinput.js'));

// Recuperando parametros
$coursemodulesid = optional_param('coursemodulesid', 0, PARAM_INT); // course_module ID, or
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 0, PARAM_INT);

$url = new moodle_url('/mod/cicleinscription/blacklist_report.php', array('coursemodulesid'=>$coursemodulesid, 'page' => $page, 'perpage' => $perpage));

if ($coursemodulesid) {
	$cm         = get_coursemodule_from_id('cicleinscription', $coursemodulesid, 0, false, MUST_EXIST);
	$course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
	$cicleinscription  = $DB->get_record('cicleinscription', array('id' => $cm->instance), '*', MUST_EXIST);
} else {
	error(get_string('errornotfoundidcourse', 'cicleinscription'));
}
require_login($course, true, $cm);
// Context

$PAGE->set_title(format_string($cicleinscription->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_url('/mod/cicleinscription/blacklist_report.php', array('coursemodulesid' => $cm->id));

// Formulario de busca
$mform  = new mod_cicleinscription_blacklist_report_form($url);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('usersblacklist', 'cicleinscription'));
// Gravando log
add_to_log($course->id, 'cicleinscription', 'blacklist_report', "blacklist_report.php?coursemodulesid={$cm->id}&page=0&perpage=10", $cicleinscription->name, $cm->id);

// Validando requisicao do form
if($mform->is_cancelled()){
	redirect($CFG->wwwroot);
}else if ($form_data = $mform->get_data()){
	
	// Continuar daqui - Terminar o filtro
	$objBlacklist = cicleinscription_get_users_blacklist($page, $perpage, cicleinscription_validaCPF($form_data->username),$form_data->courseid, $form_data->inputtype, $form_data->statusblacklist);
	
	// Recuperando a quantidade com os parametros acima
	$blacklistcount = count(cicleinscription_get_users_blacklist(null, null, cicleinscription_validaCPF($form_data->username),$form_data->courseid, $form_data->inputtype, $form_data->statusblacklist));
}else {
	$objBlacklist = cicleinscription_get_users_blacklist($page, $perpage, null, null, 'manual', 's');
	
	// Recuperando a quantidade com os parametros acima
	$blacklistcount = count(cicleinscription_get_users_blacklist(null, null, null, null, 'manual', 's'));
}

// variaveis do cabecalho da tabela
$struserfullname = get_string('fullname');
$stremail = get_string('email');
$strusername = get_string('username');
$strdatetimeinput = get_string('datetimeinput','cicleinscription');
$strinputtype = get_string('inputtype','cicleinscription');
$strcoursefullname = get_string('course');
$strshortname = get_string('shortname');
$strblacklistdel = get_string('del','cicleinscription');

// Mostrando form de pesquisa blacklist
echo $mform->display();

// Criando GRID de orgaos
$table = new html_table();
$table->width = "100%";
$table->tablealign = "center";
$table->head  = array($struserfullname, $stremail, $strusername, $strdatetimeinput, $strinputtype, $strcoursefullname, $strshortname, $strblacklistdel);
$table->align = array("left", "left", "center", "center", "center");
foreach ($objBlacklist as $blacklist){
	
	$userfullname = $blacklist->userfullname;
	$email = $blacklist->email;
	$username = $blacklist->username;
	$datetimeinput = date('d/m/Y H:i:s',  $blacklist->datetimeinput);
	$inputtype = $blacklist->inputtype;
	$coursefullname = $blacklist->fullname;
	$shortname = $blacklist->shortname;
	
	$removeblacklist = "<a href=\"blacklistdelete.php?blacklistid=".$blacklist->id."&delete=true&coursemodulesid={$cm->id} \">".
			" <img src=\"" . $OUTPUT->pix_url('t/delete') . "\" alt=\".$strblacklistdel.\" />";
			"</a>"; 
	$table->data[] = array($userfullname, $email, $username, $datetimeinput, $inputtype, $coursefullname, $shortname, $removeblacklist);
}

# Adicionando icone novo
echo "<a href='{$CFG->wwwroot}/mod/cicleinscription/blacklist.php?coursemodulesid={$coursemodulesid}'> <img src='{$OUTPUT->pix_url('t/add')}' alt='Novo Registro' style='float: left; margin-right: 5px;' />".get_string('add', 'cicleinscription')." </a> ";
echo $OUTPUT->paging_bar($blacklistcount, $page, $perpage, $url);
echo '<br />';
echo html_writer::table($table);

$strAllRecords = get_string('allrecords', 'cicleinscription');
$strRecords = get_string('records', 'cicleinscription');
echo "<div style='width: 100%; margin: 0 auto; text-align: right;'><a href='#' title='$strRecords'>{$strAllRecords}{$blacklistcount}</a></div>";
echo $OUTPUT->footer();

?>

<!-- Mascara no campo username -->
<script type="text/javascript">
// Mascara e validacao dos campos
$(document).ready(
	function(){
		$('#username').mask('999.999.999-99');
	})
</script>