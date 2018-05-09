<?php
/**
 * @package   QWcrm
 * @author    Jon Brown https://quantumwarp.com/
 * @copyright Copyright (C) 2016 - 2017 Jon Brown, All rights reserved.
 * @license   GNU/GPLv3 or later; https://www.gnu.org/licenses/gpl.html
 */

defined('_QWEXEC') or die;

require(INCLUDES_DIR.'components/customer.php');
require(INCLUDES_DIR.'components/schedule.php');
require(INCLUDES_DIR.'components/user.php');
require(INCLUDES_DIR.'components/workorder.php');

// Check if we have a employee_id and output is set to day
if($VAR['ics_type'] == 'day' && $employee_id == '') {    
    force_page('schedule', 'search', 'warning_msg='._gettext("Employee ID missing."));
    exit;
}

// Check if we have a schedule_id if output is not set to day
if($VAR['ics_type'] != 'day' && $schedule_id == '') {    
    force_page('schedule', 'search', 'warning_msg='._gettext("Schedule ID is missing."));
    exit;
}

// Check if we have all of the date information required
if($start_year == '' || $start_month == '' || $start_day == '') {
    force_page('user', 'search', 'warning_msg='._gettext("Some date information is missing."));
    exit;
}

// Add routines here to decide what is returned ie multi schedule, single schedule or a live calendar

// ICS Schedule
if($VAR['ics_type'] == 'day') {
    
    // Get Employee Display Name
    $user_display_name = get_user_details($db, $employee_id, 'display_name');
    
    // Set filename    
    $ics_filename = str_replace(' ', '-', $user_display_name).'_'._gettext("Day").'-'._gettext("Schedule").'_'.$start_year.'-'.$start_month.'-'.$start_day.'.ics';
    
    // Build Day Schedule for the employee as an .ics
    $ics_content =  build_ics_schedule_day($db, $employee_id, $start_year, $start_month, $start_day);
    
    // Log activity
    $record = 'Day Schedule'.' ('.$start_year.'-'.$start_month.'-'.$start_day.') '._gettext("for").' ' .$user_display_name.' '._gettext("has been exported.");
    write_record_to_activity_log($record, $employee_id);

// Single ICS
} else {
    
    // Get Schedule Details
    $schedule_details = get_schedule_details($db, $schedule_id);
    
    // Get Customer Display Name
    $customer_display_name = get_customer_details($db, $schedule_details['workorder_id'], 'display_name');
    
    // Set filename
    $ics_filename   = _gettext("Schedule").'-'.$schedule_id.'_'._gettext("WorkOrder").'-'.$schedule_details['workorder_id'].'_'.str_replace(' ', '-', $customer_display_name).'.ics';
    //$ics_filename   = 'schedule.ics';
    
    // Build a single schedule item as an .ics
    $ics_content =  build_single_schedule_ics($db, $schedule_id);
    
    // Log activity
    $record = _gettext("Schedule").' '.$schedule_id.' '._gettext("has been exported.");
    write_record_to_activity_log($record, $schedule_details['employee_id'], $schedule_details['customer_id'], $schedule_details['workorder_id']);
    
}

// Set the correct headers for this file
header('Content-type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $ics_filename);

// Output the .ics file
echo $ics_content;
