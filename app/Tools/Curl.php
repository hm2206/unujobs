<?php
/**
 * app/Tools/Curl.php 
 */
namespace App\Tools;
	
	/**
	 * Clase para realizar peticiones por bash
	 */
	class Curl
	{
		/**
		 * Utilizamos un agente por defacto
		 *
		 * @var string
		 */
		protected $_useragent = 'Mozilla/5.0 (X11; Fedora; Linux x86_64; rv:53.0) Gecko/20100101 Firefox/53.0';
		
		/**
		 * Undocumented variable
		 *
		 * @var string
		 */
		protected $_url;

		/**
		 * Undocumented variable
		 *
		 * @var string
		 */
		protected $_followlocation;

		/**
		 * Undocumented variable
		 *
		 * @var int
		 */
		protected $_timeout;

		/**
		 * Undocumented variable
		 *
		 * @var array
		 */
		protected $_httpheaderData = array();

		/**
		 * Undocumented variable
		 *
		 * @var array
		 */
		protected $_httpheader = array('Expect:');

		/**
		 * Undocumented variable
		 *
		 * @var int
		 */
		protected $_maxRedirects;

		/**
		 * Undocumented variable
		 *
		 * @var string
		 */
		protected $_cookieFileLocation;

		/**
		 * Undocumented variable
		 *
		 * @var string
		 */
		protected $_post;

		/**
		 * Undocumented variable
		 *
		 * @var string
		 */
		protected $_postFields;
		
		/**
		 * Undocumented variable
		 *
		 * @var string
		 */
		protected $_referer ="https://www.google.com/";

		/**
		 * Undocumented variable
		 *
		 * @var string
		 */
		protected $_session;

		/**
		 * Undocumented variable
		 *
		 * @var string
		 */
		protected $_webpage;

		/**
		 * Undocumented variable
		 *
		 * @var string
		 */
		protected $_includeHeader;

		/**
		 * Undocumented variable
		 *
		 * @var string
		 */
		protected $_noBody;

		/**
		 * Undocumented variable
		 *
		 * @var int
		 */
		protected $_status;

		/**
		 * Undocumented variable
		 *
		 * @var string
		 */
		protected $_binary;

		/**
		 * Undocumented variable
		 *
		 * @var string
		 */
		protected $_binaryFields;

		/**
		 * Undocumented variable
		 *
		 * @var boolean
		 */
		public    $proxy = false;

		/**
		 * Undocumented variable
		 *
		 * @var string
		 */
		public    $proxy_host = '';

		/**
		 * Undocumented variable
		 *
		 * @var string
		 */
		public    $proxy_port = '';

		/**
		 * Undocumented variable
		 *
		 * @var string
		 */
		public    $proxy_type = "CURLPROXY_HTTP";
		

		/**
		 * Undocumented variable
		 *
		 * @var boolean
		 */
		public    $authentication = false;

		/**
		 * Undocumented variable
		 *
		 * @var string
		 */
		public    $auth_name      = '';

		/**
		 * Undocumented variable
		 *
		 * @var string
		 */
		public    $auth_pass      = '';

		/**
		 * Constuctor
		 * 
		 * @param boolean $followlocation
		 * @param integer $timeOut
		 * @param integer $maxRedirecs
		 * @param boolean $binary
		 * @param boolean $includeHeader
		 * @param boolean $noBody
		 */
		public function __construct( $followlocation = true, $timeOut = 30, $maxRedirecs = 4, $binary = false, $includeHeader = false, $noBody = false )
		{
			$this->_followlocation = $followlocation;
			$this->_timeout = $timeOut;
			$this->_maxRedirects = $maxRedirecs;
			$this->_noBody = $noBody;
			$this->_includeHeader = $includeHeader;
			$this->_binary = $binary;

			$this->_cookieFileLocation = __DIR__ . '/cookie.txt';
			$this->s = curl_init();
		}
		
		/**
		 * Cierra la conexión
		 */
		public function __destruct()
		{
			curl_close($this->s);
		}
		
		/**
		 * Configura que proxy se va a utilizar
		 *
		 * @param boolean $use
		 * @return void
		 */
		public function useProxy( $use )
		{
			$this->proxy = false;
			if($use == true) $this->proxy = true;
		}

		/**
		 * setting para el host
		 *
		 * @param string $host
		 * @return void
		 */
		public function setHost( $host )
		{
			$this->proxy_host = $host;
		}

		/**
		 * Undocumented function
		 *
		 * @param string $port
		 * @return void
		 */
		public function setPort( $port )
		{
			$this->proxy_port = $port;
		}

		/**
		 * Undocumented function
		 *
		 * @param string $type
		 * @return void
		 */
		public function setTypeProxy( $type ) //
		{
			// CURLPROXY_SOCKS5 | CURLPROXY_SOCKS4 | CURLPROXY_HTTP
			// 5 | 4 | 0
			$this->proxy_type = $type;
		}

		/**
		 * Undocumented function
		 *
		 * @param boolean $use
		 * @return void
		 */
		public function useAuth( $use )
		{
			$this->authentication = false;
			if($use == true) $this->authentication = true;
		}

		/**
		 * Undocumented function
		 *
		 * @param string $name
		 * @return void
		 */
		public function setName( $name )
		{
			$this->auth_name = $name;
		}

		/**
		 * Undocumented function
		 *
		 * @param string $pass
		 * @return void
		 */
		public function setPass( $pass )
		{
			$this->auth_pass = $pass;
		}
		
		/**
		 * Undocumented function
		 *
		 * @param string $referer
		 * @return void
		 */
		public function setReferer( $referer )
		{
			$this->_referer = $referer;
		}

		/**
		 * Undocumented function
		 *
		 * @param array $httpheader
		 * @return void
		 */
		public function setHttpHeader( $httpheader=array() )
		{
			$this->_httpheader = array();
			foreach( $httpheader as $i=>$v )
			{
				$this->_httpheaderData[$i]=$v;
			}
			foreach( $this->_httpheaderData as $i=>$v )
			{
				$this->_httpheader[]=$i.":".$v;
			}
		}

		/**
		 * Ruta de la cookies
		 *
		 * @param string $path
		 * @return void
		 */
		public function setCookiFileLocation( $path )
		{
			$this->_cookieFileLocation = $path;
			if ( !file_exists($this->_cookieFileLocation) )
			{
				file_put_contents($this->_cookieFileLocation,"");
			}
		}

		/**
		 * Cambiamos el puerto
		 *
		 * @param array $postFields
		 * @return void
		 */
		public function setPost( $postFields = array() )
		{
			$this->_binary = false;
			$this->_post = false;
			if(count($postFields)>0)
			{
				$this->_post = true;
			}
			$this->_postFields = http_build_query($postFields);
		}

		/**
		 * Cambiamos los binarios
		 *
		 * @param string $postBinaryFields
		 * @return void
		 */
		public function setBinary( $postBinaryFields = "" )
		{
			$this->_post = false;
			$this->_binary = false;
			if(strlen($postBinaryFields)>0)
			{
				$this->_binary = true;
			}
			$this->_binaryFields = $postBinaryFields;
		}

		/**
		 * Usamos un usario de agente falso
		 *
		 * @param string $userAgent
		 * @return void
		 */
		public function setUserAgent( $userAgent )
		{
			$this->_useragent = $userAgent;
		}

		/**
		 * Creamos una nueva conexion con curl
		 *
		 * @param string $url
		 * @return void
		 */
		public function createCurl( $url = 'nul' )
		{
			if($url != 'nul')
			{
				$this->_url = $url;
			}

			//$this->s = curl_init();
			//curl_setopt($this->s, CURLOPT_FAILONERROR, true);
			//curl_setopt($this->s, CURLOPT_HEADER, true);
			//curl_setopt($this->s, CURLOPT_VERBOSE, true);
			//curl_setopt($this->s, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($this->s, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($this->s, CURLOPT_URL,$this->_url);
			curl_setopt($this->s, CURLOPT_HTTPHEADER,$this->_httpheader);
			curl_setopt($this->s, CURLOPT_TIMEOUT,$this->_timeout);
			curl_setopt($this->s, CURLOPT_MAXREDIRS,$this->_maxRedirects);
			curl_setopt($this->s, CURLOPT_RETURNTRANSFER,true);
			curl_setopt($this->s, CURLOPT_FOLLOWLOCATION,$this->_followlocation);
			curl_setopt($this->s, CURLOPT_COOKIEJAR,$this->_cookieFileLocation);
			curl_setopt($this->s, CURLOPT_COOKIEFILE,$this->_cookieFileLocation);

			if($this->proxy == true)
			{
				if( $this->proxy_host != '' && $this->proxy_port != '' )
				{
					curl_setopt($this->s, CURLOPT_HTTPPROXYTUNNEL, 0);
					curl_setopt($this->s, CURLOPT_PROXY, $this->proxy_host.':'.$this->proxy_port);
					curl_setopt($this->s, CURLOPT_PROXYTYPE, $this->proxy_type);
				}
			}
			
			if($this->authentication == true)
			{
				curl_setopt($this->s, CURLOPT_USERPWD, $this->auth_name.':'.$this->auth_pass);
			}

			if($this->_post)
			{
				curl_setopt($this->s, CURLOPT_POST, true);
				curl_setopt($this->s, CURLOPT_POSTFIELDS, $this->_postFields);
			}

			if($this->_binary)
			{
				curl_setopt($this->s, CURLOPT_BINARYTRANSFER, true);
				curl_setopt($this->s, CURLOPT_POSTFIELDS, $this->_binaryFields);
			}

			if( $this->_includeHeader )
			{
				curl_setopt($this->s, CURLOPT_HEADER, true);
			}

			if( $this->_noBody )
			{
				curl_setopt($this->s, CURLOPT_NOBODY, true);
			}

			curl_setopt( $this->s, CURLOPT_USERAGENT, $this->_useragent );
			curl_setopt( $this->s, CURLOPT_REFERER, $this->_referer );
			$this->_webpage = curl_exec( $this->s );
			$this->_status = curl_getinfo( $this->s, CURLINFO_HTTP_CODE );
			//curl_close( $this->s );
		}

		/**
		 * Obtenemos una respuesta Http
		 *
		 * @return void
		 */
		public function getHttpStatus()
		{
			return $this->_status;
		}

		/**
		 * Conversion a strind de la clase Curl
		 *
		 * @return string
		 */
		public function __toString()
		{
			return $this->_webpage;
		}
		
		/**
		 * Envio de datos de manera simplificada
		 *
		 * @param [type] $url
		 * @param array $post
		 * @return void
		 */
		public function send( $url, $post = array() )
		{
			$this->_post = false;
			if( count($post)!=0 )
				$this->setPost( $post );

			$this->createCurl( $url );
			return $this->_webpage;
		}

		/**
		 * Cambiamos binarios
		 *
		 * @param string $url
		 * @param string $binary
		 * @return void
		 */
		public function sendBinary( $url, $binary="" )
		{
			$this->_binary = false;
			if( $binary != "" )
			{
				$this->setBinary( $binary );
				$this->setHttpHeader( array('Content-Length'=>strlen($this->_binaryFields)) );
				$this->setHttpHeader( array('Content-Type'=>'application/json;charset=utf-8') );
				$this->setHttpHeader( array('Access-Control-Allow-Origin'=>'*') );
			}
			$this->createCurl( $url );
			return $this->_webpage;
		}
	}
?>
