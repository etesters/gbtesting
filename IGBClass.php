<?php
interface IGBClass
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
    
    
	public function __construct($appid, $appkey);
	public function setToken($token);
	public function getToken();
	public function info();
	public function node($id='');
	public function auth();
	public function listing($directory='/', $content='');
	public function deleteDirectory($name, $directory='/');
	public function createDirectory($name, $directory='/');
	public function putObject($object, $directory='/', $targetNodeId=null);
	public function renameObject($oldName, $newName, $directory='/');
	public function deleteObject($name, $directory='/');
	public function moveObject($name, $source, $destination);
	public function downloadObject($nodeid, $name, $source, $ssl=false);
    
	
    // new methods
    public function objectMetadata($name, $directory);
	public function graphStorageUsage($from, $to);
	public function graphBandwidthUtilized($from, $to);
	public function graphHttpRequests($from, $to);
	public function graphObjectsStored($from, $to);
	public function uploadForm($returnUrl, $datetime, $directory='/', $options='default', $meta=array(), $enableAuth='no');
	public function signature($returnUrl, $datetime, $directory='/', $options='default', $enableAuth='no', $meta=array());
    
    
    // not yet implemented
    public function jsUploadWidget();
    
    private function setAuthData($appid, $appkey);
	private function execute($verb, $displayHeaders=false);
	private function extractHeaders($headers);
	private function cleanDirectory($directory);
}