<?php

/**
 *  Author: Andrew Martlew (A.Martlew@murdoch.edu.au)
 *  Date: September 2005
 *  Version: 2.0.1
 *  Description: MAIS implementation
 *  @see README.txt
 */

/* Constants */
// Defines our constants for the PHP Remote MAIS
// library. The config is broken down into two
// sections. The first section contains variables which
// can be edited, whereas the second section
// are constants that should not be edited.
//
// Section 1: Editable Variables

// The path to the lwp-request binary. You do not need this if
// you have the cURL PHP module available (Optional)
define("LWPREQUESTBIN", "/usr/bin/lwp-request");
// The MAIS key prefix used when storing data in an associative array (Required)
define("MAISKEYPREFIX", "MAIS_");
// Enabled debugging? (Optional)
define("MAISDEBUG", "true");

// Section 2: Variables which do not require editing

// The given name for the MAIS session cookie (Required)
define("MAISCOOKIE", "MAIS_SID");
// The MAIS server URL (Required)
define("MAISSERVER", "https://mais.murdoch.edu.au/server");
// The MAIS login URL (Required)
define("MAISSERVERLOGIN", "https://mais.murdoch.edu.au/login");
// The MAIS logout URL (Required)
define("MAISSERVERLOGOUT", "https://mais.murdoch.edu.au/logout");
// The MAIS environment (Required)
define("MAISENVIRONMENT", "mais");
// The MAIS cookie domain (Required)
define("MAISCOOKIEDOMAIN", ".murdoch.edu.au");
// The MAIS protocol (Required)
define("MAISPROTOCOL", "1");
/* End of constants */

/* MAIS class */
class MAIS
{
   var $m_maisdata;
   var $m_environment;
   var $m_server;
   var $m_login;
   var $m_logout;
   var $m_cookie;
   var $m_lwpreqbin;
   var $m_maisresponse;

   // Constructor
   function MAIS()
   {
      $this->m_maisdata = "";
      $this->m_environment = "";
      $this->m_server = "";
      $this->m_login = "";
      $this->m_logout = "";
      $this->m_cookie = "";
      $this->m_lwpreqbin = "";
      $this->m_maisresponse = "";
   }

   /**
    * Returns our MAIS server connect URL
    *
    * @return MAIS Server connect URL
    */
   function Connect()
   {
      $this->Debug("Connecting to MAIS server ".$this->GetMAISServer()."/?protocol=".MAISPROTOCOL);
      return $this->GetMAISServer()."/?protocol=".MAISPROTOCOL;
   }

   /**
    * Returns our MAIS server login URL
    *
    * @return MAIS Server login URL
    */
   function Login()
   {
      return $this->GetMAISLogin();
   }

   /**
    * Returns our MAIS server logout URL
    *
    * @param $from The current request URL
    * @return MAIS Server logout URL
    */
   function Logout($from)
   {
      return $this->GetMAISLogout()."/?i_mais_env=".urlencode($this->GetMAISEnvironment())."from=".urlencode($from);
   }

   /**
    * Perform a remote MAIS server request
    *
    * @param $mais_cookie The user's MAIS session cookie value
    * @param $remote_ip The user's IP address
    * @param $action The MAIS server action to perform
    * @param $values The access restrictions
    * @return True if the request was successful, false otherwise
    */
   function RemoteMAISRequest($mais_cookie, $remote_ip, $action, $access)
   {
      $this->Debug("Performing Remote MAIS call");
      if ($this->CallMAISServer($this->Connect().
                                "&action=".urlencode($action).
                                "&sid=".urlencode($mais_cookie).
                                "&remoteip=".urlencode($remote_ip).$access,$remote_ip))
      {
         if ($this->getMaisTalkKeyExists("status"))
         {
            $this->Debug("Status of request: ".$this->getMaisTalkData("status"));
            if ($this->getMaisTalkData("status") == "ok")
            {
               $this->Debug("Status ok");
               return true;
            }
            else
            {
               // Request failed
               $this->Debug("Status not ok");
               return false;
            }
         }
         else
         {
            $this->Debug("No status received from the server");
            return false;
         }
      }
      else
      {
         // Request failed
         $this->Debug("Remote MAIS call failed");
         return false;
      }
   }

   /**
    * Performs a web request to the MAIS server. Attempts to use cURL, if cURL is not present, or
    * cURL method fails, falls back to LWP-request.
    *
    * @param $URL The URL to the MAIS server with our appended access restrictions and required action
    * @param $client_ip The user's IP address
    * @return True if the request was successful, false otherwise
    */
   function CallMAISServer($URL, $client_ip)
   {
      if (! function_exists('curl_init'))
      {
         // Fall back to LWP-request. Add your own CallMAISServer_ call here
         // if neither cURL or LWP-request are available on your system
         $this->Debug("cURL not found, falling back to LWP");
         return $this->CallMAISServer_LWP($URL, $client_ip);
      }
      $this->Debug("Using cURL");

      $ch = curl_init();
      if (!$ch)
      {
         // Couldn't initialize a cURL handle
         $this->Debug("Could not initialise cURL handle");
         return false;
      }
      curl_setopt($ch, CURLOPT_URL, $URL);
      curl_setopt($ch, CURLOPT_HEADER, false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Don't verify expired SSL certs
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Don't verify different subject name to target name
      curl_setopt($ch, CURLOPT_USERAGENT, "PHP Remote MAIS Client");
      curl_setopt($ch, CURLOPT_HTTPHEADERS, array('X-Forwarded-For' => $client_ip));
      curl_setopt($ch, CURLOPT_TIMEOUT, 30);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $serverresponse = curl_exec($ch);

      // Check we received a response
      if (empty($serverresponse))
      {
         $this->Debug("No response received from MAIS server");
         curl_close($ch);
         return false;
      }

      $this->Debug("Server response: ".$serverresponse);

      // Check the returned HTTP code
      $intReturnCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      curl_close($ch);
      if ($intReturnCode != 200)
      {
         $this->Debug("MAIS server returned non 200 code: ".$intReturnCode);
         return false;
      }

      // Parse the response
      $this->SetMAISResponse($serverresponse);
      $this->ReadMAISKeysAndValues();
      return true;
   }

   /**
    * Performs an LWP-request web request to the MAIS server
    *
    * @param $URL The URL to the MAIS server with our appended access restrictions and required action
    * @param $client_ip The user's IP address
    * @return True if the request was successful, false otherwise
    */
   function CallMAISServer_LWP($URL, $client_ip)
   {
      if (! file_exists($this->m_lwpreqbin))
      {
         // No LWP-request binary
         $this->Debug("No LWP request binary found");
         return false;
      }
      $this->Debug("Using LWP request");

      exec($this->m_lwpreqbin." ".escapeshellarg($URL), $serverresponse, $status);

      $glue = "\n";
      $implodedresponse = implode($glue,$serverresponse);
      $this->Debug("Server response: ".$implodedresponse);

      # lwp-request returns a non-zero status for a non-200 http code
      if ($status)
      {
         $this->Debug("MAIS server returned non 200 code: ".$status);
         return false;
      }

      // Parse the response
      $this->SetMAISResponse($implodedresponse);
      $this->ReadMAISKeysAndValues();
      return true;
   }

   /**
    * Parses the response from the MAIS server, and populates the MAIS data associative array
    */
   function ReadMAISKeysAndValues()
   {
      // we get data in the form like ID=130708 err=0 msg=Session Valid
      $glue = "\n";
      $exploded = explode($glue,$this->GetMAISResponse());

      $glue = "=";
      foreach ($exploded as $item)
      {
         if (! empty($item))
         {
            $pairs = explode($glue,$item);
            if (! (empty($pairs[0]) || empty($pairs[1])))
            {
               $this->AddMAISData($pairs[0],$pairs[1]);
            }
         }
      }
   }

   /**
    * Returns the keys of the MAIS data associative array
    *
    * @return Array of the keys in the MAIS data associative array
    */
   function getMaisTalkKeys()
   {
      return array_keys($this->m_maisdata);
   }

    /**
     * Returns the value for a key in the MAIS data associative array
     *
     * @param $key The key to return the data for
     * @return The value of the key if it exists, otherwise blank
     */
   function getMaisTalkData($key)
   {
      $toRet = "";
      if ($this->getMaisTalkKeyExists($key))
      {
         $toRet = $this->m_maisdata[$key];
      }
      return $toRet;
   }

    /**
     * Returns the raw response from the MAIS server
     *
     * @return The MAIS server raw response
     */
   function getMaisRawResponse()
   {
      return $this->GetMAISResponse();
   }

    /**
    * Checks whether a key exists in the MAIS data associative array
    *
    * @param $key The key to check for
    * @return True if the key exists, false otherwise
    */
   function getMaisTalkKeyExists($key)
   {
      if (isset($this->m_maisdata[$key]))
      {
         return true;
      }
      else
      {
         return false;
      }
   }

   // Access mutators
   function GetMAISEnvironment()
   {
      return $this->m_environment;
   }
   function GetMAISServer()
   {
      return $this->m_server;
   }
   function GetMAISLogin()
   {
      return $this->m_login;
   }
   function GetMAISLogout()
   {
      return $this->m_logout;
   }
   function GetMAISCookie()
   {
      return $this->m_cookie;
   }
   function GetLWPRequestBin()
   {
      return $this->m_lwpreqbin;
   }
   function GetMAISResponse()
   {
      return $this->m_maisresponse;
   }

   function SetMAISEnvironment($env)
   {
      $this->m_environment = $env;
   }
   function SetMAISServer($server)
   {
      $this->m_server = $server;
   }
   function SetMAISLogin($login)
   {
      $this->m_login = $login;
   }
   function SetMAISLogout($logout)
   {
      $this->m_logout = $logout;
   }
   function SetMAISCookie($mcookie)
   {
      $this->m_cookie = $mcookie;
   }
   function SetLWPRequestBin($lwpreqbin)
   {
      $this->m_lwpreqbin = $lwpreqbin;
   }
   function SetMAISResponse($response)
   {
      $this->m_maisresponse = $response;
   }

   function AddMAISData($key,$value)
   {
      $this->m_maisdata[$key] = $value;
   }

   function ClearMAISData()
   {
      $this->m_maisdata = null;
   }

   function GetMAISDataAsArray()
   {
      $MAISData = "";
      // Populate our MAIS data array
      $MAISkeys = $this->getMaisTalkKeys();
      foreach ($MAISkeys as $key)
      {
         $MAISData[MAISKEYPREFIX.$key] = $this->getMaisTalkData($key);
      }
      return $MAISData;
   }

   function Debug($strDebug)
   {
      if (MAISDEBUG == "true")
         $this->AddMAISData("DEBUG",$this->getMaisTalkData("DEBUG")."\n".$strDebug);
   }
}
/* End MAIS class */

/* MAIS implementation */
/**
 * Performs a Remote MAIS check, sending to the MAIS server $MAISParams
 * (internally formatted) and populating $MAISData with the response.
 *
 * The MAIS server request attempts to use cURL, falling back to lwp-request
 * if the PHP cURL module does not exist, or fails. If neither of these are
 * available, you may choose to add you own. MAIS server calls are performed
 * using the CallMAISServer* methods in the MAIS.class.php file.
 *
 * @param $MAISParams Associative array of MAIS access parameters
 * @param $MAISData Associative array of MAIS data returned by the server
 * @param $exit_on_error If an error occurs (from invalid session/unauthorised access) should
 *                       the execution of the script fail here?
 *                       Default is true
 * @return True for a successful MAIS check, otherwise false (if $exit_on_error is set to true)
 */

function MAIS_Check($MAISParams, &$MAISData, $exit_on_error = true)
{
   $MAIS = new MAIS;
   $MAIS->SetMAISEnvironment(MAISENVIRONMENT);
   $MAIS->SetMAISServer(MAISSERVER);
   $MAIS->SetMAISLogin(MAISSERVERLOGIN);
   $MAIS->SetMAISLogout(MAISSERVERLOGOUT);
   $MAIS->SetMAISCookie(MAISCOOKIE);
   $MAIS->SetLWPRequestBin(LWPREQUESTBIN);
   $mais_cookie = "";
   $is_protected = false;

   // Clear any existing MAIS data
   if(is_array($MAISData))
   {
      reset($MAISData);
   }

   // Get our access parameters
   $access = "&access=".urlencode("mais.environment=".$MAIS->GetMAISEnvironment()."\n");
   $access .= GetAccessFromParams($MAISParams, $is_protected);

   // If the resource is not protected (based on passed MAISParams)
   if (! $is_protected)
   {
      $MAIS->Debug("Resource is not protected");
      $MAISData = $MAIS->GetMAISDataAsArray();
      return true;
   }

   $mais_cookie = urlencode($_COOKIE[$MAIS->GetMAISCookie()]);
   $remote_ip = urlencode($_SERVER['REMOTE_ADDR']);
   $action = "AccessCheck";

   // Perform the Remote MAIS request
   if(! $MAIS->RemoteMAISRequest($mais_cookie, $remote_ip, $action, $access))
   {
      // MAIS request failed, what should we do?
      $client_action = $MAIS->getMaisTalkData("status");
      $MAIS->Debug($client_action);

      if ($client_action == "login")
      {
         if ($exit_on_error)
         {
            $from = GetCurrentURL();
            // Set our access cookie. access_cookie is already HTTP escaped, so we decode so
            // setcookie can re-encode it...
            setcookie("MAIS_AUTH_SUMMARY",
                      "from=".$from."\n".urldecode($MAIS->getMaisTalkData("access_cookie")),
                      null,
                      "/",
                      MAISCOOKIEDOMAIN);
            // User should login to continue
            header("Location: ".$MAIS->Login());
            exit;
         }
         else
         {
            $MAIS->Debug("Login required");
            $MAISData = $MAIS->GetMAISDataAsArray();
            return false;
         }
      }
      else
      {
         if ($exit_on_error)
         {
            // Unauthorised access (action=fail or Remote MAIS request failed).
            header('HTTP/1.1 403 Forbidden');
            print "You are not authorized to view this page";
            exit;
         }
         else
         {
            $MAIS->Debug("User is not authorised to access the resource");
            $MAISData = $MAIS->GetMAISDataAsArray();
            return false;
         }
      }
   }
   else
   {
      // User is allowed access (action=ok)

      // Populate our MAIS data array
      $MAISData = $MAIS->GetMAISDataAsArray();
      return true;
   }
}

/**
 * Returns our current URL checking for protocol and port
 *
 * @return The current URL
 */
function GetCurrentURL()
{
   // Current URL
   $proto = $_SERVER["HTTPS"] == "on" ? "https://" : "http://";
   $port = ($_SERVER["SERVER_PORT"] == 80 || $_SERVER["SERVER_PORT"] == 443) ? "" : ":".$_SERVER["SERVER_PORT"];
   $from = $proto . $_SERVER['HTTP_HOST'] . $port . $_SERVER["REQUEST_URI"];
   return $from;
}

/**
 * Builds the access string from the passed MAIS parameters
 *
 * @param $MAISParams Associative array of MAIS parameters
 * @param $isprotected Whether the access parameters define a protected resource
 * @return The encoded access string
 */
function GetAccessFromParams($MAISParams, &$isprotected)
{
   $access = "";
   $isprotected = false;
   $glue = "\n";
   // Session check?
   if ($MAISParams['access'] == "mais")
   {
      $access .= urlencode("mais=1".$glue);
      $access .= urlencode("option.getuserinfo=1".$glue);
      $isprotected = true;
      // Get authority codes
      if (isset($MAISParams['mais.authority']))
      {
         $access .= urlencode("mais.authority=".$MAISParams['mais.authority'].$glue);
      }
      // Get groups
      if (isset($MAISParams['mais.group']))
      {
         $access .= urlencode("mais.group=".$MAISParams['mais.group'].$glue);
      }
      // Get users
      if (isset($MAISParams['mais.user']))
      {
         $access .= urlencode("mais.user=".$MAISParams['mais.user'].$glue);
      }
      // Get timeout
      if (isset($MAISParams['mais.timeout']))
      {
         $access .= urlencode("mais.timeout=".$MAISParams['mais.timeout'].$glue);
      }
   }
   // Get Murdoch
   if (isset($MAISParams['murdoch']))
   {
      $access .= urlencode("murdoch=".$MAISParams['murdoch'].$glue);
      $isprotected = true;
   }
   // Get Host/IP
   if (isset($MAISParams['host.ip']))
   {
      $access .= urlencode("host.ip=".$MAISParams['host.ip'].$glue);
      $isprotected = true;
   }
   return $access;
}
/* End of MAIS implementation */

?>