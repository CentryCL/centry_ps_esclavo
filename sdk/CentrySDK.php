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
   * @var accessToken (opcional) Último access_token del que se tiene registro. Si se entrega, entonces no es necesario que el usuario tenga que volver a autorizar la aplicación.
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
   * de lectura y/o escritura a los recursos que se indican en el parámetro <code>scope</code>
   * @var code Es la concatenación con un espacio de separación (" ") de todos los ámbitos a
   * los que se solicita permiso. Estos pueden ser:
   * <ul>
   *   <li><b>public</b> Para acceder a la información publica de Centry como marcas, categorías, colores, tallas, etc.</li>
   *   <li><b>read_orders</b> Para leer información de pedidos</li>
   *   <li><b>write_orders</b> Para manipular o eliminar pedidos</li>
   *   <li><b>read_products</b>Para leer información de productos y variantes</li>
   *   <li><b>write_products</b>Para manipular o eliminar productos y variantes</li>
   *   <li><b>read_integration_config</b>Para leer información de configuraciones de integraciones</li>
   *   <li><b>write_integration_config</b>Para manipular o eliminar configuraciones de integraciones</li>
   *   <li><b>read_user</b>Para leer información de usuarios de la empresa</li>
   *   <li><b>write_user</b>Para manipular o eliminar usuarios de la empresa</li>
   *   <li><b>read_webhook</b>Para leer información de webhooks</li>
   *   <li><b>write_webhook</b>Para manipular o eliminar webhooks</li>
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
   *             estos métodos suelen estar asociados a la lectura, creación, edición y eliminación de recursos.
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
    $info = curl_getinfo($curl);

    curl_close($curl);
    return $this->response($response, $err, $info);
  }

  /**
   * Entrega la respuesta parseada como arreglo. Si se trata de un error,
   * entonces agrega los atributos <code>http_code</code> con el código
   * informado por el servidor y <code>curl_error</code> con toda la información
   * del error recogida por curl.
   * @param string $curl_exec_result
   * @param string $curl_error
   * @param array $curl_info
   * @return array
   */
  function response($curl_exec_result, $curl_error, $curl_info) {
    $http_code = $this->httpCode($curl_info);
    if ($http_code >= 200 && $http_code < 300) {
      return $this->parsedResponse($curl_exec_result);
    }

    $resp = $this->parsedResponse($curl_exec_result);
    $resp->http_code = $http_code;
    $resp->curl_error = $curl_error;
    return $resp;
  }

  /**
   * Obtiene el código de respuesta HTTP de una solicitud. Si no lo logra
   * obtener, retorna un cero.
   * @param array $curl_info Información de la solicitud.
   * @return int Código de respuesta HTTP.
   */
  function httpCode($curl_info) {
    try {
      return intval($curl_info["http_code"]);
    } catch (\Exception $e) {
      return 0;
    }
  }

  /**
   * Intenta transformar la respuesta suponiendo que se trata de un JSON. Si no
   * lo logra, retorna el mismo contenido que recibió el método pero como valor 
   * de la única llave de un arreglo cuyo nombre es <code>body</code>.
   * @param string $response Respuesta de Centry.
   * @return array Contenido de la respuesta.
   */
  function parsedResponse($response) {
    try {
      return json_decode($response);
    } catch (\Exception $e) {
      return ["body" => $response];
    }
  }

  /**
   * @param $endpoint Endpoint de Centry que se hará un POST.
   * @param array $params (opcional) Parámetros para el request.
   * @param array $payload (opcional) Body del request puede ser un objeto PHP o un arreglo (diccionario), internamente es transformado a JSON.
   * @return mixed|string
   */
  function post($endpoint, $params = array(), $payload = array()) {
    return $this->request($endpoint, "POST", $params, $payload);
  }

  /**
   * @param $endpoint Endpoint de Centry que se hará un GET.
   * @param array $params (opcional) Parámetros para el request.
   * @return mixed|string
   */
  function get($endpoint, $params = array()) {
    return $this->request($endpoint, "GET", $params, null);
  }

  /**
   * @param $endpoint Endpoint de Centry que se hará un UPDATE.
   * @param array $params (opcional) Parámetros para el request.
   * @param array $payload (opcional) Body del request puede ser un objeto PHP o un arreglo (diccionario), internamente es transformado a JSON.
   * @return mixed|string
   */
  function update($endpoint, $params = array(), $payload = array()) {
    return $this->request($endpoint, "PUT", $params, $payload);
  }

  /**
   * @param $endpoint Endpoint de Centry que se hará un DELETE.
   * @param array $params (opcional) Parámetros para el request.
   * @return mixed|string
   */
  function delete($endpoint, $params = array()) {
    return $this->request($endpoint, "DELETE", $params, null);
  }

  /**
   * Una vez que un usuario ha autorizado nuestra aplicación para que acceda a su información, Centry genera un código
   * de autorización con el cual podremos solicitar el primer access_token y refresh_token. Éste método se encarga de
   * esta tarea por lo que se le debe entregar el código de autorización como parámetro.
   * Se recomienda registrar estos tokens con algún mecanismo de persistencia como una base de datos.
   * @var code Código de autorización generado por Centry después de que el usuario autorizó la aplicación.
   * @see https://www.oauth.com/oauth2-servers/access-tokens/authorization-code-request/
   */
  function authorize($code) {
    return $this->__grant("authorization_code", array("code" => $code));
  }

  /**
   * Un access_token tiene una vigencia de 7200 segundos (2 horas) por lo que una vez cumplido ese plazo es necesario
   * solicitar un nuevo token usando como llave el refresh_token que teníamos registrado. Este método se encarga de hacer
   * esta renovación de tokens.
   * Se recomienda registrar estos nuevos tokens con algún mecanismo de persistencia como una base de datos.
   * @see https://www.oauth.com/oauth2-servers/access-tokens/authorization-code-request/
   */
  function refresh() {
    return $this->__grant("refresh_token", array("refresh_token" => $this->refreshToken));
  }

  function client_credentials($scope = null) {
    if ($scope == null or trim($scope) == "") {
      $scp = array();
    } else {
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
    $this->fillInstanceVariables($response);
    return $this;
  }

  private function fillInstanceVariables($response) {
    if (property_exists($response, 'access_token')) {
      $this->accessToken = $response->access_token;
    }
    if (property_exists($response, 'refresh_token')) {
      $this->refreshToken = $response->refresh_token;
    }
    if (property_exists($response, 'token_type')) {
      $this->tokenType = $response->token_type;
    }
    if (property_exists($response, 'scope')) {
      $this->scope = $response->scope;
    }
    if (property_exists($response, 'created_at')) {
      $this->createdAt = $response->created_at;
    }
    if (property_exists($response, 'expires_in')) {
      $this->expiresIn = $response->expires_in;
    }
  }

  // MÉTODOS PARA PRODUCTOS

  /**
   * Crea producto en Centry.
   * @param null $params
   * @param $payload
   * @return mixed|string
   */
  public function createProduct($params = null, $payload = array()) {
    return $this->post("conexion/v1/products.json", $params, $payload);
  }

  /**
   * Obtiene producto en Centry.
   * @param $product_id
   * @param null $params
   * @return mixed|string
   */
  public function getProduct($product_id, $params = null) {
    return $this->get("conexion/v1/products/" . $product_id . ".json", $params);
  }

  /**
   * Obtiene las imgenes asociadas a un producto en Centry.
   * @param $product_id
   * @param null $params
   * @return mixed|string
   */
  public function getProductImages($product_id, $params = null) {
    return $this->get("conexion/v1/products/" . $product_id . "/assets.json", $params);
  }

  /**
   * Obtiene las imgenes asociadas a una variante de un producto en Centry.
   * @param $product_id
   * @param $params variant_id
   * @return mixed|string
   */
  public function getProductVariantImages($product_id, $params = null) {
    if (isset($params['variant_id'])) {
      return $this->get("conexion/v1/products/" . $product_id . "/assets.json", $params);
    }

    return [];
  }

  /**
   * Actualiza producto en Centry.
   * @param $product_id
   * @param null $params
   * @param $payload
   * @return mixed|string
   */
  public function updateProduct($product_id, $params = null, $payload = array()) {
    return $this->update("conexion/v1/products/" . $product_id . ".json", $params, $payload);
  }

  /**
   * Elimina producto en Centry.
   * @param $product_id
   * @param null $params
   * @return mixed|string
   */
  public function deleteProduct($product_id, $params = null) {
    return $this->delete("conexion/v1/products/" . $product_id . ".json", $params);
  }

  /**
   * Entrega todos los productos de la cuenta en Centry.
   * @param null $params
   * @return mixed|string
   */
  public function listProducts($params = null) {
    return $this->get("conexion/v1/products.json", $params);
  }

  /**
   * Entrega el total de productos de la cuenta en Centry.
   * @param null $params
   * @return mixed|string
   */
  public function countProducts($params = null) {
    return $this->get("conexion/v1/products/count.json", $params);
  }

  //MÉTODOS PARA VARIANTES

  /**
   * Crea variante en Centry.
   * @param null $params
   * @param $payload
   * @return mixed|string
   */
  public function createVariant($params = null, $payload = array()) {
    return $this->post("conexion/v1/variants.json", $params, $payload);
  }

  /**
   * Obtiene variante en Centry.
   * @param $variant_id
   * @param null $params
   * @return mixed|string
   */
  public function getVariant($variant_id, $params = null) {
    return $this->get("conexion/v1/variants/" . $variant_id . ".json", $params);
  }

  /**
   * Actualiza variante por variant_id o por sku dentro del payload
   * @param null $variant_id
   * @param null $params
   * @param $payload
   * @return mixed|string
   */
  public function updateVariant($variant_id = null, $params = null, $payload = array()) {
    if (isset($variant_id)) {
      return $this->update("conexion/v1/variants/" . $variant_id . ".json", $params, $payload);
    } else {
      return $this->update("conexion/v1/variants/sku.json", $params, $payload);
    }
  }

  /**
   * Elimina variante en Centry.
   * @param $variant_id
   * @param null $params
   * @return mixed|string
   */
  public function deleteVariant($variant_id, $params = null) {
    return $this->delete("conexion/v1/variants/" . $variant_id . ".json", $params);
  }

  /**
   * Entrega todas las variantes de la cuenta en Centry.
   * @param null $params
   * @return mixed|string
   */
  public function listVariants($params = null) {
    return $this->get("conexion/v1/variants.json", $params);
  }

  // MÉTODOS PARA ÓRDENES

  /**
   * Crea orden en Centry.
   * @param null $params
   * @param $payload
   * @return mixed|string
   */
  public function createOrder($params = null, $payload = array()) {
    return $this->post("conexion/v1/orders.json", $params, $payload);
  }

  /**
   * Obtiene orden en Centry.
   * @param $order_id
   * @param null $params
   * @return mixed|string
   */
  public function getOrder($order_id, $params = null) {
    return $this->get("conexion/v1/orders/" . $order_id . ".json", $params);
  }

  /**
   * Actualiza orden en Centry.
   * @param $order_id
   * @param null $params
   * @param $payload
   * @return mixed|string
   */
  public function updateOrder($order_id, $params = null, $payload = array()) {
    return $this->update("conexion/v1/orders/" . $order_id . ".json", $params, $payload);
  }

  /**
   * Elimina orden en Centry.
   * @param $order_id
   * @param null $params
   * @return mixed|string
   */
  public function deleteOrder($order_id, $params = null) {
    return $this->delete("conexion/v1/orders/" . $order_id . ".json", $params);
  }

  /**
   * Entrega todas las ordenes de la cuenta en Centry.
   * @param null $params
   * @return mixed|string
   */
  public function listOrders($params = null) {
    return $this->get("conexion/v1/orders.json", $params);
  }

}
