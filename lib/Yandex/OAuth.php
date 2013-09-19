<?

namespace Yandex;

class OAuth {
    protected $api_url;
    protected $api_port;
    protected $api_data;

    public function __construct($api_url, $api_port, $api_app, $api_secret) {
        $this->api_url = $api_url;
        $this->api_port = $api_port;
        $this->api_data = array(
            'grant_type' => 'authorization_code',
            'client_id' => $api_app,
            'client_secret' => $api_secret,
        );
    }

    public function GetAuthToken(array $params = array()) {
        $https = curl_init();
        if (!$https)
            throw new \Exception('cURL initialization failed');
        if (!curl_setopt($https, CURLOPT_URL, $this->api_url . '/token'))
            throw new \Exception('cURL configuration (CURLOPT_URL) failed');
        if (!curl_setopt($https, CURLOPT_PORT, $this->api_port))
            throw new \Exception('cURL configuration (CURLOPT_PORT) failed');
        if (!curl_setopt($https, CURLOPT_RETURNTRANSFER, true))
            throw new \Exception('cURL configuration (CURLOPT_RETURNTRANSFER) failed');
        if (!curl_setopt($https, CURLOPT_FOLLOWLOCATION, true))
            throw new \Exception('cURL configuration (CURLOPT_FOLLOWLOCATION) failed');
        if (!curl_setopt($https, CURLOPT_POST, true))
            throw new \Exception('cURL configuration (CURLOPT_POST) failed');
        if (!curl_setopt($https, CURLOPT_SSL_VERIFYPEER, false))
            throw new \Exception('cURL configuration (CURLOPT_SSL_VERIFYPEER) failed');
        if (!curl_setopt($https, CURLOPT_MAXREDIRS, 5))
            throw new \Exception('cURL configuration (CURLOPT_MAXREDIRS) failed');
        if (!curl_setopt($https, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded')))
            throw new \Exception('cURL configuration (CURLOPT_HTTPHEADER) failed');

        if (array_key_exists('state', $params))
            unset($params['state']);
        $post_data = array_merge($this->api_data, $params);
        if (!curl_setopt($https, CURLOPT_POSTFIELDS, http_build_query($post_data)))
            throw new \Exception('cURL configuration (CURLOPT_POSTFIELDS) failed');

        $http_data = curl_exec($https);
        $data = json_decode($http_data, true);
        if (empty($data))
            throw new \Exception($http_data);
        if (!empty($data['access_token']))
            return $data['access_token'];
        if (!empty($data['error']))
            throw new \Exception($data['error']);
    }

    public function GetAuthURL() {
        return $this->api_url . '/authorize?response_type=code&client_id=' . $this->api_data['client_id'];
    }
}
