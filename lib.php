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
 * Library of interface functions and constants for module cicleinscription
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

/** example constant */
//define('CICLEINSCRIPTION_ULTIMATE_ANSWER', 42);

define('ORGAN_PER_PAGE', 20);
define('ORGAN_MAX_PER_PAGE', 60);
define('ITEMS_PER_PAGE', 20);
define('ITEMS_MAX_PER_PAGE', 60);
define('QTDE_MAX_STATUS', 4);	# Status disponiveis
define('DEFAULT_NUMBER_OF_STUDENTS', 50);
define('DEFAULT_NUMBER_OF_STUDENTS_BY_COURSE', 50);
define('DEFAULT_EMAIL_FROM_EAD', 'EAD CNJ<ead@cnj.jus.br>');
define('STR_VAGAS_REMANESCENTES', 'VR');
# Constantes para recuperar arquivos com a Funcao get_area_files();
define('CONTEXTID', 5);
define('COMPONENT', 'user');
define('FILE_AREA', 'draft');
define('PATH_BANNER', $CFG->wwwroot.'/draftfile.php/'.CONTEXTID.'/'.COMPONENT.'/'.FILE_AREA.'/');

////////////////////////////////////////////////////////////////////////////////
// Moodle core API                                                            //
////////////////////////////////////////////////////////////////////////////////

/**
 * Returns the information on whether the module supports a feature
 *
 * @see plugin_supports() in lib/moodlelib.php
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed true if the feature is supported, null if unknown
 */
function cicleinscription_supports($feature) {
    switch($feature) {
        case FEATURE_MOD_INTRO:         return true;
        default:                        return null;
    }
}

/**
 * Saves a new instance of the cicleinscription into the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $cicleinscription An object from the form in mod_form.php
 * @param mod_cicleinscription_mod_form $mform
 * @return int The id of the newly inserted cicleinscription record
 */

function cicleinscription_add_instance(stdClass $cicleinscription, mod_cicleinscription_mod_form $mform = null) {
    global $DB;
    $cicleinscription->timecreated = time();
   
    # You may have to add extra stuff in here #

    return $DB->insert_record('cicleinscription', $cicleinscription);
}

/**
 * Updates an instance of the cicleinscription in the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param object $cicleinscription An object from the form in mod_form.php
 * @param mod_cicleinscription_mod_form $mform
 * @return boolean Success/Fail
 */
function cicleinscription_update_instance(stdClass $cicleinscription, mod_cicleinscription_mod_form $mform = null) {
    global $DB;

    $cicleinscription->timemodified = time();
    $cicleinscription->id = $cicleinscription->instance;

    # You may have to add extra stuff in here #

    return $DB->update_record('cicleinscription', $cicleinscription);
}

/**
 * Removes an instance of the cicleinscription from the database
 *
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function cicleinscription_delete_instance($id) {
    global $DB;
    
    if (! $cicleinscription = $DB->get_record('cicleinscription', array('id' => $id))) {
        return false;
    }

    # Delete any dependent records here #

    $DB->delete_records('cicleinscription', array('id' => $cicleinscription->id));

    return true;
}

/**
 * Returns a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @return stdClass|null
 */
function cicleinscription_user_outline($course, $user, $mod, $cicleinscription) {

    $return = new stdClass();
    $return->time = 0;
    $return->info = '';
    
    return $return;
}

/**
 * Prints a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @param stdClass $course the current course record
 * @param stdClass $user the record of the user we are generating report for
 * @param cm_info $mod course module info
 * @param stdClass $cicleinscription the module instance record
 * @return void, is supposed to echp directly
 */
function cicleinscription_user_complete($course, $user, $mod, $cicleinscription) {
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in cicleinscription activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @return boolean
 */
function cicleinscription_print_recent_activity($course, $viewfullnames, $timestart) {
    return false;  //  True if anything was printed, otherwise false
}

/**
 * Prepares the recent activity data
 *
 * This callback function is supposed to populate the passed array with
 * custom activity records. These records are then rendered into HTML via
 * {@link cicleinscription_print_recent_mod_activity()}.
 *
 * @param array $activities sequentially indexed array of objects with the 'cmid' property
 * @param int $index the index in the $activities to use for the next record
 * @param int $timestart append activity since this time
 * @param int $courseid the id of the course we produce the report for
 * @param int $cmid course module id
 * @param int $userid check for a particular user's activity only, defaults to 0 (all users)
 * @param int $groupid check for a particular group's activity only, defaults to 0 (all groups)
 * @return void adds items into $activities and increases $index
 */
function cicleinscription_get_recent_mod_activity(&$activities, &$index, $timestart, $courseid, $cmid, $userid=0, $groupid=0) {
}

/**
 * Prints single activity item prepared by {@see cicleinscription_get_recent_mod_activity()}

 * @return void
 */
function cicleinscription_print_recent_mod_activity($activity, $courseid, $detail, $modnames, $viewfullnames) {
}

/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * @return boolean
 * @todo Finish documenting this function
 **/
function cicleinscription_cron () {
    return true;
}

/**
 * Returns all other caps used in the module
 *
 * @example return array('moodle/site:accessallgroups');
 * @return array
 */
function cicleinscription_get_extra_capabilities() {
    return array();
}

////////////////////////////////////////////////////////////////////////////////
// Gradebook API                                                              //
////////////////////////////////////////////////////////////////////////////////

/**
 * Is a given scale used by the instance of cicleinscription?
 *
 * This function returns if a scale is being used by one cicleinscription
 * if it has support for grading and scales. Commented code should be
 * modified if necessary. See forum, glossary or journal modules
 * as reference.
 *
 * @param int $cicleinscriptionid ID of an instance of this module
 * @return bool true if the scale is used by the given cicleinscription instance
 */
function cicleinscription_scale_used($cicleinscriptionid, $scaleid) {
    global $DB;

    /** @example */
    if ($scaleid and $DB->record_exists('cicleinscription', array('id' => $cicleinscriptionid, 'grade' => -$scaleid))) {
        return true;
    } else {
        return false;
    }
}

/**
 * Checks if scale is being used by any instance of cicleinscription.
 *
 * This is used to find out if scale used anywhere.
 *
 * @param $scaleid int
 * @return boolean true if the scale is used by any cicleinscription instance
 */
function cicleinscription_scale_used_anywhere($scaleid) {
    global $DB;

    /** @example */
    if ($scaleid and $DB->record_exists('cicleinscription', array('grade' => -$scaleid))) {
        return true;
    } else {
        return false;
    }
}

/**
 * Creates or updates grade item for the give cicleinscription instance
 *
 * Needed by grade_update_mod_grades() in lib/gradelib.php
 *
 * @param stdClass $cicleinscription instance object with extra cmidnumber and modname property
 * @return void
 */
function cicleinscription_grade_item_update(stdClass $cicleinscription) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    /** @example */
    $item = array();
    $item['itemname'] = clean_param($cicleinscription->name, PARAM_NOTAGS);
    $item['gradetype'] = GRADE_TYPE_VALUE;
    $item['grademax']  = $cicleinscription->grade;
    $item['grademin']  = 0;

    grade_update('mod/cicleinscription', $cicleinscription->course, 'mod', 'cicleinscription', $cicleinscription->id, 0, null, $item);
}

/**
 * Update cicleinscription grades in the gradebook
 *
 * Needed by grade_update_mod_grades() in lib/gradelib.php
 *
 * @param stdClass $cicleinscription instance object with extra cmidnumber and modname property
 * @param int $userid update grade of specific user only, 0 means all participants
 * @return void
 */
function cicleinscription_update_grades(stdClass $cicleinscription, $userid = 0) {
    global $CFG, $DB;
    require_once($CFG->libdir.'/gradelib.php');

    /** @example */
    $grades = array(); // populate array of grade objects indexed by userid

    grade_update('mod/cicleinscription', $cicleinscription->course, 'mod', 'cicleinscription', $cicleinscription->id, 0, $grades);
}

////////////////////////////////////////////////////////////////////////////////
// File API                                                                   //
////////////////////////////////////////////////////////////////////////////////

/**
 * Returns the lists of all browsable file areas within the given module context
 *
 * The file area 'intro' for the activity introduction field is added automatically
 * by {@link file_browser::get_file_info_context_module()}
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @return array of [(string)filearea] => (string)description
 */
function cicleinscription_get_file_areas($course, $cm, $context) {
    return array();
}

/**
 * File browsing support for cicleinscription file areas
 *
 * @package mod_cicleinscription
 * @category files
 *
 * @param file_browser $browser
 * @param array $areas
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @param string $filearea
 * @param int $itemid
 * @param string $filepath
 * @param string $filename
 * @return file_info instance or null if not found
 */
function cicleinscription_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename) {
    return null;
}

/**
 * Serves the files from the cicleinscription file areas
 *
 * @package mod_cicleinscription
 * @category files
 *
 * @param stdClass $course the course object
 * @param stdClass $cm the course module object
 * @param stdClass $context the cicleinscription's context
 * @param string $filearea the name of the file area
 * @param array $args extra arguments (itemid, path)
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 */
function cicleinscription_pluginfile($course, $cm, $context, $filearea, array $args, $forcedownload, array $options=array()) {
    global $DB, $CFG;

    if ($context->contextlevel != CONTEXT_MODULE) {
        send_file_not_found();
    }

    require_login($course, true, $cm);

    send_file_not_found();
}

////////////////////////////////////////////////////////////////////////////////
// Navigation API                                                             //
////////////////////////////////////////////////////////////////////////////////

/**
 * Extends the global navigation tree by adding cicleinscription nodes if there is a relevant content
 *
 * This can be called by an AJAX request so do not rely on $PAGE as it might not be set up properly.
 *
 * @param navigation_node $navref An object representing the navigation tree node of the cicleinscription module instance
 * @param stdClass $course
 * @param stdClass $module
 * @param cm_info $cm
 */
function cicleinscription_extend_navigation(navigation_node $navref, stdclass $course, stdclass $module, cm_info $cm) {
	return false;
}

/**
 * Extends the settings navigation with the cicleinscription settings
 *
 * This function is called when the context for the page is a cicleinscription module. This is not called by AJAX
 * so it is safe to rely on the $PAGE.
 *
 * @param settings_navigation $settingsnav {@link settings_navigation}
 * @param navigation_node $cicleinscriptionnode {@link navigation_node}
 */
function cicleinscription_extend_settings_navigation(settings_navigation $settingsnav, navigation_node $cicleinscriptionnode=null) {
	global $USER, $PAGE, $CFG, $DB, $OUTPUT;
    // TODO Delete this function and its docblock, or implement it.
   /* if(is_siteadmin($USER->id)){
		//cicleinscription_add_navigation($cm->id);
	}*/
	
	// Orgaos
	$node = $cicleinscriptionnode->add(get_string('organplural', 'cicleinscription'), null, navigation_node::TYPE_CONTAINER);
	$notenode = $node->add(get_string('add', 'cicleinscription'), new moodle_url('/mod/cicleinscription/organ.php?coursemodulesid='.$PAGE->cm->id));
	$notenode = $node->add(get_string('list', 'cicleinscription'), new moodle_url('/mod/cicleinscription/organ_report.php?coursemodulesid='.$PAGE->cm->id.'&page=0&perpage='.ITEMS_PER_PAGE));
	$notenode = $node->add(get_string('addtocicle', 'cicleinscription'), new moodle_url('/mod/cicleinscription/cicleorgan.php?coursemodulesid='.$PAGE->cm->id));
	$notenode = $node->add(get_string('manage', 'cicleinscription'), new moodle_url('/mod/cicleinscription/organsincicle_list.php?coursemodulesid='.$PAGE->cm->id));
	// cursos
	$node = $cicleinscriptionnode->add(get_string('courses'), null, navigation_node::TYPE_CONTAINER);
	$notenode = $node->add(get_string('addtocicle', 'cicleinscription'), new moodle_url('/mod/cicleinscription/course_prematriculation.php?coursemodulesid='.$PAGE->cm->id));
	$notenode = $node->add(get_string('manage', 'cicleinscription'), new moodle_url('/mod/cicleinscription/course_prematriculation_list.php?coursemodulesid='.$PAGE->cm->id));
	
	// Penalidades
	$node = $cicleinscriptionnode->add(get_string('blacklist', 'cicleinscription'), null, navigation_node::TYPE_CONTAINER);
	$notenode = $node->add(get_string('add', 'cicleinscription'), new moodle_url('/mod/cicleinscription/blacklist.php?coursemodulesid='.$PAGE->cm->id));
	$notenode = $node->add(get_string('list', 'cicleinscription'), new moodle_url('/mod/cicleinscription/blacklist_report.php?coursemodulesid='.$PAGE->cm->id.'&page=0&perpage='.ITEMS_PER_PAGE));
	$notenode = $node->add(get_string('removeexceededpenalties', 'cicleinscription'), new moodle_url('/mod/cicleinscription/blacklistdeleteexceeded.php?coursemodulesid='.$PAGE->cm->id.'&update=true'));
	
	// Relatorios
	$node = $cicleinscriptionnode->add(get_string('report', 'cicleinscription'), null, navigation_node::TYPE_CONTAINER);
	$notenode = $node->add(get_string('reportinscriptions', 'cicleinscription'), new moodle_url('/mod/cicleinscription/report_inscriptions.php?coursemodulesid='.$PAGE->cm->id.'&page=0&perpage='.ITEMS_PER_PAGE));
	$notenode = $node->add(get_string('inscriptionsbyorgan', 'cicleinscription'), new moodle_url('/mod/cicleinscription/inscriptionbyorgan_report.php?coursemodulesid='.$PAGE->cm->id));
	$notenode = $node->add(get_string('extractrecords', 'cicleinscription'), new moodle_url('/mod/cicleinscription/extractionrecords.php?coursemodulesid='.$PAGE->cm->id.'&download=txt'));
	
	// Status
	$node = $cicleinscriptionnode->add(get_string('status', 'cicleinscription'), new moodle_url('/mod/cicleinscription/status_report.php?coursemodulesid='.$PAGE->cm->id.'&page=0&perpage='.ITEMS_PER_PAGE));
	
	// View form
	$node = $cicleinscriptionnode->add(get_string('viewform', 'cicleinscription'), new moodle_url('/mod/cicleinscription/view.php?id='.$PAGE->cm->id.'&v=true'));
}

/**
 * Funcao responsavel por salvar um registro no banco de dados
 * 
 * @package		mod/cicleinscription
 * @param 		$table
 * @param 		$data
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @copyright	CEAJUD - CNJ
 * */
function cicleinscription_save($table, $data){
	#echo "<pre>"; var_dump($data); echo "</pre>"; die();
	global $DB;
	// Insert
	if (!$data->id) {
		return $DB->insert_record($table, $data);
	}
	// Update
	else{
		return $DB->update_record($table, $data);
	}
}

/**
 * Funcao responsavel pela listagem dos registros de uma tabela
 * @param	$table
 * @param	$page
 * @param	$perpage
 * @param	$sort
 * @package mod/cicleinscription
 * @author	Leo Santos<leo@cnj.jus.br>
 * @return	stdClass $items
 */
function cicleinscription_get_itemsTable($table, $page = 0, $perpage = 0, $sort = "o.name ASC"){
	global $DB;

	$limitsql = '';
	$page = (int) $page;
	$perpage = (int) $perpage;

	# Iniciando paginacao
	if($page || $perpage){
	if ($page < 0) {
	$page = 0;
	}

	if ($perpage > ITEMS_MAX_PER_PAGE) {
	$perpage = ITEMS_MAX_PER_PAGE;
	} else if ($perpage < 1) {
		$perpage = ITEMS_PER_PAGE;
	}
	$limitsql = " LIMIT $perpage" . " OFFSET " . $page * $perpage;
	}
	
	// recupera todos os itens cadastrados
	$items = $DB->get_records_sql("SELECT o.*
				FROM {{$table}} o
				ORDER BY {$sort} {$limitsql}"
			);
	
	return $items;
}
/**
 * Funcao responsavel por recuperar todos os registros da blacklist que ja cumpriram o periodo de 1 ano.
 * @package mod/cicleinscription
 * @author	Leo Santos<leo@cnj.jus.br>
 * @return	stdClass $items
 */
function cicleinscription_get_blacklistexceeded(){
	global $DB;
	
	$sql = "SELECT
				id,
				username,
				TIMESTAMPDIFF(YEAR, FROM_UNIXTIME(datetimeinput), CURRENT_TIMESTAMP) as difference,
				FROM_UNIXTIME(datetimeinput) datetimeinput,
				CURRENT_TIMESTAMP as today
			FROM 
				{ci_blacklist}
			WHERE 
				statusblacklist = 's'
			AND TIMESTAMPDIFF(YEAR, FROM_UNIXTIME(datetimeinput), CURRENT_TIMESTAMP) > 0;";
			
	return $DB->get_records_sql($sql);
}

/**
 * Funcao responsavel por recuperar o total de registros da blacklist que ja cumpriram o periodo de 1 ano.
 * @package mod/cicleinscription
 * @author	Leo Santos<leo@cnj.jus.br>
 * @return	stdClass $items
 */
function cicleinscription_get_count_blacklistexceeded(){
	global $DB;
	
	$sql = "SELECT
				Count(username) as total
			FROM 
				{ci_blacklist}
			WHERE 
				statusblacklist = 's'
			AND TIMESTAMPDIFF(YEAR, FROM_UNIXTIME(datetimeinput), CURRENT_TIMESTAMP) > 0;";
			
	return $DB->get_record_sql($sql);
}

/**
 * Funcao responsavel por recuperar todos os registros da GRID de blacklist.
 * @param	$page
 * @param	$perpage
 * @param	$sort
 * @package mod/cicleinscription
 * @author	Leo Santos<leo@cnj.jus.br>
 * @return	stdClass $items
 */

function cicleinscription_get_users_blacklist($page = 0, $perpage = 0, $username = null, $courseid = null, $inputtype = null, $statusblacklist = null, $sort = "u.firstname ASC"){
	global $DB;
	
	$limitsql = '';
	$page = (int) $page;
	$perpage = (int) $perpage;
	
	# Iniciando paginacao
	if($page || $perpage){
		if ($page < 0) {
			$page = 0;
		}
	
		if ($perpage > ITEMS_MAX_PER_PAGE) {
			$perpage = ITEMS_MAX_PER_PAGE;
		} else if ($perpage < 1) {
			$perpage = ITEMS_PER_PAGE;
		}
		$limitsql = " LIMIT $perpage" . " OFFSET " . $page * $perpage;
	}
	
	// validando filtro
	$andfilter = false;
	$arrayfilter = array($statusblacklist);
	
	if($username){
		$andfilter = 'AND bl.username = ?';
		array_push($arrayfilter, $username);
	}
	if ($courseid){
		$andfilter .= 'AND bl.courseid = ?';
		array_push($arrayfilter, $courseid);
	}
	if ($inputtype){
		$andfilter .= 'AND bl.inputtype = ?';
		array_push($arrayfilter, $inputtype);
	}
	
	// recupera itens cadastrados
	$users_blacklist = $DB->get_records_sql("
			SELECT 
				   bl.id,
				   concat(u.firstname,' ', u.lastname) as userfullname,
			       u.email,
			       u.username,
			       bl.datetimeinput,
			       bl.inputtype,
			       c.fullname,
			       c.shortname
			FROM   {user} u
			       INNER JOIN {ci_blacklist} bl
			               ON u.id = bl.userid
			       INNER JOIN {course} c
			               ON bl.courseid = c.id
			WHERE  bl.statusblacklist = ?
			{$andfilter}
			ORDER BY {$sort} {$limitsql}", 
			$arrayfilter
	);
	
	return $users_blacklist;
}


/**
 * Funcao responsavel por recuperar todos os registros da GRID do relatorio de inscricoes.
 * @param	$page
 * @param	$perpage
 * @param	$sort
 * @package mod/cicleinscription
 * @author	Leo Santos<leo@cnj.jus.br>
 * @return	stdClass $items
 */

function cicleinscription_get_report_inscriptions($page = 0, $perpage = 0, $username = null, $organid = null, $courseid = null, $cicleinscriptionid, $sort = "u.firstname ASC"){
	global $DB;

	$limitsql = '';
	$page = (int) $page;
	$perpage = (int) $perpage;

	# Iniciando paginacao
	if($page || $perpage){
		if ($page < 0) {
			$page = 0;
		}else if ($perpage < 1) {
			$perpage = ITEMS_PER_PAGE;
		}
		$limitsql = " LIMIT $perpage" . " OFFSET " . $page * $perpage;
	}

	// validando filtro
	$andfilter = false;
	$arrayfilter = array($cicleinscriptionid);

	if($username){
	$andfilter = 'AND ci.username = ?';
		array_push($arrayfilter, $username);
	}
	if ($courseid){
	$andfilter .= 'AND ci.course_prematriculationid = ?';
		array_push($arrayfilter, $courseid);
	}
	if ($organid){
	$andfilter .= 'AND ci.organid = ?';
		array_push($arrayfilter, $organid);
	}

	// recupera itens cadastrados
	$rs = $DB->get_recordset_sql("
	SELECT Concat(u.firstname, ' ', u.lastname) as userfullname,
	   o.name as organ,
	   c.fullname as course,
	   u.city as city,
	   u.id as userid
	FROM
	   {ci_prematriculation} AS ci
	INNER JOIN
	   {user} u
	      ON ci.username = u.username
	INNER JOIN
		{ci_organ} o
	ON ci.organid = o.id
	INNER JOIN
		{course} c
	ON ci.course_prematriculationid = c.id
	WHERE
	   ci.cicleinscriptionid = ?
	AND ci.status_prematriculationid  = 1
	{$andfilter}
	ORDER BY {$sort} {$limitsql}",
	$arrayfilter);

	return $rs;
}


/**
 * Funcao responsavel por recuperar total de registros de inscricoes do ciclo corrente.
 * @package mod/cicleinscription
 * @param $cicleinscriptionid
 * @author	Leo Santos<leo@cnj.jus.br>
 * @return	stdClass $items
 */
function cicleinscription_get_recordsextractions_by_cicle($cicleinscriptionid){
	global $DB;
	
	$sql = "SELECT Concat(u.firstname, ' ', u.lastname) as userfullname,
		   	o.name as organ,
			u.username,
			u.email,
			u.city,
			u.department,
			u.phone1,
			u.aim,
			u.address,
		   c.fullname as course,
		   c.shortname,
		   u.city as city,
		   u.id as userid
		FROM
		   {ci_prematriculation} AS ci
		INNER JOIN
		   {user} u
		      ON ci.username = u.username
		INNER JOIN
			{ci_organ} o
		ON ci.organid = o.id
		INNER JOIN
			{course} c
		ON ci.course_prematriculationid = c.id
		WHERE
		   ci.cicleinscriptionid = ?
		AND ci.status_prematriculationid  = 1;";
		
		$rs = $DB->get_records_sql($sql, array($cicleinscriptionid));
		
		return $rs;
}

/**
 * Funcao responsavel por recuperar total de registros de inscricoes do ciclo de cursos abertos corrente.
 * @package mod/cicleinscription
 * @param $cicleinscriptionid
 * @author	Leo Santos<leo@cnj.jus.br>
 * @return	stdClass $items
 */
function cicleinscription_get_recordsopencoursesextractions_by_cicle($cicleinscriptionid){
	global $DB;
	
	$sql = "SELECT Concat(u.firstname, ' ', u.lastname) as userfullname,
			c.id as courseid,
			c.fullname as course,
			c.shortname,
			o.name as organ,
			u.username,
			u.email,
			dp.civilstate,
			dp.sex,
			dp.datebirth,
			dp.race,
			dp.rolefamily,
			dp.schooling,
			dp.incomefamily,
			dp.howdid,
			dp.deficient,
			dp.deficiency,
			dp.region,
			dp.state,
			u.city,
			u.id as userid
		FROM
		   {ci_prematriculation} ci
		INNER JOIN
		   {user} u
		      ON ci.username = u.username
		INNER JOIN
			{ci_organ} o
		ON ci.organid = o.id
		INNER JOIN
			{course} c
		ON ci.course_prematriculationid = c.id
		INNER JOIN 
			{ci_data_participant} dp
		ON u.id = dp.userid
		WHERE
		   ci.cicleinscriptionid = ?
		AND ci.status_prematriculationid  = 1;";
	
	$rs = $DB->get_records_sql($sql, array($cicleinscriptionid));
	
	return $rs;
}

/**
 * Funcao responsavel por verificar se determinado participante retirou o certificado.
 * @package mod/cicleinscription
 * @param $userid
 * @param $courseid
 * @author	Leo Santos<leo@cnj.jus.br>
 * @return	Boolean
 */
function cicleinscription_ecertificado_ussued($courseid, $userid){
	global $DB;
	$sql = "SELECT ec.id, 
			       eciss.id 
			FROM   {ecertificado} ec 
			       INNER JOIN {ecertificado_issues} eciss 
			               ON ec.id = eciss.ecertificadoid 
			WHERE  ec.course = ? 
			       AND eciss.userid = ?;";
	return $DB->get_record_sql($sql, array($courseid, $userid));
}
/**
 * Funcao responsavel por recuperar total de registros da GRID do relatorio de inscricoes.
 * @package mod/cicleinscription
 * @author	Leo Santos<leo@cnj.jus.br>
 * @return	stdClass $items
 */

function cicleinscription_get_count_report_inscriptions($username = null, $organid = null, $courseid = null, $cicleinscriptionid){
	global $DB;

	// validando filtro
	$andfilter = false;
	$arrayfilter = array($cicleinscriptionid);

	if($username){
	$andfilter = 'AND ci.username = ?';
		array_push($arrayfilter, $username);
	}
	if ($courseid){
	$andfilter .= 'AND ci.course_prematriculationid = ?';
		array_push($arrayfilter, $courseid);
	}
	if ($organid){
	$andfilter .= 'AND ci.organid = ?';
		array_push($arrayfilter, $organid);
	}

	// recupera itens cadastrados
	$rs = $DB->get_record_sql("
	SELECT Count(*) as total
	FROM
	   {ci_prematriculation} AS ci
	INNER JOIN
	   {user} u
	      ON ci.username = u.username
	INNER JOIN
		{ci_organ} o
	ON ci.organid = o.id
	INNER JOIN
		{course} c
	ON ci.course_prematriculationid = c.id
	WHERE
	   ci.cicleinscriptionid = ?
	AND ci.status_prematriculationid  = 1
	{$andfilter}",
	$arrayfilter);

	return $rs->total;
}

/**
 * Funcao responsavel por recuperar todos os registros da GRID de blacklist.
 * @param	$username (CPF)
 * @package mod/cicleinscription
 * @author	Leo Santos<leo@cnj.jus.br>
 * @return	stdClass $items
 */
function cicleinscription_verify_username_blacklist($username){
	global $DB;
	
	$result = $DB->get_record('ci_blacklist', array('username'=>$username, 'statusblacklist' => 's'));
	
	return $result ? $result : false;
}

/**
 * Funcao responsavel por recuperar todos os organs que ainda não foram adicionados para um determinado ciclo.
 *
 * @package		mod/cicleinscription
 * @param 		$cicleinscriptionid
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @copyright	CEAJUD - CNJ
 * */
function cicleinscription_get_organ_available_for_cicle($cicleinscriptionid){
	global $DB;
	$sort = 'o.name ASC';
	$OrgansAvailable = $DB->get_records_sql("SELECT o.*
				FROM {ci_organ} o
				WHERE o.id NOT IN (
					SELECT organid 
					FROM {ci_cicleorgan}  
					WHERE cicleinscriptionid = ?)
				ORDER BY {$sort}", array($cicleinscriptionid));
	
	return $OrgansAvailable;
}

/**
 * Funcao responsavel por recuperar todos os organs que foram adicionados a um determinado ciclo.
 *
 * @package		mod/cicleinscription
 * @param 		$cicleinscriptionid
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @copyright	CEAJUD - CNJ
 * */
function cicleinscription_get_organ_added_on_cicle($coursemodulesid){
	global $DB;
	$sort = 'o.name ASC';
	
	$orgnsAdded = $DB->get_records_sql("
		SELECT 
			co.id as cicleorganid,
			o.id as organid, 
			o.name,
			o.limitvacancies as organslimitvacancies,
			co.cicleinscriptionid, 
			co.coursemodulesid,
			co.limitvacancies as cicleorganslimitvacancies
		FROM   {ci_organ} AS o
		INNER JOIN {ci_cicleorgan} AS co
			ON o.id = co.organid
		WHERE  co.coursemodulesid = ?
		ORDER BY {$sort}", array($coursemodulesid));
	
	return $orgnsAdded;
}

/**
 * Funcao responsavel por recuperar todos os cursos visiveis.
 *
 * @package		mod/cicleinscription
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @copyright	CEAJUD - CNJ
 * @return		$courses
 *  * */
function cicleinscription_get_all_courses_visible($cicleinscriptionid){
	global $DB;
	$sort = 'c.fullname ASC';
	$visibility = 1;
	
	$courses = $DB->get_records_sql("
		SELECT
			c.id,
			c.shortname,
			c.fullname,
			cc.name as catname,
			cc.id as catid
		FROM
			{course} c
		INNER JOIN
			{course_categories} cc
		ON c.category = cc.id
		WHERE
			c.id NOT IN (
			SELECT 
				courseid
			FROM 
				{ci_course_prematriculation}
			WHERE 
				cicleinscriptionid = ?)
		AND
			c.visible = ?
		ORDER BY {$sort}", array($cicleinscriptionid, $visibility));
	
	return $courses;
}

/**
 * Funcao responsavel por recuperar todos os cursos que foram adicionados a um determinado ciclo.
 *
 * @package		mod/cicleinscription
 * @param 		$cicleinscriptionid
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @copyright	CEAJUD - CNJ
 * @return		$courses
 * */
function cicleinscription_get_courses_added_on_cicle($coursemodulesid){
	global $DB;
	$sort = 'c.fullname ASC';
	$coursesAdded = $DB->get_records_sql("
			SELECT
				ccp.*,
				c.fullname,
				c.shortname,
				c.id as cid
			FROM 
				{ci_course_prematriculation} as ccp
			INNER JOIN
				{course} c
			ON
				c.id = ccp.courseid
			WHERE 
				ccp.coursemodulesid = ?
			ORDER BY {$sort}",
			array($coursemodulesid));
		return $coursesAdded;
}
/**
 * Funcao responsavel por recuperar todos os cursos.
 *
 * @package		mod/cicleinscription
 * @param 		$cicleinscriptionid
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @copyright	CEAJUD - CNJ
 * @return		$courses
 * */
function cicleinscription_get_courses_all(){
	global $DB;
	$sort = 'fullname ASC';
	$courses = $DB->get_records_sql("
			SELECT
				id,
				fullname,
				shortname 
			FROM {course}
			WHERE
				category <> 0
			ORDER BY {$sort}");

	return $courses;
}

/**
 * Funcao responsavel por recuperar determinado curso por ciclo e pelo seu codigo identificador
 *
 * @package		mod/cicleinscription
 * @param 		$courseid
 * @param 		$cicleinscriptionid
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @copyright	CEAJUD - CNJ
 * */
function cicleinscription_get_courses_by_cicle_and_courseid($courseid, $cicleinscriptionid){
	global $DB;
	$sql = "SELECT
				ccp.*,
				c.fullname,
				c.shortname
			FROM 
				mdl_ci_course_prematriculation as ccp
			INNER JOIN
				mdl_course c
			ON
				c.id = ccp.courseid
			WHERE 
				ccp.cicleinscriptionid = ? AND ccp.courseid = ?";
	return $DB->get_record_sql($sql, array($cicleinscriptionid, $courseid));
}

/**
 * Funcao responsavel por recuperar o termo de responsabilidade de um determinado ciclo.
 *
 * @package		mod/cicleinscription
 * @param 		$coursemodulesid
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @copyright	CEAJUD - CNJ
 * @return		$cicleinscription
 * */
function cicleinscription_get_term_responsability($coursemodulesid){
	global $DB;
	$cm = get_coursemodule_from_id('cicleinscription', $coursemodulesid, 0, false, MUST_EXIST);
	$cicleinscription  = $DB->get_record('cicleinscription', array('id' => $cm->instance), '*', MUST_EXIST);
	return $cicleinscription;
}


/**
 * @method: responsavel por validar o CPF
 * @param string $cpf
 * @return boolean
 */
function cicleinscription_validaCPF($cpf){
	// Verifiva numero digitado
	$cpf = str_pad(preg_replace('/[^0-9]/', '', $cpf), 11, '0', STR_PAD_LEFT);

	// Verifica se nenhuma das sequencias abaixo foi digitada, caso seja, retorna falso
	if (strlen($cpf) != 11 || $cpf == '00000000000' || $cpf == '11111111111' || $cpf == '22222222222' || $cpf == '33333333333' || $cpf == '44444444444' || $cpf == '55555555555' || $cpf == '66666666666' || $cpf == '77777777777' || $cpf == '88888888888' || $cpf == '99999999999'){
		return false;
	}else{
		// Calcula os numeros para verificar se o CPF verdadeiro
		for($t = 9; $t < 11; $t++){
			for ($d = 0, $c = 0; $c < $t; $c++) {
				$d += $cpf{$c} * (($t + 1) - $c);
			}
			$d = ((10 * $d) % 11) % 10;
			
			if($cpf{$c} != $d){
				return false;
			}
		}
		return $cpf;
	}
}

/**
 * Funcao responsavel por identificar a quantidade de alunos inscritos em um curso
 *
 * @package		mod/cicleinscription
 * @param 		$courseid
 * @return		$qtde
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @copyright	CEAJUD - CNJ
 * */
function cicleinscription_get_number_enrolled_course($courseid){
	global $DB;
	$sql = "SELECT Count(*) AS qtde 
			FROM   {user_enrolments} 
			WHERE  enrolid IN(SELECT id 
                  FROM   mdl_enrol 
                  WHERE  courseid = ?)";	// Para recuperar apenas os inscritos com Funcao de estudante, basta fazer um join com a tabela {role_assignments} informando o roleid = 5
	 return $DB->get_record_sql($sql, array($courseid))->qtde;
}
/**
 * Funcao responsavel por recuperar um status_prematriculation
 *
 * @package		mod/cicleinscription
 * @param 		$status_prematriculationid
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @copyright	CEAJUD - CNJ
 * */
function cicleinscription_get_status_prematriculation($status_prematriculationid){
	global $DB;
	return $DB->get_record('ci_status_prematriculation', array('id' => $status_prematriculationid));
}

/**
 * Funcao responsavel por matricular aluno no curso
 *
 * @package		mod/cicleinscription
 * @param 		$courseid
 * @param		$userid
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @copyright	CEAJUD - CNJ
 * */
function cicleinscription_enrolling_user_on_course($courseid, $userid){
	global $DB;
	// Recuperando a chave da modalidade da matricula do curso
	$objEnrol = $DB->get_record_sql("SELECT id FROM {enrol} WHERE courseid=? AND enrol='manual'", array($courseid));
	
	// /Inscrevemdo o aluno na tabela mdl_user_enrolments
	$objUserEnrolments = new stdClass();
	$objUserEnrolments->status = 0;
	$objUserEnrolments->enrolid = $objEnrol->id;
	$objUserEnrolments->userid = $userid;
	$objUserEnrolments->timestart = strtotime('now');
	$objUserEnrolments->timeend = 0;
	$objUserEnrolments->timecreated = 0;
	$objUserEnrolments->timemodified = 0;
	
	cicleinscription_save('user_enrolments', $objUserEnrolments);
	// Recuperando o contexto do curso
	$objContext = $DB->get_record_sql("SELECT id FROM {context} WHERE instanceid=? AND contextlevel=50", array($courseid));
	
	// Efetuando a matricula do aluno no curso
	$objRoleAssignments = new stdClass();
	$objRoleAssignments->roleid = 5;
	$objRoleAssignments->contextid = $objContext->id;
	$objRoleAssignments->userid = $userid;
	$objRoleAssignments->timemodified = strtotime('now');
	
	return $DB->insert_record('role_assignments ', $objRoleAssignments);
}

/**
 * Funcao responsavel por enviar mensagem para o participante
 *
 * @package		mod/cicleinscription
 * @param 		$coursemodulesid
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @copyright	CEAJUD - CNJ
 * */
function cicleinscription_send_email_message($to, $subject, $messagetext, $userid){
	$user = cicleinscription_get_user_by_id($userid);
	$user->email = $to;
	#var_dump($subject);
	
	$from = DEFAULT_EMAIL_FROM_EAD;
	#resultado
	return email_to_user($user, $from, $subject, $messagetext);
}

/**
 * Funcao responsavel gerar a mensagem a ser enviada por email.
 *
 * @package		mod/cicleinscription
 * @param 		$coursename
 * @param 		$username
 * @param 		$password
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @copyright	CEAJUD - CNJ
 * */
function cicleinscription_generation_email_message($coursename, $username, $password, $name, $courseid){
	$email_message = "Prezado(a) {$name},

Informamos que você realizou sua matrícula para participar do curso '{$coursename}'.

No ambiente do curso você poderá conhecer o conteúdo, realizar as atividades propostas, visualizar a apresentação do curso, metodologia e o manual do aluno. Você poderá também, ambientar-se com a plataforma, conhecer as funcionalidades.

Instruções para acesso ao ambiente do curso, caso sua inscrição seja aceita:

1) Em seu navegador Mozilla firefox ou Google Chrome
2) Acessar o link http://www.cnj.jus.br/eadcnj/course/view.php?id={$courseid}
3) No lado esquerdo do site digite:

Nome de Usuário: {$username}
Senha: {$password}

Caso o(a) senhor(a) não consiga acessar a plataforma moodle, favor entrar em contato com a equipe técnica do CNJ/EAD pelo e-mail ead@cnj.jus.br.

Bons estudos!!

CEAJUD - Centro de Formação e Aperfeicoamento
de Servidores do Poder Judiciário
Conselho Nacional de Justiça";
	
// Retornando mensagem
#return htmlspecialchars($email_message);
return $email_message;
}

/**
 * Funcao responsavel gerar a mensagem a ser enviada por email para Vagas Remanescentes.
 *
 * @package		mod/cicleinscription
 * @param 		$coursename
 * @param 		$username
 * @param 		$password
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @copyright	CEAJUD - CNJ
 * */
function cicleinscription_generation_email_message_waiting_list($coursename, $username, $password, $name){
	$email_message = "Prezado(a) {$name},
	
Informamos que seu nome esta na lista de espera para as vagas remanescentes do curso '{$coursename}'.

Caso seja contemplado(a) com uma das vagas remanescentes, entraremos em contato para avisá-lo(a).

CEAJUD - Centro de Formaçao e Aperfeicoamento
de Servidores do Poder Judiciario
Conselho Nacional de Justiça";
	// Retornando mensagem
	return htmlspecialchars($email_message);
}

/// Criar Funcao para recuperar vagas remanecentes, ou seja, apresentar quantidade de vagas ainda restam por curso
function cicleinscription_get_vacancies_remaning(){
	
}
/**
 * Funcao responsavel por recuperar user a partir de um id
 *
 * @package		mod/cicleinscription
 * @param 		$userid
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @copyright	CEAJUD - CNJ
 * */
function cicleinscription_get_user_by_id($userid){
	global $DB;
	return $DB->get_record('user', array('id'=>$userid));
}

/**
 * Funcao responsavel por recuperar user a partir de um username
 *
 * @package		mod/cicleinscription
 * @param 		$username
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @copyright	CEAJUD - CNJ
 * */
function cicleinscription_get_user_by_username($username){
	global $DB;
	return $DB->get_record('user', array('username'=>$username));
}

/**
 * Funcao responsavel por recuperar {ci_data_participant} a partir de um userid
 *
 * @package		mod/cicleinscription
 * @param 		$userid
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @copyright	CEAJUD - CNJ
 * */
function cicleinscription_get_data_participant_by_userid($userid){
	global $DB;
	return $DB->get_record('ci_data_participant', array('userid'=>$userid));
}

/**
 * Funcao responsavel pela gravacao de um novo participante de cursos abertos na tabela {ci_data_participant}
 *
 * @package		mod/cicleinscription
 * @param 		stdClass $dataform
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @copyright	CEAJUD - CNJ
 * */

function cicleinscription_generate_objParticipant_opencourse(stdClass $dataform, $dataFormUserid){
	// Recebendo data de nascimento
	list($dia, $mes, $ano) = explode('/', $dataform->datebirth);
	
	// Montando objeto
	$objOpenCourses = new stdClass();
	$objOpenCourses->id = null;
	$objOpenCourses->userid = $dataFormUserid;
	$objOpenCourses->courseid = $dataform->course_prematriculationid;	# Recebendo id course (O nome parece referenciar o id da table ci_prematriculation) mas esta na verdade esta recebendo o id course passado pelo select box do formulario.
	$objOpenCourses->organid = $dataform->organid;
	$objOpenCourses->civilstate = $dataform->civilstate;
	$objOpenCourses->sex = $dataform->sex;
	$objOpenCourses->datebirth = mktime(00, 00, 00, $mes, $dia, $ano);
	$objOpenCourses->race = $dataform->race;
	$objOpenCourses->rolefamily = $dataform->rolefamily;
	$objOpenCourses->schooling = $dataform->schooling;
	$objOpenCourses->incomefamily = $dataform->incomefamily;
	$objOpenCourses->howdid = $dataform->howdid;
	$objOpenCourses->deficient = $dataform->deficient;
	$objOpenCourses->region = $dataform->region;
	$objOpenCourses->state = $dataform->state;
	
	// Verificando se usuario ja possui registro na tabela ci_data_participant
	if($objParticipant = cicleinscription_get_data_participant_by_userid($objOpenCourses->userid)){
		$objOpenCourses->id = $objParticipant->id;
	}
	
	// Salvando
	cicleinscription_save('ci_data_participant', $objOpenCourses);
}
