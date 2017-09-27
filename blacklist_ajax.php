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


require_login();

if($_POST['username']){
	$username = cicleinscription_validaCPF($_POST['username']);
	
	if($username){
		global $DB;
		$objUser = $DB->get_record('user', array('username'=>$username));
		echo json_encode($objUser);
	}else {
		# retornar mensagem de erro
		echo json_encode(false);
	}
}