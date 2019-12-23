<?php

namespace Centry;

class CentrySDK {

  const PublicEndpoints = array(
    "oauth/token"
  );

  private $clientId;
  private $clientSecret;
  private $redirectUri;

  public $accessToken;
  public $refreshToken;
  public $tokenType;
  public $scope;
  public $createdAt;
  public $expiresIn;

  /**
  * Constructor de la clase SDK.
  * @var clientId Identificador de la aplicación. Es generado por Centry.
  * @var clientSecret Clave secreta de la aplicación. Es generado por Centry, debe ser conocido sólo por la aplicación y
  *                   Centry. Los usuarios no tienen que tener acceso a este dato.
  * @var redirectUri URL a la que Centry enviará el código de autorización como parámetro GET cada vez que un usuario
  *                  autorice a ésta a acceder a sus datos. Si se usa la URI `urn:ietf:wg:oauth:2.0:oob`, entonces el
  *                  código de autorización se mostrará en pantalla y el usuario deberá copiarlo y pegarlo donde la
  *                  aplicación pueda leerlo.
  * @var accessToken (opcional) Último access_token del que se tiene registro. Si se entrega, entonces no es necesario que el usuario tenga que volver a autorizar la aplicacción.
  * @var refreshToken (opcional) Último refresh_token del que se tiene registro.
  */

  function __construct($clientId, $clientSecret, $redirectUri, $accessToken = null, $refreshToken = null) {
    $this->clientId = $clientId;
    $this->clientSecret = $clientSecret;
    $this->redirectUri = $redirectUri;
    $this->accessToken = $accessToken;
    $this->refreshToken = $refreshToken;
  }

  /**
  * Genera la URL con la que le pediremos a un usuario que nos entregue los permisos
  * de lecturo y/o escritura a los recursos que se indican en el parámetro <code>scope</code>
  * @var code Es la concatenación con un espacio de separación (" ") de todos los ámbitos a
  * los que se solicita permiso. Estos pueden ser:
  * <ul>
  *   <li><b>public</b> Para acceder a la información publica de Centry como marcas, categorías, colores, tallas, etc.</li>
  *   <li><b>read_orders</b> Para leer información de pedidos</li>
  *   <li><b>write_orders</b> Para manulupar o eliminar pedidos</li>
  *   <li><b>read_products</b>Para leer información de productos y variantes</li>
  *   <li><b>write_products</b>Para manulupar o eliminar productos y variantes</li>
  *   <li><b>read_integration_config</b>Para leer información de configuraciones de integraciones</li>
  *   <li><b>write_integration_config</b>Para manulupar o eliminar configuraciones de integraciones</li>
  *   <li><b>read_user</b>Para leer información de usuarios de la empresa</li>
  *   <li><b>write_user</b>Para manulupar o eliminar usuarios de la empresa</li>
  *   <li><b>read_webhook</b>Para leer información de webhooks</li>
  *   <li><b>write_webhook</b>Para manulupar o eliminar webhooks</li>
  * </ul>
  */
  function authorizationURL($scope) {
    $params = array(
      "client_id" => $this->clientId,
      "redirect_uri" => $this->redirectUri,
      "response_type" => "code",
      "scope" => $scope
    );
    return "https://www.centry.cl/oauth/authorize?" . http_build_query($params);
  }

  /**
  * Método encargado de hacer todo tipo de solicitudes a Centry, desde autorizaciones hasta manipulación de inventario.
  * @var endpoint
  * @var method String indicado el método HTTP a usar. Las opciones son "GET", "POST", "PUT", "DELETE". Como es una API REST,
  *             estos métodos suelen estar asociados a la lectura, creación, edición y eliminacion de recursos.
  * @var params (opcional) Parámetros
  * @var payload (opcional) Body del request puede ser un objeto PHP o un arreglo (diccionario), internamente es transformado a JSON.
  */


  function request($endpoint, $method, $params = array(), $payload = array()) {
    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, "https://www.centry.cl/$endpoint" . ($params ? "?" . http_build_query($params) : ""));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    $header = array("Content-Type: application/json");
    if ($payload) {
      $stringPayload = json_encode($payload);
      curl_setopt($curl, CURLOPT_POSTFIELDS, $stringPayload);
      $header[] = 'Content-Length: ' . strlen($stringPayload);
    }
    if (!in_array($endpoint, CentrySDK::PublicEndpoints)) {
      $header[] = "Authorization: Bearer $this->accessToken";
    }
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);
    return $err ? $err : json_decode($response);
  }

    /**
     * @param $endpoint Endpoint de Centry que se hara un POST.
     * @param array $params (opcional) Parámetros para el request.
     * @param array $payload (opcional) Body del request puede ser un objeto PHP o un arreglo (diccionario), internamente es transformado a JSON.
     * @return mixed|string
     */
    function post($endpoint, $params=array(), $payload){
        return $this->request($endpoint, "POST", $params, $payload);
    }

    /**
     * @param $endpoint Endpoint de Centry que se hara un GET.
     * @param array $params (opcional) Parámetros para el request.
     * @return mixed|string
     */
  function get($endpoint, $params=array()){
      return $this->request($endpoint, "GET", $params, null);
  }

    /**
     * @param $endpoint Endpoint de Centry que se hara un UPDATE.
     * @param array $params (opcional) Parámetros para el request.
     * @param array $payload (opcional) Body del request puede ser un objeto PHP o un arreglo (diccionario), internamente es transformado a JSON.
     * @return mixed|string
     */
    function update($endpoint, $params=array(), $payload=array()){
        return $this->request($endpoint, "PUT", $params, $payload);
    }


    /**
     * @param $endpoint Endpoint de Centry que se hara un DELETE.
     * @param array $params (opcional) Parámetros para el request.
     * @return mixed|string
     */
    function delete($endpoint, $params=array()){
        return $this->request($endpoint, "DELETE", $params, null);
    }

  /**
  * Una vez que un usuario ha autorizado nuestra aplicación para que accceda a su información, Centry genera un código
  * de autorización con el cual podremos solicitar el primer access_token y refresh_token. Éste método se encarga de
  * esta tarea por lo que se le debe entrecar el código de autorización como parámetro.
  * Se recomienda registrar estos tokens con algún mecanismo de persistencia como una base de datos.
  * @var code Código de autorización generado por Centry depués de que el usuario autorizó la aplicación.
  * @see https://www.oauth.com/oauth2-servers/access-tokens/authorization-code-request/
  */
  function authorize($code) {
    return $this->__grant("authorization_code", array("code" => $code));
  }

  /**
  * Un access_token tiene una vigencia de 7200 segudos (2 horas) por lo que una vez cumplido ese plazo es necesario
  * solicitar un nuevo token usando como llave el refresh_token que teníamos registrado. Este método se encarga de hacer
  * esta renovacion de tokens.
  * Se recomienda registrar estos nuevos tokens con algún mecanismo de persistencia como una base de datos.
  * @see https://www.oauth.com/oauth2-servers/access-tokens/authorization-code-request/
  */
  function refresh() {
    return $this->__grant("refresh_token", array("refresh_token" => $this->refreshToken));
  }

  function client_credentials($scope = null) {
    if ($scope == null or trim($scope) == ""){
        $scp = array();
    }
    else{
        $scp = array("scope" => trim($scope));
    }
    return $this->__grant("client_credentials", $scp);
  }

  function __grant($grant_type, $extras) {
      $endpoint = 'oauth/token';
      $method = "POST";
      $params = null;
      $p = array(
          "client_id" => $this->clientId,
          "client_secret" => $this->clientSecret,
          "redirect_uri" => $this->redirectUri,
          "grant_type" => $grant_type
      );
      $payload = array_merge($p, $extras);
      $response = $this->request($endpoint, $method, $params, $payload);
      if (property_exists($response, 'access_token'))   $this->accessToken = $response->access_token;
      if (property_exists($response, 'refresh_token'))  $this->refreshToken = $response->refresh_token;
      if (property_exists($response, 'token_type'))     $this->tokenType = $response->token_type;
      if (property_exists($response, 'scope'))          $this->scope = $response->scope;
      if (property_exists($response, 'created_at'))     $this->createdAt = $response->created_at;
      if (property_exists($response, 'expires_in'))     $this->expiresIn = $response->expires_in;
      return $this;
  }



}

