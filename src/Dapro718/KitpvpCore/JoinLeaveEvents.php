<?php

declare(strict_types=1);

namespace Dapro718\KitpvpCore;

use pocketmine\Player;
use pocketmine\utils\Config;
use Dapro718\KitpvpCore\Main;

class JoinLeaveEvents {

  public $prefix;
  public $data;
  
  $prefix = "§l§8[§1KitPvP§8]§r";

  public function joinArena($player, $playerLevel, $arena) {
    if($playerLevel === 3) {
      $player->sendMessage($prefix . "§aYou have joined the $arena arena.");
      $player->teleport(new Position($this->config->get($arena . $playerLevel . "-x"), $this->config->get($arena . $playerLevel . "-y"), $this->config->get($arena . $playerLevel . "-z")));
      $data = new Config($this->getDataFolder() . "arenas.yml", Config::YAML);
      $data->set($arena . $playerLevel, $this->getArenaPlayerCount($playerLevel, $arena) + 1);
      $data->save();
    }
    if($playerLevel === 2) {
      $player->sendMessage($prefix . "§aYou have joined the $arena arena.");
      $player->teleport(new Position($this->config->get($arena . $playerLevel . "-x"), $this->config->get($arena . $playerLevel . "-y"), $this->config->get($arena . $playerLevel . "-z")));
      $data = new Config($this->getDataFolder() . "arenas.yml", Config::YAML);
      $data->set($arena . $playerLevel, $this->getArenaPlayerCount($playerLevel, $arena) + 1);
      $data->save();
    }
    if($playerLevel === 1) {
      $player->sendMessage($prefix . "§aYou have joined the $arena arena.");
      $player->teleport(new Position($this->config->get($arena . $playerLevel . "-x"), $this->config->get($arena . $playerLevel . "-y"), $this->config->get($arena . $playerLevel . "-z")));
      $data = new Config($this->getDataFolder() . "arenas.yml", Config::YAML);
      $data->set($arena . $playerLevel, $this->getArenaPlayerCount($playerLevel, $arena) + 1);
      $data->save();
    }
  }
}
