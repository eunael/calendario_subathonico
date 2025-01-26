<?php

namespace App\Controller;

use App\Entity\Time;
use App\Repository\TimeRepository;
use Carbon\Carbon;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class TimeController extends AbstractController
{
    public function __construct(
        protected readonly ParameterBagInterface $params
    ) {
    }

    #[Route('/update/{pass}')]
    public function update(string $pass, Request $request, TimeRepository $timeRepository): JsonResponse
    {
        if($pass !== $this->params->get('timer.password')) {
            throw new NotFoundHttpException('Not route found for "' . $request->getUri() . '"');
        }

        $times = $timeRepository->findAll();
        $time = empty($times) ? null : $times[0];

        $time->setTimeToUpdate(Carbon::now('America/Sao_Paulo')->toDateTimeString());

        $timeRepository->update();

        return $this->json([
            'message' => 'Success'
        ]);
    }

    #[Route('/time', name: 'app_time')]
    public function index(HttpClientInterface $httpClient, TimeRepository $timeRepository): JsonResponse
    {
        try {
            $times = $timeRepository->findAll();
            $time = empty($times) ? null : $times[0];
            $timerEndpoint = $this->params->get('timer.timer_endpoint');

            if ($time === null) {
                $timeLeft = (int) $httpClient->request('GET', $timerEndpoint)->toArray()['timeLeft'];

                $currentTime = Carbon::now('America/Sao_Paulo')->getTimestampMs();

                $finalTime = $currentTime + $timeLeft;

                $time = new Time(
                    $finalTime,
                    Carbon::now('America/Sao_Paulo')->addDay()->hour(6)->toDateTimeString()
                );

                $timeRepository->add($time);

                return $this->json($time);
            } elseif (Carbon::now('America/Sao_Paulo')->isAfter(Carbon::parse($time->getTimeToUpdate()))) {
                $timeLeft = (int) $httpClient->request('GET', $timerEndpoint)->toArray()['timeLeft'];
                $currentTime = Carbon::now('America/Sao_Paulo')->getTimestampMs();

                $finalTime = strval($currentTime + $timeLeft);

                $time->setTimestamp($finalTime);
                $time->setTimeToUpdate(Carbon::now('America/Sao_Paulo')->addDay()->hour(6)->toDateTimeString());

                $timeRepository->update();

                return $this->json($time);
            }

            return $this->json($time);
        } catch (\Throwable $th) {
            return $this->json([
                'error' => $th->getMessage()
            ]);
        }
    }
}
