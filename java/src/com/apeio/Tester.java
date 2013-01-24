/**
 * 
 */
package com.apeio;

import java.security.NoSuchAlgorithmException;

//import java.io.File;
//import java.util.Calendar;

/**
 * @author amosl
 *
 */
public class Tester {

	/**
	 * @param args
	 * @throws NoSuchAlgorithmException 
	 */
	public static void main(String[] args) throws NoSuchAlgorithmException {
		GBClass gbClass = new GBClass("27e081d7", "25ab189e95d16aad4ec6ef8ed94db92b");		
		String response = "";
		
		//response = gbClass.info();
		
		//response = gbClass.node("");
		
		//response = gbClass.node("br1");
		
		//gbClass.auth();
		//response = gbClass.getToken();
		
		//response = gbClass.listing("/", "");
		
		//response = gbClass.listing("/", gbClass.OBJECTS);
		
		//response = gbClass.listing("/", gbClass.DIRECTORIES);
		
		//response = gbClass.deleteDirectory("name of directory to be deleted directory", "/");
		
		//response = gbClass.createDirectory("name of the directory to created", "/");
		
		//String directory = "/this/is/the/location/of/object/file.extension";
		//File object = new File(directory);
		//response = gbClass.putObject(object, "/");
		
		//response = gbClass.renameObject("old name.jpg", "new name.extension", "/");
		
		//response = gbClass.deleteObject("name of the object.extension", "/");
		
		//response = gbClass.moveObject("name of the object.extension", "/", "/4/");
				
		//response = gbClass.objectMetadata("amazon.extension", "/");
		
		
		//String from = gbClass.gbDate(-10);
		//String to = gbClass.gbDate(0);
		//response = gbClass.graphStorageUsage(from, to);		
		
		//String from = gbClass.gbDate(-10);
		//String to = gbClass.gbDate(0);
		//response = gbClass.graphBandwidthUtilized(from, to);		
		
		//String from = gbClass.gbDate(-10);
		//String to = gbClass.gbDate(0);
		//response = gbClass.graphHttpRequests(from, to);		
		
		//String from = gbClass.gbDate(-10);
		//String to = gbClass.gbDate(0);
		//response = gbClass.graphObjectsStored(from, to);
		
		//String returnUrl="http://www.domain.com";
		//String directory = "/";
		//String datetime = String.valueOf(System.currentTimeMillis() / 1000);		
		//String options = "default";
		//String enableAuth = "no";
		//String meta = "{'1': '2'}";
		//response = gbClass.uploadForm(returnUrl, datetime, directory, options, enableAuth, meta);
		
		String returnUrl="http://www.domain.com";
		String directory = "/";
		String datetime = String.valueOf(System.currentTimeMillis() / 1000);		
		String options = "default";
		String enableAuth = "no";
		String meta = "{'1': '2'}";
		response  = gbClass.signature(returnUrl, datetime, directory, options, enableAuth, meta);
		
		System.out.println(response);
	}

}
