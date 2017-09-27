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
require_once($CFG->dirroot.'/mod/cicleinscription/report_inscriptions_form.php');

$PAGE->requires->js(new moodle_url($CFG->wwwroot.'/mod/cicleinscription/assets/js/query-1.10.2.min.js'));
$PAGE->requires->js(new moodle_url($CFG->wwwroot.'/mod/cicleinscription/assets/js/jquery.maskedinput.js'));

// Recuperando parametros
$coursemodulesid = optional_param('coursemodulesid', 0, PARAM_INT); // course_module ID, or
// Items per page
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 0, PARAM_INT);
$perpage = $perpage ? $perpage : ITEMS_PER_PAGE;
$url = new moodle_url('/mod/cicleinscription/report_inscriptions.php', array('coursemodulesid'=>$coursemodulesid, 'page' => $page, 'perpage' => $perpage));

if ($coursemodulesid) {
	$cm         = get_coursemodule_from_id('cicleinscription', $coursemodulesid, 0, false, MUST_EXIST);
	$course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
	$cicleinscription  = $DB->get_record('cicleinscription', array('id' => $cm->instance), '*', MUST_EXIST);
	
} else {
	error(get_string('errornotfoundidcourse', 'cicleinscription'));
}

require_login($course, true, $cm);
// Context
$context = context_module::instance($cm->id);
$PAGE->set_context($context);
$PAGE->set_cm($cm);
$PAGE->set_title(format_string($cicleinscription->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_url('/mod/cicleinscription/report_inscriptions.php', array('coursemodulesid' => $cm->id));

$PAGE->set_title(format_string($cicleinscription->name));
$PAGE->set_heading(format_string($course->fullname));
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('reportinscriptions', 'cicleinscription'));
// Gravando log
add_to_log($course->id, 'cicleinscription', 'report_inscriptions', "report_inscriptions.php?coursemodulesid={$cm->id}&page=0&perpage=10", $cicleinscription->name, $cm->id);

// Formulario de busca
$mform  = new mod_cicleinscription_report_inscriptions_form($CFG->wwwroot.'/mod/cicleinscription/report_inscriptions.php?coursemodulesid='.$cm->id.'&perpage='.ITEMS_PER_PAGE.'&page=0');

// Validando requisicao do form
if($mform->is_cancelled()){
	redirect($CFG->wwwroot);
}else if ($form_data = $mform->get_data()){
	$form_data->cicleinscriptionid =  $cicleinscription->id;
	// Continuar daqui - Terminar o filtro
	$objIscriptions = cicleinscription_get_report_inscriptions($page, $perpage, cicleinscription_validaCPF($form_data->username), $form_data->organid, $form_data->courseid, $form_data->cicleinscriptionid);
	
	// Recuperando a quantidade com os parametros acima
	$inscriptionscount = cicleinscription_get_count_report_inscriptions(cicleinscription_validaCPF($form_data->username), $form_data->organid, $form_data->courseid, $form_data->cicleinscriptionid);
}else {
	$objIscriptions = cicleinscription_get_report_inscriptions($page, $perpage, null, null, null, $cicleinscription->id);
	
	// Recuperando a quantidade com os parametros acima
	$inscriptionscount = cicleinscription_get_count_report_inscriptions(null, null, null, $cicleinscription->id);
}

// variaveis do cabecalho da tabela
$struserfullname = get_string('fullname');
$strcity = get_string('city');
$strcoursefullname = get_string('course');
$strorgan = get_string('organ','cicleinscription');
$strview = get_string('view','cicleinscription');
$strprofile = get_string('profile');

// Mostrando form de pesquisa blacklist
echo $mform->display();

// Criando GRID de orgaos
$table = new html_table();
$table->width = "100%";
$table->tablealign = "center";
$table->head  = array($struserfullname, $strorgan, $strcoursefullname, $strcity, $strprofile);
$table->align = array("left", "left", "center", "center", "center");
foreach ($objIscriptions as $inscription){
	
	$userfullname = "<a href=\"{$CFG->wwwroot}/user/view.php?id=".$inscription->userid." \" target='_black'>".
		$inscription->userfullname
	."</a>";
	
	$organ = $inscription->organ;
	$coursefullname = $inscription->course;
	$city = $inscription->city;
	
	$lkprofile = "<a href=\"{$CFG->wwwroot}/user/view.php?id=".$inscription->userid." \" target='_black'>".
			" <img src=\"" . $OUTPUT->pix_url('t/user') . "\" alt=\".$strview.\" />";
			"</a>"; 
	$table->data[] = array($userfullname, $organ, $coursefullname, $city, $lkprofile);
}

$objIscriptions->close();

echo $OUTPUT->paging_bar($inscriptionscount, $page, $perpage, $url);
echo '<br />';
echo html_writer::table($table);

$strAllRecords = get_string('allrecords', 'cicleinscription');
$strRecords = get_string('records', 'cicleinscription');
echo "<div style='width: 100%; margin: 0 auto; text-align: right;'><a href='#' title='$strRecords'>{$strAllRecords}{$inscriptionscount}</a></div>";

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