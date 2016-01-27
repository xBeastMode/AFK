<?php
namespace AFK;
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as Color;
use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\level\Level;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerDropItemEvent;
class Main extends PluginBase implements Listener {

    private $afk = [];

    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    private function isAFK($p){
        if($p instanceof Player){
            $p = $p->getName();
        }
        $this->afk[] = $p;
        return in_array($p, $this->afk);
    }

    private function enableAFK($p){
        if($p instanceof Player){
            $p = $p->getName();
        }
        $this->afk[] = $p;
    }

    private function disableAFK($p){
        if($p instanceof Player){
            $p = $p->getName();
        }
        if(($key = array_search($p, $this->afk)) !== null){
            unset($this->afk[$key]);
        }
    }
    public function onCommand(CommandSender $sender, Command $cmd, $label, array $args) {
        if(strtolower($cmd->getName()) === "afk"){
            $name = $sender->getName();
            if($this->isAFK($name)){
                $this->disableAFK($name);
                $this->getServer()->broadcastMessage(Color::YELLOW . $name . " is no longer AFK");
            }else{
                $this->enableAFK($name);
                $this->getServer()->broadcastMessage(Color::YELLOW . $name . " is now AFK");
            }
        }
    }

    public function onHurt(EntityDamageEvent $event) {
        $entity = $event->getEntity();
        if(!($entity instanceof Player)) {return;}
        elseif($this->isAFK($entity->getName())){
            $event->setCancelled();
        }
    }
    public function onMove(PlayerMoveEvent $event) {
        $p = $event->getPlayer();
            if($this->isAFK($p->getName())){
              $event->setCancelled();
            }
    }

    public function onDrop(PlayerDropItemEvent $event) {
        $p = $event->getPlayer();
            if($this->isAFK($p->getName())){
              $event->setCancelled();
            }
    }
}
?>
