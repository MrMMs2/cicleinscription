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
// Verifica se o usuario esta logado
require_login();

$msgrestore = get_string('msgrestore', 'cicleinscription');

global $DB;
$context = get_system_context();
require_capability('mod/ecertificado:manage', $context);

$coursemodulesid = optional_param('coursemodulesid',0, PARAM_INT);
$restore = optional_param('restore', 0, PARAM_BOOL);

if($coursemodulesid){
	$cm         = get_coursemodule_from_id('cicleinscription', $coursemodulesid, 0, false, MUST_EXIST);
	$course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
	$cicleinscription  = $DB->get_record('cicleinscription', array('id' => $cm->instance), '*', MUST_EXIST);

}
// Gravando log
#add_to_log($course->id, 'cicleinscription', 'statusrestore', "statusrestore.php?coursemodulesid={$cm->id}", $cicleinscription->name, $cm->id);

// Removendo organ
if($restore){
	echo $OUTPUT->header();
	echo $OUTPUT->heading(get_string('restorestatus', 'cicleinscription'));
	echo $OUTPUT->confirm(
		get_string("msgrestoreconfirm", "cicleinscription"), "statusrestore.php?restore=false", "status_report.php?coursemodulesid={$coursemodulesid}&page=0&perpage=10"
	);
	$_SESSION['coursemodulesid'] = $coursemodulesid;
	echo $OUTPUT->footer();
	
	die();
}

global $CFG;
$con = mysql_connect($CFG->dbhost, $CFG->dbuser, $CFG->dbpass) or print(mysql_error()); 
mysql_select_db($CFG->dbname, $con) or print(mysql_error());

// Limpando a tabela
$sql = "TRUNCATE TABLE mdl_ci_status_prematriculation";
$rs = mysql_query($sql);

// Inserindo novos dados
$sql = "INSERT INTO `mdl_ci_status_prematriculation` (`id`, `name`, `description`, `emailmessage`) VALUES
(1, 'sucess', 'Cadastro foi efetuado com sucesso!', 'Prezado Aluno, seu cadastro foi efetuado com sucesso! No mais estamos a dosposicao. Atenciosamente, CEAJUD - Centro de Forma��o e Aperfei�oamento de Servidores do Poder Judici�rio.'),
(2, 'blacklist', 'Aluno nao pode se matricular, porque seu nome consta na blacklist!', 'Prezado Aluno, sua matricula nao foi realizada, porque seu nome consta na blacklist! Estamos a disposi��o. Atenciosamente, CEAJUD - Centro de Forma��o e Aperfei�oamento de Servidores do Poder Judici�rio.'),
(3, 'enrolled', 'Aluno nao pode se matricular, porque ja esta matriculado em outro curso!', 'Prezado Aluno, sua matricula nao foi realizada, porque voc� ja esta matriculado em outro curso! Estamos a disposi��o. Atenciosamente, CEAJUD - Centro de Forma��o e Aperfei�oamento de Servidores do Poder Judici�rio.'),
(4, 'limitExceeded', 'Limite de pessoas por tribunal ja excedido!', 'Prezado Aluno, sua matricula nao foi realizada, porque o limite de pessoas por tribunal foi excedido! Estamos a disposi��o.Atenciosamente, CEAJUD - Centro de Forma��o e Aperfei�oamento de Servidores do Poder Judici�rio.')";
 
$rs = mysql_query($sql);

mysql_free_result($rs);
mysql_close($con);

if(!$rs){
	error(get_string('error'));
}

// Recuperando variavel $coursemodulesid ;
$coursemodulesid = $_SESSION['coursemodulesid'];
unset($_SESSION['coursemodulesid']);

echo $OUTPUT->notification($msgdel, 'notifysuccess');
redirect($CFG->wwwroot."/mod/cicleinscription/status_report.php?coursemodulesid={$coursemodulesid}&page=0&perpage=10");

