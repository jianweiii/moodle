<?php

use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\ChatGrant;


function createAccessToken($duration) {
  // Required for all Twilio access tokens
  $twilioAccountSid = getenv('TWILIO_ACCOUNT_SID');
  $twilioApiKey = getenv('TWILIO_API_KEY');
  $twilioApiSecret = getenv('TWILIO_API_SECRET');

  // Required for Chat grant
  $serviceSid = getenv('SERVICE_SID');
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
