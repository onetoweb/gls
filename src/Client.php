<?php

namespace Onetoweb\Gls;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\RequestOptions;
use Onetoweb\Gls\Exception\FileException;
use Onetoweb\Gls\Exception\InputException;

/**
 * Gls Api Client
 * 
 * @author Jonathan van 't Ende <jvantende@onetoweb.bnl>
 * @copyright Onetoweb B.V.
 */
class Client
{
    const BASE_URI = 'https://api.gls.nl';
    const API_VERSION = '1.0';
    
    /**
     * @var string
     */
    private $username;
    
    /**
     * @var string
     */
    private $password;
    
    /**
     * @var string
     */
    private $apiKey;
    
    /**
     * @var bool
     */
    private $testModus;
    
    /**
     * @var GuzzleClient
     */
    private $client;
    
    /**
     * @param string $username
     * @param string $password
     * @param string $apiKey
     * @param bool $testModus = false
     */
    public function __construct(string $username, string $password, string $apiKey, bool $testModus = false)
    {
        $this->username = $username;
        $this->password = $password;
        $this->apiKey = $apiKey;
        $this->testModus = $testModus;
        
        $this->client = new GuzzleClient([
            'base_uri' => self::BASE_URI,
            'http_errors' => false,
        ]);
        
    }
    
    /**
     * Send request
     *
     * @param string $method
     * @param string $endpoint
     * @param array $data = null (optional)
     *
     * @throws RequestException
     *
     * @return array
     */
    private function request(string $method, string $endpoint, array $data = [])
    {
        $options = [
            RequestOptions::HEADERS => [
                'Cache-Control' => 'no-cache',
                'Connection' => 'close',
                'Content-Type' => 'application/json',
                'Ocp-Apim-Subscription-Key' => $this->apiKey
            ],
        ];
        
        if ($method == 'POST') {
            
            $data['username'] = $this->username;
            $data['password'] = $this->password;
            
            $options[RequestOptions::JSON] = $data;
        }
        
        $this->getEndpoint($endpoint);
        
        try {
            $result = $this->client->request($method, $this->getEndpoint($endpoint), $options);
            $contents = $result->getBody()->getContents();
        } catch (Guzzle\Http\Exception\BadResponseException $e) {
            $contents = $e->getMessage();
        }
        
        return json_decode($contents, true);
    }
    
    /**
     * @param string $endpoint = ''
     * @return string
     */
    private function getEndpoint(string $endpoint = '')
    {
        $endpoint = ($this->testModus ? '/Test/V1/api' : '/V1/api') . $endpoint;
        $endpoint .= '?' . http_build_query([
            'api-version' => self::API_VERSION
        ]);
        
        return $endpoint;
    }
    
    /**
     * Send a POST request
     *
     * @param string $endpoint
     * @param array $data = []
     *
     * @return array
     */
    public function post(string $endpoint, array $data = [])
    {
        return $this->request('POST', $endpoint, $data);
    }
    
    /**
     * Send a HEAD request
     *
     * @param string $endpoint
     *
     * @return array
     */
    public function head(string $endpoint)
    {
        return $this->request('HEAD', $endpoint);
    }
    
    /**
     * Validate login
     *
     * @link https://api-portal.gls.nl/docs/services/gls-api-acceptatie/operations/ValidateLoginAuthentication
     *
     * @return array
     */
    public function validateLogin()
    {
        return $this->post('/Authentication/ValidateLogin');
    }
    
    /**
     * Confirm label
     * 
     * @link https://api-portal.gls.nl/docs/services/gls-api-acceptatie/operations/ConfirmLabelLabelControllerV1
     * 
     * @param array $data = []
     * 
     * @return array
     */
    public function confirmLabel(array $data = [])
    {
        return $this->post('/Label/Confirm', $data);
    }
    
    /**
     * Create label
     * 
     * @link https://api-portal.gls.nl/docs/services/gls-api-acceptatie/operations/CreateLabelLabelControllerV1
     * 
     * @param array $data = []
     * 
     * @return array
     */
    public function createLabel(array $data = [])
    {
        return $this->post('/Label/Create', $data);
    }
    
    /**
     * Save label
     * 
     * @param string $filename
     * @param array $unit
     * 
     * @throws InputException if the unit parameter does not contain label data
     * @throws FileException if the file directory is not writable 
     * @throws FileException label could not be saved in the file
     */
    public function saveLabel(string $filename, array $unit)
    {
        if (!isset($unit['label']) and !isset($unit['labelShopReturn'])) {
            throw new InputException("unit does not contain label data");
        }
        
        if (isset($unit['label'])) {
            $label = $unit['label'];
        } else {
            $label = $unit['labelShopReturn'];
        }
        
        if (!is_writable(dirname($filename))) {
            throw new FileException("file: $filename is not writable");
        }
        
        if (false === file_put_contents($filename, base64_decode($label))) {
            throw new FileException("label could not be saved in file: $filename");
        }
    }
    
    /**
     * Delete label
     * 
     * @link https://api-portal.gls.nl/docs/services/gls-api-acceptatie/operations/DeleteLabelLabelControllerV1
     * 
     * @param array $data = []
     * 
     * @return array
     */
    public function deleteLabel(array $data = [])
    {
        return $this->post('/Label/Delete', $data);
    }
    
    /**
     * Get delivery options
     * 
     * @link https://api-portal.gls.nl/docs/services/gls-api-acceptatie/operations/GetDeliveryOptionsDeliveryOptions
     * 
     * @param array $data = []
     * 
     * @return array
     */
    public function getDeliveryOptions(array $data = [])
    {
        return $this->post('/DeliveryOptions/GetDeliveryOptions', $data);
    }
    
    /**
     * Get parcel shops
     * 
     * @link https://api-portal.gls.nl/docs/services/gls-api-acceptatie/operations/GetParcelShopsParcelShop
     * 
     * @param array $data = []
     * 
     * @return array
     */
    public function getParcelShops(array $data = [])
    {
        return $this->post('/ParcelShop/GetParcelShops', $data);
    }
    
    /**
     * Create pickup
     * 
     * @link https://api-portal.gls.nl/docs/services/gls-api-acceptatie/operations/CreatePickup
     * 
     * @param array $data = []
     * 
     * @return array
     */
    public function createPickup(array $data = [])
    {
        return $this->post('/Pickup/Create', $data);
    }
    
    /**
     * Delete pickup
     *
     * @link https://api-portal.gls.nl/docs/services/gls-api-acceptatie/operations/DeletePickup
     *
     * @param array $data = []
     *
     * @return array
     */
    public function deletePickup(array $data = [])
    {
        return $this->post('/Pickup/Delete', $data);
    }
    
    /**
     * Create shop return
     *
     * @link https://api-portal.gls.nl/docs/services/gls-api-acceptatie/operations/CreateLabelShopReturn
     *
     * @param array $data = []
     *
     * @return array
     */
    public function createShopReturn(array $data = [])
    {
        return $this->post('/ShopReturn/Create', $data);
    }
    
    /**
     * Health probe
     * 
     * @link https://api-portal.gls.nl/docs/services/gls-api-acceptatie/operations/healthprobe
     * 
     * @return null
     */
    public function healthProbe()
    {
        return $this->head('/Monitor/HealthProbe');
    }
}
