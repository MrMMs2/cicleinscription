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
 * Library of functions of validation for module cicleinscription
 *
 * All the core Moodle functions, neeeded to allow the module to work
 * integrated in Moodle should be placed here.
 * All the cicleinscription specific functions, needed to implement all the module
 * logic, should go to locallib.php. This will help to save some memory when
 * Moodle is performing actions across all modules.
 *
 * @package    mod
 * @subpackage cicleinscription
 * @copyright  2013 CEAJUD | CNJ | Tecninys
 * @author	   Leo Santos
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/** *	Funcao responsavel por verificar se o candidato ja possui cadastro na plataforma EAD CNJ.
 *		@param	$username
 *		@param	$email
 *		@return	boolean
 *		@author	Leo Santos
 **/
function cicleinscription_verify_cadastre_user($username, $email){
	global $DB;
	
	$sql = "SELECT id, username, email FROM {user} WHERE username = ? OR email = ?";
	$result = $DB->get_record_sql($sql, array($username, $email));
	
	return $result ? $result : false;
}


/** *	Funcao responsavel por identificar se o canditado ja esta matriculado em um determinado course do corrente ciclo.
 *		@param	$username
 *		@param	$coursemodulesid
 *		@param	$cicleinscriptionid
 *		@return	boolean
 *		@author	Leo Santos
 **/
function cicleinscription_verify_user_registered_in_course_of_cicle($username, $cicleinscriptionid){

	// Verificando o usuario existe
	if ($userid = cicleinscription_get_user_by_username($username)->id) {
		global $DB;
		$sql = "SELECT ue.userid, 
				       e.enrol, 
				       e.courseid, 
				       e.roleid, 
				       cp.courseid as cpcourseid
				FROM   {user_enrolments} ue 
				       INNER JOIN {enrol} e 
				               ON ue.enrolid = e.id 
				       INNER JOIN {ci_course_prematriculation} cp 
				               ON e.courseid = cp.courseid 
				WHERE  cp.cicleinscriptionid = ? 
				       AND ue.userid = ?";
		$result = $DB->get_record_sql($sql, array($cicleinscriptionid, $userid));
		return $result ? $result : false;
	}
	return false;
}

/** *	Funcao responsavel por verificar disponibilidade de vagas por orgao.
 *		@param	$organid
 *		@param	$coursemodulesid
 *		@param	$cicleinscriptionid
 *		@return	boolean
 *		@author	Leo Santos
 */
function cicleinscription_verify_limit_vacancies_by_organ($organid, $coursemodulesid, $cicleinscriptionid){
	global $DB;
	
	$qtde_enrolled = (int) cicleinscription_get_qtde_enrolld_in_organ_of_cicle($organid, $cicleinscriptionid)->qtde;
	$limitvacancies_of_organ = (int) cicleinscription_get_limitvacancies_of_organ($organid, $coursemodulesid, $cicleinscriptionid)->limitvacancies;
	
	if ($qtde_enrolled < $limitvacancies_of_organ) {
		return true;
	}
	return false;
	# verificando a quantidade de vagas oferecidas para aquele organ para o cicle em questao
}

/** *	Funcao responsavel por recuperar quantidade de matriculados em um determinado orgao de um determinado ciclo.
 *		@param	$organid
 *		@param	$coursemodulesid
 *		@param	$cicleinscriptionid
 *		@return	int $qtde
 *		@author	Leo Santos
 */
function cicleinscription_get_qtde_enrolld_in_organ_of_cicle($organid, $cicleinscriptionid){
	global $DB;
	// status_prematriculationid 1 -> sucess
	$status_prematriculationid = 1;
	
	$sql = "SELECT count(*) AS qtde FROM {ci_prematriculation} WHERE organid = ? AND cicleinscriptionid = ? AND status_prematriculationid = ?";
	$result = $DB->get_record_sql($sql, array($organid, $cicleinscriptionid, $status_prematriculationid));
	
	return $result;
}

/** *	Funcao responsavel por recuperar o limite de vagas por orgao.
 *		@param	$organid
 *		@param	$coursemodulesid
 *		@param	$cicleinscriptionid
 *		@return	int $limitvacancies
 *		@author	Leo Santos
 */
function cicleinscription_get_limitvacancies_of_organ($organid, $coursemodulesid, $cicleinscriptionid){
	global $DB;
	
	$sql = "SELECT limitvacancies FROM {ci_cicleorgan} WHERE organid = ? AND cicleinscriptionid = ? AND coursemodulesid = ?";
	
	$limitvacancies_cicleorgan = $DB->get_record_sql($sql, array($organid, $cicleinscriptionid, $coursemodulesid));
	
	if($limitvacancies_cicleorgan->limitvacancies){
		return $limitvacancies_cicleorgan;
	}else{
		$sql = "SELECT limitvacancies FROM {ci_organ} WHERE id = ?";
		$limitvacancies_organ = $DB->get_record_sql($sql, array($organid));
		return $limitvacancies_organ;
	}
}

/** *	Funcao responsavel por verificar se o participante ja esta possui registro na table {ci_prematriculation} 
 * 		em um determinado ciclo e em um determinado status_prematriculationid.
 *		@param	$username
 *		@param	$status_prematriculationid
 *		@param	$cicleinscriptionid
 *		@return	boolean $result
 *		@author	Leo Santos
 */
function cicleinscription_verify_prematriculation_by_cicle_and_status($username, $status_prematriculationid, $cicleinscriptionid){
	global $DB;
	
	$sql = "SELECT id, username, status_prematriculationid, cicleinscriptionid, course_prematriculationid FROM {ci_prematriculation} WHERE username = ? AND status_prematriculationid = ? AND cicleinscriptionid = ?";
	$result = $DB->get_record_sql($sql, array($username, $status_prematriculationid, $cicleinscriptionid));
	return $result;
}
