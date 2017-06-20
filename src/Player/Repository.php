<?php namespace BoundedContext\Player;

use BoundedContext\Contracts\Player\Player;
use BoundedContext\Contracts\Player\Snapshot\Repository as SnapshotRepository;
use BoundedContext\Contracts\Player\Factory as PlayerFactory;
use BoundedContext\Player\Snapshot\ClassName;
use EventSourced\ValueObject\ValueObject\Integer as Integer_;

class Repository implements \BoundedContext\Contracts\Player\Repository
{
    private $player_factory;
    private $snapshot_repository;
    
    public function __construct(
        PlayerFactory $player_factory,
        SnapshotRepository $snapshot_repository
    )
    {
        $this->player_factory = $player_factory;
        $this->snapshot_repository = $snapshot_repository;
    }

    public function get(ClassName $class_name)
    {
        $snapshot = $this->snapshot_repository->get($class_name);

        if (!$snapshot) {
            $player = $this->player_factory->make($class_name);
            $this->snapshot_repository->create($player->snapshot());
            return $player;
        }
        return $this->player_factory->snapshot($snapshot);
    }

    public function save(Player $player)
    {
        $this->snapshot_repository->save($player->snapshot());
    }

    public function hasVersionChanged(ClassName $class_name)
    {
        $snapshot = $this->snapshot_repository->get($class_name);

        $active_version = new Integer_(1);
        if ($snapshot) {
            $active_version = $snapshot->version();
        }

        $player = $this->player_factory->make($class_name);

        return $active_version->value() != $player->version();
    }
}
