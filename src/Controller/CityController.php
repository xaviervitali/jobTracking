<?php

namespace App\Controller;

use App\Repository\CityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CityController extends AbstractController
{
    #[Route('/city/autocomplete', name: 'city_autocomplete')]
    public function autocomplete(Request $request, CityRepository $cityRepository)
    {
        $term = $request->query->get('term');
        $cities = $cityRepository->findBySearchTerm($term);

        $results = [];
        foreach ($cities as $city) {
            $results[] = [
                'id' => $city->getId(),
                'label' => ucwords($city->getCityCode()) .  ' (' . $city->getZipCode() . ')'
            ];
        }

        return $this->json($results);
    }
}
