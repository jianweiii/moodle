<?php

use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\ChatGrant;


function createAccessToken($duration) {
  // Required for all Twilio access tokens
  $twilioAccountSid = 'AC366a8fd9a2425a6e4cbd0b14cd9a740f';
  $twilioApiKey = 'SKa3fce6ac1236acc79905b4be5aec15da';
  $twilioApiSecret = 'ER4GMbeqpj8xM1sMvevfp96j2fpjZAES';

  // Required for Chat grant
  $serviceSid = 'IS59f0f5295d5947debd792c730ce74577';
  // choose a random username for the connecting user
  $identity = "Jianwei";

  // Create access token, which we will serialize and send to the client
  $token = new AccessToken(
      $twilioAccountSid,
      $twilioApiKey,
      $twilioApiSecret,
      $duration,
      $identity
  );

  // Create Chat grant
  $chatGrant = new ChatGrant();
  $chatGrant->setServiceSid($serviceSid);

  // Add grant to token
  $token->addGrant($chatGrant);

  // render token to string
  return $token->toJWT();
}
