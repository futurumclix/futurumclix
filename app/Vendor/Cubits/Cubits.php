<?php

class Cubits
{

    private $_rpc;
    private $_authentication;

    public static function configure($api_base,$ssl_verify)
    {
      define("CUBITS_API_BASE",  $api_base);
      define("CUBITS_SSL_VERIFY",  $ssl_verify);
    }

    public static function withApiKey($key, $secret)
    {
        return new Cubits(new Cubits_ApiKeyAuthentication($key, $secret));
    }

    // This constructor is deprecated.
    public function __construct($authentication, $tokens=null, $apiKeySecret=null)
    {
        // First off, check for a legit authentication class type
        if (is_a($authentication, 'Cubits_Authentication')) {
            $this->_authentication = $authentication;
        } else {
            // Here, $authentication was not a valid authentication object, so
            // analyze the constructor parameters and return the correct object.
            // This should be considered deprecated, but it's here for backward compatibility.
            // In older versions of this library, the first parameter of this constructor
            // can be either an API key string or an OAuth object.
            if ($authentication !== null && is_string($authentication)) {
                $apiKey = $authentication;

                $this->_authentication = new Cubits_ApiKeyAuthentication($apiKey, $apiKeySecret);

            } else {
                throw new Cubits_ApiException('Could not determine API authentication scheme');
            }
        }

        $this->_rpc = new Cubits_Rpc(new Cubits_Requestor(), $this->_authentication);
    }

    public function get($path, $params=array())
    {
        return $this->_rpc->request("GET", $path, $params);
    }

    public function post($path, $params=array())
    {
        return $this->_rpc->request("POST", $path, $params);
    }

    public function delete($path, $params=array())
    {
        return $this->_rpc->request("DELETE", $path, $params);
    }

    public function put($path, $params=array())
    {
        return $this->_rpc->request("PUT", $path, $params);
    }

    public function postTest($params)
    {
       $response = json_decode($this->post("test", $params));
       $returnValue = new stdClass();
       $returnValue->status = $response->status;
       return $returnValue;
    }
    public function getTest()
    {
       $response = json_decode($this->get("test"));
       $returnValue = new stdClass();
       $returnValue->status = $response->status;
       return $returnValue;

    }
    /* post invoices */
    /*
    @params $name       string(256)     (optional) Name of the item displayed to the customer
    @params $price      string(16)      Price of the invoice that the merchant wants to receive, as a decimal floating point number, converted to string (e.g. "123.05")
    @params $currency   string(3)       ISO 4217 code of the currency that the merchant wants to receive (e.g. "EUR")
    @params $options    array
        @params $share_to_keep_in_btc   number          (optional) Percentage of the invoice amount to be kept in BTC, as an integer number from 0 to 100. If not specified, a default value is used from the Cubits Pay / Payouts / Percentage Kept in BTC
        @params $description            string(512)     (optional) Description of the item displayed to the customer
        @params $reference              string(512)     (optional) Individual free-text field stored in the invoice as-is
        @params $callback_url           string(512)     (optional) URL that is called on invoice status updates
        @params $success_url            string(512)     (optional) URL to redirect the customer to after a successful
    */
    public function createInvoice($name, $price, $currency, $options=array())
    {
        $microseconds = sprintf('%0.0f',round(microtime(true) * 1000000));
        $params = array(
            "name" => $name,
            "price" => number_format($price, 8, '.', ''),
            "currency" => $currency
        );

        foreach($options as $option => $value) {
            $params[$option] = $value;
        }
        return $this->createInvoiceWithOptions($params);
    }

    public function createInvoiceWithOptions($options=array())
    {

        $response = json_decode($this->post("invoices", $options));

        $returnValue = new stdClass();
        $returnValue->embedHtml = "<div class=\"cubits-button\" data-code=\"55555\" style=\"background: yellow;padding: 10px 25px;float:left\">Pay with bitcoin</div>";
        $returnValue->id = $response->id;
        $returnValue->invoice_url = $response->invoice_url;
        $returnValue->address = $response->address;
        $returnValue->valid_until_time = $response->valid_until_time;

        return $returnValue;

    }
    /* get invoice */
    /*
      @params $channelId             string        Unique identifier of the invoice
    */
    public function getInvoice($invoice_id)
    {
        $microseconds = sprintf('%0.0f',round(microtime(true) * 1000000));

        $invoice_url = "invoices/" . $invoice_id;

        $response = json_decode($this->get($invoice_url));

        $returnValue = new stdClass();        
        $returnValue->id = $response->id;
        $returnValue->status = $response->status;

        $returnValue->address = $response->address;

        $returnValue->merchant_currency = $response->merchant_currency;
        $returnValue->merchant_amount = $response->merchant_amount;

        $returnValue->invoice_currency = $response->invoice_currency;
        $returnValue->invoice_amount = $response->invoice_amount;
        $returnValue->invoice_url = $response->invoice_url;

        $returnValue->paid_currency = $response->paid_currency;
        $returnValue->paid_amount = $response->paid_amount;

        $returnValue->name = $response->name;
        $returnValue->description = $response->description;
        $returnValue->reference = $response->reference;

        $returnValue->callback_url = $response->callback_url;
        $returnValue->success_url = $response->success_url;
        $returnValue->cancel_url = $response->cancel_url;

        $returnValue->notify_email = $response->notify_email;

        return $returnValue;

    }

    /* post send_money */
    /*
        @params $address       string(64)     Bitcoin address the amount is to be sent to
        @params $amount        string(32)     Amount in BTC to be sent, decimal number as a string (e.g. "0.12500000")
    */

    public function sendMoney($address, $amount)
    {
        $params = array(
            "amount" => number_format($amount, 8, '.', ''),
            "address" => $address
        );
        $response = json_decode($this->post("send_money", $params));

        $returnValue = new stdClass();
        $returnValue->tx_ref_code = $response->tx_ref_code;
        return $returnValue;
    }

    /* get accounts */
    public function listAccounts()
    {
        $response = json_decode($this->get("accounts"));
        $returnValue = new stdClass();
        $returnValue->accounts = $response->accounts;
        return $returnValue;
    }

    /* post quote
        @params $operation              string(256)     Type of the transaction: ￼buy or sell
        @params $sender_currency        string(3)       ISO 4217 code of the currency that you want to spend (e.g. "EUR")
        @params $sender_amount          string(16)      Price of the invoice that the merchant wants to receive, as a decimal floating point number, converted to string (e.g. "123.05")
        @params $receiver_currency      string(3)       ISO 4217 code of the currency that you want to spend (e.g. "EUR")
        @params $receiver_amount        string(16)      Price of the invoice that the merchant wants to receive, as a decimal floating point number, converted to string (e.g. "123.05")

        Required Attributes
        Exactly one amount, either sender.amount or receiver.amount must be specified.
    */

    public function requestQuote($operation, $sender_currency,$sender_amount, $receiver_currency, $receiver_amount)
    {
        $sender = array(
          'currency' => $sender_currency,
          'amount' => $sender_amount
        );
        $receiver = array(
          'currency' => $receiver_currency,
          'amount' => $receiver_amount
        );
        $params = array(
            "operation" => $operation,
            "sender" => $sender,
            "receiver" => $receiver
        );

        return $this->requestQuoteWithParams($params);
    }

    public function requestQuoteWithParams($params)
    {
        $response = json_decode($this->post("quotes", $params));

        $returnValue = new stdClass();
        $returnValue->operation = $response->operation;
        $returnValue->sender = array(
            'currency' => $response->sender->currency,
            'amount' => $response->sender->amount
        );

        $returnValue->receiver = array(
            'currency' => $response->receiver->currency,
            'amount' => $response->receiver->amount
        );
        return $returnValue;
    }
    /* post buy
        @params $sender_currency       string(3)     ISO 4217 code of the currency that you want to spend (e.g. "EUR")
        @params $sender_amount         string(32)     Amount in specified currency to be spent, decimal number as a string (e.g. "12.50")
    */
    public function buy($sender_currency, $sender_amount)
    {
        $sender = array(
            "currency" => $sender_currency,
            "amount" => number_format($sender_amount, 8, '.', '')
        );
        $params = array(
            "sender" => $sender
        );
        $response = json_decode($this->post("buy", $params));

        $returnValue = new stdClass();
        $returnValue->tx_ref_code = $response->tx_ref_code;
        return $returnValue;
    }

    /* post sell */
    /*
        @params $sender_amount         string(32)     Amount in specified currency to be spent, decimal number as a string (e.g. "12.50")
        @params $receiver_currency     string(3)     ISO 4217 code of the currency that you want to spend (e.g. "EUR")
    */
    public function sell($sender_amount, $receiver_currency)
    {
        $sender = array(
            "amount" => number_format($sender_amount, 8, '.', '')
        );
        $receiver = array(
            "currency" => $receiver_currency
        );
        $params = array(
            "sender" => $sender,
            "receiver" => $receiver
        );
        $response = json_decode($this->post("sell", $params));

        $returnValue = new stdClass();
        $returnValue->tx_ref_code = $response->tx_ref_code;
        return $returnValue;
    }

    /* get channel */
    /*
      @params $channelId             string        Unique identifier of the channel
    */
    public function getChannel($channelId)
    {
        $url = "channels/" . $channelId;

        $response = json_decode($this->get($url));

        $returnValue = new stdClass();
        $returnValue->id = $response->id;
        $returnValue->address = $response->address;
        $returnValue->receiver_currency = $response->receiver_currency;
        $returnValue->name = $response->name;
        $returnValue->description = $response->description;
        $returnValue->reference = $response->reference;
        $returnValue->channel_url = $response->channel_url;
        $returnValue->callback_url = $response->callback_url;
        $returnValue->success_url = $response->success_url;
        $returnValue->created_at = $response->created_at;
        $returnValue->updated_at = $response->updated_at;
        $returnValue->transactions = isset($response->transactions) ? $response->transactions : array();
        $returnValue->txs_callback_url = $response->txs_callback_url;

        return $returnValue;
    }

    /* create channel */
    /*
        @params $receiver_currency     string(3)     ISO 4217 code of the currency that you want to spend (e.g. "EUR")
        @params $name                  string(256)   (optional) Name of the channel, displayed to the customer on the payment screen
        @params $description           string(512)   (optional) Description of the item displayed to the customer on the payment screen
        @params $reference             string(512)   (optional) Individual free-text field stored in the channel as-is
        @params $callback_url          string(512)   (optional) URL that is called on channel status updates
        @params $success_url           string(512)   (optional) URL to redirect the user to after a successful payment
    */

    public function createChannel($receiver_currency, $name=null,$description=null, $reference=null, $callback_url=null, $success_url=null,$txs_callback_url=null )
    {
        $params = array(
            "receiver_currency" => $receiver_currency,
            "name" => $name,
            "description" => $description,
            "reference" => $reference,
            "callback_url" => $callback_url,
            "success_url" => $success_url,
            "txs_callback_url" => $txs_callback_url
        );

        $response = json_decode($this->post("channels", $params));

        $returnValue = new stdClass();

        $returnValue->id = $response->id;
        $returnValue->address = $response->address;
        $returnValue->receiver_currency = $response->receiver_currency;
        $returnValue->name = $response->name;
        $returnValue->description = $response->description;
        $returnValue->reference = $response->reference;
        $returnValue->channel_url = $response->channel_url;
        $returnValue->callback_url = $response->callback_url;
        $returnValue->success_url = $response->success_url;
        $returnValue->created_at = $response->created_at;
        $returnValue->updated_at = $response->updated_at;
        $returnValue->transactions = isset($response->transactions) ? $response->transactions : array();
        $returnValue->txs_callback_url = $response->txs_callback_url;
        return $returnValue;
    }

    /* update channel */
    /*
        @params $channelId             string        Unique identifier of the channel
        @params $receiver_currency     string(3)     ISO 4217 code of the currency that you want to spend (e.g. "EUR")
        @params $name                  string(256)   (optional) Name of the channel, displayed to the customer on the payment screen
        @params $description           string(512)   (optional) Description of the item displayed to the customer on the payment screen
        @params $reference             string(512)   (optional) Individual free-text field stored in the channel as-is
        @params $callback_url          string(512)   (optional) URL that is called on channel status updates
        @params $success_url           string(512)   (optional) URL to redirect the user to after a successful payment
        @params $tx_callback_url       string(512)   (optional) URL that is called on channel transaction status updates
    */
    public function updateChannel($channelId, $receiver_currency, $name=null,$description=null, $reference=null, $callback_url=null, $success_url=null,$tx_callback_url=null  )
    {
        $url = "channels/" . $channelId;
        $params = array(
            "receiver_currency" => $receiver_currency,
            "name" => $name,
            "description" => $description,
            "reference" => $reference,
            "callback_url" => $callback_url,
            "success_url" => $success_url,
            "tx_callback_url" => $tx_callback_url
        );

        $response = json_decode($this->post($url, $params));

        $returnValue = new stdClass();

        $returnValue->id = $response->id;
        $returnValue->address = $response->address;
        $returnValue->receiver_currency = $response->receiver_currency;
        $returnValue->name = $response->name;
        $returnValue->description = $response->description;
        $returnValue->reference = $response->reference;
        $returnValue->channel_url = $response->channel_url;
        $returnValue->callback_url = $response->callback_url;
        $returnValue->success_url = $response->success_url;
        $returnValue->created_at = $response->created_at;
        $returnValue->updated_at = $response->updated_at;
        $returnValue->transactions = isset($response->transactions) ? $response->transactions : array();
        $returnValue->txs_callback_url = $response->txs_callback_url;
        return $returnValue;
    }

}

?>
