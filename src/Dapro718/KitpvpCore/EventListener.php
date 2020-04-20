<?php

declare(strict_types=1);

namespace Dapro718\KitpvpCore;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\tile\Sign;
use pocketmine\block\SignChangeEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\Player;
use pocketmine\event\Level;
use pocketmine\utils\Config;
use pocketmine\level\Position;
use pocketmine\Server;
use Dapro718\KitpvpCore\Main;
  
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
      if($event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
        if($tile instanceof Sign) {
          $line = $tile->getText();
          $playerLevel = $this->getPlayerLevel($player);
          $arena = $line[1];
          $this->joinArena($player, $playerLevel, $arena);
          $data = new Config($this->plugin->getDataFolder() . "arenas.yml", Config::YAML);
          $tile->setLine(2, $data->get($arena . $playerLevel) . " Players.");
        }
      }
  }
  

  public function joinArena($player, $playerLevel, $arena) {
    if($playerLevel === 3) {
      $player->sendMessage($this->prefix . "§aYou have joined the $arena arena.");
      $player->teleport(new Position($this->config->get($arena . $playerLevel . "-x"), $this->config->get($arena . $playerLevel . "-y"), $this->config->get($arena . $playerLevel . "-z")));
      $data = new Config($this->plugin->getDataFolder() . "arenas.yml", Config::YAML);
      $data->set($arena . $playerLevel, $this->getArenaPlayerCount($playerLevel, $arena) + 1);
      $data->save();
      $this->getServer()->broadcastMessage("$player has joined $arena in level $playerLevel");
    }
    if($playerLevel === 2) {
      $player->sendMessage($this->prefix . "§aYou have joined the $arena arena.");
      $player->teleport(new Position($this->config->get($arena . $playerLevel . "-x"), $this->config->get($arena . $playerLevel . "-y"), $this->config->get($arena . $playerLevel . "-z")));
      $data = new Config($this->plugin->getDataFolder() . "arenas.yml", Config::YAML);
      $data->set($arena . $playerLevel, $this->getArenaPlayerCount($playerLevel, $arena) + 1);
      $data->save();
      $this->getServer()->broadcastMessage("$player has joined $arena in level $playerLevel");
    }
    if($playerLevel === 1) {
      $player->sendMessage($this->prefix . "§aYou have joined the $arena arena.");
      $player->teleport(new Position($this->config->get($arena . $playerLevel . "-x"), $this->config->get($arena . $playerLevel . "-y"), $this->config->get($arena . $playerLevel . "-z")));
      $data = new Config($this->plugin->getDataFolder() . "arenas.yml", Config::YAML);
      $data->set($arena . $playerLevel, $this->getArenaPlayerCount($playerLevel, $arena) + 1);
      $data->save();
      $this->getServer()->broadcastMessage("$player has joined $arena in level $playerLevel");
    }
  }
  
  
  public function getArenaPlayerCount($playerLevel, $arena) {
    $data = new Config($this->plugin->getDataFolder() . "arenas.yml", Config::YAML);
    $count = $this->config->get($arena . $playerLevel);
    $this->getServer()->broadcastMessage("Arena data fetched: $arena with $count players");
    return $count;
  }

    
  public function getPlayerLevel($player) {
    $pureperms = $this->plugin->getServer()->gePluginManager()->getPlugin("PurePerms");
    $group = $pureperms->getUserDataMrg()->getGroup($player);
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
}
