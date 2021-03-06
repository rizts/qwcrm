<?php
/**
 * @package   QWcrm
 * @author    Jon Brown https://quantumwarp.com/
 * @copyright Copyright (C) 2016 - 2017 Jon Brown, All rights reserved.
 * @license   GNU/GPLv3 or later; https://www.gnu.org/licenses/gpl.html
 */

defined('_QWEXEC') or die;

// Check if we have a workorder_id
if(!isset(\CMSApplication::$VAR['workorder_id']) || !\CMSApplication::$VAR['workorder_id']) {
    $this->app->system->variables->systemMessagesWrite('danger', _gettext("No Workorder ID supplied."));
    $this->app->system->page->forcePage('workorder', 'search');
}

// Check if we can edit the workorder resolution
if($this->app->components->workorder->getRecord(\CMSApplication::$VAR['workorder_id'], 'is_closed')) {
    $this->app->system->variables->systemMessagesWrite('danger', _gettext("Cannot edit the resolution of a closed Work Order."));
    $this->app->system->page->forcePage('workorder', 'details&workorder_id='.\CMSApplication::$VAR['workorder_id']);
}

if(isset(\CMSApplication::$VAR['submit'])) {
    
    // Update Work Resolution Only
    if(\CMSApplication::$VAR['submit'] == 'submitchangesonly') {
        $this->app->components->workorder->updateResolution(\CMSApplication::$VAR['workorder_id'], \CMSApplication::$VAR['resolution']);
        $this->app->system->variables->systemMessagesWrite('success', _gettext("Resolution has been updated."));
        $this->app->system->page->forcePage('workorder', 'details&workorder_id='.\CMSApplication::$VAR['workorder_id']);
    }

    // Close without invoice
    if(\CMSApplication::$VAR['submit'] == 'closewithoutinvoice') {
        $this->app->components->workorder->closeWithoutInvoice(\CMSApplication::$VAR['workorder_id'], \CMSApplication::$VAR['resolution']);
        $this->app->system->variables->systemMessagesWrite('success', _gettext("Work Order has been closed without an invoice."));
        $this->app->system->page->forcePage('workorder', 'details&workorder_id='.\CMSApplication::$VAR['workorder_id']);
    }

    // Close with invoice
    if(\CMSApplication::$VAR['submit'] == 'closewithinvoice') {
        $this->app->components->workorder->closeWithInvoice(\CMSApplication::$VAR['workorder_id'], \CMSApplication::$VAR['resolution']);
        
        // Create a new invoice attached to this work order
        $this->app->system->variables->systemMessagesWrite('success', _gettext("Work Order has been closed with an invoice."));
        $this->app->system->page->forcePage('invoice', 'new&workorder_id='.\CMSApplication::$VAR['workorder_id']);
    }

}
        
// Build the page
$this->app->smarty->assign('resolution', $this->app->components->workorder->getRecord(\CMSApplication::$VAR['workorder_id'], 'resolution'));

    
    
