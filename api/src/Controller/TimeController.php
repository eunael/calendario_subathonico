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
        if ($pass !== $this->params->get('timer.password')) {
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
            $resetValueInSeconds = $this->params->get('timer.reset_value');
            $initialDay = Carbon::create(2024, 4, 26);

            if ($time === null) {
                $timeLeft = (int) $httpClient->request('GET', $timerEndpoint)->toArray()['timeLeft'];

                $currentTime = Carbon::now('America/Sao_Paulo')->getTimestampMs();

                $finalTime = Carbon::createFromTimestampMs($currentTime + $timeLeft, 'America/Sao_Paulo')->toDateTimeString();
                $timeToUpdate = Carbon::now('America/Sao_Paulo')->addSeconds($resetValueInSeconds)->toDateTimeString();
                $totalDays = (int) $initialDay->diffInDays($finalTime);

                $time = new Time(
                    $finalTime,
                    $timeToUpdate,
                    $totalDays
                );

                $timeRepository->add($time);

                return $this->json($time);
            } elseif (Carbon::now('America/Sao_Paulo')->isAfter(Carbon::parse($time->getTimeToUpdate(), 'America/Sao_Paulo'))) {
                $timeLeft = (int) $httpClient->request('GET', $timerEndpoint)->toArray()['timeLeft'];

                $timeToUpdate = Carbon::now('America/Sao_Paulo')->addSeconds($resetValueInSeconds)->toDateTimeString();
                $time->setTimeToUpdate($timeToUpdate);

                if ($timeLeft === 0) {
                    $timeRepository->update();

                    return $this->json($time);
                }

                $currentTime = Carbon::now('America/Sao_Paulo')->getTimestampMs();

                $finalTime = Carbon::createFromTimestampMs($currentTime + $timeLeft, 'America/Sao_Paulo')->toDateTimeString();
                $totalDays = (int) Carbon::create(2024, 4, 26)->diffInDays($finalTime);

                $time->setFinalTime($finalTime);
                $time->setTotalDays($totalDays);

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
