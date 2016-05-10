<?php

namespace Dime\Api\Repository;

use Dime\Server\Relation\OneToManyFlatten;
use Dime\Server\Repository;
use Dime\Server\Stream;
use Dime\Server\Scope\OrderByScope;

class Timeslices extends Repository
{

    const TABLE = 'timeslices';
    const RELATION_ACTIVITY = 'activites';
    const RELATION_TAGS = 'timeslice_tags';

    private $otmTags;

    public function __construct(\Doctrine\DBAL\Connection $connection)
    {
        parent::__construct($connection, self::TABLE);

        $this->otmTags = new OneToManyFlatten(new Repository($connection, self::RELATION_TAGS), 'timeslice_id', 'tags', 'tag_id');
    }

    public function find(array $with = [])
    {
        $timeslice = parent::find($with);

        return Stream::of($timeslice)
                        ->execute($this->otmTags)
                        ->collect();
    }

    public function findAll(array $with = [])
    {
        $with[] = new OrderByScope(['started_at' => 'DESC']);

        $timeslices = parent::findAll($with);

        return Stream::of($timeslices)
                        ->map($this->otmTags)
                        ->collect();
    }
}
