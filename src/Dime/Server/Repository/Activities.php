<?php

namespace Dime\Server\Repository;

use Dime\Server\Repository;
use Dime\Server\Relation\OneToMany;
use Dime\Server\Relation\OneToManyFlatten;
use Dime\Server\Stream;
use Dime\Server\Scope\OrderBy;

class Activities extends Repository
{
    const TABLE = 'activities';
    const RELATION_TIMESLICE = 'timeslices';
    const RELATION_TAGS = 'activity_tags';

    private $otmTimeslices;
    private $otmTags;

    public function __construct(\Doctrine\DBAL\Connection $connection)
    {
        parent::__construct($connection, self::TABLE);

        $this->otmTimeslices = new OneToMany(new Timeslices($connection), 'activity_id', 'timeslices');
        $this->otmTags = new OneToManyFlatten(new Repository($connection, self::RELATION_TAGS), 'activity_id', 'tags', 'tag_id');
    }

    public function find(array $with = [])
    {
        $activitiy = parent::find($with);

        return Stream::of($activitiy)
                        ->execute($this->otmTimeslices)
                        ->execute($this->otmTags)
                        ->collect();
    }

    public function findAll(array $with = [])
    {
        $with[] = new OrderBy(['updated_at' => 'DESC']);

        $activities = parent::findAll($with);

        return Stream::of($activities)
                        ->map($this->otmTimeslices)
                        ->map($this->otmTags)
                        ->collect();
    }

    public function filter($by)
    {
        // Running?
        // Search
        // Date
        // Relation (Customer, Project, Service, Tag)
        //
    }
}
