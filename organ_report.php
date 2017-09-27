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

$url = new moodle_url('/mod/cicleinscription/organ_report.php', array('coursemodulesid'=>$coursemodulesid, 'page' => $page, 'perpage' => $perpage));

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

$PAGE->set_url('/mod/cicleinscription/organ_report.php', array('coursemodulesid' => $cm->id));
$PAGE->set_title(format_string($cicleinscription->name));
$PAGE->set_heading(format_string($course->fullname));

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('organplural', 'cicleinscription'));
// Gravando log
add_to_log($course->id, 'cicleinscription', 'organ_report', "organ_report.php?coursemodulesid={$cm->id}&page=0&perpage=20", $cicleinscription->name, $cm->id);

// variaveis do cabecalho da tabela
$strorgan = get_string('organ','cicleinscription');
$stracronym = get_string('acronym','cicleinscription');
$strlimitvacancies = get_string('limitvacancies','cicleinscription');
$strorganupdate = get_string('update','cicleinscription');
$strorgandel = get_string('del','cicleinscription');

$organs = cicleinscription_get_itemsTable('ci_organ', $page, $perpage);
# Adicionando icone novo
$icone = "<a href='{$CFG->wwwroot}/mod/cicleinscription/organ.php?coursemodulesid={$coursemodulesid}'> <img src='{$OUTPUT->pix_url('t/add')}' alt='Novo Registro' /> </a> ";

// Criando GRID de orgaos
$table = new html_table();
$table->width = "100%";
$table->tablealign = "center";
$table->head  = array($icone.$strorgan, $stracronym, $strlimitvacancies, $strorganupdate, $strorgandel);
$table->align = array("left", "left", "center", "center", "center");
foreach ($organs as $organ){
	$nameOrgan = $organ->name;
	$acronymOrgan = $organ->acronym;
	$limitvacanciesOrgan = $organ->limitvacancies;
	$updateOrgan = "<a href=\"organ.php?organid=".$organ->id."&update=true&coursemodulesid={$cm->id} \">".
			" <img src=\"" . $OUTPUT->pix_url('t/edit') . "\" alt=\".$strorganupdate.\" />";
			"</a>"; 
	$deleteOrgan = "<a href=\"organdelete.php?organid=".$organ->id."&delete=true&coursemodulesid={$cm->id} \">".
			" <img src=\"" . $OUTPUT->pix_url('t/delete') . "\" alt=\".$strorgandel.\" />";
			"</a>"; 
	$table->data[] = array($nameOrgan, $acronymOrgan, $limitvacanciesOrgan, $updateOrgan, $deleteOrgan);
}

// barra de paginacao
$organcount = count(cicleinscription_get_itemsTable('ci_organ'));

echo $OUTPUT->paging_bar($organcount, $page, $perpage, $url);

echo '<br />';
echo html_writer::table($table);

$strAllRecords = get_string('allrecords', 'cicleinscription');
$strRecords = get_string('records', 'cicleinscription');
echo "<br /><div style='width: 77%; margin: 0 auto; text-align: right;'><a href='#' title='$strRecords'>{$strAllRecords}{$organcount}</a></div>";
// Adicionando link para funcionalidade restaurar organ
$restaurar = "<a href='{$CFG->wwwroot}/mod/cicleinscription/organrestore.php?coursemodulesid={$coursemodulesid}&restore=true'> <img src='{$OUTPUT->pix_url('i/restore')}' alt='Restaurar' style='float: left; margin-right: 5px;' />".get_string('restore', 'cicleinscription')." </a> ";
echo $restaurar;
echo $OUTPUT->footer();