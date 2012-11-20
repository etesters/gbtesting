<?php
/**
* Released Under MIT License
* 
* Copyright (C), API IO LLC - Singapore @link <http://www.ape.io>
* 
* @author Erson G. Puyos <erson.puyos@gmail.com>
* 
* Permission is hereby granted, free of charge, to any person obtaining 
* a copy of this software and associated documentation files (the "Software"), 
* to deal in the Software without restriction, including without limitation 
* the rights to use, copy, modify, merge, publish, distribute, sublicense, 
* and/or sell copies of the Software, and to permit persons to 
* whom the Software is furnished to do so, subject to the following conditions: 
* 
* The above copyright notice and this permission notice shall be 
* included in all copies or substantial portions of the Software.
* 
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,  
* EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, 
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT 
* HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, 
* TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR 
* THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/
/**
* GBClass SDK is a helper class providing you to curl the GRIDBLAZE API easily.
*/
class GBClass
{
	const OBJECTS='objects';
	const DIRECTORIES='directories';
	const OBJECT_RENAME='rename';

	public $format='json';

	private $_protocol='https://';
	private $_host='api.gridblaze.com';
	private $_version='v1';
	private $_url=null;	

	private $_appid;
	private $_appkey;		
	private $_token=null;
	private $_headers=array();
	private $_action=null;
	private $_object=null;

	/**
	* Constructor
	*
	* @param $appid Application ID
	* @param $appkey Application Key
	* @return void
	*/ 
	public function __construct($appid, $appkey)
	{
		if($appid==='' || $appid==null){
			die('Application Id cannot be empty.');
		}elseif($appkey==='' || $appkey===null){
			die('Application Key cannot be empty.');
		}else{
			$this->setAuthData($appid, $appkey);
		}
	}
	/**
	* Setting the Token Value
	* 
	* @param $token The token to be set
	* @return void
	*/
	public function setToken($token){$this->_token=$token;}
	/**
	* Getting the Token
	* 
	* @return Token value
	*/
	public function getToken(){return $this->_token;}
	/**
	* Returns detailed information about the version of the API
	* 
	* <pre>
	* $gbClass=new GBClass('Application ID', 'Application Key');
	* $gbClass->format='json OR xml';
	* $gbClass->info();
	* </pre>
	* 
	* @return json | xml  Detailed information about the version of the API
	*/
	public function info()
	{
		$this->_url=$this->_protocol . $this->_host . '/' . $this->_version;
		$this->_headers=array();
		$this->_headers[]='Content-Type: application/' . $this->format;		
		return $this->execute("GET");
	}
	/**
	* Get the list of nodes of GRIDBLAZE
	* 
	* <pre>
	* $id='ServerId';
	* $gbClass=new GBClass('Application ID', 'Application Key');
	* $gbClass->format='json OR xml';
	* $gbClass->node($id);
	* </pre>
	* 
	* @params $id Node ID
	* @return json | xml Nodes list/information
	*/
	public function node($id='')
	{
		$this->_url=$this->_protocol . $this->_host . '/' . $this->_version . '/node/' . $id;
		$this->_headers=array();
		$this->_headers[]='Content-Type: application/' . $this->format;
		return $this->execute("GET");
	}
	/**
	* Authentication to GRIDBLAZE API
	* 
	* <pre>
	* $gbClass=new GBClass('Application ID', 'Application Key');	
	* $gbClass->auth();
	* </pre>
	*/ 
	public function auth()
	{
		if($this->_token===null){
			$this->_url=$this->_protocol . $this->_host . '/';
			$this->_headers=array();
			$this->_headers[]='X-Auth-User: ' . $this->_appid;
			$this->_headers[]='X-Auth-Key: ' . $this->_appkey;
			$data=$this->execute("GET", true);
			if($this->_token===null) $this->_token=$data['headers']['X-Auth-Token'];
		}
	}
	/**
	* Get list of objects and/or directories
	* 
	* <pre>
	* $gbClass=new GBClass('Application ID', 'Application Key');
	* $gbClass->format='json OR xml';	
	* $gbClass->listing('/this/is/the/location/', '');
	* </pre>
	* 
	* @param $directory The directory
	* @param $content GBClass::OBJECTS | GBClass::DIRECTORIES 
	* @return List of objects and/or directories (json | xml)
	*/ 
	public function listing($directory='/', $content='')
	{
		if($this->_token===null){
			$this->auth();
		}
		$this->_url=$this->_protocol . $this->_host . '/' . $this->_version . '/' . $this->_appid . '?format=' . $this->format . '&prefix=' . urlencode($this->cleanDirectory($directory)) . '&data=' . $content;        
		$this->_headers=array();
		$this->_headers[]='X-Auth-Token: ' . $this->_token;
		return $this->execute('GET');
	}
	/**
	* Deleting a directory
	* 
	* <pre>
	* $gbClass=new GBClass('Application ID', 'Application Key');    
	* $gbClass->deleteDirectory('MyObjectName.Extension', '/this/is/the/location/');
	* </pre>
	* 
	* @param $name The directory name to be deleted
	* @param $directory The directory
	* @return Headers
	*/ 
	public function deleteDirectory($name, $directory='/')
	{
		if($this->_token===null){
			$this->auth();
		}
		$this->_url=$this->_protocol . $this->_host . '/' . $this->_version . '/' . $this->_appid . '/' . urlencode($this->cleanDirectory($directory) . $name);
		$this->_headers=array();
		$this->_headers[]='X-Auth-Token: ' . $this->_token;
		$data=$this->execute("DELETE", true);
		return $data['headers'];
	}
	/**
	* Creating a new directory
	* 
	* <pre>
	* $gbClass=new GBClass('Application ID', 'Application Key');    
	* $gbClass->createDirectory('MyDirectoryName', '/this/is/the/location/');
	* </pre>
	* 
	* @param $name The name of directory to be created
	* @param $directory The directory
	* @return Headers
	*/ 
	public function createDirectory($name, $directory='/')
	{
		if($this->_token===null){
			$this->auth();
		}
		$this->_url=$this->_protocol . $this->_host . '/' . $this->_version . '/' . $this->_appid . '/' . urlencode($this->cleanDirectory($directory) . $name);
		$this->_action=self::DIRECTORIES;
		$this->_headers=array();
		$this->_headers[]='X-Auth-Token: ' . $this->_token;		
		$data=$this->execute("PUT", true);
		return $data['headers'];
	}
	/**
	* Uploading an object
	*
	* <pre>	 
	* $object=$_FILES['object'];	 
	* $gbClass=new GBClass('Application ID', 'Application Key');
	* $gbClass->putObject($object, '/this/is/the/location/');	 
	* </pre>
	* 
	* @param $object Contains the data of $_FILES['object']
	* @param $directory The directory
	* @param $targetNodeId The target node id for object to upload
	* @return Headers
	*/ 
	public function putObject($object, $directory='/', $targetNodeId=null)
	{
		if($this->_token===null){
			$this->auth();
		}
		$this->_url=$this->_protocol . $this->_host . '/' . $this->_version . '/' . $this->_appid . '/' . urlencode($this->cleanDirectory($directory) . $object['name']);
		$this->_action=self::OBJECTS;
		$this->_object=$object;
		$this->_headers=array();
		$this->_headers[]='X-Auth-Token: ' . $this->_token;
		if($targetNodeId!==null || (!empty($targetNodeId))) $this->_headers[]='X-Target-Node: ' . $targetNodeId;
		$data=$this->execute("PUT", true);
		return $data['headers'];
	}
	/**
	* Rename an Object
	* 
	* <pre>
	* $gbClass=new GBClass('Application ID', 'Application Key');
	* $gbClass->renameObject('MyOldObjectName.extension', 'MyNewObjectName.extension', '/this/is/the/location/');
	* </pre>
	* 
	* @param $oldName The old object name
	* @param $newName The new / target name of an object
	* @param $directory The directory
	* @return Headers
	*/ 
	public function renameObject($oldName, $newName, $directory='/')
	{
		if($this->_token===null){
			$this->auth();
		}
		$this->_url=$this->_protocol . $this->_host . '/' . $this->_version . '/' . $this->_appid . '/' . urlencode($this->cleanDirectory($directory) . $newName);
		$this->_action=self::OBJECT_RENAME;
		$this->_headers=array();
		$this->_headers[]='X-Auth-Token: ' . $this->_token;
		$this->_headers[]='X-Copy-From: ' . $this->_appid . '/' . urlencode($this->cleanDirectory($directory) . $oldName);
		$data=$this->execute("PUT", true);
		return $data['headers'];
	}
	/**
	* Deleting an Object
	* 
	* <pre>
	* $gbClass=new GBClass('Application ID', 'Application Key');
	* $gbClass->deleteObject('MyObjectName.extension', '/this/is/the/location/');
	* </pre>
	* 
	* @param $name The object name to be deleted
	* @param $directory The directory
	* @return Headers
	*/ 
	public function deleteObject($name, $directory='/')
	{
		if($this->_token===null){
			$this->auth();
		}
		$this->_url=$this->_protocol . $this->_host . '/' . $this->_version . '/' . $this->_appid . '/' . urlencode($this->cleanDirectory($directory) . $name);
		$this->_headers=array();
		$this->_headers[]='X-Auth-Token: ' . $this->_token;
		$data=$this->execute("DELETE", true);
		return $data['headers'];
	}
	/**
	* Moving an Object
	* 
	* <pre>
	* $gbClass=new GBClass('Application ID', 'Application Key');
	* $gbClass->moveObject('MyObjectName.extension', '/this/is/the/source/', '/this/is/the/destination/');
	* </pre>
	* 
	* @name $name The object name to be move
	* @name $source The source location of an object to be move
	* @name $destination The new location/destination of object
	*/ 
	public function moveObject($name, $source, $destination)
	{
		if($this->_token===null){
			$this->auth();
		}
		$this->_url=$this->_protocol . $this->_host . '/' . $this->_version . '/' . $this->_appid . '/' . urlencode($this->cleanDirectory($destination) . $name);
		$this->_action=self::OBJECT_RENAME;
		$this->_headers=array();
		$this->_headers[]='X-Auth-Token: ' . $this->_token;
		$this->_headers[]='X-Copy-From: ' . $this->_appid . '/' . urlencode($this->cleanDirectory($source) . $name);
		$data=$this->execute("PUT", true);
		return $data['headers'];
	}
	/**
	* Downloading an Object
	* 
	* <pre>
	* $gbClass=new GBClass('Application ID', 'Application Key');
	* $gbClass->downloadObject('MyObjectName.extension', '/this/is/the/source/');
	* 
	* @name $name The object name to download
	* @name $source The source location of an object to download
	* @return Object Content
	* </pre>     
	*/ 
	public function downloadObject($name, $source)
	{
		if($this->_token===null){
			$this->auth();
		}
		$this->_url=$this->_protocol . $this->_host . '/' . $this->_version . '/' . $this->_appid . '/' . urlencode($this->cleanDirectory($source) . $name);
		$this->_headers=array();
		$this->_headers[]='X-Auth-Token: ' . $this->_token;
		$data=$this->execute("GET");
		return $data;
	}
	/**
	* Get the metadata of an object
	*
	* <pre>
	* $gbClass=new GBClass('Application ID', 'Application Key');
	* $gbClass->objectMetadata('MyObjectName.extension', '/this/is/the/location/'); 
	*
	* @name $name The object name
	* @name $directory The directory
	* @return Array of metadata
	* </pre>
	*/
	public function objectMetadata($name, $directory)
	{
		if($this->_token===null){
			$this->auth();
		}
		$this->_url=$this->_protocol . $this->_host . '/' . $this->_version . '/' . $this->_appid . '/' . urlencode($this->cleanDirectory($directory) . $name);
		$this->_headers=array();
		$this->_headers[]='X-Auth-Token: ' . $this->_token;
		$data=$this->execute("HEAD", true);
		$headers=$data['headers'];
		$metadata=array();
		foreach($headers as $key=>$val){
			if(strpos($key, 'X-Object-Meta')!==false) $metadata[$key]=$val;
		}
		return $metadata;
	}
	/**
	* Get the storage usage with date specified
	*
	* <pre>
	* $gbClass=new GBClass('Application ID', 'Application Key');
	* $gbClass->format='json OR xml';
	* $to=date('Y-m-d');
	* $from=date('Y-m-d', strtotime(date('Y-m-d', strtotime($to)) . "-7 day"));
	* $gbClass->graphStorageUsage($from, $to);
	*
	* @name $from Start date to pull the record
	* @name $to End date to pull the record
	* @return json | xml Record
	* </pre>
	*/
	public function graphStorageUsage($from, $to)
	{
		if($this->_token===null){
			$this->auth();
		}
		$this->_url=$this->_protocol . $this->_host . '/' . $this->_version . '/' . $this->_appid . '/storage/usage/';
		$this->_headers=array();
		$this->_headers[]='X-Auth-Token: ' . $this->_token;
		$this->_headers[]='X-Graph-From: ' . $from;
		$this->_headers[]='X-Graph-To: ' . $to;
		$this->_headers[]='Content-Type: application/' . $this->format;
		$data=$this->execute("GET");
		return $data;
	}
	/**
	* Get the bandwidth utilized with date specified
	*
	* <pre>
	* $gbClass=new GBClass('Application ID', 'Application Key');
	* $gbClass->format='json OR xml';
	* $to=date('Y-m-d');
	* $from=date('Y-m-d', strtotime(date('Y-m-d', strtotime($to)) . "-7 day"));
	* $gbClass->graphBandwidthUtilized($from, $to);
	*
	* @name $from Start date to pull the record
	* @name $to End date to pull the record
	* @return json | xml Record
	* </pre>
	*/
	public function graphBandwidthUtilized($from, $to)
	{
		if($this->_token===null){
			$this->auth();
		}
		$this->_url=$this->_protocol . $this->_host . '/' . $this->_version . '/' . $this->_appid . '/bandwidth/utilized/';			
		$this->_headers=array();
		$this->_headers[]='X-Auth-Token: ' . $this->_token;
		$this->_headers[]='X-Graph-From: ' . $from;
		$this->_headers[]='X-Graph-To: ' . $to;
		$this->_headers[]='Content-Type: application/' . $this->format;
		$data=$this->execute("GET");
		return $data;		
	}
	/**
	* Get the http requests with date specified
	*
	* <pre>
	* $gbClass=new GBClass('Application ID', 'Application Key');
	* $gbClass->format='json OR xml';
	* $to=date('Y-m-d');
	* $from=date('Y-m-d', strtotime(date('Y-m-d', strtotime($to)) . "-7 day"));
	* $gbClass->graphHttpRequests($from, $to);
	*
	* @name $from Start date to pull the record
	* @name $to End date to pull the record
	* @return json | xml Record
	* </pre>
	*/
	public function graphHttpRequests($from, $to)
	{
		if($this->_token===null){
			$this->auth();
		}
		$this->_url=$this->_protocol . $this->_host . '/' . $this->_version . '/' . $this->_appid . '/http/requests/';
		$this->_headers=array();
		$this->_headers[]='X-Auth-Token: ' . $this->_token;
		$this->_headers[]='X-Graph-From: ' . $from;
		$this->_headers[]='X-Graph-To: ' . $to;
		$this->_headers[]='Content-Type: application/' . $this->format;
		$data=$this->execute("GET");
		return $data;
	}
	/**
	* Get the number of objects stored each day with date specified
	*
	* <pre>
	* $gbClass=new GBClass('Application ID', 'Application Key');
	* $gbClass->format='json OR xml';
	* $to=date('Y-m-d');
	* $from=date('Y-m-d', strtotime(date('Y-m-d', strtotime($to)) . "-7 day"));
	* $gbClass->graphObjectsStored($from, $to);
	*
	* @name $from Start date to pull the record
	* @name $to End date to pull the record
	* @return json | xml Record
	* </pre>
	*/
	public function graphObjectsStored($from, $to)
	{
		if($this->_token===null){
			$this->auth();
		}
		$this->_url=$this->_protocol . $this->_host . '/' . $this->_version . '/' . $this->_appid . '/objects/stored/';
		$this->_headers=array();
		$this->_headers[]='X-Auth-Token: ' . $this->_token;
		$this->_headers[]='X-Graph-From: ' . $from;
		$this->_headers[]='X-Graph-To: ' . $to;
		$this->_headers[]='Content-Type: application/' . $this->format;
		$data=$this->execute("GET");
		return $data;
	}
	/**
	* Create html form upload
	*
	* <pre>
	* $gbClass=new GBClass('Application ID', 'Application Key');
	* $gbClass->format='json OR xml';
	* $to=date('Y-m-d');
	* $from=date('Y-m-d', strtotime(date('Y-m-d', strtotime($to)) . "-7 day"));
	* $gbClass->uploadForm();
	*
	* @return html form upload
	* </pre>
	*/
	public function uploadForm($returnUrl, $datetime, $directory='/', $options='default', $meta=array(), $enableAuth='no')
	{
		if(count($meta)==0){
			$meta=array(
				'uploaded_from'=>'GBClass',
				'upload_date'=>date('Y-m-d', $datetime),
			);
		}
		$meta=json_encode($meta);
		$signature=$this->signature($returnUrl, $datetime, $directory, $options, $meta);
		$meta=urlencode($meta);        
		$form=<<<FORM
		<form action='http://upload.gridblaze.com' enctype='multipart/form-data' method='post'>
			<input type="file" name="file">                
			<input id="appid"       type="hidden"  name="appid"       value="$this->_appid" />
			<input id="enable_auth" type="hidden"  name="enable_auth" value="$enableAuth" />
			<input id="return_url"  type="hidden"  name="return_url"  value="$returnUrl" />
			<input id="directory"   type="hidden"  name="directory"   value="$directory" />
			<input id="options"     type="hidden"  name="options"     value="$options" />
			<input id="meta"        type="hidden"  name="meta"        value="$meta" />
			<input id="datetime"    type="hidden"  name="datetime"    value="$datetime" />
			<input id="signature"   type="hidden"  name="signature"   value="$signature" />
			<input type="submit" name="upload" value="submit">
		</form>
FORM;
		return $form;
	}
	/**
	* Generate signature for file upload
	*
	* <pre>
	* $gbClass=new GBClass('Application ID', 'Application Key');
	* $gbClass->signature();
	*
	* @return hash signature for file upload
	* </pre>
	*/
	public function signature($returnUrl, $datetime, $directory='/', $options='default', $meta='')
	{
		return hash('sha256', $this->_appid.$this->_appkey.$returnUrl.$directory.$datetime.$options.$meta);
	}
	private function setAuthData($appid, $appkey)
	{
		$this->_appid=$appid;
		$this->_appkey=$appkey;
	}
	private function execute($verb, $displayHeaders=false)
	{
		$ch=curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->_url);
		curl_setopt($ch, CURLOPT_USERAGENT, 'GBClass');
		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_headers);
		curl_setopt($ch, CURLOPT_HEADER, $displayHeaders);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		if($this->_protocol==='https://'){
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		}
		switch($verb){
			case "GET":
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $verb);
				break;
			case "POST":
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $verb);
				break;
			case "DELETE":
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $verb);
				break;
			case "PUT":
				if($this->_action===self::DIRECTORIES || $this->_action===self::OBJECT_RENAME){
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $verb);
				}elseif($this->_action===self::OBJECTS){
					$file=$this->_object['tmp_name'];
					$fhandle=fopen($this->_object['tmp_name'], "rb");
					curl_setopt($ch, CURLOPT_PUT, true);
					curl_setopt($ch, CURLOPT_INFILESIZE, filesize($file));
					curl_setopt($ch, CURLOPT_INFILE, $fhandle);
					curl_setopt($ch, CURLOPT_VERBOSE, true);
				}
				break;
			case "HEAD":
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $verb);
				break;
		}
		$response=curl_exec($ch);
		curl_close($ch);
		if($displayHeaders){
			return $this->raws($response);
		}else{
			return $response;
		}
	}
	private function raws($data)
	{
		$content=null;
		$headers=array();
		$fields=explode("\r\n", preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $data));
		foreach($fields as $field){
			if(preg_match('/([^:]+): (.+)/m', $field, $match)){
				$match[1] = preg_replace('/(?<=^|[\x09\x20\x2D])./e', 'strtoupper("\0")', strtolower(trim($match[1])));
				if(isset($headers[$match[1]])){
					$headers[$match[1]]=array($headers[$match[1]], $match[2]);
				}else{
					$headers[$match[1]]=trim($match[2]);
				}
			}else{
				if(strpos($field, 'HTTP/1.1')===false){
					$content.=$field . "\n";
				}
			}
		}
		return array(
			'content'=>$content,
			'headers'=>$headers,
		);
	}
	private function cleanDirectory($directory)
	{
		if((empty($directory)) || ($directory==='/')) return '';		
		if($directory[0]!=='/') $directory='/'.$directory;                   
		$last=substr($directory, strlen($directory) - 1);
		if($last!=='/') $directory.='/';
		$directory=str_replace(' ', '_', $directory);
		$directory=substr($directory, 1);		
		return $directory;
	}
}