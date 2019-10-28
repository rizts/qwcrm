<?php
/**
 * @package   QWcrm
 * @author    Jon Brown https://quantumwarp.com/
 * @copyright Copyright (C) 2016 - 2017 Jon Brown, All rights reserved.
 * @license   GNU/GPLv3 or later; https://www.gnu.org/licenses/gpl.html
 */

defined('_QWEXEC') or die;

require(CINCLUDES_DIR.'client.php');
require(CINCLUDES_DIR.'expense.php');
require(CINCLUDES_DIR.'invoice.php');
require(CINCLUDES_DIR.'otherincome.php');
require(CINCLUDES_DIR.'payment.php');
require(CINCLUDES_DIR.'refund.php');
require(CINCLUDES_DIR.'report.php');
require(CINCLUDES_DIR.'voucher.php');
require(CINCLUDES_DIR.'workorder.php');

// Prevent direct access to this page
if(!check_page_accessed_via_qwcrm('payment', 'status')) {
    header('HTTP/1.1 403 Forbidden');
    die(_gettext("No Direct Access Allowed."));
}

// Check if we have an payment_id
if(!isset(\CMSApplication::$VAR['payment_id']) || !\CMSApplication::$VAR['payment_id']) {
    systemMessagesWrite('danger', _gettext("No Payment ID supplied."));
    force_page('payment', 'search');
}   

// This is a dirty hack because QWcrm is not fully OOP yet
class DeletePayment {
    
    private $VAR = null;
    private $type = null;    
    private $payment_details = null;
    
    function __construct(&$VAR) {
        
        // Set class variables
        $this->VAR = &$VAR;
        
        // Set Payment details
        $this->payment_details = get_payment_details($VAR['payment_id']);
        
        // Set the various payment type IDs
        $this->VAR['qpayment']['payment_id'] = $this->payment_details['payment_id'];
        $this->VAR['qpayment']['type'] = $this->payment_details['type'];
        $this->VAR['qpayment']['invoice_id'] = $this->payment_details['invoice_id'];
        $this->VAR['qpayment']['voucher_id'] = $this->payment_details['voucher_id'];
        $this->VAR['qpayment']['refund_id'] = $this->payment_details['refund_id'];
        $this->VAR['qpayment']['expense_id'] = $this->payment_details['expense_id'];
        $this->VAR['qpayment']['otherincome_id'] = $this->payment_details['otherincome_id'];
                               
        // Set the payment type class
        $this->set_payment_type();
        
        // Run the type specific delete routines
        $this->type->delete();       
               
    }
        
    function set_payment_type() {
        
        // Load the routines specific for the specific payment type
        switch($this->VAR['qpayment']['type']) {

            case 'invoice':
            require(COMPONENTS_DIR.'payment/types/invoice.php');
            break;

            case 'refund':
            require(COMPONENTS_DIR.'payment/types/refund.php');
            break;

            case 'expense':
            require(COMPONENTS_DIR.'payment/types/expense.php');
            break;

            case 'otherincome':
            require(COMPONENTS_DIR.'payment/types/otherincome.php');
            break;

            default:
            systemMessagesWrite('danger', _gettext("Invalid Payment Type."));
            force_page('payment', 'search');
            break;

        }
        
        // Load and set the relevant class
        $this->type = new PType($this->VAR);
    
    }
    
}

// Instanciate Delete Payment Class
$payment = new DeletePayment(\CMSApplication::$VAR);