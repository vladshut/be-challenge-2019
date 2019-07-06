<?php
/**
 * Created by PhpStorm.
 *
 * Date: 17.03.19
 * Time: 13:21
 */

namespace App\Services;

use Google\ApiCore\ApiException;
use Google\ApiCore\ValidationException;
use Google\Cloud\Dialogflow\V2\QueryResult;
use Google\Cloud\Dialogflow\V2\SessionsClient;
use Google\Cloud\Dialogflow\V2\TextInput;
use Google\Cloud\Dialogflow\V2\QueryInput;

class DialogFlowService
{
    /**
     * @param string $text
     * @param string|null $sessionId
     * @param string $languageCode
     * @return QueryResult
     * @throws ApiException
     * @throws ValidationException
     */
    public function detectIntentText(
        string $text,
        string $sessionId = null,
        string $languageCode = 'en-US'
    ): string
    {
        $projectId = 'be-challenge-mwknnt';

        // new session
        $credentials = ['credentials' => 'client-secret.json'];
        $sessionsClient = new SessionsClient($credentials);
        $session = $sessionsClient->sessionName($projectId, $sessionId ?: uniqid());

        // create text input
        $textInput = new TextInput();
        $textInput->setText($text);
        $textInput->setLanguageCode($languageCode);

        // create query input
        $queryInput = new QueryInput();
        $queryInput->setText($textInput);

        // get response and relevant info
        $response = $sessionsClient->detectIntent($session, $queryInput);
        $queryResult = $response->getQueryResult();

        $sessionsClient->close();

        return $queryResult->getFulfillmentText();
    }
}