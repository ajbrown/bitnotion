<?php
/**
 * My new Zend Framework project
 *
 * @author
 * @version
 */

define( 'ROOT', dirname( dirname( __FILE__ ) ) );
set_include_path(
	'.'
	. PATH_SEPARATOR . ROOT
	. PATH_SEPARATOR . ROOT . '/application/default'
	. PATH_SEPARATOR . ROOT . '/library'
	. PATH_SEPARATOR . ROOT . '/models'
	. PATH_SEPARATOR . ROOT . '/models/handlers'
	. PATH_SEPARATOR . '/usr/share/php/Zend/library'
	. PATH_SEPARATOR . '/usr/share/php/Doctrine/lib'
	. PATH_SEPARATOR . '/usr/share/php'
);

require_once 'Zend/Controller/Plugin/Abstract.php';
require_once 'Zend/Controller/Front.php';
require_once 'Zend/Controller/Request/Abstract.php';
require_once 'Zend/Controller/Action/HelperBroker.php';

/**
 *
 * Initializes configuration depndeing on the type of environment
 * (test, development, production, etc.)
 *
 * This can be used to configure environment variables, databases,
 * layouts, routers, helpers and more
 *
 */
class Initializer extends Zend_Controller_Plugin_Abstract
{
    /**
     * @var Zend_Config
     */
    protected static $_config;

    /**
     * @var string Current environment
     */
    protected $_env;

    /**
     * @var Zend_Controller_Front
     */
    protected $_front;

    /**
     * @var string Path to application root
     */
    protected $_root;

    /**
     * Constructor
     *
     * Initialize environment, root path, and configuration.
     *
     * @param  string $env
     * @param  string|null $root
     * @return void
     */
    public function __construct($env, $root = null)
    {
        $this->_setEnv($env);
        if (null === $root) {
            $root = realpath(dirname(__FILE__) . '/../');
        }
        $this->_root = $root;

        $this->initPhpConfig();

        $this->_front = Zend_Controller_Front::getInstance();

        date_default_timezone_set( 'UTC' );

        // set the test environment parameters
        if ( true || $env == 'test') {
			// Enable all errors so we'll know when something goes wrong.
			error_reporting(E_ALL | E_STRICT);
			ini_set('display_startup_errors', 1);
			ini_set('display_errors', 1);

			$this->_front->throwExceptions(true);
        }

    	$configFile = dirname( __FILE__ ) . '/config.xml';

    	require_once 'Zend/Config/Xml.php';
    	self::$_config = new Zend_Config_Xml( $configFile, $this->_env );

    	require_once 'Zend/Registry.php';
    	Zend_Registry::set( 'Config', self::$_config );

    }

    /**
     * Initialize environment
     *
     * @param  string $env
     * @return void
     */
    protected function _setEnv($env)
    {
		$this->_env = $env;
    }


    /**
     * Initialize Data bases
     *
     * @return void
     */
    public function initPhpConfig()
    {

    }

    /**
     * Route startup
     *
     * @return void
     */
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
    	$this->initApp();
       	$this->initDb();
        $this->initHelpers();
        $this->initView();
        $this->initPlugins();
        $this->initRoutes();
        $this->initControllers();
    }

    /**
     * Initialize configured application objets, such as backend cache object,
     * global logger, etc.
     *
     */
    public function initApp()
    {
    	$oBackend  = new Zend_Cache_Backend_Memcached(
    		array(
				'servers' => array( array(
					'host' => '127.0.0.1',
    				'port' => '11211'
				)),
				'compression' => false
    		)
    	);

    	Zend_Registry::set( 'CacheBackend', $oBackend );

    	$oLogger = new Zend_Log();
    	$oLogger->addWriter( new Zend_Log_Writer_Null() );

    	Zend_Registry::set( 'Logger', $oLogger );

    	//configure mail transport
    	$tr = new Zend_Mail_Transport_Sendmail( '-fno-relay@zendforge.org' );
    	Zend_Mail::setDefaultTransport( $tr );

    }

    /**
     * Initialize data bases
     *
     * @return void
     */
    public function initDb()
    {
        require_once 'Doctrine.php';
        spl_autoload_register( array( 'Doctrine', 'autoload' ) );

		Doctrine_Manager::connection( self::$_config->db->dsn );
		foreach( self::$_config->db->attributes as $key => $value ) {
		    Doctrine_Manager::getInstance()->setAttribute( $key, $value );
		}

		Doctrine::loadModels( array(
		    dirname( dirname( __FILE__ ) ) .'/models'
		) );


		//Manager Level Caching
		if ( self::$_config->db->caching->enabled  ) {
		    Doctrine_Manager::getInstance()->setAttribute(
		        Doctrine::ATTR_CACHE,
		        new Doctrine_Cache_Memcache( self::$_config->db->caching->options->toArray() )
		    );
		}

    }

    /**
     * Initialize action helpers
     *
     * @return void
     */
    public function initHelpers()
    {
    	// register the default action helpers
    	Zend_Controller_Action_HelperBroker::addPath(
    		'../application/default/helpers',
    		'Zend_Controller_Action_Helper'
    	);
    }

    /**
     * Initialize view
     *
     * @return void
     */
    public function initView()
    {
		// Bootstrap layouts
		$layout = Zend_Layout::startMvc(array(
		    'layoutPath' => $this->_root .  '/application/default/layouts',
		    'layout' => 'main'
		));

		$view = $layout->getView()
		    ->addHelperPath('ZForge/View/Helper/', 'ZForge_View_Helper')
		    ->addHelperPath('Zend/Dojo/View/Helper/', 'Zend_Dojo_View_Helper');

		Zend_Dojo::enableView( $view );

    }

    /**
     * Initialize plugins
     *
     * @return void
     */
    public function initPlugins()
    {
        $this->_front->setDefaultModule( 'default' );
    }

    /**
     * Initialize routes
     *
     * @return void
     */
    public function initRoutes()
    {
    	$router = $this->_front->getRouter();
    	$router->addConfig( self::$_config, 'routes' );

    }

    /**
     * Initialize Controller paths
     *
     * @return void
     */
    public function initControllers()
    {
    	$this->_front->addControllerDirectory($this->_root . '/application/default/controllers', 'default');
		$this->_front->addControllerDirectory($this->_root . '/application/dashboard/controllers', 'dashboard');
		$this->_front->addControllerDirectory($this->_root . '/application/data/controllers', 'data');
    }

    /**
     *
     * Retreives the current configuration
     *
     * @return Zend_Config_Xml
     */
    public static function getConfig()
    {
        return self::$_config;
    }
}