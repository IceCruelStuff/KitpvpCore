<?php

declare(strict_types=1);

namespace Dapro718\KitpvpCore;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\tile\Sign;
use pocketmine\block\SignChangeEvent;
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
 
  public function onJoin(PlayerJoinEvent $event) {
    $player = $event->getPlayer()->getName();
    $this->plugin->registerPlayer($player);
  }

  public function onInteract(PlayerInteractEvent $event) {
    $prefix = "§l§8[§1KitPvP§8]§r";
    $block = $event->getBlock();
    $player = $event->getPlayer();
    $tile = $player->getLevel()->getTile($block);
    $this->plugin->getServer()->broadcastMessage("Event activated");
    if($event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK) { 
      if($tile instanceof Sign) {
        $line = $tile->getText();
        $playerLevel = $this->plugin->getPlayerLevel($player);
        $arena = strtolower($line[1]);
        $this->plugin->joinArena($player, $playerLevel, $arena);
        $data = new Config($this->plugin->getDataFolder() . "arenas.yml", Config::YAML);
        $tile->setLine(2, $data->get($arena . $playerLevel) . " Players");
      }
    }
  }
  
  
  public function playerDeath(PlayerDeathEvent $event) {
    $prefix = "§l§8[§1KitPvP§8]§r";
    $player = $event->getPlayer();
    $cause = $player->getLastDamageCause();
    if($cause instanceof EntityDamageByEntityEvent) {
      $killer = $cause->getDamager();
      $playerData = new Config($this->plugin->getDataFolder() . "Data/" . "{$player}.yml");
      $killerData = new Config($this->plugin->getDataFolder() . "Data/" . "{$killer}.yml");
      if($player instanceof Player) {
        if($killer instanceof Player) {
          if($playerData->get("playing")) { 
            $msg = ("$prefix $player has been killed by $killer");
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
            $this->plugin->leaveArena($player, $playerLevel, $arena);
            $award = $playerWorth * .6;
            $player->sendMessage("$prefix You have killed $player and have been awarded \${$award}!");
            $playerData->save();
            $killerData->save();
          }
        }
      }
    }
  }
}
