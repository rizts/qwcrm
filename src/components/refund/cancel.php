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
require(INCLUDES_DIR.'refund.php');
require(INCLUDES_DIR.'report.php');
require(INCLUDES_DIR.'voucher.php');
require(INCLUDES_DIR.'workorder.php');

// Prevent direct access to this page
if(!check_page_accessed_via_qwcrm('refund', 'status')) {
    header('HTTP/1.1 403 Forbidden');
    die(_gettext("No Direct Access Allowed."));
}

// Check if we have a refund_id
if(!isset(\QFactory::$VAR['refund_id']) || !\QFactory::$VAR['refund_id']) {
    force_page('refund', 'search', 'msg_danger='._gettext("No Refund ID supplied."));
} 

// Cancel the refund function call
cancel_refund(\QFactory::$VAR['refund_id']);

// Load the refund search page
force_page('refund', 'search', 'msg_success='._gettext("Refund cancelled successfully."));
