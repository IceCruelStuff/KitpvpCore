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
use Dapro718\KitpvpCore\FloatingTextParticleCreator;

class Main extends PluginBase {
  
  public $config;
  public $joinleave;
  public $bountyPlayers = [];
  
  public function onEnable() {
      $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
      $this->config = $this->getConfig();
  }
 
  public function joinArena($player, $playerLevel, $arena) {
    $prefix = "§l§8[§1KitPvP§8]§r";
    $player->sendMessage($prefix . "§aYou have joined the $arena arena.");
    $player->teleport(new Position($this->config->get($arena . $playerLevel . "-x"), $this->config->get($arena . $playerLevel . "-y"), $this->config->get($arena . $playerLevel . "-z")));
    $data = new Config($this->getDataFolder() . "arenas.yml", Config::YAML);
    $number = $data->get($arena);
    $data->set($arena, $number + 1);
    $playerData = new Config($this->getDataFolder() . "Data/" . "{$player}.yml");
    $playerData->set("currentArena", $arena);
    $playerData->set("playing", TRUE);
    $playerData->save();
    $data->save();
    $this->getServer()->broadcastMessage("$player has joined $arena in level $playerLevel");
  }
  
  public function leaveArena($player, $arena) {
      $data = new Config($this->getDataFolder() . "arenas.yml", Config::YAML);
      $number = $data->get($arena);
      $data->set($arena, $number - 1);
      $playerData = new Config($this->getDataFolder() . "Data/" . "{$player}.yml");
      $playerData->set("currentArena", "n/a");
      $playerData->set("playing", false);
      $playerData->save();
      $data->save();
      $this->getServer()->broadcastMessage("$player has left $arena in level $playerLevel");
    }
  
  public function getArenaPlayerCount($arena) {
    $data = new Config($this->getDataFolder() . "arenas.yml", Config::YAML);
    $count = $this->config->get($arena);
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
  
  public function createBountyBoard() {
    $master = new Config($this->getDataFolder() . "Data/" . "master.yml", Config::YAML);
    $players = $master->get("players");
    $onlinePlayers = array();
    foreach ($players as $player) {
      if($player->isOnline()) {
        $onlinePlayers = array_push($onlinePlayers, $player);
      }
    }
    $max = count($onlinePlayers) - 1;
    if($max < 10) {
      for ($i = 0; $i < 10; $i++) {
      $rand = rand(0, $max);
      $this->bountyPlayers[$i] = $onlinePlayers[$rand]; 
      unset($onlinePlayers[$rand]; }
    } else {
      for ($i = 0; $i < 10; $i++) {
      $rand = rand(0, $max);
      $this->bountyPlayers[$i] = $onlinePlayers[$rand];
      unset($onlinePlayers[$rand]; } }
    foreach ($this->bountyPlayers as $player) {
      $c = 0;
      $playerData = new Config($this->getDataFolder() . "Data/" . "{$player}.yml", Config.YAML);
      $kills = $playerData->get("totalKills");
      if($kills === 0) {
        $bounty = 1000;
      } else {
        $min = $kills * 1000;
        $max = $kills * 2000;
        $bounty = rand($min, $max); 
      }
      $playerData->set("bounty", true);
      $playerData->set("bounty-amount", $bounty);
      $line[$c] = ($c++) . ". {$player}: \${$bounty}.\n";
      $playerData->save();
    }
    for ($j = 0; $j < count($line); $j++) {
      $text = $text . $line[$j]; }
    $title = "Bounties";
    $ftp = new FloatingTextParticleCreator(new Position(5, 100, 5, Server::getInstance()->getLevelByName('world')), $text, $title);
    Main::$particles[$ftp->getEntityId()] = $ftp;
    Server::getInstance()->getLevelByName('world')->addParticle($ftp, Server::getInstance()->getLevelByName('world')->getPlayers());
  }
    
  public function registerPlayer($player) {
    if(!is_dir($this->getDataFolder() . "Data/")){
      mkdir($this->getDataFolder() . "Data/"); }
    $playerData = new Config($this->getDataFolder() . "Data/" . "{$player}.yml", Config::YAML);
    $master = new Config($this->getDataFolder() . "Data/" . "master.yml", Config::YAML);
    if(!$playerData->exists("totalKills")) {
      $playerData->setAll(["totalKills" => 0, "totalDeaths" => 0, "worth" => 0, "currentArena" => "n/a", "playing" => FALSE, "bounty" => false, "bounty-amount" => 0]);
      $players = $master->get("players");
      $players = array_push($players, $player);
      $master->set("players", $players);
      $master->save();
    }
    $playerData->save();
  }
  
  //api functions
  public function getKills($player) {
    $playerData = new Config($this->getDataFolder() . "Data/" . "{$player}.yml", Config::YAML);
    $kills = $playerData->get("totalkills");
    return $kills;
  }
  
  public function getDeaths($player) {
    $playerData = new Config($this->getDataFolder() . "Data/" . "{$player}.yml", Config::YAML);
    $deaths = $playerData->get("totalDeaths");
    return $deaths;
  }
  
  public function getWorth($player) {
    $playerData = new Config($this->getDataFolder() . "Data/" . "{$player}.yml", Config::YAML);
    $worth = $playerData->get("worth");
    return $worth;
  }
  
  public function getCurrentArena($player) {
    $playerData = new Config($this->getDataFolder() . "Data/" . "{$player}.yml", Config::YAML);
    $currentArena = $playerData->get("currentArena");
    return $currentArena;
  }
  
  public function getPlaying($player) {
    $playerData = new Config($this->getDataFolder() . "Data/" . "{$player}.yml", Config::YAML);
    $playing = $playerData->get("playing");
    return $playing;
  }
  
  public function getBounty($player) {
    $playerData = new Config($this->getDataFolder() . "Data/" . "{$player}.yml", Config::YAML);
    $bounty = $playerData->get("bounty");
    return $bounty;
  }
  
  public function getBountyAmount($player) {
    $playerData = new Config($this->getDataFolder() . "Data/" . "{$player}.yml", Config::YAML);
    $bA = $playerData->get("bounty-amount");
    return $bA;
  }
  
  public function setKills($player, $value) {
    $playerData = new Config($this->getDataFolder() . "Data/" . "{$player}.yml", Config::YAML);
    $playerData->set("totalKills", $value);
    $playerData->save();
  }
  
  public function setDeaths($player, $value) {
    $playerData = new Config($this->getDataFolder() . "Data/" . "{$player}.yml", Config::YAML);
    $playerData->set("totalDeaths", $value);
    $playerData->save();
  }
  
  public function setWorth($player, $value) {
    $playerData = new Config($this->getDataFolder() . "Data/" . "{$player}.yml", Config::YAML);
    $playerData->set("worth", $value);
    $playerData->save();
  }
  
  public function setCurrentArena($player, $value) {
    $playerData = new Config($this->getDataFolder() . "Data/" . "{$player}.yml", Config::YAML);
    $playerData->set("currentArena", $value);
    $playerData->save();
  }
  
  public function setPlaying($player, $value) {
    $playerData = new Config($this->getDataFolder() . "Data/" . "{$player}.yml", Config::YAML);
    $playerData->set("playing", $value);
    $playerData->save();
  }
  
  public function setBounty($player, $value) {
    $playerData = new Config($this->getDataFolder() . "Data/" . "{$player}.yml", Config::YAML);
    $playerData->set("bounty", $value);
    $playerData->save();
  }
  
  public function setBountyAmount($player, $value) {
    $playerData = new Config($this->getDataFolder() . "Data/" . "{$player}.yml", Config::YAML);
    $playerData->set("bounty-amount", $value);
    $playerData->save();
  }
}
