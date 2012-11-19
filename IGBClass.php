<?php
interface IGBClass
{
	public $format='json';
	
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
	public function downloadObject($name, $source);
	
	public function objectMetadata($name, $source);	
	public function graphStorageUsage($from, $to);
	public function graphBandwidthUtilized($from, $to);
	public function graphHttpRequests($from, $to);
	public function graphObjectsStored($from, $to);
	public function uploadForm();
	public function jsUploadWidget();
}