<?php

namespace Kagatan\PrivatbankAutoClient;

/**
 * Autoclient PrivatBank low-level API implementation
 *
 * @description Class for working with account statement PrivatBank (via autoclient)
 * @category PrivatBank
 * @version 1.0
 * @author Stupakov Maxim <info@wi-fi-point.com>
 *
 * PrivatBank API:
 * https://docs.google.com/document/d/e/2PACX-1vTion-fu1RzMCQgZXOYKKWAmvi-QAAxZ7AKnAZESGY5lF2j3nX61RBsa5kXzpu7t5gacl6TgztonrIE/pub
 * https://docs.google.com/document/d/e/2PACX-1vTtKvGa3P4E-lDqLg3bHRF6Wi9S7GIjSMFEFxII5qQZBGxuTXs25hQNiUU1hMZQhOyx6BNvIZ1bVKSr/pub
 *
 */
class ClientAPI
{
    private $id;
    private $token;
    protected $url = 'https://acp.privatbank.ua/api/proxy';

    function __construct($id = '', $token = '')
    {
        $this->id = $id;
        $this->token = $token;
    }


    /**
     * Get previous transactions
     *
     * @param bool $acc - bank account
     * @param bool $startDate - time ts
     * @param bool $endDate - time ts
     * @return mixed
     */
    public function getPreviousTransactions($acc = false, $startDate = false, $endDate = false)
    {
        $path = '/transactions';

        $data = array();

        // Bank account
        if ($acc) {
            $data["acc"] = $acc;
        }

        if ($startDate) {
            $data["startDate"] = $this->getDateForPrivat($startDate);
        } else {
            // previous 3 day
            $startDate = time() - 3600 * 24 * 3;
            $data["startDate"] = $this->getDateForPrivat($startDate);
        }

        if ($endDate) {
            $data["endDate"] = $this->getDateForPrivat($endDate);
        } else {
            // now
            $endDate = time();
            $data["endDate"] = $this->getDateForPrivat($endDate);
        }

        $response = $this->sendRequest($this->url . $path, $data, array(), "GET", true);

        if (isset($response['StatementsResponse']['statements'])) {
            return $response['StatementsResponse']['statements'];
        } else {
            return $response;
        }
    }


    /**
     * Get lastday transactions
     *
     * @param bool $acc - bank account
     * @return mixed
     */
    public function getLastdayTransactions($acc = false)
    {
        $path = '/transactions/lastday';

        $data = array();

        // Bank account
        if ($acc) {
            $data["acc"] = $acc;
        }

        $response = $this->sendRequest($this->url . $path, $data, array(), "GET", true);

        if (isset($response['StatementsResponse']['statements'])) {
            return $response['StatementsResponse']['statements'];
        } else {
            return $response;
        }
    }


    /**
     * Get today transactions
     *
     * @param bool $acc - bank account
     * @return mixed
     */
    public function getTodayTransactions($acc = false)
    {
        $path = '/transactions/today';

        $data = array();

        // Bank account
        if ($acc) {
            $data["acc"] = $acc;
        }

        $response = $this->sendRequest($this->url . $path, $data, array(), "GET", true);

        if (isset($response['StatementsResponse']['statements'])) {
            return $response['StatementsResponse']['statements'];
        } else {
            return $response;
        }
    }


    /**
     * Format ts date to ДД-ММ-ГГГГ
     *
     * @param $ts
     * @return false|string
     */
    public function getDateForPrivat($ts)
    {
        return date('d-m-Y', $ts);
    }


    /**
     * Send request via CURL
     *
     * @param $url
     * @param $data
     * @param array $paramCURL
     * @param string $method
     * @param bool $json_decode
     * @return mixed
     */
    private function sendRequest($url, $data, $paramCURL = array(), $method = "POST", $json_decode = false)
    {
        $param = array(
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 45,
            CURLOPT_VERBOSE        => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER     => array(
                'User-Agent: WiFi Point parser',
                'id: ' . $this->id,
                'token: ' . $this->token,
                'Content-Type: application/json;charset=utf8'
            )
        );

        $data = http_build_query($data, '', '&');
        if ($method == "POST") {
            $param[CURLOPT_POST] = true;
            $param[CURLOPT_POSTFIELDS] = $data;
        } else {
            $param[CURLOPT_URL] = $url . "?" . $data;
        }

        $options = $param + $paramCURL;

        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        curl_close($ch);

        if ($json_decode) {
            return json_decode($result, true);
        } else {
            return $result;
        }
    }
}
