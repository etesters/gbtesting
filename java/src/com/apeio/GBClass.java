/**
 * 
 */
package com.apeio;

import java.io.File;
import java.io.IOException;
import java.io.UnsupportedEncodingException;
import java.net.URLEncoder;
import java.util.Calendar;
import java.util.HashMap;
import java.util.Iterator;
import java.util.Map;
import java.util.Set;

import org.apache.http.Header;
import org.apache.http.HttpRequest;
import org.apache.http.HttpResponse;
import org.apache.http.client.ClientProtocolException;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpDelete;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.client.methods.HttpHead;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.client.methods.HttpPut;
import org.apache.http.client.methods.HttpUriRequest;
import org.apache.http.entity.FileEntity;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.params.HttpProtocolParams;

/**
 * @author <a href="mailto:alaboso@gmail.com">Amos L.</a>
 * 
 */
public class GBClass {

	/*
	 * CONSTANTS
	 */
	public static final String OBJECTS = "objects";
	public static final String DIRECTORIES = "directories";
	public static final String OBJECT_RENAME = "rename";

	/*
	 * PUBLIC VARIABLES
	 */
	public String format = "json";

	/*
	 * PRIVATE VARIABLES
	 */
	private String protocol = "https://";
	private String host = "api.gridblaze.com";
	private String version = "v1";
	private String url = null;
	private String appid = null;
	private String appkey = null;
	private String token = null;
	private Map<String, String> map = new HashMap<String, String>();
	private Map<String, String> responseMap = new HashMap<String, String>();
	private String action;
	private File object = null;

	/**
	 * Default constructor.
	 * 
	 * If appID AND/OR appKEY are NOT provided, execution is terminated!
	 */
	public GBClass(String appID, String appKEY) 
	{
		if (appID.equals("") || appID == null) {
			System.err.println("Application Id cannot be empty.");
			System.exit(1);
		} else if (appKEY.equals("") || appKEY == null) {
			System.err.println("Application Key cannot be empty.");
			System.exit(1);
		} else {
			this.setAuthData(appID, appKEY);
		}
	}
	/**
	 * @param token
	 *            the token to set
	 */
	public void setToken(String token) { this.token = token; }
	/**
	 * @return the token
	 */
	public String getToken() { return token; }
	/**
	 * Returns detailed information about the version of the API
	 * 
	 * <pre>
	 * GBClass gbClass = new GBClass('Application ID', 'Application Key');
	 * gbClass.format = [json/xml];
	 * response = gbClass.info();
	 * </pre>
	 * 
	 * @return json | xml Detailed information about the version of the API
	 */
	public String info() 
	{
		this.url = this.protocol + this.host + "/" + this.version;
		this.map.put("Accept", "application/" + this.format);
		return this.execute("GET", false, this.map);
	}
	/**
	 * Get the list of nodes of GRIDBLAZE
	 * 
	 * <pre>
	 * String id='ServerId';
	 * GBClass gbClass = new GBClass('Application ID', 'Application Key');
	 * gbClass.format = [json/xml];
	 * response = gbClass.node(id);
	 * </pre>
	 * 
	 * @params String id Node ID
	 * @return json | xml Nodes list/information
	 */
	public String node(String id)
	{
		this.url = this.protocol + this.host + "/" + this.version + "/node/" + id;
		this.map.put("Accept", "application/" + this.format);
		return this.execute("GET", false, this.map);
	}

	/**
	 * Authentication to GRIDBLAZE API
	 * 
	 * <pre>
	 * GBClass gbClass = new GBClass('Application ID', 'Application Key');
	 * gbClass.auth();
	 * String token = gbClass.getToken();
	 * </pre>
	 */
	public void auth() 
	{
		if (this.token == null) {
			this.url = this.protocol + this.host + "/";
			this.map = new HashMap<String, String>();
			this.map.put("X-Auth-User", this.appid);
			this.map.put("X-Auth-Key", this.appkey);
			this.execute("GET", true, this.map);
			if (this.token == null) {
				this.token = this.responseMap.get("X-Auth-Token");
			}
		}
	}
	/**
	 * Get list of objects and/or directories
	 * 
	 * <pre>
	 * GBClass gbClass = new GBClass('Application ID', 'Application Key');
	 * gbClass.format = [json/xml];
	 * response = gbClass.listing('/this/is/the/location/', '');
	 * </pre>
	 * 
	 * @param String directory The directory
	 * @param String content GBClass::OBJECTS | GBClass::DIRECTORIES
	 * @return List of objects and/or directories (json | xml)
	 */
	public String listing(String directory, String content)
	{
		if (this.token == null) {
			this.auth();
		}		
		String encodedURL = "";
		try {
			encodedURL = URLEncoder.encode(this.cleanDirectory(directory), "UTF-8");
		} catch (UnsupportedEncodingException e) {
			e.printStackTrace();
		}
		this.url = this.protocol + this.host + "/" + this.version + "/" + this.appid + "?format=" + this.format + "&prefix=" + encodedURL + "&data=" + content;		
		this.map = new HashMap<String, String>();
		this.map.put("X-Auth-Token", this.token);
		return this.execute("GET", false, this.map);
	}
	/**
	 * Deleting a directory
	 * 
	 * <pre>
	 * GBClass gbClass = new GBClass('Application ID', 'Application Key');
	 * response = gbClass.deleteDirectory('MyObjectName.Extension', '/this/is/the/location/');
	 * </pre>
	 * 
	 * @param String name The directory name to be deleted
	 * @param String directory The directory
	 * @return Map of Headers
	 */
	public String deleteDirectory(String name, String directory) 
	{
		if (this.token == null) {
			this.auth();
		}
		String encodedURL = "";
		try {
			encodedURL = URLEncoder.encode(this.cleanDirectory(directory) + name, "UTF-8");
		} catch (UnsupportedEncodingException e) {
			e.printStackTrace();
		}
		this.url = this.protocol + this.host + "/" + this.version + "/" + this.appid + "/" + encodedURL;
		this.map = new HashMap<String, String>();
		this.map.put("X-Auth-Token", this.token);
		this.map.put("Content-Type", "application/" + this.format);
		this.execute("DELETE", true, this.map);
		return this.responseMap.toString();
	}
	/**
	 * Creating a new directory
	 * 
	 * <pre>
	 * GBClass gbClass = new GBClass('Application ID', 'Application Key');
	 * response = gbClass.createDirectory('MyDirectoryName', '/this/is/the/location/');
	 * </pre>
	 * 
	 * @param String name The name of directory to be created 
	 * @param String directory The directory
	 * @return Map of Headers
	 */
	public String createDirectory(String name, String directory) 
	{
		if (this.token == null) {
			this.auth();
		}
		String encodedURL = "";
		try {
			encodedURL = URLEncoder.encode(this.cleanDirectory(directory) + name, "UTF-8");
		} catch (UnsupportedEncodingException e) {
			e.printStackTrace();
		}
		this.url = this.protocol + this.host + "/" + this.version + "/" + this.appid + "/" + encodedURL;
		this.action = this.DIRECTORIES;
		this.map = new HashMap<String, String>();
		this.map.put("X-Auth-Token", this.token);
		this.map.put("Content-Type", "application/" + this.format);
		this.execute("PUT", true, this.map);
		return this.responseMap.toString();
	}
	/**
	 * Uploading an object
	 * 
	 * <pre>
	 * File object = new File(directory);
	 * GBClass gbClass=new GBClass('Application ID', 'Application Key');
	 * response = gbClass.putObject(object, '/this/is/the/location/');
	 * </pre>
	 * 
	 * @param File object Contains the data of object
	 * @param String directory The directory
	 * @return Map of Headers
	 */
	public String putObject(File object, String directory)
	{
		if (this.token == null) {
			this.auth();
		}
		String encodedURL = "";
		try {
			encodedURL = URLEncoder.encode(this.cleanDirectory(directory) + object.getName(), "UTF-8");
		} catch (UnsupportedEncodingException e) {
			e.printStackTrace();
		}
		this.url = this.protocol + this.host + "/" + this.version + "/" + this.appid + "/" + encodedURL;
		this.action = this.OBJECTS;
		this.object = object;
		this.map = new HashMap<String, String>();
		this.map.put("X-Auth-Token", this.token);
		this.execute("PUT", true, this.map);
		return this.responseMap.toString();
	}
	/**
	 * Rename an Object
	 * 
	 * <pre>
	 * GBClass gbClass=new GBClass('Application ID', 'Application Key');
	 * response = gbClass.renameObject('MyOldObjectName.extension', 'MyNewObjectName.extension', '/this/is/the/location/');
	 * </pre>
	 * 
	 * @param String oldName The old object name
	 * @param String newName The new / target name of an object
	 * @param String directory The directory
	 * @return Map of Headers
	 */
	public String renameObject(String oldName, String newName, String directory) 
	{
		if (this.token == null) {
			this.auth();
		}
		String encodedNewNameURL = "";
		String encodedOldNameURL = "";
		try {
			encodedNewNameURL = URLEncoder.encode(this.cleanDirectory(directory) + newName, "UTF-8");
			encodedOldNameURL = URLEncoder.encode(this.cleanDirectory(directory) + oldName, "UTF-8");
		} catch (UnsupportedEncodingException e) {
			e.printStackTrace();
		}
		this.url = this.protocol + this.host + "/" + this.version + "/" + this.appid + "/" + encodedNewNameURL;
		this.action = this.OBJECT_RENAME;
		this.map = new HashMap<String, String>();
		this.map.put("X-Auth-Token", this.token);
		this.map.put("X-Copy-From", this.appid + "/" + encodedOldNameURL);
		this.map.put("Content-Type", "application/" + this.format);
		this.execute("PUT", true, this.map);
		return this.responseMap.toString();
	}

	/**
	 * Deleting an Object
	 * 
	 * <pre>
	 * GBClass gbClass=new GBClass('Application ID', 'Application Key');
	 * response = gbClass.deleteObject('MyObjectName.extension', '/this/is/the/location/');
	 * </pre>
	 * 
	 * @param String name The object name to be deleted
	 * @param String directory The directory
	 * @return Map of Headers
	 */
	public String deleteObject(String name, String directory) 
	{
		if (this.token == null) {
			this.auth();
		}
		String encodedURL = "";
		try {
			encodedURL = URLEncoder.encode(this.cleanDirectory(directory) + name, "UTF-8");
		} catch (UnsupportedEncodingException e) {
			e.printStackTrace();
		}
		this.url = this.protocol + this.host + "/" + this.version + "/" + this.appid + "/" + encodedURL;
		this.map = new HashMap<String, String>();
		this.map.put("X-Auth-Token", this.token);
		this.map.put("Content-Type", "application/" + this.format);
		this.execute("DELETE", true, this.map);
		return this.responseMap.toString();
	}

	/**
	 * Moving an Object
	 * 
	 * <pre>
	 * GBClass gbClass=new GBClass('Application ID', 'Application Key');
	 * response = gbClass.moveObject('MyObjectName.extension', '/this/is/the/source/', '/this/is/the/destination/');
	 * </pre>
	 * 
	 * @name String name The object name to be move
	 * @name String source The source location of an object to be move
	 * @name String destination The new location/destination of object
	 * @return Map of Headers
	 */
	public String moveObject(String name, String source, String destination) 
	{
		if (this.token == null) {
			this.auth();
		}
		String encodedDestinationURL = "";
		String encodedSourceURL = "";
		try {
			encodedDestinationURL = URLEncoder.encode(this.cleanDirectory(destination) + name, "UTF-8");
			encodedSourceURL = URLEncoder.encode(this.cleanDirectory(source) + name, "UTF-8");
		} catch (UnsupportedEncodingException e) {
			e.printStackTrace();
		}
		this.url = this.protocol + this.host + "/" + this.version + "/" + this.appid + "/" + encodedDestinationURL;
		this.action = this.OBJECT_RENAME;
		this.map = new HashMap<String, String>();
		this.map.put("X-Auth-Token", this.token);
		this.map.put("X-Copy-From", this.appid + "/" + encodedSourceURL);
		this.execute("PUT", true, this.map);
		return this.responseMap.toString();
	}
	/**
	* Get the metadata of an object
	*
	* <pre>
	* GBClass gbClass=new GBClass('Application ID', 'Application Key');
	* response = gbClass.moveObject('MyObjectName.extension', '/this/is/the/source/', '/this/is/the/destination/');
	* </pre>
    * 
	* @name String name The object name
	* @name Sting directory The directory
	* @return Map of Headers
	*/
	public String objectMetadata(String name, String directory)
	{
		if (this.token == null) {
			this.auth();
		}
		String encodedURL = "";
		try {
			encodedURL = URLEncoder.encode(this.cleanDirectory(directory) + name, "UTF-8");
		} catch (UnsupportedEncodingException e) {
			e.printStackTrace();
		}			
		this.url = this.protocol + this.host + "/" + this.version + "/" + this.appid + "/" + encodedURL;		
		this.map = new HashMap<String, String>();
		this.map.put("X-Auth-Token", this.token);		
		this.execute("HEAD", true, this.map);
		Map<String, String> metadata = new HashMap<String, String>();
		Set<String> keys = this.responseMap.keySet();
		for(String key : keys){
			if(key.toLowerCase().contains("x-object-meta")){
				metadata.put(key, this.responseMap.get(key));
			}
		}		
		return metadata.toString();
	}
	/**
	* Get the storage usage with date specified
	*
	* <pre>
	* GBClass gbClass = new GBClass('Application ID', 'Application Key');
	* response = gbClass.graphStorageUsage(gbClass.gbDate(-10), gbClass.gbDate(0));	
    * </pre>
	*
	* @name String from Start date to pull the record
	* @name String to End date to pull the record
	* @return json | xml Record
	*/
	public String graphStorageUsage(String from, String to)
	{
		if (this.token == null) {
			this.auth();
		}
		this.url = this.protocol + this.host + "/" + this.version + "/" + this.appid + "/storage/usage/";
		this.map = new HashMap<String, String>();
		this.map.put("X-Auth-Token", this.token);
		this.map.put("X-Graph-From", from);
		this.map.put("X-Graph-To", to);
		this.map.put("Content-Type", "application/" + this.format);
		return this.execute("GET", false, this.map);
	}
	/**
	* Get the bandwidth utilized with date specified
	*
	* <pre>
	* GBClass gbClass = new GBClass('Application ID', 'Application Key');
	* response = gbClass.graphBandwidthUtilized(gbClass.gbDate(-10), gbClass.gbDate(0));	
    * </pre>
	*
	* @name String from Start date to pull the record
	* @name String to End date to pull the record
	* @return json | xml Record
	*/
	public String graphBandwidthUtilized(String from, String to)
	{
		if (this.token == null) {
			this.auth();
		}
		this.url = this.protocol + this.host + "/" + this.version + "/" + this.appid + "/bandwidth/utilized/";
		this.map = new HashMap<String, String>();
		this.map.put("X-Auth-Token", this.token);
		this.map.put("X-Graph-From", from);
		this.map.put("X-Graph-To", to);
		this.map.put("Content-Type", "application/" + this.format);
		return this.execute("GET", false, this.map);
	}
	/**
	* Get the http requests with date specified
	*
	* <pre>
	* GBClass gbClass = new GBClass('Application ID', 'Application Key');
	* response = gbClass.graphHttpRequests(gbClass.gbDate(-10), gbClass.gbDate(0));	
    * </pre>
	*
	* @name String from Start date to pull the record
	* @name String to End date to pull the record
	* @return json | xml Record
	*/
	public String graphHttpRequests(String from, String to)
	{
		if (this.token == null) {
			this.auth();
		}
		this.url = this.protocol + this.host + "/" + this.version + "/" + this.appid + "/http/requests/";
		this.map = new HashMap<String, String>();
		this.map.put("X-Auth-Token", this.token);
		this.map.put("X-Graph-From", from);
		this.map.put("X-Graph-To", to);
		this.map.put("Content-Type", "application/" + this.format);
		return this.execute("GET", false, this.map);
	}
	/**
	* Get the number of objects stored each day with date specified
	*
	* <pre>
	* GBClass gbClass = new GBClass('Application ID', 'Application Key');
	* response = gbClass.graphObjectsStored(gbClass.gbDate(-10), gbClass.gbDate(0));	
    * </pre>
	*
	* @name String from Start date to pull the record
	* @name String to End date to pull the record
	* @return json | xml Record
	*/
	public String graphObjectsStored(String from, String to)
	{
		if (this.token == null) {
			this.auth();
		}
		this.url = this.protocol + this.host + "/" + this.version + "/" + this.appid + "/objects/stored/";
		this.map = new HashMap<String, String>();
		this.map.put("X-Auth-Token", this.token);
		this.map.put("X-Graph-From", from);
		this.map.put("X-Graph-To", to);
		this.map.put("Content-Type", "application/" + this.format);
		return this.execute("GET", false, this.map);
	}
	public String uploadForm(String returnUrl, String datetime, String directory, String options, String[] meta, String enableAuth)
	{
		return "";
	}
	public String signature(String returnUrl, String datetime, String directory, String options, String enableAuth, String meta)
	{
		return "";
	}
	/**
	 * Return date with format Year-Month-Date
	 * 
	 * @param day
	 * 		0  = Current Date
	 * 		-1 = Subtract one day at current date
	 * 		+1 = Add one day at current date
	 * @return
	 */
	public String gbDate(int day)
	{
		String arrayMonths[] = { "01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12" };
		java.util.Calendar calendar = java.util.Calendar.getInstance();
		int year=calendar.get(java.util.Calendar.YEAR);
		int month=calendar.get(java.util.Calendar.MONTH);
		int date=calendar.get(java.util.Calendar.DATE);
		calendar.set(year, month, date);		
		if(day < 0 || day > 0){
			calendar.add(java.util.Calendar.DATE, day);			
			year=calendar.get(Calendar.YEAR);
			month=calendar.get(Calendar.MONTH);
			date=calendar.get(Calendar.DATE);
		}		
		return Integer.toString(year) + "-" + arrayMonths[month] + "-" + Integer.toString(date);
	}
	private void setAuthData(String appID, String appKEY)
	{
		this.appid = appID;
		this.appkey = appKEY;
	}
	private String execute(String verb, boolean responseHeaders, Map<String, String> headers) 
	{
		HttpClient httpClient = new DefaultHttpClient();
		HttpProtocolParams.setUserAgent(httpClient.getParams(), "GBClass");

		String responseString = null;

		HttpRequest request = null;
		HttpResponse response = null;

		if (this.protocol.equalsIgnoreCase("https://")) {
			httpClient = new SSLUtils().SSLClient(httpClient);
		}

		if (verb.equalsIgnoreCase("GET")) {
			request = (HttpGet) new HttpGet(this.url);
		} else if (verb.equalsIgnoreCase("POST")) {
			request = (HttpPost) new HttpPost(this.url);
		} else if (verb == "DELETE") {
			request = (HttpDelete) new HttpDelete(this.url);
		} else if (verb.equalsIgnoreCase("PUT")) {
			if (this.action.equalsIgnoreCase(this.DIRECTORIES)
					|| this.action.equalsIgnoreCase(this.OBJECT_RENAME)) {
				request = (HttpPut) new HttpPut(this.url);
			} else if (this.action.equalsIgnoreCase(this.OBJECTS)) {
				FileEntity fileEntity = new FileEntity(this.object, "");
				request = (HttpPut) new HttpPut(this.url);

				((HttpPut) request).setEntity(fileEntity);
			}
		} else if(verb.equalsIgnoreCase("HEAD")){
			request = (HttpHead) new HttpHead(this.url);
		}

		Set vals = headers.keySet();
		for (Iterator i = vals.iterator(); i.hasNext();) {
			String key = (String) i.next();
			String value = (String) map.get(key);

			request.addHeader(key, value);
		}

		try {
			response = httpClient.execute((HttpUriRequest) request);			
			if (responseHeaders) {
				responseString = this.extractHeaders(response);
			} else {
				responseString = ResponseUtils.getResponse(response);
			}
		} catch (ClientProtocolException e) {
			e.printStackTrace();
		} catch (IOException e) {
			e.printStackTrace();
		} finally {
			httpClient.getConnectionManager().shutdown();
		}

		return responseString;
	}
	private String extractHeaders(HttpResponse response) 
	{
		this.responseMap = new HashMap<String, String>();
		this.responseMap.put("StatusCode", response.getStatusLine().toString());
		Header[] heads = response.getAllHeaders();
		for (int i = 0; i < heads.length; i++) {
			this.responseMap.put(heads[i].getName(), heads[i].getValue());
		}
		return this.responseMap.toString();
	}
	private String cleanDirectory(String directory) 
	{

		if (directory.isEmpty() || directory.equalsIgnoreCase("/")) {
			return "";
		}
		if (directory.charAt(0) != '/') {
			directory = "/" + directory;
		}
		char last = directory.charAt(directory.length() - 1);
		if (last != '/') {
			directory = directory + '/';
		}
		directory = directory.replace(' ', '_');
		directory = directory.substring(1, directory.length());
		return directory;
	}	
}
