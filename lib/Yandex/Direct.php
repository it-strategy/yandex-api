<?

namespace Yandex;

class Direct {
    protected $https;
    protected $api_data;
    protected $api_path;

    public function __construct($api_url, $api_port, $api_app, $api_token) {
        $https = curl_init();
        if (!$https)
            throw new \Exception('cURL initialization failed');
        if (!curl_setopt($https, CURLOPT_URL, $api_url))
            throw new \Exception('cURL configuration (CURLOPT_URL) failed');
        if (!curl_setopt($https, CURLOPT_PORT, $api_port))
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
        if (!curl_setopt($https, CURLOPT_HTTPHEADER, array('Content-Type: application/json')))
            throw new \Exception('cURL configuration (CURLOPT_HTTPHEADER) failed');
        $this->https = $https;
        $this->api_data = array(
            'application_id' => $api_app,
            'token' => $api_token,
            'locale' => 'ru',
        );
        $this->response('PingAPI');
    }

    protected function response($method, $params = '') {
        $post_data = array_merge($this->api_data, array(
            'method' => $method,
        ));
        if (!empty($params))
            $post_data['param'] = $params;
        $post_data = json_encode($post_data);
        if (!curl_setopt($this->https, CURLOPT_POSTFIELDS, $post_data))
            throw new \Exception('cURL configuration (CURLOPT_POSTFIELDS) failed');
        $data = json_decode(curl_exec($this->https), true);
        if (!empty($data['data']))
            return $data['data'];
        if (!empty($data['error_code']))
            throw new \Exception($data['error_str'] . ' (' . $data['error_detail'] . ') ' . $post_data, $data['error_code']);
        if (empty($data['data']))
            return array();
        throw new \Exception($post_data . '=>' . json_encode($data));
    }

    public function GetCampaignsList($clients = '') {
        $campaigns = array();
        if (empty($clients))
            $campaigns = array_merge($campaigns, $this->response('GetCampaignsList', ''));
        else {
            if (!is_array($clients)) {
                $clients = array($clients);
            }
            // Yandex API limits amount of logins by 100
            foreach (array_chunk($clients, 100) as $c) {
                $campaigns = array_merge($campaigns, $this->response('GetCampaignsList', $c));
            }
        }
        return $campaigns;
    }

    public function GetClientsList($params = '') {
        return $this->response('GetClientsList', $params);
    }

    public function GetClientInfo($clients = '') {
        $infos = array();
        if (empty($clients))
            $infos = array_merge($infos, $this->response('GetClientInfo', ''));
        else {
            if (!is_array($clients)) {
                $clients = array($clients);
            }
            // Yandex API limits amount of logins by 1000
            foreach (array_chunk($clients, 1000) as $c) {
                $infos = array_merge($infos, $this->response('GetClientInfo', $c));
            }
        }
        return $infos;
    }

    public function GetBanners($campaigns) {
        if (!is_array($campaigns))
            $campaigns = array($campaigns,);
        $banners = array();
        // Yandex API limits amount of campaigns by 10
        foreach (array_chunk($campaigns, 10) as $c) {
            $banners = array_merge($banners, $this->response('GetBanners', array(
                'CampaignIDS' => $c,
                'GetPhrases' => 'WithPrices',
            )));
        }
        return $banners;
    }

    public function UpdatePrices($prices) {
        foreach (array_chunk($prices, 1000) as $p) {
            $this->response('UpdatePrices', $p);
        }
        return true;
    }
}
