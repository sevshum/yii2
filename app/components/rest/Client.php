<?php
namespace app\components\rest;

use Yii;
use yii\base\Component;
use yii\helpers\VarDumper;

/**
 * Yii RESTClient Components
 * 
 * Make REST requests to RESTful services with simple syntax.
 * Ported from CodeIgniter REST Class.
 * 
 * 
 * example
 * 
 * $client = Yii::$app->get('rest');
 * $response = $client->setServer('http://search.twitter.com')
 *                    ->get('/search.json', array('q' => 'rock'), 'json');
 * if ($response !== false) {
 *     //success
 * } else {
 *    //error    
 * }
 *
 * @package        	CodeIgniter
 * @subpackage    	Libraries
 * @category    	Libraries
 * @author        	Philip Sturgeon
 * @created			04/06/2009
 * @license         http://philsturgeon.co.uk/code/dbad-license
 * @link			http://getsparks.org/packages/restclient/show
 */
class Client extends Component
{

    public $supported_formats = [
        'xml'       => 'application/xml',
        'json'      => 'application/json',
        'serialize' => 'application/vnd.php.serialized',
        'php'       => 'text/plain',
        'csv'       => 'text/csv'
    ];
    public $auto_detect_formats = [
        'application/xml'                => 'xml',
        'text/xml'                       => 'xml',
        'application/json'               => 'json',
        'text/json'                      => 'json',
        'text/csv'                       => 'csv',
        'application/csv'                => 'csv',
        'application/vnd.php.serialized' => 'serialize'
    ];
    private $rest_server;
    private $format;
    private $mime_type;
    private $http_auth = null;
    private $http_user = null;
    private $http_pass = null;
    private $_responseString;
    private $_curl;
    private $_headers = [];

    /**
     * Logs a message.
     *
     * @param string $message Message to be logged
     * @param string $level Level of the message (e.g. 'trace', 'warning',
     * 'error', 'info', see CLogger constants definitions)
     */
    public static function log($message, $level = 'error')
    {
        Yii::$level($message, __CLASS__);
    }

    /**
     * Dumps a variable or the object itself in terms of a string.
     *
     * @param mixed variable to be dumped
     */
    protected function dump($var = 'dump-the-object', $highlight = true)
    {
        if ($var === 'dump-the-object') {
            return VarDumper::dumpAsString($this, $depth = 15, $highlight);
        } else {
            return VarDumper::dumpAsString($var, $depth = 15, $highlight);
        }
    }

    public function __construct($config = [])
    {
        $this->_curl = new Curl;
        empty($config) || $this->initialize($config);
    }

    public function __destruct()
    {
        $this->_curl->set_default();
    }

    public function initialize($config)
    {
        isset($config['server']) && $this->setServer($config['server']);
        isset($config['http_auth']) && $this->http_auth = $config['http_auth'];
        isset($config['http_user']) && $this->http_user = $config['http_user'];
        isset($config['http_pass']) && $this->http_pass = $config['http_pass'];
    }

    /**
     * @param string $server
     * @return static
     */
    public function setServer($server)
    {
        $this->rest_server = $server;

        if (substr($this->rest_server, -1, 1) != '/') {
            $this->rest_server .= '/';
        }
        return $this;
    }

    public function get($uri, $params = [], $format = null)
    {
        if ($params) {
            $uri .= '?' . (is_array($params) ? http_build_query($params) : $params);
        }
        return $this->_call('get', $uri, null, $format);
    }

    public function post($uri, $params = [], $format = null)
    {
        return $this->_call('post', $uri, $params, $format);
    }

    public function put($uri, $params = [], $format = null)
    {
        return $this->_call('put', $uri, $params, $format);
    }

    public function delete($uri, $params = [], $format = null)
    {
        return $this->_call('delete', $uri, $params, $format);
    }

    public function setApiKey($key, $name = 'X-API-KEY')
    {
        $this->_curl->http_header($name, $key);
        return $this;
    }

    public function setHeader($name, $value)
    {
        $this->_headers[$name] = $value;
    }

    public function setLanguage($lang)
    {
        if (is_array($lang)) {
            $lang = implode(', ', $lang);
        }

        $this->_curl->http_header('Accept-Language', $lang);
    }

    private function _call($method, $uri, $params = [], $format = null)
    {
        $this->_setHeaders();

        // Initialize cURL session
        $this->_curl->create($this->rest_server . $uri);

        // If authentication is enabled use it
        if ($this->http_auth != '' && $this->http_user != '') {
            $this->_curl->http_login($this->http_user, $this->http_pass, $this->http_auth);
        }

        // We still want the response even if there is an error code over 400
        $this->_curl->option('failonerror', false);

        // Call the correct method with parameters
        $this->_curl->{$method}($params);
        // Execute and return the response from the REST server
        $response = $this->_responseString = $this->_curl->execute();
        // Format and return
        
        if ($format !== null && $response !== false) {
            $this->setFormat($format);
            return $this->_formatResponse($response);
        }
        return $response;
    }

    /**
     * @return string
     */
    public function getRawBody()
    {
        return $this->_responseString;
    }

    // If a type is passed in that is not supported, use it as a mime type
    public function setFormat($format)
    {
        if (array_key_exists($format, $this->supported_formats)) {
            $this->format = $format;
            $this->mime_type = $this->supported_formats[$format];
        } else {
            $this->mime_type = $format;
        }

        return $this;
    }

    public function debug()
    {
        $request = $this->_curl->debug();

        echo "=============================================<br/>\n";
        echo "<h2>REST Test</h2>\n";
        echo "=============================================<br/>\n";
        echo "<h3>Request</h3>\n";
        echo $request['url'] . "<br/>\n";
        echo "=============================================<br/>\n";
        echo "<h3>Response</h3>\n";

        if ($this->_responseString) {
            echo "<code>" . nl2br(htmlentities($this->_responseString)) . "</code><br/>\n\n";
        } else {
            echo "No response<br/>\n\n";
        }

        echo "=============================================<br/>\n";

        if ($this->_curl->error_string) {
            echo "<h3>Errors</h3>";
            echo "<strong>Code:</strong> " . $this->_curl->error_code . "<br/>\n";
            echo "<strong>Message:</strong> " . $this->_curl->error_string . "<br/>\n";
            echo "=============================================<br/>\n";
        }

        echo "<h3>Call details</h3>";
        echo "<pre>";
        print_r($this->_curl->info);
        echo "</pre>";
    }

    // Return HTTP status code
    public function status()
    {
        return $this->info('http_code');
    }

    /**
     * @return boolean
     */
    public function isSuccess()
    {
        return $this->status() == 200;
    }

    // Return curl info by specified key, or whole array
    public function info($key = null)
    {
        return $key === null ? $this->_curl->info : @$this->_curl->info[$key];
    }

    // Set custom options
    public function setOption($code, $value)
    {
        $this->_curl->option($code, $value);
        return $this;
    }

    private function _setHeaders()
    {
        if (!array_key_exists("Accept", $this->_headers) && $this->mime_type) {
            $this->setHeader("Accept", $this->mime_type);
        }
        foreach ($this->_headers as $k => $v) {
            $this->_curl->http_header(sprintf("%s: %s", $k, $v));
        }
    }

    private function _formatResponse($response)
    {
        $this->_responseString = & $response;

        // It is a supported format, so just run its formatting method
        if (array_key_exists($this->format, $this->supported_formats)) {
            return $this->{"_" . $this->format}($response);
        }

        // Find out what format the data was returned in
        $returnedMime = @$this->_curl->info['content_type'];

        // If they sent through more than just mime, stip it off
        if (strpos($returnedMime, ';')) {
            list($returnedMime) = explode(';', $returnedMime);
        }

        $returnedMime = trim($returnedMime);

        if (array_key_exists($returnedMime, $this->auto_detect_formats)) {
            return $this->{'_' . $this->auto_detect_formats[$returned_mime]}($response);
        }

        return $response;
    }

    // Format XML for output
    private function _xml($string)
    {
        return $string ? (array) simplexml_load_string($string, 'SimpleXMLElement', LIBXML_NOCDATA) : [];
    }

    // Format HTML for output
    // This function is DODGY! Not perfect CSV support but works with my REST_Controller
    private function _csv($string)
    {
        $data = [];

        // Splits
        $rows = explode("\n", trim($string));
        $headings = explode(',', array_shift($rows));
        foreach ($rows as $row) {
            // The substr removes " from start and end
            $data_fields = explode('","', trim(substr($row, 1, -1)));

            if (count($data_fields) == count($headings)) {
                $data[] = array_combine($headings, $data_fields);
            }
        }

        return $data;
    }

    // Encode as JSON
    private function _json($string)
    {
        return json_decode(trim($string), true);
    }

    // Encode as Serialized array
    private function _serialize($string)
    {
        return unserialize(trim($string));
    }

    // Encode raw PHP
    private function _php($string)
    {
        $string = trim($string);
        $populated = [];
        eval("\$populated = \"$string\";");
        return $populated;
    }

}
