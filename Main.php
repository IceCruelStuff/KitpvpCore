<?php

declare(strict_types=1);

namespace Dapro718\KitpvpCore;
  
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\Plugin;
use pocketmine\Server;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener{
  
  public $config;
  public $joinleave;

  public function onLoad(): void {
      $this->getLogger()->info("KitPvP Loaded");
  }
  
  
  public function onEnable() {
      $this->getLogger()->info("KitPvP Enabled");
      $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
      $this->config = $this->getConfig();
      $this->joinleave = new JoinLeaveEvents();
      $this->getServer()->getPluginManager()->joinleave(new EventListener($this, $this->joinleave), $this);
      
  }
  
  
  public function onDisable(): void {
      $this->getLogger()->info("KitPvP Disabled");
  }
}
