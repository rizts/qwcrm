<?php
/**
 * @package   QWcrm
 * @author    Jon Brown https://quantumwarp.com/
 * @copyright Copyright (C) 2016 - 2017 Jon Brown, All rights reserved.
 * @license   GNU/GPLv3 or later; https://www.gnu.org/licenses/gpl.html
 */

defined('_QWEXEC') or die;

// Check if we have an user_id
if(!isset(\CMSApplication::$VAR['user_id']) || !\CMSApplication::$VAR['user_id']) {
    $this->app->system->variables->systemMessagesWrite('danger', _gettext("No User ID supplied."));
    $this->app->system->general->force_page('user', 'search');
}

// If user data has been submitted, Update the record
if(isset(\CMSApplication::$VAR['submit'])) {

    // Check if the username or email have been used (the extra vareiable is to ignore the users current username and email to prevent submission errors when only updating other values)
    if (
            $this->app->components->user->check_user_username_exists(\CMSApplication::$VAR['qform']['username'], $this->app->components->user->get_user_details(\CMSApplication::$VAR['qform']['user_id'], 'username')) ||
            $this->app->components->user->check_user_email_exists(\CMSApplication::$VAR['qform']['email'], $this->app->components->user->get_user_details(\CMSApplication::$VAR['qform']['user_id'], 'email'))
        ) {

        // Reload the page with the POST'ed data        
        $this->app->smarty->assign('user_details', \CMSApplication::$VAR['qform']);               
        
    } else {    
            
        // Insert user record
        $this->app->components->user->update_user(\CMSApplication::$VAR['qform']);

        // Redirect to the new users's details page
        $this->app->system->variables->systemMessagesWrite('success', _gettext("User details updated."));
        $this->app->system->general->force_page('user', 'details&user_id='.\CMSApplication::$VAR['qform']['user_id']);
            
    }

} else { 
  
    $this->app->smarty->assign('user_details', $this->app->components->user->get_user_details(\CMSApplication::$VAR['user_id']));     
    
}

// Set the template for the correct user type (client/employee)
if($this->app->components->user->get_user_details(\CMSApplication::$VAR['user_id'], 'is_employee')) {
    $this->app->smarty->assign('is_employee', '1');
    $this->app->smarty->assign('usergroups', $this->app->components->user->get_usergroups('employees'));
} else {    
    $this->app->smarty->assign('is_employee', '0');
    $this->app->smarty->assign('client_display_name', $this->app->components->client->get_client_details($this->app->components->user->get_user_details(\CMSApplication::$VAR['user_id'], 'client_id'), 'client_display_name'));
    $this->app->smarty->assign('usergroups', $this->app->components->user->get_usergroups('clients')); 
}

// Build the page
$this->app->smarty->assign('user_locations', $this->app->components->user->get_user_locations());