<?php

declare(strict_types=1);

namespace Dapro718\KitpvpCore;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\tile\Sign;
use pocketmine\block\SignChangeEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\Player;
use pocketmine\event\Level;
use pocketmine\utils\Config;
use pocketmine\level\Position;
use pocketmine\Server;
use Dapro718\KitpvpCore\Main;
use _64FF00\PurePerms\PurePerms;
use _64FF00\PurePerms\PPGroup;
  
class EventListener implements Listener {
  
  public $plugin;
  public $config;  
  public $prefix;
  public $data;
 
  public function __construct(Main $plugin) {
      $this->plugin = $plugin;
  }
 
  public function onInteract(PlayerInteractEvent $event) {
    $prefix = "§l§8[§1KitPvP§8]§r";
    $block = $event->getBlock();
    $player = $event->getPlayer();
    $tile = $player->getLevel()->getTile($block);
    $this->plugin->getServer()->broadcastMessage("Event activated");
    if($event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK) { 
      if($tile instanceof Sign) {
        if(!file_exists($this->plugin->getDataFolder() . "Data/" . "{$player}.yml")) {
          $this->registerPlayer();
        }
        $line = $tile->getText();
        $playerLevel = $this->getPlayerLevel($player);
        $arena = strtolower($line[1]);
        $this->joinArena($player, $playerLevel, $arena);
        $data = new Config($this->plugin->getDataFolder() . "arenas.yml", Config::YAML);
        $tile->setLine(2, $data->get($arena . $playerLevel) . " Players");
      }
    }
  }
  

  public function joinArena($player, $playerLevel, $arena) {
    if($playerLevel === 3) {
      $player->sendMessage($this->prefix . "§aYou have joined the $arena arena.");
      $player->teleport(new Position($this->config->get($arena . $playerLevel . "-x"), $this->config->get($arena . $playerLevel . "-y"), $this->config->get($arena . $playerLevel . "-z")));
      $data = new Config($this->plugin->getDataFolder() . "arenas.yml", Config::YAML);
      $number = $data->get($arena . $playerLevel);
      $data->set($arena . $playerLevel, $number + 1);
      $playerData = new Config($this->plugin->getDataFolder() . "Data/" . "{$player}.yml");
      $playerData->set("currentArena", $arena . $playerLevel);
      $playerData->set("playing", TRUE);
      $playerData->save();
      $data->save();
      $this->plugin->getServer()->broadcastMessage("$player has joined $arena in level $playerLevel");
    }
    if($playerLevel === 2) {
      $player->sendMessage($this->prefix . "§aYou have joined the $arena arena.");
      $player->teleport(new Position($this->config->get($arena . $playerLevel . "-x"), $this->config->get($arena . $playerLevel . "-y"), $this->config->get($arena . $playerLevel . "-z")));
      $data = new Config($this->plugin->getDataFolder() . "arenas.yml", Config::YAML);
      $number = $data->get($arena . $playerLevel);
      $data->set($arena . $playerLevel, $number + 1);
      $playerData = new Config($this->plugin->getDataFolder() . "Data/" . "{$player}.yml");
      $playerData->set("currentArena", $arena . $playerLevel);
      $playerData->set("playing", TRUE);
      $playerData->save();
      $data->save();
      $this->plugin->getServer()->broadcastMessage("$player has joined $arena in level $playerLevel");
    }
    if($playerLevel === 1) {
      $player->sendMessage($this->prefix . "§aYou have joined the $arena arena.");
      $player->teleport(new Position($this->config->get($arena . $playerLevel . "-x"), $this->config->get($arena . $playerLevel . "-y"), $this->config->get($arena . $playerLevel . "-z")));
      $data = new Config($this->plugin->getDataFolder() . "arenas.yml", Config::YAML);
      $number = $data->get($arena . $playerLevel);
      $data->set($arena . $playerLevel, $number + 1);
      $playerData = new Config($this->plugin->getDataFolder() . "Data/" . "{$player}.yml");
      $playerData->set("currentArena", $arena . $playerLevel);
      $playerData->set("playing", TRUE);
      $playerData->save();
      $data->save();
      $this->plugin->getServer()->broadcastMessage("$player has joined $arena in level $playerLevel");
    }
  }
  
  
  public function getArenaPlayerCount($playerLevel, $arena) {
    $data = new Config($this->plugin->getDataFolder() . "arenas.yml", Config::YAML);
    $count = $this->config->get($arena . $playerLevel);
    $this->plugin->getServer()->broadcastMessage("Arena data fetched: $arena with $count players");
    return $count;
  }

    
  public function getPlayerLevel($player) {
    $pureperms = $this->plugin->getServer()->getPluginManager()->getPlugin("PurePerms");
    $group = $pureperms->getUserDataMgr()->getGroup($player);
    $groupname = $group->getName();
    $this->plugin->getServer()->broadcastMessage("Player group fetched: $player is $groupname");
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
    if($playerLevel === 3) {
      $data = new Config($this->plugin->getDataFolder() . "arenas.yml", Config::YAML);
      $number = $data->get($arena . $playerLevel);
      $data->set($arena . $playerLevel, $number - 1);
      $playerData = new Config($this->plugin->getDataFolder() . "Data/" . "{$player}.yml");
      $playerData->set("currentArena", $arena . $playerLevel);
      $playerData->set("playing", TRUE);
      $playerData->save();
      $data->save();
      $this->plugin->getServer()->broadcastMessage("$player has left $arena in level $playerLevel");
    }
    if($playerLevel === 2) {
      $data = new Config($this->plugin->getDataFolder() . "arenas.yml", Config::YAML);
      $number = $data->get($arena . $playerLevel);
      $data->set($arena . $playerLevel, $number - 1);
      $playerData = new Config($this->plugin->getDataFolder() . "Data/" . "{$player}.yml");
      $playerData->set("currentArena", $arena . $playerLevel);
      $playerData->set("playing", TRUE);
      $playerData->save();
      $data->save();
      $this->plugin->getServer()->broadcastMessage("$player has left $arena in level $playerLevel");
    }
    if($playerLevel === 1) {
      $data = new Config($this->plugin->getDataFolder() . "arenas.yml", Config::YAML);
      $number = $data->get($arena . $playerLevel);
      $data->set($arena . $playerLevel, $number - 1);
      $playerData = new Config($this->plugin->getDataFolder() . "Data/" . "{$player}.yml");
      $playerData->set("currentArena", $arena . $playerLevel);
      $playerData->set("playing", TRUE);
      $playerData->save();
      $data->save();
      $this->plugin->getServer()->broadcastMessage("$player has left $arena in level $playerLevel");
    }
  }
  
  
  public function onDeath(PlayerDeathEvent $event) {
    $prefix = "§l§8[§1KitPvP§8]§r";
    $player = $event->getPlayer();
    $playerData = new Config($this->plugin->getDataFolder() . "Data/" . "{$player}.yml");
    $killer = $event->getDamager();
    $killerData = new Config($this->plugin->getDataFolder() . "Data/" . "{$killer}.yml");
    $cause = $player->getLastDamageCause();
    if($player instanceof Player) {
      if($damager instanceof Player) {
        if($playerData->get("playing")) { 
          $msg = ("$prefix $player has been killed by $killer using $cause");
          $event->setDeathMessage($msg);
          $killerKills = $killerData->get("totalKills");
          $playerDeaths = $playerData->get("totalDeaths");
          $killerData->set("totalKills", $killerKills + 1);
          $playerData->set("totalDeaths", $playerDeaths + 1);
          $playerWorth = $playerData->get("worth");
          $killerWorth = $killerData->get("worth");
          if($playerWorth === 0) {
            $killerData->set("worth", $killerWorth + 50);
          } else {
            $killerData->set("worth", $killerWorth + ($playerWorth * .6));
            $playerData->set("worth", $playerWorth * .4);
          }
          $playerLevel = $this->getPlayerLevel($player);
          $arena = $playerData->get("currentArena");
          $this->leaveArena($player, $playerLevel, $arena);
          $award = $playerWorth * .6;
          $player->sendMessage("$prefix You have killed $player and have been awarded \${$award}!");
          $playerData->save();
          $killerData->save();
        }
      }
    }
  }
           
           
  public function registerPlayer($player) {
    if(!file_exists($this->plugin->getDataFolder() . "Data/" . "{$player}.yml")) {
      $playerData = new Config($this->plugin->getDataFolder() . "Data/" . "{$player}.yml", Config::YAML, ["totalKills" => 0, "totalDeaths" => 0, "worth" => 0, "currentArena" => "n/a", "playing" => FALSE]);
    } else {
      return true;
    } 
  }
}
