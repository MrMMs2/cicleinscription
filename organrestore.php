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
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');

$msgrestore = get_string('msgrestore', 'cicleinscription');

$coursemodulesid = optional_param('coursemodulesid',0, PARAM_INT);
$restore = optional_param('restore', 0, PARAM_BOOL);

if($coursemodulesid){
	$cm         = get_coursemodule_from_id('cicleinscription', $coursemodulesid, 0, false, MUST_EXIST);
	$course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
	$cicleinscription  = $DB->get_record('cicleinscription', array('id' => $cm->instance), '*', MUST_EXIST);
}

require_login($course, true, $cm);
// Context
$context = context_module::instance($cm->id);
$PAGE->set_context($context);
$PAGE->set_cm($cm);
$PAGE->set_title(format_string($cicleinscription->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_url('/mod/cicleinscription/organrestore.php', array('coursemodulesid' => $cm->id));

if($restore){
	echo $OUTPUT->header();
	echo $OUTPUT->heading(get_string('restore', 'cicleinscription'));
	echo $OUTPUT->confirm(
		get_string("msgrestoreconfirm", "cicleinscription"), "organrestore.php?coursemodulesid={$coursemodulesid}", "organ_report.php?coursemodulesid={$coursemodulesid}&page=0&perpage=20"
	);
	$_SESSION['coursemodulesid'] = $coursemodulesid;
	echo $OUTPUT->footer();
	
	die();
}

global $CFG;
$con = mysql_connect($CFG->dbhost, $CFG->dbuser, $CFG->dbpass) or print(mysql_error()); 
mysql_select_db($CFG->dbname, $con) or print(mysql_error());

// Limpando a tabela
$sql = "TRUNCATE TABLE mdl_ci_organ";
$rs = mysql_query($sql);

// Inserindo novos dados
$sql = "INSERT INTO `mdl_ci_organ` (`id`, `name`, `acronym`, `limitvacancies`) VALUES
(1, 'Conselho Nacional de Justiça', 'CNJ', 2),
(2, 'Conselho da Justiça Federal', 'CJF', 2),
(3, 'Conselho Superior da Justiça do Trabalho', 'CSJT', 2),
(4, 'Escola Nacional da Magistratura do Trabalho ', 'ENAMAT', 2),
(5, 'Escola Nacional de Formação e Aperfeiçoamento de Magistrados', 'ENFAM', 2),
(6, 'Supremo Tribunal Federal', 'STF', 2),
(7, 'Superior Tribunal de Justiça', 'STJ', 2),
(8, 'Superior Tribunal Militar', 'STM', 2),
(9, 'Tribunal Superior Eleitoral', 'TSE', 2),
(10, 'Tribunal Superior do Trabalho', 'TST', 2),
(11, 'Tribunal Regional Federal da 1ª Região', 'TRF1 ', 2),
(12, 'Tribunal Regional Federal da 2ª Região', 'TRF2', 2),
(13, 'Tribunal Regional Federal da 3ª Região', 'TRF3', 2),
(14, 'Tribunal Regional Federal da 4ª Região', 'TRF4', 2),
(15, 'Tribunal Regional Federal da 5ª Região', 'TRF5', 2),
(16, 'Tribunal Regioal Eleitoral do Acre', 'TREAC', 2),
(17, 'Tribunal Regional Eleitoral de Alagoas', 'TREAL', 2),
(18, 'Tribunal Regional Eleitoral do Amapá', 'TREAP', 2),
(19, 'Tribunal Regional Eleitoral  do Amazonas', 'TREAM', 2),
(20, 'Tribunal Regional Eleitoral da Bahia', 'TREBA', 2),
(21, 'Tribunal Regional Eleitoral do Ceará', 'TRECE', 2),
(22, 'Tribunal Regional Eleitoral do DF', 'TREDF', 2),
(23, 'Tribunal Regional Eleitoral do Espirito Santo', 'TREES', 2),
(24, 'Tribunal Regional Eleitoral de Goiás', 'TREGO', 2),
(25, 'Tribunal Regional Eleitoral do Maranhão', 'TREMA', 2),
(26, 'Tribunal Regional Eleitoral de Minas Gerais', 'TREMG', 2),
(27, 'Tribunal Regional Eleitoral do Mato Grosso', 'TREMT', 2),
(28, 'Tribunal Regional Eleitoral de Mato Grosso do Sul', 'TREMS', 2),
(29, 'Tribunal Regional Eleitoral Pará', 'TREPA', 2),
(30, 'Tribunal Regional Eleitoral de Pernambuco', 'TREPE', 2),
(31, 'Tribunal Regional Eleitoral Paraíba', 'TREPB', 2),
(32, 'Tribunal Regioal Eleitoral Paraná', 'TREPR', 2),
(33, 'Tribunal Regional Eleitoral Piauí', 'TREPI', 2),
(34, 'Tribunal de Justiça Militar do Estado do Rio Grande do Sul', 'TJMRS', 2),
(35, 'Tribunal Regional Eleitoral Rio de Janeiro', 'TRERJ', 2),
(36, 'Tribunal Regional Eleitoral Rio Grande do Norte', 'TRERN', 2),
(37, 'Tribunal Regional Eleitoral Rio Grande do Sul', 'TRERS', 2),
(38, 'Tribunal Regional Eleitoral Rondônia', 'TRERO', 2),
(39, 'Tribunal Regional Eleitoral Roraima', 'TRERR', 2),
(40, 'Tribunal Regional Eleitoral Santa Catarina', 'TRESC', 2),
(41, 'Tribunal Regional Eleitoral São Paulo', 'TRESP', 2),
(42, 'Tribunal Regional Eleitoral de Sergipe', 'TRESE', 2),
(43, 'Tribunal Regional Eleitoral Tocantins', 'TRETO', 2),
(44, 'Tribunal Regional do Trabalho da 1ª Região - RJ', 'TRT1', 2),
(45, 'Tribunal Regional do Trabalho da 2ª Região - SP', 'TRT2', 2),
(46, 'Tribunal Regional do Trabalho da 3ª Região - MG', 'TRT3', 2),
(47, 'Tribunal de Justiça Militar do Estado de São Paulo', 'TJMSP', 2),
(48, 'Tribunal Regional do Trabalho da 4ª Região - RS', 'TRT4', 2),
(49, 'Tribunal Regional do Trabalho da 5ª Região - BA', 'TRT5', 2),
(50, 'Tribunal Regional do Trabalho da 6ª Região - PE', 'TRT6', 2),
(51, 'Tribunal Regional do Trabalho da 7ª Região - CE', 'TRT7', 2),
(52, 'Tribunal Regional do Trabalho da 8ª Região - PA/AP', 'TRT8', 2),
(53, 'Tribunal Regional do Trabalho da 9ª Região - PR', 'TRT9', 2),
(54, 'Tribunal Regional do Trabalho da 10ª Região - DF', 'TRT10', 2),
(55, 'Tribunal Regional do Trabalho da 11ª Região - AM/RR', 'TRT11', 2),
(56, 'Tribunal Regional do Trabalho da 12ª Região - SC', 'TRT12', 2),
(57, 'Tribunal Regional do Trabalho da 13ª Região - PB', 'TRT13', 2),
(58, 'Tribunal Regional do Trabalho da 14ª Região - AC/RO', 'TRT14', 2),
(59, 'Tribunal Regional do Trabalho da 15ª Região - Campinas', 'TRT15', 2),
(60, 'Tribunal Regional do Trabalho da 16ª Região - MA', 'TRT16', 2),
(61, 'Tribunal Regional do Trabalho da 17ª Região - ES', 'TRT17', 2),
(62, 'Tribunal Regional do Trabalho da 18ª Região - GO', 'TRT18', 2),
(63, 'Tribunal Regional do Trabalho da 19ª Região - AL', 'TRT19', 2),
(64, 'Tribunal Regional do Trabalho da 20ª Região - SE', 'TRT20', 2),
(65, 'Tribunal Regional do Trabalho da 21ª Região - RN', 'TRT21', 2),
(66, 'Tribunal Regional do Trabalho da 22ª Região - PI', 'TRT22', 2),
(67, 'Tribunal Regional do Trabalho da 23ª Região - MT', 'TRT23', 2),
(68, 'Tribunal Regional do Trabalho da 24ª Região - MS', 'TRT24', 2),
(69, 'Tribunal de Justiça do Estado do Acre', 'TJAC', 2),
(70, 'Tribunal de Justiça do Estado de Alagoas', 'TJAL', 2),
(71, 'Tribunal de Justiça do Estado do Amazonas', 'TJAM', 2),
(72, 'Tribunal de Justiça do Estado do Amapá', 'TJAP', 2),
(73, 'Tribunal de Justiça do Estado da Bahia', 'TJBA', 2),
(74, 'Tribunal de Justiça do Estado do Ceará', 'TJCE', 2),
(75, 'Tribunal de Justiça do Distrito Federal e Territ�rios', 'TJDFT', 2),
(76, 'Tribunal de Justiça do Estado do Espírito Santo', 'TJES', 2),
(77, 'Tribunal de Justiça do Estado de Goiás', 'TJGO', 2),
(78, 'Tribunal de Justiça do Estado do Maranhão', 'TJMA', 2),
(79, 'Tribunal de Justiça do Estado do Mato Grosso', 'TJMT', 2),
(80, 'Tribunal de Justiça do Estado de Minas Gerais', 'TJMG', 2),
(81, 'Tribunal de Justiça do Estado do Pará', 'TJPA', 2),
(82, 'Tribunal de Justiça do Estado da Paraiba', 'TJPB', 2),
(83, 'Tribunal de Justiça do Estado do Paraná', 'TJPR', 2),
(84, 'Tribunal de Justiça do Estado de Pernambuco', 'TJPE', 2),
(85, 'Tribunal de Justiça do Estado do Piauí', 'TJPI', 2),
(86, 'Tribunal de Justiça do Estado do Rio de Janeiro', 'TJRJ', 2),
(87, 'Tribunal de Justiça do Estado do Rio Grande do Norte', 'TJRN', 2),
(88, 'Tribunal de Justiça do Estado do Rio Grande do Sul', 'TJRS', 2),
(89, 'Tribunal de Justiça do Estado de Rondônia', 'TJRO', 2),
(90, 'Tribunal de Justiça do Estado de Roraima', 'TJRR', 2),
(91, 'Tribunal de Justiça do Estado de Santa Catarina', 'TJSC', 2),
(92, 'Tribunal de Justiça do Estado de São Paulo', 'TJSP', 2),
(93, 'Tribunal de Justiça do Estado de Sergipe', 'TJSE', 2),
(94, 'Tribunal de Justiça do Estado de Tocantins', 'TJTO', 2),
(95, 'Tribunal de Justiça Militar do Estado de Minas Gerais', 'TJMMG', 2)";
$rs = mysql_query($sql);

mysql_free_result($rs);
mysql_close($con);

if(!$rs){
	error(get_string('error'));
}

// Recuperando variavel $coursemodulesid ;
$coursemodulesid = $_SESSION['coursemodulesid'];
unset($_SESSION['coursemodulesid']);

echo $OUTPUT->notification($msgrestore, 'notifysuccess');
redirect($CFG->wwwroot."/mod/cicleinscription/organ_report.php?coursemodulesid={$coursemodulesid}&page=0&perpage=20");

