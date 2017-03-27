<?php

namespace Dime\Api\Repository;

use Dime\Server\Repository;
use Dime\Server\Relation\OneToMany;
use Dime\Server\Relation\OneToManyFlatten;
use Dime\Server\Stream;
use Dime\Server\Scope\OrderByScope;

class Activities extends Repository
{
    const TABLE = 'activities';
    const RELATION_TIMESLICE = 'timeslices';
    const RELATION_TAGS = 'activity_tags';

    private $otmTimeslices;
    private $activityTagRepository;
    private $otmTags;

    public function __construct(\Doctrine\DBAL\Connection $connection)
    {
        parent::__construct($connection, self::TABLE);

        $this->otmTimeslices = new OneToMany(new Timeslices($connection), 'activity_id', 'timeslices');
        $this->activityTagRepository = new Repository($connection, self::RELATION_TAGS);
        $this->otmTags = new OneToManyFlatten($this->activityTagRepository, 'activity_id', 'tags', 'tag_id');
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
        $with[] = new OrderByScope(['updated_at' => 'DESC']);

        $activities = parent::findAll($with);

        return Stream::of($activities)
                        ->map($this->otmTimeslices)
                        ->map($this->otmTags)
                        ->collect();
    }

    public function insert(array $data)
    {
        $activityId = parent::insert($data);
        if (array_key_exists("tags", $data)) {
          // TODO check tag id
            foreach ($data['tags'] as $tagId) {
                $this->activityTagRepository->insert(['activity_id' => $activityId, 'tag_id' => $tagId]);
            }
        }
        return $activityId;
    }

    public function update(array $data, array $identifier)
    {
        parent::update($data, $identifier);

        // Update tag
        if (array_key_exists("tags", $data)) {
            // TODO check tag id
            $dbActivitiy = $this->find($identifier);
            $removeTags = array_diff($dbActivitiy['tags'], $data['tags']);
            foreach ($removeTags as $id) {
                $this->activityTagRepository->delete(['activity_id' => $identifier['id'], 'tag_id' => $id]);
            }
            $addTags = array_diff($data['tags'], $dbActivitiy['tags']);
            foreach ($addTags as $id) {
                $this->activityTagRepository->insert(['activity_id' => $identifier['id'], 'tag_id' => $id]);
            }
        }
    }
}
