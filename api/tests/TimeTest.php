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

        $finalTime = Carbon::createFromTimestampMs($currentTime + $timeLeft, 'America/Sao_Paulo')->toDateTimeString();
        $timeToUpdate = Carbon::now('America/Sao_Paulo')->addMinutes(5)->toDateTimeString();

        $time = new Time(
            $finalTime,
            $timeToUpdate
        );

        // instance Entity Manager
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        assert($entityManager instanceof EntityManagerInterface);
        // persisting time
        $entityManager->persist($time);
        $entityManager->flush();
        
        // get all times
        $timesFromDb = $this->getTimeRepository()->findAll();

        // assert that there is only one time
        assertEquals(count($timesFromDb), 1);

        // assert that the time saved has correct data
        $timeFromDb = $timesFromDb[0];
        assert($timeFromDb instanceof Time);
        assertEquals($timeFromDb->getFinalTime(), $finalTime);
        assertEquals($timeFromDb->getTimeToUpdate(), $timeToUpdate);
    }

    private function getTimeRepository(): TimeRepository
    {
        return self::getContainer()->get(TimeRepository::class);
    }
}
