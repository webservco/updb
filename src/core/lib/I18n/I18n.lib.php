<?php
/**
 * $Id: I18n.lib.php 220 2016-05-19 14:50:17Z radu $
 *
 * Internationalization library.
 *
 * @author     Radu Maier <radu@webserv.co>
 * @copyright  Copyright (c) 2008 - 2014, Radu Maier
 * @license    http://www.opensource.org/licenses/bsd-license.php
 * @package    WEb
 * @subpackage WebI18n
 * @version    $Revision: 220 $
 * @since      Version 1
 * @access     public
 * @filesource
 */

/**
 * Security check.
 */
if (! defined('_WEB')) {
    /**
     * Stop script execution.
     */
    exit();
}

/**
 * I18n
 * Internationalization library.
 *
 * @author Radu Maier <radu@webserv.co>
 * @package WEb
 * @subpackage WebI18n
 */
class I18n extends Library
{

    /**
     * Stores current locale.
     *
     * @var string
     */
    private $_mLocale;

    /**
     * Stores the list of loaded domains.
     *
     * @var array
     */
    private $_mDomains = array();

    /**
     * Stores the current domain.
     *
     * @var string
     */
    private $_mDomain;

    /**
     * Implement GetInstance() method.
     * This is required for PHP < 5.3 compatibility (no late static binding)
     *
     * @param string $className
     *            class name to instantiate
     * @return object
     */
    public static function GetInstance($className = __CLASS__)
    {
        
        /**
         * Call parent method with current class name as parameter.
         */
        return parent::_GetInstance($className);
    }

    /**
     * Initialize.
     * Sets locale data and loads the default domain message catalog.
     *
     * @param string $i18nLocale            
     * @param string $defaultDomain            
     *
     * @return bool
     */
    public function Init($i18nLocale = NULL, $defaultDomain = NULL)
    {
        
        /**
         * Set locale data.
         */
        $this->SetLocale($i18nLocale);
        
        /**
         * Add default domain message catalog.
         */
        $this->Add($defaultDomain);
        
        /**
         * Load default domain message catalog.
         */
        $this->Load($defaultDomain);
        
        return TRUE;
    }

    /**
     * Set locale data.
     *
     * @param string $i18nLocale            
     *
     * @return bool
     */
    public function SetLocale($i18nLocale = NULL)
    {
        if (empty($i18nLocale)) {
            /**
             * Check if we have a session.
             */
            if (! empty($_SESSION[__CLASS__]['locale'])) {
                /**
                 *
                 * @var string
                 */
                $i18nLocale = $_SESSION[__CLASS__]['locale'];
            } else {
                /**
                 *
                 * @var string
                 */
                $i18nLocale = Core::Config()->Get('i18n.locale');
            }
        }
        
        /**
         *
         * @var string
         */
        $this->_mLocale = $i18nLocale;
        
        /**
         * Rumored to allow using a locale regardless on server locale setup.
         * putenv("LANGUAGE=" . $this->_mLocale);
         */
        
        /**
         * Rumored to be needed on Win.
         * putenv("LANG=" . $this->_mLocale);
         */
        
        /**
         * Use as below instead of
         * setlocale(LC_ALL, $this->_mLocale);
         * in order to skip
         * LC_NUMERIC
         */
        setlocale(LC_COLLATE, $this->_mLocale);
        setlocale(LC_CTYPE, $this->_mLocale);
        setlocale(LC_MONETARY, $this->_mLocale);
        setlocale(LC_TIME, $this->_mLocale);
        setlocale(LC_MESSAGES, $this->_mLocale);
        
        /**
         * Check if we have a session.
         */
        if (! empty($_SESSION)) {
            /**
             *
             * @var array
             */
            $_SESSION[__CLASS__]['locale'] = $this->_mLocale;
        }
        
        /**
         * Check if we have domains already set.
         */
        if (! empty($this->_mDomains)) {
            /**
             * Iterate domains.
             */
            foreach ($this->_mDomains as $domain => $raw_domain) {
                /**
                 * Add domain.
                 */
                $this->Add($raw_domain);
            }
        }
        
        /**
         * Check if we have a domain already loaded.
         */
        if (! empty($this->_mDomain)) {
            /**
             * Re-load the domain.
             */
            $this->Load($this->_mDomain);
        }
        
        return TRUE;
    }

    /**
     * Get current locale.
     *
     * @return string
     */
    public function GetLocale()
    {
        return $this->_mLocale;
    }

    /**
     * Add a new domain message catalog.
     *
     * @param string $rawDomain            
     *
     * @return bool
     */
    public function Add($rawDomain = NULL)
    {
        
        /**
         *
         * @var string
         */
        $raw_domain = ($rawDomain) ? $rawDomain : 'Core_WEB';
        
        /**
         *
         * @var array
         */
        $domain_data = $this->GetDomainData($raw_domain);
        
        if (FALSE === $domain_data) {
            return FALSE;
        }
        
        bindtextdomain($domain_data[0], $domain_data[1]);
        
        /**
         * Add domain to list.
         *
         * @var array
         */
        $this->_mDomains[$domain_data[0]] = $raw_domain;
        
        return TRUE;
    }

    /**
     * Load domain message catalog.
     *
     * @param string $domainName            
     *
     * @return bool
     */
    public function Load($domainName = NULL)
    {
        
        /**
         *
         * @var string
         */
        $domain_name = ($domainName) ? $domainName : 'WEb';
        
        /**
         *
         * @var array
         */
        $parts = explode('_', $domain_name);
        
        /**
         *
         * @var string
         */
        $domain_name = (array_key_exists(1, $parts)) ? $parts[1] : $parts[0];
        
        textdomain($domain_name);
        
        /**
         * Set active domain.
         *
         * @var string
         */
        $this->_mDomain = $domain_name;

        /**
         * Check if current locale contains a custom codeset.
         */
        if (FALSE !== strpos($this->_mLocale, '.')) {
            /**
             *
             * @var array
             */
            $parts = explode('.', $this->_mLocale);
            
            /**
             *
             * @var string
             */
            $codeset = $parts[1];
            
            /**
             * Set the character encoding for the loaded domain.
             */
            bind_textdomain_codeset($domain_name, $codeset);
        }
        
        return TRUE;
    }

    /**
     * Get current domain.
     *
     * @return string
     */
    public function GetDomain()
    {
        return $this->_mDomain;
    }

    public function GetDomainData($rawDomain = NULL)
    {
        
        /**
         *
         * @var array
         */
        $parts = explode('_', $rawDomain);
        
        /**
         * Init domain base directory.
         *
         * @var mixed
         */
        $base = NULL;
        
        /**
         * Default domain type.
         *
         * @var string
         */
        $type = 'App';
        
        /**
         * Default domain name.
         *
         * @var string
         */
        $domain = $parts[0];
        
        /**
         * Check if the raw domain name also contains the type.
         */
        if (array_key_exists(1, $parts)) {
            /**
             *
             * @var string
             */
            $type = $parts[0];
            
            /**
             *
             * @var string
             */
            $domain = $parts[1];
        }
        
        /**
         * Check domain type.
         */
        switch ($type) {
            case 'Core':
                /**
                 *
                 * @var string
                 */
                $base = 'path.core';
                
                /**
                 *
                 * @var string
                 */
                $dir = 'dir.core.i18n';
                
                break;
            
            case 'App':
            default:
                /**
                 *
                 * @var string
                 */
                $base = 'path.app';
                
                /**
                 *
                 * @var string
                 */
                $dir = 'dir.app.i18n';
                
                break;
        }
        
        /**
         *
         * @var string
         */
        $path = Core::Config()->Get($base) . Core::Config()->Get($dir) . '/';
        
        /**
         * Check domain path.
         */
        if (! is_readable($path)) {
            return FALSE;
        }
        
        return array(
            $domain,
            $path
        );
    }

    /**
     * Get list of available languages.
     *
     * @return array
     */
    public function GetAvailableLangs()
    {
        $langs = array();
        
        $langs_config = Core::Config()->Get('i18n.langs.item');
        
        if (! empty($langs_config)) {
            
            if ('lang' === key($langs_config)) {
                $langs_config = array(
                    0 => $langs_config
                );
            }
            
            foreach ($langs_config as $item) {
                
                // $langs [$item ['lang']] = $item ['locale'];
                $langs[$item['lang']] = $item;
            }
        }
        
        return $langs;
    }
}
?>