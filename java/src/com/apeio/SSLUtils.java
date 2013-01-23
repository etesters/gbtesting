/**
 * 
 */
package com.apeio;

import java.security.KeyManagementException;
import java.security.NoSuchAlgorithmException;

import javax.net.ssl.SSLContext;
import javax.net.ssl.TrustManager;
import javax.net.ssl.X509TrustManager;

import org.apache.http.client.HttpClient;
import org.apache.http.conn.scheme.Scheme;
import org.apache.http.conn.ssl.SSLSocketFactory;

/**
 * This is a utility class for SSL related utilities.
 * 
 * @author <a href="mailto:alaboso@gmail.com">Amos L.</a>
 * 
 */
public class SSLUtils {

	public SSLUtils() {

	}

	/**
	 * This function configures the http client to accept server certificates
	 * 
	 * @param httpClient HttpClient object
	 * 
	 * @return HttpClient
	 */
	public HttpClient SSLClient(HttpClient httpClient) {

		TrustManager[] trustAllCerts = new TrustManager[] { new X509TrustManager() {
			public java.security.cert.X509Certificate[] getAcceptedIssuers() {
				return null;
			}

			public void checkClientTrusted(
					java.security.cert.X509Certificate[] certs, String authType) {
			}

			public void checkServerTrusted(
					java.security.cert.X509Certificate[] certs, String authType) {
			}
		} };

		SSLContext sslContext = null;

		try {
			sslContext = SSLContext.getInstance("SSL");
			sslContext.init(null, trustAllCerts,
					new java.security.SecureRandom());
			
		} catch (NoSuchAlgorithmException e) {
			e.printStackTrace();
		} catch (KeyManagementException e) {
			e.printStackTrace();
		}

		SSLSocketFactory sf = new SSLSocketFactory(sslContext,
				SSLSocketFactory.ALLOW_ALL_HOSTNAME_VERIFIER);
		Scheme sch = new Scheme("https", 443, sf);
		httpClient.getConnectionManager().getSchemeRegistry().register(sch);

		return httpClient;
	}
}
