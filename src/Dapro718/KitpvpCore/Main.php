<?php

declare(strict_types=1);

namespace Dapro718\KitpvpCore;
  
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\Plugin;
use pocketmine\tile\Sign;
use pocketmine\block\SignChangeEvent;
use pocketmine\Player;
use pocketmine\event\Level;
use pocketmine\utils\Config;
use pocketmine\level\Position;
use pocketmine\Server;
use _64FF00\PurePerms\PurePerms;
use _64FF00\PurePerms\PPGroup;

class Main extends PluginBase {
  
  public $config;
  public $joinleave;

  public function onLoad(): void {
      $this->getLogger()->info("Loaded");
  }
  
  
  public function onEnable() {
      $this->getLogger()->info("Enabled");
      $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
      $this->config = $this->getConfig();
  }
  
  
  public function onDisable(): void {
      $this->getLogger()->info("Disabled");
  }

  

  public function joinArena($player, $playerLevel, $arena) {
    $player->sendMessage($this->prefix . "§aYou have joined the $arena arena.");
    $player->teleport(new Position($this->config->get($arena . $playerLevel . "-x"), $this->config->get($arena . $playerLevel . "-y"), $this->config->get($arena . $playerLevel . "-z")));
    $data = new Config($this->getDataFolder() . "arenas.yml", Config::YAML);
    $number = $data->get($arena . $playerLevel);
    $data->set($arena . $playerLevel, $number + 1);
    $playerData = new Config($this->getDataFolder() . "Data/" . "{$player}.yml");
    $playerData->set("currentArena", $arena . $playerLevel);
    $playerData->set("playing", TRUE);
    $playerData->save();
    $data->save();
    $this->getServer()->broadcastMessage("$player has joined $arena in level $playerLevel");
  }
  
  
  public function getArenaPlayerCount($playerLevel, $arena) {
    $data = new Config($this->getDataFolder() . "arenas.yml", Config::YAML);
    $count = $this->config->get($arena . $playerLevel);
    $this->getServer()->broadcastMessage("Arena data fetched: $arena with $count players");
    return $count;
  }

    
  public function getPlayerLevel($player) {
    $pureperms = $this->getServer()->getPluginManager()->getPlugin("PurePerms");
    $group = $pureperms->getUserDataMgr()->getGroup($player);
    $groupname = $group->getName();
    $this->getServer()->broadcastMessage("Player group fetched: $player is $groupname");
    if($groupname === "Leather"){
      return 1;
    } elseif ($groupname === "Chain") {
      return 1;
    } elseif ($groupname === "Iron") {
      return 1;
    } elseif ($groupname === "Diamond") {
      return 2;
    } elseif ($groupname === "Lapis") {
      return 2;
    } elseif ($groupname === "Emerald") {
      return 2;
    } elseif ($groupname === "Obsidian") {
      return 3;
    } elseif ($groupname === "Bedrock") {
      return 3;
    }
  }
  
  
  public function leaveArena($player, $playerLevel, $arena) {
      $data = new Config($this->getDataFolder() . "arenas.yml", Config::YAML);
      $number = $data->get($arena . $playerLevel);
      $data->set($arena . $playerLevel, $number - 1);
      $playerData = new Config($this->getDataFolder() . "Data/" . "{$player}.yml");
      $playerData->set("currentArena", $arena . $playerLevel);
      $playerData->set("playing", false);
      $playerData->save();
      $data->save();
      $this->getServer()->broadcastMessage("$player has left $arena in level $playerLevel");
    }
  
  
  public function registerPlayer($player) {
    if(!is_dir($this->getDataFolder() . "Data/")){
      mkdir($this->getDataFolder() . "Data/"); }
    $playerData = new Config($this->getDataFolder() . "Data/" . "{$player}.yml", Config::YAML);
    if(!$playerData->exists("totalKills") {
      $playerData->setAll("totalKills" => 0, "totalDeaths" => 0, "worth" => 0, "currentArena" => "n/a", "playing" => FALSE]);
    }
  }
}
