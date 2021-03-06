<?php
/**
 * @package   QWcrm
 * @author    Jon Brown https://quantumwarp.com/
 * @copyright Copyright (C) 2016 - 2017 Jon Brown, All rights reserved.
 * @copyright Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license   GNU/GPLv3 or later; https://www.gnu.org/licenses/gpl.html
 */

defined('_QWEXEC') or die;

// Config File
if(file_exists('configuration.php')){require_once('configuration.php');}

// Defines
require QFRAMEWORK_DIR . 'includes/defines.php';             // Load System Constants

/*require QFRAMEWORK_DIR . 'qwcrm/Error.php';                      // Configure PHP error reporting
require QFRAMEWORK_DIR . 'qwcrm/General.php';                      // Load General Library
require QFRAMEWORK_DIR . 'qwcrm/Security.php';                     // Load QWcrm Security including mandatory security code
require QFRAMEWORK_DIR . 'qwcrm/Pdf.php';                          // Load mPDF functions
require QFRAMEWORK_DIR . 'qwcrm/Email.php';                        // Load email transport
require QFRAMEWORK_DIR . 'qwcrm/Variables.php';                    // Configure variables to be used by QWcrm
require QFRAMEWORK_DIR . 'qwcrm/Router.php';                       // Route the page request
require QFRAMEWORK_DIR . 'qwcrm/Page.php';                         // Page related functions
require QFRAMEWORK_DIR . 'qwcrm/Sections/System.php';              // System Classes wrapping class
require QFRAMEWORK_DIR . 'qwcrm/CMSApplication.php';               // Main Framework class*/

// Joomla 3.x files dont all use proper autoloading conventions for their files and classes, these can all be autoloaded when i use Joomla 4.x framework

// Misc (Joomla)
require QFRAMEWORK_DIR . 'joomla/libraries/vendor/joomla/registry/src/Registry.php';            // Used to create a register for the class which can be manipulated (set/get/clear) and can be serialised into JSON compatible string for storage in the session
require QFRAMEWORK_DIR . 'joomla/libraries/vendor/joomla/application/src/Web/WebClient.php';    // Gets the browser details from the session (used in cookie creation)

// Input (Joomla)
require QFRAMEWORK_DIR . 'joomla/libraries/vendor/joomla/input/src/Input.php';                  // Joomla! Input Base Class                                         - Part of the Joomla Framework Input Package
require QFRAMEWORK_DIR . 'joomla/libraries/vendor/joomla/string/src/phputf8/native/core.php';   // Used just for function utf8_strpos() from JFilterInput           - Part of the Joomla Framework String Package
require QFRAMEWORK_DIR . 'joomla/libraries/vendor/joomla/string/src/StringHelper.php';          // Filtering of strings                                             - Part of the Joomla Framework String Package
require QFRAMEWORK_DIR . 'joomla/libraries/vendor/joomla/filter/src/InputFilter.php';           // InputFilter is a class for filtering input from any data source  - Part of the Joomla Framework String Package
require QFRAMEWORK_DIR . 'joomla/libraries/src/Filter/InputFilter.php';                         // A class for filtering input from any data source - used for QCookie and authentication
require QFRAMEWORK_DIR . 'joomla/libraries/src/Input/Input.php';                                // Joomla! Input Base Class - This is an abstracted input class used to manage retrieving data from the application environment.
require QFRAMEWORK_DIR . 'joomla/libraries/src/Input/Cookie.php';                               // Cookie Object with set and get
require QFRAMEWORK_DIR . 'joomla/libraries/fof/input/jinput/input.php';                         // This is an abstracted input class used to manage retrieving data from the application environment. (i.e. cookie.php)
require QFRAMEWORK_DIR . 'joomla/libraries/fof/input/jinput/cookie.php';                        // Extends input.php with cookie get and set functions to allow manipulation of cookie data via input.php class

// Crypto (Joomla)
require QFRAMEWORK_DIR . 'joomla/libraries/src/Crypt/Crypt.php';                                // Joomla Crypto Library
class_alias('\Joomla\CMS\Crypt\Crypt', '\JCrypt');                                              // Joomla uses an alias of 'Crypt'

// Session (Joomla)
require QFRAMEWORK_DIR . 'joomla/libraries/src/Session/Session.php';                            // Primary Class for managing HTTP sessions
require QFRAMEWORK_DIR . 'joomla/libraries/joomla/session/handler/interface.php';               // Interface for managing HTTP sessions - 'index file' no function shere
require QFRAMEWORK_DIR . 'joomla/libraries/joomla/session/handler/native.php';                  // Interface for managing HTTP sessions - extends interface.php
require QFRAMEWORK_DIR . 'joomla/libraries/joomla/session/handler/joomla.php';                  // Interface for managing HTTP sessions - extends native.php
require QFRAMEWORK_DIR . 'joomla/libraries/joomla/session/storage.php';                         // Custom session storage handler for PHP
require QFRAMEWORK_DIR . 'joomla/libraries/joomla/session/storage/none.php';                    // File session handler for PHP - Allows to set 'none' for session handler which defaults to standard session files
require QFRAMEWORK_DIR . 'joomla/libraries/joomla/session/storage/database.php';                // Database session storage handler for PHP - can use databse for session control

// Authentication (Joomla)
require QFRAMEWORK_DIR . 'joomla/plugins/system/remember/remember.php'; 
require QFRAMEWORK_DIR . 'joomla/plugins/authentication/cookie/cookie.php';                     // Facilitates 'Remember me' cookie authorisation
require QFRAMEWORK_DIR . 'joomla/plugins/authentication/joomla/joomla.php';                     // Facilitates standard username and password authorisation
require QFRAMEWORK_DIR . 'joomla/libraries/src/Authentication/AuthenticationResponse.php';      // Authentication response class, provides an object for storing user and error details - this is used to store the responses from the qwcrm.php and remember.php authorisation plugins
require QFRAMEWORK_DIR . 'joomla/libraries/src/Authentication/Authentication.php';              // Authentication class, provides an interface for the Joomla authentication system

// User (Joomla)
require QFRAMEWORK_DIR . 'joomla/libraries/src/User/User.php';                                  // User class - Handles all application interaction with a user
require QFRAMEWORK_DIR . 'joomla/libraries/src/User/UserHelper.php';                            // This contains password hassing functions etc.. associated with users but used elswhere
require QFRAMEWORK_DIR . 'joomla/libraries/src/User/UserWrapper.php';                           // Wrapper class for UserHelper
require QFRAMEWORK_DIR . 'joomla/plugins/user/joomla/joomla.php';                               // Basic User Objects interactions (login/logout) - class PlgUserJoomla extends JPlugin

// Load dependencies via composer
require(VENDOR_DIR.'autoload.php');

// Main QWcrm Factory class
require(QFRAMEWORK_DIR.'includes/Factory.php');

// Main QWcrm Framework class
require QFRAMEWORK_DIR . 'includes/CMSApplication.php';                                            

// Load the qframework class files
\CMSApplication::classFilesLoad(QFRAMEWORK_DIR.'Sections/', 'sections');
\CMSApplication::classFilesLoad(QFRAMEWORK_DIR.'System/', 'system');

// Load the application classe files
\CMSApplication::classFilesLoad(COMPONENTS_DIR.'_includes', 'components');
\CMSApplication::classFilesLoad(MODULES_DIR, 'modules');
\CMSApplication::classFilesLoad(PLUGINS_DIR, 'plugins');