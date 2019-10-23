<?php
/**
 * @package   QWcrm
 * @author    Jon Brown https://quantumwarp.com/
 * @copyright Copyright (C) 2016 - 2017 Jon Brown, All rights reserved.
 * @license   GNU/GPLv3 or later; https://www.gnu.org/licenses/gpl.html
 */

defined('_QWEXEC') or die;

require(INCLUDES_DIR.'client.php');
require(INCLUDES_DIR.'invoice.php');
require(INCLUDES_DIR.'workorder.php');

// Create an invoice for the supplied workorder
if(isset(\QFactory::$VAR['workorder_id']) && \QFactory::$VAR['workorder_id'] && !get_workorder_details(\QFactory::$VAR['workorder_id'], 'invoice_id')) {

    // Get client_id from the workorder    
    \QFactory::$VAR['client_id'] = get_workorder_details(\QFactory::$VAR['workorder_id'], 'client_id');
    
    // Create the invoice and return the new invoice_id
    \QFactory::$VAR['invoice_id'] = insert_invoice(\QFactory::$VAR['client_id'], \QFactory::$VAR['workorder_id'], get_client_details(\QFactory::$VAR['client_id'], 'unit_discount_rate'));
    
    // Update the workorder with the new invoice_id
    update_workorder_invoice_id(\QFactory::$VAR['workorder_id'], \QFactory::$VAR['invoice_id']);

    // Load the newly created invoice edit page
    force_page('invoice', 'edit&invoice_id='.\QFactory::$VAR['invoice_id']);
    
} 

// Invoice only
if((isset(\QFactory::$VAR['client_id'], \QFactory::$VAR['invoice_type']) && \QFactory::$VAR['client_id'] && \QFactory::$VAR['invoice_type'] == 'invoice-only')) {
    
    // Create the invoice and return the new invoice_id
    \QFactory::$VAR['invoice_id'] = insert_invoice(\QFactory::$VAR['client_id'], '', get_client_details(\QFactory::$VAR['client_id'], 'unit_discount_rate'));

    // Load the newly created invoice edit page
    force_page('invoice', 'edit&invoice_id='.\QFactory::$VAR['invoice_id']);
}    
  
// Fallback Error Control 
force_page('workorder', 'search', 'msg_danger='._gettext("You cannot create an invoice by the method you just tried, report to admins."));
