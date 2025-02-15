<?php

namespace App\Tests\natanael\Downloads\calendario_subathonico\api\tests;

use App\Entity\Time;
use App\Repository\TimeRepository;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Zenstruck\Foundry\Test\ResetDatabase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertObjectEquals;

class TimeTest extends KernelTestCase
{
    use ResetDatabase;

    public function test_should_create_and_return_time()
    {
        self::bootKernel();

        //region Mock request to API
        $timeLeftResponse = 100 * 60 * 60 * 1000;
        $responses = [
            new MockResponse(body: json_encode(['timeLeft' => $timeLeftResponse]))
        ];
        $client = new MockHttpClient($responses);
        //endregion

        $timeLeft = $client->request('GET', 'https://test.dev/api/time-left')->toArray()['timeLeft'];

        $currentTime = Carbon::now('America/Sao_Paulo')->getTimestampMs();

        $finalTime = Carbon::createFromTimestampMs($currentTime + $timeLeft, 'America/Sao_Paulo');
        $timeToUpdate = Carbon::now('America/Sao_Paulo')->addMinutes(5);
        $totalDays = (int) Carbon::create(2024, 4, 26)->diffInDays($finalTime, true);

        $time = new Time(
            $finalTime->toDateTimeString(),
            $timeToUpdate->toDateTimeString(),
            $totalDays
        );

        $timeRepository = $this->getTimeRepository();

        $timeRepository->add($time);

        // get all times
        $timesFromDb = $timeRepository->findAll();

        // assert that there is only one time
        assertEquals(count($timesFromDb), 1);

        // assert that the time saved has correct data
        $timeFromDb = $timesFromDb[0];
        assert($timeFromDb instanceof Time);
        assertEquals($timeFromDb->getFinalTime(), $finalTime);
        assertEquals($timeFromDb->getTimeToUpdate(), $timeToUpdate);
        assertEquals($timeFromDb->getTotalDays(), $totalDays);
    }

    public function test_should_update_if_already_exists_and_return_time()
    {
        self::bootKernel();

        $entityManager = self::getContainer()->get(EntityManagerInterface::class);

        //region Create time entity
        $timeLeftEntity = 50 * 60 * 60 * 1000;
        $currentTimeEntity = Carbon::now('America/Sao_Paulo')->getTimestampMs();
        $timeToUpdateEntity = Carbon::now('America/Sao_Paulo')->addMinutes(5)->toDateTimeString();
        $finalTimeEntity = Carbon::createFromTimestampMs($currentTimeEntity + $timeLeftEntity, 'America/Sao_Paulo')->toDateTimeString();
        $totalDaysEntity = (int) Carbon::create(2024, 4, 26)->diffInDays($finalTimeEntity);
        $timeEntity = new Time($finalTimeEntity, $timeToUpdateEntity, $totalDaysEntity);
        $entityManager->persist($timeEntity);
        $entityManager->flush();
        //endregion

        //region Mock request to API
        $timeLeftResponse = 100 * 60 * 60 * 1000;
        $responses = [
            new MockResponse(body: json_encode(['timeLeft' => $timeLeftResponse]))
        ];
        $client = new MockHttpClient($responses);
        //endregion

        $timeLeft = $client->request('GET', 'https://test.dev/api/time-left')->toArray()['timeLeft'];

        $currentTime = Carbon::now('America/Sao_Paulo')->getTimestampMs();

        $finalTime = Carbon::createFromTimestampMs($currentTime + $timeLeft, 'America/Sao_Paulo')->toDateTimeString();
        $timeToUpdate = Carbon::now('America/Sao_Paulo')->addMinutes(5)->toDateTimeString();
        $totalDays = (int) Carbon::create(2024, 4, 26)->diffInDays($finalTime);

        $timeRepository = $this->getTimeRepository();

        $time = $timeRepository->find($timeEntity->getId());
        $time->setFinalTime($finalTime);
        $time->setTimeToUpdate($timeToUpdate);
        $time->setTotalDays($totalDays);

        // persisting time
        $timeRepository->update();

        // assert that the time saved has correct data
        assertEquals($time->getFinalTime(), $finalTime);
        assertEquals($time->getTimeToUpdate(), $timeToUpdate);
        assertEquals($time->getTotalDays(), $totalDays);
    }

    private function getTimeRepository(): TimeRepository
    {
        return self::getContainer()->get(TimeRepository::class);
    }
}
