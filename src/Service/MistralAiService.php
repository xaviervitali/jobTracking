<?php
// src/Service/MistralAiService.php
namespace App\Service;

use App\Entity\Job;
use Smalot\PdfParser\Parser;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use SoftCreatR\MistralAI\MistralAI;


class MistralAiService
{
    // private $mistralAi;

    // public function __construct(
    //     RequestFactoryInterface $requestFactory,
    //     StreamFactoryInterface $streamFactory,
    //     UriFactoryInterface $uriFactory,
    //     ClientInterface $httpClient,
    //     string $apiKey
    // ) {
    //     $this->mistralAi = new MistralAI(
    //         $requestFactory,
    //         $streamFactory,
    //         $uriFactory,
    //         $httpClient,
    //         $apiKey
    //     );
    // }

    public function generateCoverLetter(string $jobDescription, string $cvFilePath): string
    {


        


        $cvContent = $this->extractTextFromPdf($cvFilePath);
        $params = [
            "model" => "mistral-small-latest",
            "messages" => [
                [
                    "role" => "user",
                    "content" => "Génère une texte de motivation pour le poste suivant :\n\n $jobDescription \n\nBasé sur le CV suivant :\n\n $cvContent"
                ],
            ],
            "temperature" => 0.7,
            "top_p" => 1
        ];

        return $this->sendRequest($params);



    }

    public function generateThankYouPrompt(Job $job): string
    {
        $prompt = "Génère un message de remerciement et demande d'explication pour un refus de candidature.";


    }

    private function extractTextFromPdf(string $filePath): string
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($filePath);
        $text = $pdf->getText();

        return $text;
    }

    private function sendRequest($params){
        $psr17Factory = new HttpFactory();
        $httpClient = new Client([
            // 'stream' => true,
        ]);
        $apiKey = $_ENV['MISTRAL_AI_API_KEY'];

        $mistral =  new MistralAI(
            requestFactory: $psr17Factory,
            streamFactory: $psr17Factory,
            uriFactory: $psr17Factory,
            httpClient: $httpClient,
            apiKey: $apiKey
        );
        try {
            $response = $mistral->createChatCompletion([],$params);
            
            if ($response->getStatusCode() === 200) {
                $responseObj = json_decode($response->getBody()->getContents(), true);
            return $responseObj['choices'][0]['message']['content'];
                
            } 

        } catch (\Exception $e) {
            throw new \RuntimeException('Erreur lors de la génération de la lettre de motivation : ' . $e->getMessage());
        }

    }
}
