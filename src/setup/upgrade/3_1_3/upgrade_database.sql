/*
 * @package   QWcrm
 * @author    Jon Brown https://quantumwarp.com/
 * @copyright Copyright (C) 2016 - 2017 Jon Brown, All rights reserved.
 * @license   GNU/GPLv3 or later; https://www.gnu.org/licenses/gpl.html
 */

--
-- Correct Payment Status on some records, issue introduced in an earlier version and fixed but records were never corrected
--

UPDATE `#__invoice_records`
SET `status` = 'paid', `closed_on` = `last_active`, `is_closed` = 1
WHERE `status` = 'partially_paid' AND `balance` = 0 AND `unit_gross` > 0;

UPDATE `#__refund_records`
SET `status` = 'paid', `closed_on` = `last_active`
WHERE `status` = 'partially_paid' AND `balance` = 0 AND `unit_gross` > 0;

UPDATE `#__expense_records`
SET `status` = 'paid', `closed_on` = `last_active`
WHERE `status` = 'partially_paid' AND `balance` = 0 AND `unit_gross` > 0;

UPDATE `#__otherincome_records`
SET `status` = 'paid', `closed_on` = `last_active`
WHERE `status` = 'partially_paid' AND `balance` = 0 AND `unit_gross` > 0;

--
--  Remove some erroneous indexes in #__user_keys
--

ALTER TABLE `#__user_keys` DROP INDEX `series_2`;
ALTER TABLE `#__user_keys` DROP INDEX `series_3`;