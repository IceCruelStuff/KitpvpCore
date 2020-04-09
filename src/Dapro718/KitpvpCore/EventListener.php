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
use Dapro718\KitpvpCore\JoinLeaveEvents;


class EventListener implements Listener {
  
  public $plugin;
  public $joinleave;
  
  public function __construct($plugin) {
      $this->plugin = $plugin;
  }
  public function __construct($joinLeave) {
      $this->joinleave = $joinLeave;
  }
  
  public function onInteract(PlayerInteractEvent $event) {
      $block = $event->getBlock();
      $player = $event->getPlayer();
      $tile = $player->getLevel()->getTile($block);
      if($event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
        if($tile instanceof Sign) {
          $line = $tile->getText();
          $playerLevel = getPlayerLevel($player);
          $arena = $line[1];
          $this->joinArena($player, $playerLevel, $arena);
          $data = new Config($this->getDataFolder() . "arenas.yml", Config::YMAL);
          $tile->setLine(2, $data->get($arena . $playerLevel) . " Players.");
        }
      }
  }
}
