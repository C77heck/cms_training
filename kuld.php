<?php

/**
 * Plugin Name: GLS Fuvarlevél
 * Description: Próbanap feladata.
 * Version: 1.0.0
 * Author: TODO:
 */


class InvoiceManagement
{

    private $order_num = 100022087;

    private $password = 'PiciPamut01';

    private $username = 'molnarkristof0@gmail.com';

    private $api = "https://api.test.mygls.hu/";

    public function __construct()
    {
        add_action('woocommerce_order_status_completed', [$this, 'order_submitted'], 10, 2);
    }

    public function order_submitted($order_id, $order)
    {
        if ($order->get_shipping_method() != 'GLS') {
            exit;
        }
        $this->createInvoice($order_id, $order);
    }


    public function createInvoice($order_id, $order)
    {

        $password = unpack('C*', hash('sha512', $this->password, true));

        $password = array_values($password);

        $this->order_id = $order_id;
        $address = $order->get_address();

        preg_match("/(?P<street>([a-zA-Z]*) ([a-zA-Z.]*))/", "Merges ut 5. 3/b", $street);
        preg_match("/(?P<number>(\d[.]+))/", $address["address_1"], $number);
        preg_match("/(?P<name>(\d[\/]\w+))/", $address["address_1"], $name);


        $data = $order->get_data();


        $parcel = [
            "ClientNumber" => "",
            "ClientReference" => "TEST",
            "CODAmount" => 0, 
            "CODReference" => "COD REFERENCE",
            "Content" => "CONTENT",
            "Count" => 1,
            "DeliveryAddress" => [
                "City" => "$address[city]",
                "ContactEmail" => "$address[email]",
                "ContactName" => "$address[first_name] $address[first_name]",
                "ContactPhone" => "$address[phone]",
                "CountryIsoCode" => "$address[country]",
                "HouseNumber" => "2",
                "Name" => "$address[last_name] $address[first_name]",
                "Street" => "$address[address_1]",
                "ZipCode" => "$address[postcode]",
                "HouseNumberInfo" => "/b"
            ]
        ];
        // this is not right method for label print or whatever.
        $responseData = $this->getResponse('post', json_encode($parcel));
        var_dump($responseData);
        die();
    }


    public function getResponse($method, $request)
    {
        // figure aout what goes in the name part
        $serviceName = 'SERVICE_NAME.svc/';
        $url = "$this->api.$serviceNam./.$format./.$method";
        //construct the api url
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 6000);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $request);


        curl_setopt(
            $curl,
            CURLOPT_HTTPHEADER,
            [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($request)
            ]
        );

        $response = curl_exec($curl);

        if (curl_getinfo($curl)["http_code"] == "401") {
            die('Unauthorized');
        }


        if ($response === false) {

            die('curl_error:"' . curl_error($curl) . '";curl_errno:' . curl_errno($curl));
        }

        return $response;
    }
}



//  find out tomorrow. service name needs to be figured out. wtf. format

new InvoiceManagement;
