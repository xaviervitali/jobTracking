<?php

namespace App\Service;

use GuzzleHttp\Client;


class ApiService
{
    public function __construct(private MistralAiService $mistralAiService) {}
    public function   getAdzunaJobs(array $params, $country = 'fr'): array
    {
        $params = array_merge($params, [
            'app_id' => $_ENV['ADZUNA_API_ID'],
            'app_key' => $_ENV['ADZUNA_API_KEY'],
        ]);

        $url = 'https://api.adzuna.com/v1/api/jobs/' . $country . '/search/1';

        return $this->sendRequest($url, $params);
    }

    public function getFranceTravailJobs($params): array
    {

        $ftMaxOlDays = [1, 3, 7, 14];
        $params['publieeDepuis'] = $this->findClosestValue($params['publieeDepuis'],   $ftMaxOlDays);


        $url = 'https://api.francetravail.io/partenaire/offresdemploi/v2/offres/search';
        $headers = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->getFranceTravailAccessToken()
        ];


        return $this->sendRequest($url, $params, $headers);
    }

    private function sendRequest(string $url, array $params = [], array $headers = [], $method = 'get'): array
    {
        $client = new Client();

        try {
            $response = $client->$method($url, [
                'query' => $method === 'get' ? $params : '',
                'headers' => $headers,
                'json' => $method === 'post' ? $params : []
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);

            return $responseData;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            throw new \RuntimeException('Erreur lors de l\'appel à l\'API : ' . $e->getMessage());
        }
    }

    private  function getFranceTravailAccessToken(): string
    {
        $url = 'https://entreprise.francetravail.fr/connexion/oauth2/access_token?realm=partenaire';

        $clientId = $_ENV['FRANCE_TRAVAIL_API_ID'];
        $clientSecret = $_ENV['FRANCE_TRAVAIL_API_KEY'];
        $grantType = 'client_credentials';

        $client = new Client();

        try {
            $response = $client->post($url, [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'form_params' => [
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                    'grant_type' => $grantType,
                    'scope' => 'api_offresdemploiv2 o2dsoffre'
                ],
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);

            return $responseData['access_token']; // Retourne le token d'accès
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            throw new \RuntimeException('Erreur lors de l\'appel à l\'API : ' . $e->getMessage());
        }
    }

    public function generateCoverLetter(string $jobDescription, string $cvFilePath): string
    {
        return $this->mistralAiService->generateCoverLetter($jobDescription, $cvFilePath);
    }


    private function findClosestValue($value, $array)
    {
        $closest = null;
        $minDiff = PHP_INT_MAX;

        foreach ($array as $item) {
            $diff = abs($item - $value);
            if ($diff < $minDiff) {
                $minDiff = $diff;
                $closest = $item;
            }
        }

        return $closest;
    }

    public function getJoobleJobs($params)
    {
        $url = 'https://fr.jooble.org/api/' . $_ENV['JOOBLE_API'];
        $data =  $this->sendRequest($url, $params, [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ], 'post');
        
        usort($data['jobs'], function ($a, $b) {
            return strtotime($b['updated']) - strtotime($a['updated']);
        });

        return $data;
    }
}
