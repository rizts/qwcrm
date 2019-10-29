<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\User;

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Access\Access;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Table\Table;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;

/**
 * User class.  Handles all application interaction with a user
 *
 * @since  1.7.0
 */
class User //extends \JObject
{
    /**
     * A cached switch for if this user has root access rights.
     *
     * @var    boolean
     * @since  1.7.0
     */
    protected $isRoot = null;

    /**
     * Unique id
     *
     * @var    integer
     * @since  1.7.0
     */
    public $id = null;

    /**
     * The user's real name (or nickname)
     *
     * @var    string
     * @since  1.7.0
     */
    public $name = null;

    /**
     * The login name
     *
     * @var    string
     * @since  1.7.0
     */
    public $username = null;

    /**
     * The email
     *
     * @var    string
     * @since  1.7.0
     */
    public $email = null;

    /**
     * MD5 encrypted password
     *
     * @var    string
     * @since  1.7.0
     */
    public $password = null;

    /**
     * Clear password, only available when a new password is set for a user
     *
     * @var    string
     * @since  1.7.0
     */
    public $password_clear = '';

    /**
     * Block status
     *
     * @var    integer
     * @since  1.7.0
     */
    public $block = null;

    /**
     * Should this user receive system email
     *
     * @var    integer
     * @since  1.7.0
     */
    public $sendEmail = null;

    /**
     * Date the user was registered
     *
     * @var    \DateTime
     * @since  1.7.0
     */
    public $registerDate = null;

    /**
     * Date of last visit
     *
     * @var    \DateTime
     * @since  1.7.0
     */
    public $lastvisitDate = null;

    /**
     * Activation hash
     *
     * @var    string
     * @since  1.7.0
     */
    public $activation = null;

    /**
     * User parameters
     *
     * @var    Registry
     * @since  1.7.0
     */
    public $params = null;

    /**
     * Associative array of user names => group ids
     *
     * @var    array
     * @since  1.7.0
     */
    public $groups = array();

    /**
     * Guest status
     *
     * @var    integer
     * @since  1.7.0
     */
    public $guest = null;

    /**
     * Last Reset Time
     *
     * @var    string
     * @since  3.0.1
     */
    public $lastResetTime = null;

    /**
     * Count since last Reset Time
     *
     * @var    int
     * @since  3.0.1
     */
    public $resetCount = null;

    /**
     * Flag to require the user's password be reset
     *
     * @var    int
     * @since  3.2
     */
    public $requireReset = null;

    /**
     * User parameters
     *
     * @var    Registry
     * @since  1.7.0
     */
    protected $_params = null;

    /**
     * Authorised access groups
     *
     * @var    array
     * @since  1.7.0
     */
    protected $_authGroups = null;

    /**
     * Authorised access levels
     *
     * @var    array
     * @since  1.7.0
     */
    protected $_authLevels = null;

    /**
     * Authorised access actions
     *
     * @var    array
     * @since  1.7.0
     */
    protected $_authActions = null;

    /**
     * Error message
     *
     * @var    string
     * @since  1.7.0
     */
    protected $_errorMsg = null;

    /**
     * UserWrapper object
     *
     * @var    UserWrapper
     * @since  3.4
     * @deprecated  4.0  Use `Joomla\CMS\User\UserHelper` directly
     */
    protected $userHelper = null;

    /**
     * @var    array  User instances container.
     * @since  1.7.3
     */
    protected static $instances = array();
    
    /**
     * @var    string QWcrm User variables
     * 
     */    
    public $login_user_id           = null;    
    public $login_username          = null;
    public $login_usergroup_id      = 9;    // Default to a 'Public' user
    public $login_display_name      = null;
    public $login_token             = null;    
    public $login_is_employee       = null;
    public $login_client_id         = null;

    /**
     * Constructor activating the default information of the language
     *
     * @param   integer      $identifier  The primary key of the user to load (optional).
     * @param   UserWrapper  $userHelper  The UserWrapper for the static methods. [@deprecated 4.0]
     *
     * @since   1.7.0
     */
    public function __construct($identifier = 0, UserWrapper $userHelper = null)
    {
        if (null === $userHelper)
        {
            $userHelper = new UserWrapper;
        }

        $this->userHelper = $userHelper;

        // Create the user parameters object
        $this->_params = new Registry;

        // Load the user if it exists
        if (!empty($identifier))
        {
            $this->load($identifier);
        }
        else
        {
            // Initialise
            $this->id = 0;
            $this->sendEmail = 0;
            $this->aid = 0;
            $this->guest = 1;
        }
    }

    /**
     * Returns the global User object, only creating it if it doesn't already exist.
     *
     * @param   integer      $identifier  The primary key of the user to load (optional).
     * @param   UserWrapper  $userHelper  The UserWrapper for the static methods. [@deprecated 4.0]
     *
     * @return  User  The User object.
     *
     * @since   1.7.0
     */
    public static function getInstance($identifier = 0, UserWrapper $userHelper = null)
    {
        if (null === $userHelper)
        {
            $userHelper = new UserWrapper;
        }

        // Find the user id
        if (!is_numeric($identifier))
        {
            if (!$id = $userHelper->getUserId($identifier))
            {
                // If the $identifier doesn't match with any id, just return an empty User.
                return new User;
            }
        }
        else
        {
            $id = $identifier;
        }

        // If the $id is zero, just return an empty User.
        // Note: don't cache this user because it'll have a new ID on save!
        if ($id === 0)
        {
            return new User;
        }

        // Check if the user ID is already cached.
        if (empty(self::$instances[$id]))
        {
            $user = new User($id, $userHelper);
            self::$instances[$id] = $user;
        }

        return self::$instances[$id];
    }

    /**
     * Method to get a parameter value
     *
     * @param   string  $key      Parameter key
     * @param   mixed   $default  Parameter default value
     *
     * @return  mixed  The value or the default if it did not exist
     *
     * @since   1.7.0
     */
    public function getParam($key, $default = null)
    {
        return $this->_params->get($key, $default);
    }

    /**
     * Method to set a parameter
     *
     * @param   string  $key    Parameter key
     * @param   mixed   $value  Parameter value
     *
     * @return  mixed  Set parameter value
     *
     * @since   1.7.0
     */
    public function setParam($key, $value)
    {
        return $this->_params->set($key, $value);
    }

    /**
     * Method to set a default parameter if it does not exist
     *
     * @param   string  $key    Parameter key
     * @param   mixed   $value  Parameter value
     *
     * @return  mixed  Set parameter value
     *
     * @since   1.7.0
     */
    public function defParam($key, $value)
    {
        return $this->_params->def($key, $value);
    }

    /**
     * Method to check User object authorisation against an access control
     * object and optionally an access extension object
     *
     * @param   string  $action     The name of the action to check for permission.
     * @param   string  $assetname  The name of the asset on which to perform the action.
     *
     * @return  boolean  True if authorised
     *
     * @since   1.7.0
     */
    public function authorise($action, $assetname = null)
    {
        // Make sure we only check for core.admin once during the run.
        if ($this->isRoot === null)
        {
            $this->isRoot = false;

            // Check for the configuration file failsafe.
            $rootUser = \JFactory::getConfig()->get('root_user');

            // The root_user variable can be a numeric user ID or a username.
            if (is_numeric($rootUser) && $this->id > 0 && $this->id == $rootUser)
            {
                $this->isRoot = true;
            }
            elseif ($this->username && $this->username == $rootUser)
            {
                $this->isRoot = true;
            }
            elseif ($this->id > 0)
            {
                // Get all groups against which the user is mapped.
                $identities = $this->getAuthorisedGroups();
                array_unshift($identities, $this->id * -1);

                if (Access::getAssetRules(1)->allow('core.admin', $identities))
                {
                    $this->isRoot = true;

                    return true;
                }
            }
        }

        return $this->isRoot ? true : (bool) Access::check($this->id, $action, $assetname);
    }

    /**
     * Method to return a list of all categories that a user has permission for a given action
     *
     * @param   string  $component  The component from which to retrieve the categories
     * @param   string  $action     The name of the section within the component from which to retrieve the actions.
     *
     * @return  array  List of categories that this group can do this action to (empty array if none). Categories must be published.
     *
     * @since   1.7.0
     */
    public function getAuthorisedCategories($component, $action)
    {
        // Brute force method: get all published category rows for the component and check each one
        // TODO: Modify the way permissions are stored in the db to allow for faster implementation and better scaling
        $db = \JFactory::getDbo();

        $subQuery = $db->getQuery(true)
            ->select('id,asset_id')
            ->from('#__categories')
            ->where('extension = ' . $db->quote($component))
            ->where('published = 1');

        $query = $db->getQuery(true)
            ->select('c.id AS id, a.name AS asset_name')
            ->from('(' . (string) $subQuery . ') AS c')
            ->join('INNER', '#__assets AS a ON c.asset_id = a.id');
        $db->setQuery($query);
        $allCategories = $db->loadObjectList('id');
        $allowedCategories = array();

        foreach ($allCategories as $category)
        {
            if ($this->authorise($action, $category->asset_name))
            {
                $allowedCategories[] = (int) $category->id;
            }
        }

        return $allowedCategories;
    }

    /**
     * Gets an array of the authorised access levels for the user
     *
     * @return  array
     *
     * @since   1.7.0
     */
    public function getAuthorisedViewLevels()
    {
        if ($this->_authLevels === null)
        {
            $this->_authLevels = array();
        }

        if (empty($this->_authLevels))
        {
            $this->_authLevels = Access::getAuthorisedViewLevels($this->id);
        }

        return $this->_authLevels;
    }

    /**
     * Gets an array of the authorised user groups
     *
     * @return  array
     *
     * @since   1.7.0
     */
    public function getAuthorisedGroups()
    {
        if ($this->_authGroups === null)
        {
            $this->_authGroups = array();
        }

        if (empty($this->_authGroups))
        {
            $this->_authGroups = Access::getGroupsByUser($this->id);
        }

        return $this->_authGroups;
    }

    /**
     * Clears the access rights cache of this user
     *
     * @return  void
     *
     * @since   3.4.0
     */
    public function clearAccessRights()
    {
        $this->_authLevels = null;
        $this->_authGroups = null;
        $this->isRoot = null;
        Access::clearStatics();
    }

    /**
     * Pass through method to the table for setting the last visit date
     *
     * @param   integer  $timestamp  The timestamp, defaults to 'now'.
     *
     * @return  boolean  True on success.
     *
     * @since   1.7.0
     */
    public function setLastVisit($timestamp = null)
    {
        /*
        // Create the user table object
        $table = $this->getTable();
        $table->load($this->id);

        return $table->setLastVisit($timestamp);
        */
        
        $db = \Factory::getDbo();        
            $sql = "UPDATE ".PRFX."user_records SET last_active = ".mysql_datetime()." WHERE user_id = " . $db->qstr($this->id);
            $db->Execute($sql);
    
        return;
        
    }

    /**
     * Method to get the user parameters
     *
     * This method used to load the user parameters from a file.
     *
     * @return  object   The user parameters object.
     *
     * @since   1.7.0
     * @deprecated  4.0 - Instead use User::getParam()
     */
    public function getParameters()
    {
        // @codeCoverageIgnoreStart
        \JLog::add('User::getParameters() is deprecated. User::getParam().', \JLog::WARNING, 'deprecated');

        return $this->_params;

        // @codeCoverageIgnoreEnd
    }

    /**
     * Method to get the user timezone.
     *
     * If the user didn't set a timezone, it will return the server timezone
     *
     * @return \DateTimeZone
     *
     * @since 3.7.0
     */
    public function getTimezone()
    {
        $timezone = $this->getParam('timezone', \JFactory::getApplication()->get('offset', 'GMT'));

        return new \DateTimeZone($timezone);
    }

    /**
     * Method to get the user parameters
     *
     * @param   object  $params  The user parameters object
     *
     * @return  void
     *
     * @since   1.7.0
     */
    public function setParameters($params)
    {
        $this->_params = $params;
    }

    /**
     * Method to get the user table object
     *
     * This function uses a static variable to store the table name of the user table to
     * instantiate. You can call this function statically to set the table name if
     * needed.
     *
     * @param   string  $type    The user table name to be used
     * @param   string  $prefix  The user table prefix to be used
     *
     * @return  object  The user table object
     *
     * @note    At 4.0 this method will no longer be static
     * @since   1.7.0
     */
    public static function getTable($type = null, $prefix = 'JTable')
    {
        static $tabletype;

        // Set the default tabletype;
        if (!isset($tabletype))
        {
            $tabletype['name'] = 'user';
            $tabletype['prefix'] = 'JTable';
        }

        // Set a custom table type is defined
        if (isset($type))
        {
            $tabletype['name'] = $type;
            $tabletype['prefix'] = $prefix;
        }

        // Create the user table object
        return Table::getInstance($tabletype['name'], $tabletype['prefix']);
    }

    /**
     * Method to bind an associative array of data to a user object
     *
     * @param   array  &$array  The associative array to bind to the object
     *
     * @return  boolean  True on success
     *
     * @since   1.7.0
     */
    public function bind(&$array)
    {
        // Let's check to see if the user is new or not
        if (empty($this->id))
        {
            // Check the password and create the crypted password
            if (empty($array['password']))
            {
                $array['password'] = $this->userHelper->genRandomPassword();
                $array['password2'] = $array['password'];
            }

            // Not all controllers check the password, although they should.
            // Hence this code is required:
            if (isset($array['password2']) && $array['password'] != $array['password2'])
            {
            //\JFactory::getApplication()->enqueueMessage(\JText::_('JLIB_USER_ERROR_PASSWORD_NOT_MATCH'), 'error');
                        \Factory::getApplication()->enqueueMessage(_gettext("Passwords do not match. Please re-enter password."), 'error');             

                return false;
            }

            $this->password_clear = ArrayHelper::getValue($array, 'password', '', 'string');

            $array['password'] = $this->userHelper->hashPassword($array['password']);

            // Set the registration timestamp
            $this->set('registerDate', \Factory::getDate()->toSql());

            // Check that username is not greater than 150 characters
            $username = $this->get('username');

            if (strlen($username) > 150)
            {
                $username = substr($username, 0, 150);
                $this->set('username', $username);
            }
        }
        else
        {
            // Updating an existing user
            if (!empty($array['password']))
            {
                if ($array['password'] != $array['password2'])
                {
                    $this->setError(_gettext("Passwords do not match. Please re-enter password."));

                    return false;
                }

                $this->password_clear = ArrayHelper::getValue($array, 'password', '', 'string');

                // Check if the user is reusing the current password if required to reset their password
                if ($this->requireReset == 1 && $this->userHelper->verifyPassword($this->password_clear, $this->password))
                {
                    $this->setError(_gettext("You can't reuse your current password, please enter a new password."));

                    return false;
                }

                $array['password'] = $this->userHelper->hashPassword($array['password']);

                // Reset the change password flag
                $array['requireReset'] = 0;
            }
            else
            {
                $array['password'] = $this->password;
            }
        }

        if (array_key_exists('params', $array))
        {
            $this->_params->loadArray($array['params']);

            if (is_array($array['params']))
            {
                $params = (string) $this->_params;
            }
            else
            {
                $params = $array['params'];
            }

            $this->params = $params;
        }

        // Bind the array
        if (!$this->setProperties($array))
        {
            $this->setError(_gettext("Unable to bind array to user object."));

            return false;
        }

        // Make sure its an integer
        $this->id = (int) $this->id;

        return true;
    }

    /**
     * Method to save the User object to the database
     *
     * @param   boolean  $updateOnly  Save the object only if not a new user
     *                                Currently only used in the user reset password method.
     *
     * @return  boolean  True on success
     *
     * @since   1.7.0
     * @throws  \RuntimeException
     */
    public function save($updateOnly = false)
    { 
    
        // This function is not needed but is called by the shutdown code.
        return;

        // Create the user table object
        $table = $this->getTable();
        $this->params = (string) $this->_params;
        $table->bind($this->getProperties());

        // Allow an exception to be thrown.
        try
        {
            // Check and store the object.
            if (!$table->check())
            {
                $this->setError($table->getError());

                return false;
            }

            // If user is made a Super Admin group and user is NOT a Super Admin

            // @todo ACL - this needs to be acl checked

            $my = \JFactory::getUser();

            // Are we creating a new user
            $isNew = empty($this->id);

            // If we aren't allowed to create new users return
            if ($isNew && $updateOnly)
            {
                return true;
            }

            // Get the old user
            $oldUser = new User($this->id);

            // Access Checks

            // The only mandatory check is that only Super Admins can operate on other Super Admin accounts.
            // To add additional business rules, use a user plugin and throw an Exception with onUserBeforeSave.

            // Check if I am a Super Admin
            $iAmSuperAdmin = $my->authorise('core.admin');

            $iAmRehashingSuperadmin = false;

            if (($my->id == 0 && !$isNew) && $this->id == $oldUser->id && $oldUser->authorise('core.admin') && $oldUser->password != $this->password)
            {
                $iAmRehashingSuperadmin = true;
            }

            // We are only worried about edits to this account if I am not a Super Admin.
            if ($iAmSuperAdmin != true && $iAmRehashingSuperadmin != true)
            {
                // I am not a Super Admin, and this one is, so fail.
                if (!$isNew && Access::check($this->id, 'core.admin'))
                {
                    throw new \RuntimeException('User not Super Administrator');
                }

                if ($this->groups != null)
                {
                    // I am not a Super Admin and I'm trying to make one.
                    foreach ($this->groups as $groupId)
                    {
                        if (Access::checkGroup($groupId, 'core.admin'))
                        {
                            throw new \RuntimeException('User not Super Administrator');
                        }
                    }
                }
            }

            // Fire the onUserBeforeSave event.
            PluginHelper::importPlugin('user');
            $dispatcher = \JEventDispatcher::getInstance();

            $result = $dispatcher->trigger('onUserBeforeSave', array($oldUser->getProperties(), $isNew, $this->getProperties()));

            if (in_array(false, $result, true))
            {
                // Plugin will have to raise its own error or throw an exception.
                return false;
            }

            // Store the user data in the database
            $result = $table->store();

            // Set the id for the User object in case we created a new user.
            if (empty($this->id))
            {
                $this->id = $table->get('id');
            }

            if ($my->id == $table->id)
            {
                $registry = new Registry($table->params);
                $my->setParameters($registry);
            }

            // Fire the onUserAfterSave event
            $dispatcher->trigger('onUserAfterSave', array($this->getProperties(), $isNew, $result, $this->getError()));
        }
        catch (\Exception $e)
        {
            $this->setError($e->getMessage());

            return false;
        }

        return $result;

    }

    /**
     * Method to delete the User object from the database
     *
     * @return  boolean  True on success
     *
     * @since   1.7.0
     */
    public function delete()
    {
        PluginHelper::importPlugin('user');

        // Trigger the onUserBeforeDelete event
        $dispatcher = \JEventDispatcher::getInstance();
        $dispatcher->trigger('onUserBeforeDelete', array($this->getProperties()));

        // Create the user table object
        $table = $this->getTable();

        if (!$result = $table->delete($this->id))
        {
            $this->setError($table->getError());
        }

        // Trigger the onUserAfterDelete event
        $dispatcher->trigger('onUserAfterDelete', array($this->getProperties(), $result, $this->getError()));

        return $result;
    }

    /**
     * Method to load a User object by user id number
     *
     * @param   mixed  $id  The user id of the user to load
     *
     * @return  boolean  True on success
     *
     * @since   1.7.0
     */
    public function load($id)
    {
            /*
            // Create the user table object
            $table = $this->getTable();

            // Load the UserModel object based on the user id or throw a warning.
            if (!$table->load($id))
            {
                // Reset to guest user
                $this->guest = 1;

                \JLog::add(\JText::sprintf('JLIB_USER_ERROR_UNABLE_TO_LOAD_USER', $id), \JLog::WARNING, 'jerror');

                return false;
            }        
            */
        
            $db = \Factory::getDbo();

            $sql = "SELECT * FROM ".PRFX."user_records WHERE user_id = " . $db->qstr($id);        

            if(!$rs = $db->execute($sql)){

                // Reset to guest user
                $this->guest = 1;

                //$this->app->system->general->force_error_page('database', __FILE__, __FUNCTION__, $this->db->ErrorMsg(), $sql, _gettext("Failed to get the user details."));

                return false;

            } else {

                // Load the user record into an array
                $record = $rs->GetRowAssoc();            

                $this->username                 = $record['username'];
                $this->id                       = $record['user_id'];

                // Extra Variables (Added for QWcrm)
                $this->login_user_id            = $record['user_id'];
                $this->login_username           = $record['username'];            
                $this->login_usergroup_id       = $record['usergroup'];
                $this->login_display_name       = $record['first_name'].' '.$record['last_name'];
                $this->login_token              = 'login_verified';
                $this->login_is_employee        = $record['is_employee'];
                $this->login_client_id          = $record['client_id'];

                // If not active block the account
                if($record['active'] != '1') { $this->block = 1; }

            }

            /* Cannot use this method currently because components/user.php is not included
            if($record != get_user_details($id)) {

                // Reset to guest user
                $this->guest = 1;

                return false;

            } else {

                $this->username                 = $record['username'];
                $this->id                       = $record['user_id'];

                // Extra Variables (Added for QWcrm)
                $this->login_user_id            = $record['user_id'];
                $this->login_username           = $record['username'];            
                $this->login_usergroup_id       = $record['usergroup'];
                $this->login_display_name       = $record['display_name'];
                $this->login_token              = 'login_verified';
                $this->login_is_employee        = $record['is_employee'];
                $this->login_client_id        = $record['client_id'];

                // If not active block the account
                if($record['active'] != '1') { $this->block = 1; }

            }
            */


            /*
             * Set the user parameters using the default XML file.  We might want to
             * extend this in the future to allow for the ability to have custom
             * user parameters, but for right now we'll leave it how it is.
             *

            if ($table->params)
            {
                $this->_params->loadString($table->params);
            }
            */

            // Assuming all is well at this point let's bind the data
            //$this->setProperties($table->getProperties());

            // The user is no longer a guest
            if ($this->id != 0)
            {
                $this->guest = 0;
            }
            else
            {
                $this->guest = 1;
            }

            return true;            
    }

    /**
     * Method to allow serialize the object with minimal properties.
     *
     * @return  array  The names of the properties to include in serialization.
     *
     * @since   3.6.0
     */
    public function __sleep()
    {
        return array('id');
    }

    /**
     * Method to recover the full object on unserialize.
     *
     * @return  void
     *
     * @since   3.6.0
     */
    public function __wakeup()
    {
        // Initialise some variables
        $this->userHelper = new UserWrapper;
        $this->_params    = new Registry;

        // Load the user if it exists
        if (!empty($this->id) && $this->load($this->id))
        {
            // Push user into cached instances.
            self::$instances[$this->id] = $this;
        }
        else
        {
            // Initialise
            $this->id = 0;
            $this->sendEmail = 0;
                        //$this->aid = 0;     // not used in QWcrm
            $this->guest = 1;
        }
    }
        
    /**************************** Extra Functions Added ****************************/
        
    /**
     * From Joomla 3.7.0 joomla/libraries/cms/layout/base.php
     * Method to get the value from the data array
     *
     * @param   string  $key           Key to search for in the data array
     * @param   mixed   $defaultValue  Default value to return if the key is not set
     *
     * @return  mixed   Value from the data array | defaultValue if doesn't exist
     *
     * @since   3.5
     */
    public function get($key, $defaultValue = null)
    {
        return isset($this->data[$key]) ? $this->data[$key] : $defaultValue;
    }
    /**
     * From Joomla 3.7.0 joomla/libraries/cms/layout/base.php
     * Get the data being rendered
     *
     * @return  array
     *
     * @since   3.5
     */
    public function getData()
    {
        return $this->data;
    }
    /**
     * From Joomla 3.7.0 joomla/libraries/cms/layout/base.php
     * Method to set a value in the data array. Example: $layout->set('items', $items);
     *
     * @param   string  $key    Key for the data array
     * @param   mixed   $value  Value to assign to the key
     *
     * @return  self
     *
     * @since   3.5
     */
    public function set($key, $value)
    {
        $this->data[(string) $key] = $value;
        return $this;
    }     
}
