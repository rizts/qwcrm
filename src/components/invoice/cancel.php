<?php
/**
 * @package   QWcrm
 * @author    Jon Brown https://quantumwarp.com/
 * @copyright Copyright (C) 2016 - 2017 Jon Brown, All rights reserved.
 * @license   GNU/GPLv3 or later; https://www.gnu.org/licenses/gpl.html
 */

defined('_QWEXEC') or die;

require(CINCLUDES_DIR.'client.php');
require(CINCLUDES_DIR.'invoice.php');
require(CINCLUDES_DIR.'payment.php');
require(CINCLUDES_DIR.'report.php');
require(CINCLUDES_DIR.'voucher.php');
require(CINCLUDES_DIR.'workorder.php');

// Prevent direct access to this page
if(!check_page_accessed_via_qwcrm('invoice', 'status')) {
    header('HTTP/1.1 403 Forbidden');
    die(_gettext("No Direct Access Allowed."));
}

// Check if we have an invoice_id
if(!isset(\CMSApplication::$VAR['invoice_id']) || !\CMSApplication::$VAR['invoice_id']) {
    systemMessagesWrite('danger', _gettext("No Invoice ID supplied."));
    force_page('invoice', 'search');
}

// Cancel Invoice
if(!cancel_invoice(\CMSApplication::$VAR['invoice_id'])) {    
    
    // Load the invoice details page with error
    force_page('invoice', 'details&invoice_id='.\CMSApplication::$VAR['invoice_id'].'&msg_success='._gettext("The invoice failed to be cancelled."));
    
    
} else {   
    
    // Load the invoice search page with success message
    systemMessagesWrite('success', _gettext("The invoice has been cancelled successfully."));
    force_page('invoice', 'search');
    
}