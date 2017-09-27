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

$coursemodulesid = optional_param('coursemodulesid',0, PARAM_INT);
$download = optional_param('download', '', PARAM_ALPHA);

if ($coursemodulesid) {
	$cm         = get_coursemodule_from_id('cicleinscription', $coursemodulesid, 0, false, MUST_EXIST);
	$course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
	$cicleinscription  = $DB->get_record('cicleinscription', array('id' => $cm->instance), '*', MUST_EXIST);
} else {
	error(get_string('errornotfoundidcourse', 'cicleinscription'));
}

require_login($course, true, $cm);
// Context

// strings
$strreport = get_string('report', 'cicleinscription');
// Gravando log
add_to_log($course->id, 'cicleinscription', 'extractionrecords', "extractionrecords.php?coursemodulesid={$cm->id}", $cicleinscription->name, $cm->id);

if($download == "xls"){
	require_once("$CFG->libdir/excellib.class.php");
	
	// Planilha para cursos de ciclo
	switch ($cicleinscription->typeform){
		case 'opencourses':	// Planilha de cursos abertos
			$records = cicleinscription_get_recordsopencoursesextractions_by_cicle($cicleinscription->id);
		
		    // Calculate file name
		    $filename = strip_tags(format_string(get_string('extractionrecords', 'cicleinscription'), true)).' - '.clean_filename("$cicleinscription->name " . userdate(time()) . '.xls');
		    // Creating a workbook
		    $workbook = new MoodleExcelWorkbook("-");
		    // Send HTTP headers
		    $workbook->send($filename);
		    // Creating the first worksheet
		    $myxls = $workbook->add_worksheet($strreport);
		
		    // Print names of all the fields
		    $myxls->write_string(0, 0, get_string("userfullname", "cicleinscription"));
			$myxls->write_string(0, 1, get_string("course"));
			$myxls->write_string(0, 2, get_string("shortname"));
		    $myxls->write_string(0, 3, get_string("organ", "cicleinscription"));
		    $myxls->write_string(0, 4, get_string("username"));
		    $myxls->write_string(0, 5, get_string("email"));
		    $myxls->write_string(0, 6, get_string("civilstate", "cicleinscription"));
		    $myxls->write_string(0, 7, get_string("sex", "cicleinscription"));
		    $myxls->write_string(0, 8, get_string("datebirth", "cicleinscription"));
		    $myxls->write_string(0, 9, get_string("age", "cicleinscription"));
		    $myxls->write_string(0, 10, get_string("race", "cicleinscription"));
		    $myxls->write_string(0, 11, get_string("rolefamily", "cicleinscription"));
		    $myxls->write_string(0, 12, get_string("schooling", "cicleinscription"));
		    $myxls->write_string(0, 13, get_string("incomefamily", "cicleinscription"));
		    $myxls->write_string(0, 14, get_string("howdid", "cicleinscription"));
		    $myxls->write_string(0, 15, get_string("deficient", "cicleinscription"));
		    $myxls->write_string(0, 16, get_string("deficiency", "cicleinscription"));
		    $myxls->write_string(0, 17, get_string("region", "cicleinscription"));
		    $myxls->write_string(0, 18, get_string("state", "cicleinscription"));
		    $myxls->write_string(0, 19, get_string("city"));
		    $myxls->write_string(0, 20, get_string("id", "cicleinscription"));
		
		    // Generate the data for the body of the spreadsheet
		    $i = 0;
		    $row = 1;
		    if ($records) {
		        foreach ($records as $record) {
		            $myxls->write_string($row, 0, $record->userfullname);
		            $myxls->write_string($row, 1, $record->course);
		            $myxls->write_string($row, 2, $record->shortname);
		            $myxls->write_string($row, 3, $record->organ);
		            $myxls->write_string($row, 4, $record->username);
		            $myxls->write_string($row, 5, $record->email);
		            $myxls->write_string($row, 6, $record->civilstate);
		            $myxls->write_string($row, 7, $record->sex);
		            $myxls->write_string($row, 8, date('d/m/Y',$record->datebirth));
		            $myxls->write_string($row, 9, date('Y') - date('Y', $record->datebirth));
		            $myxls->write_string($row, 10, $record->race);
		            $myxls->write_string($row, 11, $record->rolefamily);
		            $myxls->write_string($row, 12, $record->schooling);
		            $myxls->write_string($row, 13, $record->incomefamily);
		            $myxls->write_string($row, 14, $record->howdid);
		            $myxls->write_string($row, 15, $record->deficient);
		            $myxls->write_string($row, 16, $record->deficiency);
		            $myxls->write_string($row, 17, $record->region);
		            $myxls->write_string($row, 18, $record->state);
		            $myxls->write_string($row, 19, $record->city);
		            $myxls->write_string($row, 20, $record->userid);
		            $row++;
		        }
		        //$pos = 6;
		    }
		    // Close the workbook
		    $workbook->close();
		    exit;
			break;
			
		case 'cicle':		// Planilha de cursos com tutoria
			
			$records = cicleinscription_get_recordsextractions_by_cicle($cicleinscription->id);
		
		    // Calculate file name
		    $filename = strip_tags(format_string(get_string('extractionrecords', 'cicleinscription'), true)).' - '.clean_filename("$cicleinscription->name " . userdate(time()) . '.xls');
		    // Creating a workbook
		    $workbook = new MoodleExcelWorkbook("-");
		    // Send HTTP headers
		    $workbook->send($filename);
		    // Creating the first worksheet
		    $myxls = $workbook->add_worksheet($strreport);
		
		    // Print names of all the fields
		    $myxls->write_string(0, 0, get_string("userfullname", "cicleinscription"));
			$myxls->write_string(0, 1, get_string("course"));
			$myxls->write_string(0, 2, get_string("shortname"));
		    $myxls->write_string(0, 3, get_string("organ", "cicleinscription"));
		    $myxls->write_string(0, 4, get_string("username"));
		    $myxls->write_string(0, 5, get_string("email"));
		    $myxls->write_string(0, 6, get_string("city"));
		    $myxls->write_string(0, 7, get_string("stockingunit", "cicleinscription"));
		    $myxls->write_string(0, 8, get_string("stockingunitphone", "cicleinscription"));
		    $myxls->write_string(0, 9, get_string("matriculation", "cicleinscription"));
		    $myxls->write_string(0, 10, get_string("functionalemail", "cicleinscription"));
		    $myxls->write_string(0, 11, get_string("id", "cicleinscription"));
		
		    // Generate the data for the body of the spreadsheet
		    $i = 0;
		    $row = 1;
		    if ($records) {
		        foreach ($records as $record) {
		            $myxls->write_string($row, 0, $record->userfullname);
		            $myxls->write_string($row, 1, $record->course);
		            $myxls->write_string($row, 2, $record->shortname);
		            $myxls->write_string($row, 3, $record->organ);
		            $myxls->write_string($row, 4, $record->username);
		            $myxls->write_string($row, 5, $record->email);
		            $myxls->write_string($row, 6, $record->city);
		            $myxls->write_string($row, 7, $record->department);
		            $myxls->write_string($row, 8, $record->phone1);
		            $myxls->write_string($row, 9, $record->aim);
		            $myxls->write_string($row, 10, $record->address);
		            $myxls->write_string($row, 11, $record->userid);
		            $row++;
		        }
		        //$pos = 6;
		    }
		    // Close the workbook
		    $workbook->close();
		    exit;
			break;
	} // fim switch
	
} else if($download == "txt"){
	// Calculate file name
	$filename = strip_tags(format_string(get_string('extractionrecords', 'cicleinscription'), true)).' - '.clean_filename("$cicleinscription->name " . userdate(time()) . '.txt');
	
	header("Content-Type: application/download\n");
	header("Content-Disposition: attachment; filename=\"$filename\"");
	header("Expires: 0");
	header("Cache-Control: must-revalidate,post-check=0,pre-check=0");
	header("Pragma: public");
	
	switch ($cicleinscription->typeform){
		case 'opencourses':	// Planilha de cursos abertos
			$records = cicleinscription_get_recordsopencoursesextractions_by_cicle($cicleinscription->id);
			
			echo get_string("userfullname", "cicleinscription"). "\t";
			echo get_string("course"). "\t";
			echo get_string("shortname"). "\t";
			echo get_string("organ", "cicleinscription"). "\t";
			echo get_string("username"). "\t";
			echo get_string("email"). "\t";
			echo get_string("civilstate", "cicleinscription"). "\t";
			echo get_string("sex", "cicleinscription"). "\t";
			echo get_string("datebirth", "cicleinscription"). "\t";
			echo get_string("age", "cicleinscription"). "\t";
			echo get_string("race", "cicleinscription"). "\t";
			echo get_string("rolefamily", "cicleinscription"). "\t";
			echo get_string("schooling", "cicleinscription"). "\t";
			echo get_string("incomefamily", "cicleinscription"). "\t";
			echo get_string("howdid", "cicleinscription"). "\t";
			echo get_string("deficient", "cicleinscription"). "\t";
			echo get_string("deficiency", "cicleinscription"). "\t";
			echo get_string("region", "cicleinscription"). "\t";
			echo get_string("state", "cicleinscription"). "\t";
			echo get_string("city"). "\t";
			echo get_string("certifyissued", "cicleinscription"). "\t";
			echo get_string("id", "cicleinscription"). "\n";
			
			// Generate the data for the body of the spreadsheet
			$i = 0;
			$row = 1;
			if ($records) {
				foreach ($records as $record) {
					echo $record->userfullname . "\t";
					echo $record->course. "\t";
					echo $record->shortname. "\t";
					echo $record->organ. "\t";
					echo $record->username. "\t";
					echo $record->email. "\t";
					echo $record->civilstate. "\t";
					echo $record->sex. "\t";
					echo date('d/m/Y',$record->datebirth). "\t";
					echo date('Y') - date('Y', $record->datebirth). "\t";
					echo $record->race. "\t";
					echo $record->rolefamily. "\t";
					echo $record->schooling. "\t";
					echo $record->incomefamily. "\t";
					echo $record->howdid. "\t";
					echo $record->deficient. "\t";
					echo $record->deficiency. "\t";
					echo $record->region. "\t";
					echo $record->state. "\t";
					echo $record->city. "\t";
					if(cicleinscription_ecertificado_ussued($record->courseid, $record->userid)){
						echo "SIM \t";
					}else 
						echo "NAO \t";
					echo $record->userid . "\n";
					$row++;
				}
			}
			
			exit;
			break;
			
			case 'cicle':	// Planilha de cursos abertos
				$records = cicleinscription_get_recordsextractions_by_cicle($cicleinscription->id);
				
				// Print names of all the fields
				echo get_string("userfullname", "cicleinscription"). "\t";
				echo get_string("course"). "\t";
				echo get_string("shortname"). "\t";
				echo get_string("organ", "cicleinscription"). "\t";
				echo get_string("username"). "\t";
				echo get_string("email"). "\t";
				echo get_string("city"). "\t";
				echo get_string("stockingunit", "cicleinscription"). "\t";
				echo get_string("stockingunitphone", "cicleinscription"). "\t";
				echo get_string("matriculation", "cicleinscription"). "\t";
				echo get_string("functionalemail", "cicleinscription"). "\t";
				echo get_string("id", "cicleinscription"). "\n";
				
				// Generate the data for the body of the spreadsheet
				$i = 0;
				$row = 1;
				if ($records) {
					foreach ($records as $record) {
						echo $record->userfullname . "\t";
						echo $record->course . "\t";
						echo $record->shortname . "\t";
						echo $record->organ . "\t";
						echo $record->username . "\t";
						echo $record->email . "\t";
						echo $record->city . "\t";
						echo $record->department . "\t";
						echo $record->phone1 . "\t";
						echo $record->aim . "\t";
						echo $record->address . "\t";
						echo $record->userid . "\n";
						$row++;
					}
				}
					
			exit;
			break;
	}
}