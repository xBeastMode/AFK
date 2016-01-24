<?php
/**
 * Created by developer: jasonwynn10.
 */

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

class Main extends PluginBase implements Listener {
    public function onEnable() {
        $this->enabled = true;
        $this->getServer()->getPluginManager()->registerEvents($this,$this);
    }
    public function onCommand(CommandSender $issuer,Command $cmd,$label,array $args) {
        if(strtolower($cmd->getName()) == "afk" ) {
            if($issuer->hasPermission("afk") or $issuer->hasPermission("afk.toggle")) {
                $this->enabled = false;
                if($this->enabled) {
                    $issuer->sendMessage(Color::YELLOW . "You are now AFK");
                } else {
                    $issuer->sendMessage(Color::YELLOW . "You are no longer AFK");
                }
            }
            return true;
        } else {
            return false;
        }
    }
    /**
     * @param EntityDamageEvent $event
     *
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function onHurt(EntityDamageEvent $event) {
        $entity = $event->getEntity();
        if(($entity instanceof Player) && ($this->enabled == true)) {
            $event->setCancelled();
        }
    }
}

?>
