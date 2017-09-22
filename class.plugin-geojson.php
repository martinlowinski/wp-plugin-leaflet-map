<?php
/**
* 
* Used to generate GeoJSON data
* 
* 
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

include_once(LEAFLET_MAP__PLUGIN_DIR . 'class.plugin-option.php');

class Leaflet_Map_Plugin_Geojson {
	/**
    * Prefix for options, for unique db entries
    * @var string $prefix
    */
    public $prefix = 'leaflet_';
	
    /**
     * @var Leaflet_Map_Plugin_Settings
     **/
    private static $instance = null;

	/**
	* Default values and admin form information
	* @var array $options
	*/
        public $options = array(
        'csv_file' => array(
            'default' => "",
            'type' => 'file',
            'helptext' => 'CSV data to generate the regions. Generated data is shown in the following textarea.'
        ),
        'geojson_data' => array(
            'default' => "",
            'type' => 'textarea',
            'helptext' => 'Generated geojson data of the regions.'
        )
        );

	/**
	 * Singleton
	 * @static
	 */
	public static function init() {
	    if ( !self::$instance ) {
	        self::$instance = new self;
	    }

	    return self::$instance;
	}

	private function __construct () {

        /* update leaflet version from main class */
        $leaflet_version = Leaflet_Map::$leaflet_version;

        $this->options['js_url']['default'] = sprintf($this->options['js_url']['default'], $leaflet_version);
        $this->options['css_url']['default'] = sprintf($this->options['css_url']['default'], $leaflet_version);

		foreach ($this->options as $name => $details) {
			$this->options[ $name ] = new Leaflet_Map_Plugin_Option( $details );
		}
	}

	/*
	* wrapper for WordPress get_options (adds prefix to default options)
	*
	* @param string $key                
	* @param varies $default   default value if not found in db
	* @return varies
	*/

	public function get ($key) {
		$default = $this->options[ $key ]->default;
		$key = $this->prefix . $key;
		return get_option($key, $default);
	}

	/*
	* wrapper for WordPress update_option (adds prefix to default options)
	*
	* @param string $key
	* @param varies $value
	* @param varies $default   default value if not found in db
	* @return varies
	*/

	public function set ($key, $value) {
		$key = $this->prefix . $key;
		update_option($key, $value);
		return $this;
	}

	/*
	* wrapper for WordPress delete_option (adds prefix to default options)
	*
	* @param string $key
	* @param varies $default   default value if not found in db
	* @return varies
	*/

	public function delete ($key) {
		$key = $this->prefix . $key;
		return delete_option($key);
	}

	/*
	* wrapper for WordPress delete_option (adds prefix to default options)
	*
	* @param string $key
	* @param varies $default   default value if not found in db
	* @return varies
	*/

	public function reset () {
		foreach ($this->options as $name => $option) {
			$this->delete( $name );
		}
		return $this;
	}
}
