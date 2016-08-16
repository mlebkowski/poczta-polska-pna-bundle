<?php

namespace Nassau\PocztaPolskaPnaBundle\Services\Indexer;

use AlgoliaSearch\Client;
use AlgoliaSearch\Index;
use Nassau\PocztaPolskaPnaBundle\Entity\PnaCity;

class Indexer
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $indexName;

    /**
     * @param Client $client
     * @param string $indexName
     */
    public function __construct(Client $client, $indexName = "pna_city")
    {
        $this->client = $client;
        $this->indexName = $indexName;
    }

    public function beginTransaction($cleanup)
    {
        $tempName = sprintf('%s_temp', $this->indexName);

        $hasIndex = array_reduce($this->client->listIndexes()['items'], function ($hasIndex, $item) use ($tempName) {
            return $hasIndex || $item['name'] === $tempName;
        });

        if ($hasIndex) {
            if ($cleanup) {
                $this->client->deleteIndex($tempName);
            } else {
                throw new IndexerException('Temporary index already exists. Remove it first.');
            }
        }

        $index = $this->client->initIndex($tempName);
        $index->setSettings([
            'attributesToIndex' => ['name', 'county', 'commune'],
            'customRanking'     => ['desc(rank)']
        ]);

        return $index;
    }

    /**
     * @param PnaCity[] $records
     * @param Index     $index
     */
    public function batchIndex($records, Index $index)
    {
        $records = array_map(function (PnaCity $city) {
            return [
                "id" => $city->getId(),
                "name" => $city->getName(),
                "county" => $city->getCounty(),
                "commune" => $city->getCommune(),
                "province" => $city->getProvince(),
                "code" => $city->getCode(),
                "rank" => array_sum([
                    (null === $city->getCode()) * 100,
                    ($city->getName() === $city->getCounty()) * 50,
                    (soundex($city->getName()) === soundex($city->getCounty())) * 25,
                    ($city->getName() === $city->getCommune()) * 10,
                    (soundex($city->getName()) === soundex($city->getCommune())) * 1,
                ]),
            ];
        }, $records);

        $index->addObjects($records, "id");

    }

    public function commit(Index $index)
    {
        /** @noinspection PhpParamsInspection */
        $this->client->moveIndex($index->indexName, $this->indexName);
    }

    public function rollback(Index $index)
    {
        $this->client->deleteIndex($index->indexName);
    }

}
