<?php
/**
 * Class to get domain data from Amazon's Alexa Web Information Sevice (AWIS).
 *
 * @since   0.1.1
 *
 * @package    News Netrics
 * @subpackage news-netrics/includes/api/
 */

/**
 * Make request to AWIS, return data customized for News Netrics.
 *
 * Adapted from Code Samples for AWIS: PHP
 * @see https://aws.amazon.com/awis/getting-started/
 */
class UrlInfo {
    protected static $ActionName        = 'UrlInfo';
    protected static $ResponseGroupName = 'Rank,RankByCountry,LinksInCount,Speed,SiteData,ContactInfo';
    protected static $ServiceHost       = 'awis.amazonaws.com';
    protected static $ServiceEndpoint   = 'awis.us-west-1.amazonaws.com';
    protected static $NumReturn         = 10;
    protected static $StartNum          = 1;
    protected static $SigVersion        = '2';
    protected static $HashAlgorithm     = 'HmacSHA256';
    protected static $ServiceURI        = "/api";
    protected static $ServiceRegion     = "us-west-1";
    protected static $ServiceName       = "awis";
    public function UrlInfo($accessKeyId, $secretAccessKey, $site) {
        $this->accessKeyId = $accessKeyId;
        $this->secretAccessKey = $secretAccessKey;
        $this->site = $site;
        $now = time();
        $this->amzDate = gmdate("Ymd\THis\Z", $now);
        $this->dateStamp = gmdate("Ymd", $now);
    }
    /**
     * Get site info from AWIS.
     */
    public function getUrlInfo() {
        $canonicalQuery = $this->buildQueryParams();
        $canonicalHeaders =  $this->buildHeaders(true);
        $signedHeaders = $this->buildHeaders(false);
        $payloadHash = hash('sha256', "");
        $canonicalRequest = "GET" . "\n" . self::$ServiceURI . "\n" . $canonicalQuery . "\n" . $canonicalHeaders . "\n" . $signedHeaders . "\n" . $payloadHash;
        $algorithm = "AWS4-HMAC-SHA256";
        $credentialScope = $this->dateStamp . "/" . self::$ServiceRegion . "/" . self::$ServiceName . "/" . "aws4_request";
        $stringToSign = $algorithm . "\n" .  $this->amzDate . "\n" .  $credentialScope . "\n" .  hash('sha256', $canonicalRequest);
        $signingKey = $this->getSignatureKey();
        $signature = hash_hmac('sha256', $stringToSign, $signingKey);
        $authorizationHeader = $algorithm . ' ' . 'Credential=' . $this->accessKeyId . '/' . $credentialScope . ', ' .  'SignedHeaders=' . $signedHeaders . ', ' . 'Signature=' . $signature;
        $url = 'https://' . self::$ServiceHost . self::$ServiceURI . '?' . $canonicalQuery;
        $ret = self::makeRequest($url, $authorizationHeader);
        // echo "\nResults for " . $this->site .":\n\n";
        // echo $ret;
        $nn_data = self::parseResponse($ret);
        return $nn_data;
    }
    protected function sign($key, $msg) {
        return hash_hmac('sha256', $msg, $key, true);
    }
    protected function getSignatureKey() {
        $kSecret = 'AWS4' . $this->secretAccessKey;
        $kDate = $this->sign($kSecret, $this->dateStamp);
        $kRegion = $this->sign($kDate, self::$ServiceRegion);
        $kService = $this->sign($kRegion, self::$ServiceName);
        $kSigning = $this->sign($kService, 'aws4_request');
        return $kSigning;
    }
    /**
     * Builds headers for the request to AWIS.
     * @return String headers for the request
     */
    protected function buildHeaders($list) {
        $params = array(
            'host'            => self::$ServiceEndpoint,
            'x-amz-date'      => $this->amzDate
        );
        ksort($params);
        $keyvalue = array();
        foreach($params as $k => $v) {
            if ($list)
              $keyvalue[] = $k . ':' . $v;
            else {
              $keyvalue[] = $k;
            }
        }
        return ($list) ? implode("\n",$keyvalue) . "\n" : implode(';',$keyvalue) ;
    }
    /**
     * Builds query parameters for the request to AWIS.
     * Parameter names will be in alphabetical order and
     * parameter values will be urlencoded per RFC 3986.
     * @return String query parameters for the request
     */
    protected function buildQueryParams() {
        $params = array(
            'Action'            => self::$ActionName,
            'Count'             => self::$NumReturn,
            'ResponseGroup'     => self::$ResponseGroupName,
            'Start'             => self::$StartNum,
            'Url'               => $this->site
        );
        ksort($params);
        $keyvalue = array();
        foreach($params as $k => $v) {
            $keyvalue[] = $k . '=' . rawurlencode($v);
        }
        return implode('&',$keyvalue);
    }
    /**
     * Makes request to AWIS
     * @param String $url   URL to make request to
     * @param String authorizationHeader  Authorization string
     * @return String       Result of request
     */
    protected function makeRequest($url, $authorizationHeader) {
        // echo "\nMaking request to:\n$url\n";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 4);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          'Accept: application/xml',
          'Content-Type: application/xml',
          'X-Amz-Date: ' . $this->amzDate,
          'Authorization: ' . $authorizationHeader
        ));
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    /**
     * Parses XML response from AWIS and displays selected data
     * @param String $response    xml response from AWIS
     */
    public static function parseResponse($response) {
        $xml = new SimpleXMLElement($response,LIBXML_ERR_ERROR,false,'http://awis.amazonaws.com/doc/2005-07-11');
        if($xml->count() && $xml->Response->UrlInfoResult->Alexa->count()) {
            $info = $xml->Response->UrlInfoResult->Alexa;
            $nn_alexa_arr = array(
                'rank'     => $info->TrafficData->Rank,
                // 'rank_us'  => $info->TrafficData->RankByCountry->Country[0]->Rank,
                'title'    => $info->ContentData->SiteData->Title,
                'desc'     => $info->ContentData->SiteData->Description,
                'since'    => $info->ContentData->SiteData->OnlineSince,
                'links'    => $info->ContentData->LinksInCount,
                'speed'    => $info->ContentData->Speed->MedianLoadTime,
                'speed_pc' => $info->ContentData->Speed->Percentile,
                'date'     => date( 'Y-m' ),
                'error'    => 0,
            );
        }
        $nn_alexa = array();
        foreach($nn_alexa_arr as $key => $value) {
            $nn_alexa[$key] = (string) $value;
        }
        return $nn_alexa;
    }
}
/*
Array
(
    [rank] => 74977
    [title] => Albuquerque Journal - ABQJournal
    [desc] => Provides New Mexico news and sports.
    [since] => 03-Feb-1996
    [links] => 2273
    [speed] => 2867
    [speed_pc] => 26
    [date] => 201906
)
// Commented out by BG 2019-02
if (count($argv) < 4) {
    // echo "Usage: $argv[0] ACCESS_KEY_ID SECRET_ACCESS_KEY site $site\n";
    // exit(-1);
}
else {
    // $accessKeyId = $argv[1];
    // $secretAccessKey = $argv[2];
    // $site = $argv[3];
}
*/
