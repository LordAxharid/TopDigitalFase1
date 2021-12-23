<?php

namespace Drupal\co_150_core\Service;
use Drupal\file\Entity\File;
use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;

use Drupal;
use Exception;

class SendEmailMarketingCloud {
//   protected $urlToken;
//   protected $clientId;
//   protected $clientSecret;
  
  /**
   * __construct
   *
   * @return void
   */
  public function __construct() {
    $this->urlToken = "https://auth.exacttargetapis.com/v1/requestToken";
    $this->clientId = "0llu839c27lmftigqw74ot5q";
    $this->clientSecret = "BfZ4cQ18LnfGiERBqvpgbvw=";
  }
  
  /**
   * sendEmail
   *
   * Send Email By marketing, you can use the next code
   * 
   *        use Drupal\co_150_core\Service\SendEmailMarketingCloud as SEMC;
   *    
   *        $tokenEmail =  'adf21716-bd09-ec11-b82f-48df37d1dee3';  // This token is the url token for send email you can find after from v1/messageDefinitionSends/
   *        $email = 'felipe.m@150porciento.com';
   *        $data = [
   *            "abi_name" =>  $user['name'].' '.$user['lastname'],
   *            "abi_rappi" =>  $user['code_rappi']
   *           ];
   *        $SEMC = new SEMC();
   *        return $SEMC->sendEmail($tokenEmail, $email, $data);
   * 
   * @param  mixed $tokenEmail
   * @param  mixed $email
   * @param  mixed $SubscriberAttributes
   * @return void
   */
  public function sendEmail($tokenEmail, $email, $SubscriberAttributes) {
    $token = $this->token();
    $result = [];
    $url = "https://www.exacttargetapis.com/messaging/v1/messageDefinitionSends/${tokenEmail}/send";
    
    $SubscriberAttributes['EmailAdress'] = $email;
    $body = [
      'To' => [
        'Address' => $email,
        'SubscriberKey' => $email,
        'ContactAttributes' => [
          'SubscriberAttributes' => $SubscriberAttributes
        ]
      ]
    ];

    $options = [
      'headers' => [
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer ' . $token->token
      ],
      'body' => json_encode($body)
    ];


    try {
      $client = \Drupal::httpClient();
      $request = $client->request('POST', $url, $options);

      if ($request->getStatusCode() === 200 || $request->getStatusCode() === 202) {
        $result = json_decode($request->getBody()->getContents());
        \Drupal::logger('MKC EMAIL')->info("Operacion @operation realizada con éxito , mensaje: <pre>@server</pre> ", [
          '@server' => print_r($result, 1),
        ]);
      }
    }
    catch (\Exception $e) {
      \Drupal::logger('MKC EMAIL')->error("No se pudo conectar con el servicio, mensaje: <pre>@server</pre> ", [
        '@server' => $e->getMessage()
      ]);
    }


    return $result;
  
  }


  public function token() {
    $response = new \stdClass();
    $response->token = null;
    $response->message = null;

    $body = [
      "clientId" => $this->clientId,
      "clientSecret" => $this->clientSecret
    ];

    $options = [
      'headers' => [
        'Content-Type' => 'application/json'
      ],
      'body' => json_encode($body)
    ];

    try {
      $client = \Drupal::httpClient();;

      $request = $client->request('POST', $this->urlToken, $options);

      if ($request->getStatusCode() === 200) {
        $result = json_decode($request->getBody()->getContents());
        $response->token = $result->accessToken;
      }
    }
    catch (\Exception $e) {
      \Drupal::logger('SendEmailMarketingCloud token')->error("No se pudo conectar con el servicio, problema al generar el token, mensaje: <pre>@server</pre> ", [
        '@server' => print_r($e->getMessage(), 1)
      ]);
    }
    return $response;
  }
}

