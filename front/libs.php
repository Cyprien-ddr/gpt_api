<?php
use Symfony\Component\HttpClient\HttpClient;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\TransportException;
use GuzzleHttp\Exception\ClientException;

//$apiClient = HttpClient::create();
$apiClient = new Client();

function process($cmd) {
  $descriptorspec = array(
    0 => array("pipe", "r"),  // // stdin est un pipe où le processus va lire
    1 => array("pipe", "w"),  // stdout est un pipe où le processus va écrire
    2 => array("pipe", "w", "a") // stderr est un fichier
  );

  $cwd = '/tmp';
  $env = array('quelques_options' => 'aeiou');

  $process = proc_open($cmd, $descriptorspec, $pipes, $cwd, $env);

  if (is_resource($process)) {
      // $pipes ressemble à :
      // 0 => fichier accessible en écriture, connecté à l'entrée standard du processus fils
      // 1 => fichier accessible en lecture, connecté à la sortie standard du processus fils
      // Toute erreur sera ajoutée au fichier /tmp/error-output.txt

      $res = array();
      
      $res["stdout"] = stream_get_contents($pipes[1]);
      fclose($pipes[1]);
      $res["stderr"] = stream_get_contents($pipes[2]); fclose($pipes[2]);
      // Il est important que vous fermiez les pipes avant d'appeler
      // proc_close afin d'éviter un verrouillage.
      $return_value = proc_close($process);

      $res["status"] = proc_close($process);
      return $res;
  }
}
getAuthToken();
function getAuthToken(){
    global $apiAuthToken;
    global $apiUser;
    global $apiPwd;
    $tokenFilePath = dirname(__FILE__).'/api_token.txt';
    $newTokenNeeded = true;
    if(file_exists($tokenFilePath)) {
      if (time()-filemtime($tokenFilePath) < 3600) {
        $newTokenNeeded = false;
      }
    }
    if ($newTokenNeeded) {
      $response = getResponse('POST', 'auth', json_encode(["user" =>$apiUser, "password" => $apiPwd]));
      $token = $response->access_token;
      file_put_contents($tokenFilePath, $token);
    }

    $apiAuthToken = file_get_contents($tokenFilePath);
    return $apiAuthToken ;
}

function getResponse($method, $endpoint, $body = null, $files = null) {
      global $connectionException;
      global $apiAuthToken;
      global $apiUrl;
      global $apiClient;
      $contentType = 'application/json';
      if($files) {
        $contentType='multipart/form-data';
        $body = new MultipartStream($files);
      }

      $headers = [
          'contentType' => $contentType,
          'Accept' => 'application/json',
          'Authorization' => 'Bearer '.$apiAuthToken
      ];

      $request = new Request(
          $method,
          $apiUrl. '/' . $endpoint,
          $headers,
          $body
      );
      try{
        $response = $apiClient->send($request, ['connect_timeout' => 10]);
        $reponseBody = $response->getBody();
        $results = json_decode($reponseBody, false);
      } catch(ConnectException $e){ 
        $connectionException = true;
        $results = $e->getMessage();
      } catch(ServerException $e){ 
        $results = $e->getMessage();
      }
      catch(TransportException $e)
      {
        $results = $e->getMessage();
      }
      catch (ClientException $e) {
        $connectionException = true;
        $results = $e->getMessage();
      }

      return $results;
}

/*
function getAuthToken(){

    global $apiUrl;
    global $apiUser;
    global $apiPwd;
    global $apiClient;
    $tokenFilePath = dirname(__FILE__).'/api_token.txt';
    $newTokenNeeded = true;
    if(file_exists($tokenFilePath)) {
      if (time()-filemtime($tokenFilePath) < 3600) {
        $newTokenNeeded = false;
      }
    }
    if ($newTokenNeeded) {
      $response = $apiClient->request('POST', $apiUrl . '/auth',[
      'headers' => [
          'Content-Type' => 'application/json',
      ],
      'body' => '{"user": "'.$apiUser.'", "password": "'.$apiPwd.'"}',
      ]);
      $statusCode = $response->getStatusCode();
      $reponseBody = $response->getContent();
      $token = json_decode($reponseBody)->access_token;
      file_put_contents($tokenFilePath, $token);
    }

    return file_get_contents($tokenFilePath);
}

function getResponse($method, $endpoint, $contentType='application/json', $body = null) {
      global $apiUrl;
      global $apiClient;
      $headers = [
          'contentType' => $contentType,
          'Accept' => 'application/json',
          //'Authorization' => 'Bearer '.getAuthToken()
      ];

      $response = $apiClient->request($method, $apiUrl . '/' . $endpoint,[
      'headers' => $headers,
      'body' => $body
      ]);
      $reponseBody = $response->getContent();
      $results = json_decode($reponseBody, true);
      
      return $results;
}
*/