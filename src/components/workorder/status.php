<?php
/**
 * @package   QWcrm
 * @author    Jon Brown https://quantumwarp.com/
 * @copyright Copyright (C) 2016 - 2017 Jon Brown, All rights reserved.
 * @license   GNU/GPLv3 or later; https://www.gnu.org/licenses/gpl.html
 */

defined('_QWEXEC') or die;

require(INCLUDES_DIR.'client.php');
require(INCLUDES_DIR.'workorder.php');
require(INCLUDES_DIR.'user.php');

// Check if we have a workorder_id
if(!isset(\QFactory::$VAR['workorder_id']) || !\QFactory::$VAR['workorder_id']) {
    force_page('workorder', 'search', 'msg_danger='._gettext("No Workorder ID supplied."));
}

// Get the Id of the employee assigned to the workorder
$assigned_employee_id = get_workorder_details(\QFactory::$VAR['workorder_id'], 'employee_id');

// Update Work Order Status
if(isset(\QFactory::$VAR['change_status'])){
    update_workorder_status(\QFactory::$VAR['workorder_id'], \QFactory::$VAR['assign_status']);    
    force_page('workorder', 'status&workorder_id='.\QFactory::$VAR['workorder_id']);
}

// Assign Work Order to another employee
if(isset(\QFactory::$VAR['change_employee'])) {
    assign_workorder_to_employee(\QFactory::$VAR['workorder_id'], \QFactory::$VAR['target_employee_id']);    
    force_page('workorder', 'status&workorder_id='.\QFactory::$VAR['workorder_id']);
}

// Build the page with the current status from the database
$smarty->assign('allowed_to_change_status',     check_workorder_status_can_be_changed(\QFactory::$VAR['workorder_id']) );
$smarty->assign('allowed_to_change_employee',   check_workorder_allowed_to_change_employee(\QFactory::$VAR['workorder_id']));
$smarty->assign('allowed_to_delete',            check_workorder_status_allows_for_deletion(\QFactory::$VAR['workorder_id'])  );
$smarty->assign('active_employees',             get_active_users('employees')                                     );
$smarty->assign('workorder_statuses',           get_workorder_statuses(true)                                      );
$smarty->assign('workorder_status',             get_workorder_details(\QFactory::$VAR['workorder_id'], 'status')             );
$smarty->assign('workorder_status_display_name',get_workorder_status_display_name(get_workorder_details(\QFactory::$VAR['workorder_id'], 'status')));
$smarty->assign('assigned_employee_id',         $assigned_employee_id                                             );
$smarty->assign('assigned_employee_details',    get_user_details($assigned_employee_id)                           );