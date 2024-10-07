<?php

namespace App\Service;

use App\Entity\User;
use GuzzleHttp\Client;


class ApiService
{
    public function   getAdzunaJobs(array $params, $country = 'fr')
    {


        $client = new Client();

        try {
            $response = $client->get('https://api.adzuna.com/v1/api/jobs/' . $country . '/search/1', [
                'query' => $params,
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);

            return $responseData;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            throw new \RuntimeException('Erreur lors de l\'appel à l\'API Adzuna : ' . $e->getMessage());
        }
    }
}
