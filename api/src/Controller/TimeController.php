<?php

namespace App\Controller;

use App\Entity\Time;
use App\Repository\TimeRepository;
use Carbon\Carbon;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use function Symfony\Component\Clock\now;

final class TimeController extends AbstractController
{
    #[Route('/time', name: 'app_time')]
    public function index(HttpClientInterface $httpClient, TimeRepository $timeRepository): JsonResponse
    {
        $times = $timeRepository->findAll();
        $time = empty($times) ? null : $times[0];

        if($time === null) {
            $timeLeft = 639358459;
            $currentTime = Carbon::now('America/Sao_Paulo')->getTimestampMs();

            $finalTime = $currentTime + $timeLeft;

            $time = new Time(
                $finalTime,
                Carbon::now('America/Sao_Paulo')->addDay()->toDateTimeString()
            );

            $timeRepository->add($time);

            return $this->json($time);
        } else if (Carbon::now('America/Sao_Paulo')->isAfter(Carbon::parse($time->getTimeToUpdate()))) {
            $timeLeft = 639358459;
            $currentTime = Carbon::now('America/Sao_Paulo')->getTimestampMs();

            $finalTime = strval($currentTime + $timeLeft);

            $time->setTimestamp($finalTime);
            $time->setTimeToUpdate(Carbon::now('America/Sao_Paulo')->addDay()->hour(6)->toDateTimeString());

            $timeRepository->update();

            return $this->json($time);
        }
        
        return $this->json($time);
    }
}
