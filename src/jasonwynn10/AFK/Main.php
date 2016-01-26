<?php

  namespace AFK;

  use pocketmine\plugin\PluginBase;
  use pocketmine\command\Command;
  use pocketmine\command\CommandSender;
  use pocketmine\utils\TextFormat as Color;
  use pocketmine\Server;
  use pocketmine\Player;
  use pocketmine\event\Listener;
  use pocketmine\level\Level;
  use pocketmine\entity\Entity;
  use pocketmine\event\entity\EntityDamageEvent;
  use pocketmine\event\player\PlayerMoveEvent;
  use pocketmine\event\player\PlayerDeathEvent;
  use pocketmine\event\player\PlayerDropItemEvent;


  class Main extends PluginBase implements Listener {
    
      public function onEnable() {
        $this->isEnabled();
        $this->getServer()->getPluginManager()->registerEvents($this,$this);
      }
      
      public function _construct(Player $entity, array $drops, $deathMessage) {
        $deathMessage = "You can't die in AFK mode!";
        $this->setDeathMessage($deathMessage);
      }

      public function onCommand(CommandSender $sender,Command $cmd,$label,array $args) {
          if(strtolower($cmd->getName()) == "afk" ) {
              $player_name = $this->getName();
              if($this->isEnabled()) {
                  $this->getServer()->broadcastMessage(Color::YELLOW . $player_name . " is no longer AFK");
                  $this->isDisabled();
                } else {
                  $this->setEnabled();
                  $this->getServer()->broadcastMessage(Color::YELLOW . $player_name . " is now AFK");
              }
          }
          if(strtolower($cmd->getName()) == "kill" ) {
            $deathMessage = "You can't die in AFK mode!";
            $this->setDeathMessage($deathMessage);
          }
      }

      public function onHurt(PlayerDamageEvent $event) {
          if ($this->isEnabled() == true) {
              $event->setCancelled();
          }
      }

      public function onMove(PlayerMoveEvent $event) {
          if ($this->isEnabled() == true) {
              $event->setCancelled();
          }
      }

      public function onDeath(PlayerDeathEvent $event) {
          if($this->isEnabled() == true) {
              $event->setCancelled();

          }
      }

      public function onDrop(PlayerDropItemEvent $event) {
          if($this->isEnabled() == true) {
              $event->setCancelled();

          }
      }
  }

?>
