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
	public function __construct($appid, $appkey);
	/**
	* Setting the Token Value
	* 
	* @param $token The token to be set
	* @return void
	*/
	public function setToken($token);
	/**
	* Getting the Token
	* 
	* @return Token value
	*/
	public function getToken();
	/**
	* Returns detailed information about the version of the API
	* 
	* <pre>
	* $gbClass=new GBClass('Application ID', 'Application Key');
	* $gbClass->format=[json/xml];
	* $gbClass->info();
	* </pre>
	* 
	* @return json | xml  Detailed information about the version of the API
	*/
	public function info();
	/**
	* Get the list of nodes of GRIDBLAZE
	* 
	* <pre>
	* $id='ServerId';
	* $gbClass=new GBClass('Application ID', 'Application Key');
	* $gbClass->format=[json/xml];
	* $gbClass->node($id);
	* </pre>
	* 
	* @params $id Node ID
	* @return json | xml Nodes list/information
	*/
	public function node($id='');
	/**
	* Authentication to GRIDBLAZE API
	* 
	* <pre>
	* $gbClass=new GBClass('Application ID', 'Application Key');	
	* $gbClass->auth();
	* </pre>
	*/ 
	public function auth();
	/**
	* Get list of objects and/or directories
	* 
	* <pre>
	* $gbClass=new GBClass('Application ID', 'Application Key');
	* $gbClass->format=[json/xml];	
	* $gbClass->listing('/this/is/the/location/', '');
	* </pre>
	* 
	* @param $directory The directory
	* @param $content GBClass::OBJECTS | GBClass::DIRECTORIES 
	* @return List of objects and/or directories (json | xml)
	*/ 
	public function listing($directory='/', $content='');
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
	public function deleteDirectory($name, $directory='/');
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
	public function createDirectory($name, $directory='/');
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
	public function putObject($object, $directory='/', $targetNodeId=null);
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
	public function renameObject($oldName, $newName, $directory='/');
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
	public function deleteObject($name, $directory='/');
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
	public function moveObject($name, $source, $destination);
	/**
	* Downloading an Object
	* 
	* <pre>
	* $gbClass=new GBClass('Application ID', 'Application Key');
	* $gbClass->moveObject('MyObjectName.extension', '/this/is/the/source/');
	* 
	* @name $name The object name to download
	* @name $source The source location of an object to download
	* @return Object Content
	* </pre>     
	*/ 
	public function downloadObject($name, $source);
}