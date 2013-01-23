/**
 * 
 */
package com.apeio;

import java.io.BufferedInputStream;
import java.io.IOException;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.ParseException;
import org.apache.http.util.EntityUtils;

/**
 * This is a utility class for formatting the response from the server into a
 * required format
 * 
 * @author <a href="mailto:alaboso@gmail.com">Amos L.</a>
 * 
 */
public class ResponseUtils {

	public static String getResponse(HttpResponse response) {

		String _response = "";
		HttpEntity responseEntity = null;

		try {
			if (response != null) {
				responseEntity = response.getEntity();
			}

			if (responseEntity != null) {
				_response = EntityUtils.toString(responseEntity);
			}

		} catch (ParseException e) {
			e.printStackTrace();
		} catch (IOException e) {
			e.printStackTrace();
		} catch (Exception e){
			e.printStackTrace();
		} finally {
			try {
				EntityUtils.consume(responseEntity);
			} catch (IOException e) {
				e.printStackTrace();
			}
		}

		return _response;
	}
}
