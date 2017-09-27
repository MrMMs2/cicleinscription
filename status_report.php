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

// Recuperando parametros
$coursemodulesid = optional_param('coursemodulesid', 0, PARAM_INT); // course_module ID, or
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 0, PARAM_INT);
$strorganupdate = get_string('update', 'cicleinscription');

$url = new moodle_url('/mod/cicleinscription/status_report.php', array('coursemodulesid'=>$coursemodulesid, 'page' => $page, 'perpage' => $perpage));

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
$PAGE->set_url('/mod/cicleinscription/status_report.php', array('coursemodulesid' => $cm->id));

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('status', 'cicleinscription'));
// Gravando log
add_to_log($course->id, 'cicleinscription', 'status_report', "status_report.php?coursemodulesid={$cm->id}&page=0&perpage=10", $cicleinscription->name, $cm->id);

// variaveis do cabecalho da tabela
$stridstatus = get_string('id','cicleinscription');
$strstatus = get_string('status','cicleinscription');
$strdescription = get_string('description','cicleinscription');
$stremailmessage = get_string('emailmessage','cicleinscription');
$strupdate = get_string('update','cicleinscription');

// Orgaos
$sort = "o.id ASC";
$status = cicleinscription_get_itemsTable('ci_status_prematriculation', $page, $perpage, $sort);

// Criando GRID de Orgaos
$table = new html_table();
$table->width = "100%";
$table->tablealign = "center";
$table->head  = array($stridstatus, $strstatus, $strdescription, $stremailmessage, $strupdate);
$table->align = array("left", "left", "center", "center", "center");
foreach ($status as $st){
	$idstatus = $st->id;
	$nameStatus = $st->name;
	$description = $st->description;
	$emailmessage = $st->emailmessage;
	$updateStatus = "<a href=\"status.php?statusid=".$st->id."&update=true&coursemodulesid={$cm->id} \">".
			" <img src=\"" . $OUTPUT->pix_url('t/edit') . "\" alt=\".$strorganupdate.\" />";
			"</a>"; 
	$table->data[] = array($idstatus, $nameStatus, $description, $emailmessage, $updateStatus);
}
// Adicionando link para novo status, caso o idstatus seja < 4
$adicionar = "<a href='{$CFG->wwwroot}/mod/cicleinscription/status.php?coursemodulesid={$coursemodulesid}'> <img src='{$OUTPUT->pix_url('t/addgreen')}' alt='Novo Registro' style='float: left; margin-right: 5px;' />".get_string('add', 'cicleinscription')." </a> ";
echo $idstatus < QTDE_MAX_STATUS ? $adicionar : '';

// barra de paginacao
$organcount = count(cicleinscription_get_itemsTable('ci_status_prematriculation'));
echo $OUTPUT->paging_bar($organcount, $page, $perpage, $url);

echo '<br />';
echo html_writer::table($table);

// Adicionando link para funcionalidade restaurar status
$restaurar = "<a href='{$CFG->wwwroot}/mod/cicleinscription/statusrestore.php?coursemodulesid={$coursemodulesid}&restore=true'> <img src='{$OUTPUT->pix_url('i/restore')}' alt='Restaurar' style='float: left; margin-right: 5px;' />".get_string('restore', 'cicleinscription')." </a> ";
echo $restaurar;
echo $OUTPUT->footer();