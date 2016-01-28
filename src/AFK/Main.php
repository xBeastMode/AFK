<?php
namespace AFK;
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as Color;
use pocketmine\Player;

//use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\Listener;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerDropItemEvent;

class Main extends PluginBase implements Listener{

    private $afk = [];

    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onHurt(EntityDamageEvent $event) {
        $entity = $event->getEntity();
        if(!($entity instanceof Player)) return;
        if($this->isAFK($entity->getName())){
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

    private function isAFK($p){
        $this->afk[] = $p;
        return in_array($p, $this->afk);
    }

    private function enableAFK($p){
        $this->afk[] = $p;
    }

    private function disableAFK($p){
        if(($key = array_search($p, $this->afk)) !== null){
            unset($this->afk[$key]);
        }
    }
    public function onCommand(CommandSender $sender, Command $cmd, $label, array $args) {
        if(strtolower($cmd->getName()) === "afk"){
            if($sender instanceof Player) {
                $name = $sender->getName();
                if (!($this->isAFK($name))) {
                    $this->disableAFK($name);
                    $this->getServer()->broadcastMessage(Color::YELLOW . $name . " is no longer AFK");
                } else {
                    $this->enableAFK($name);
                    $this->getServer()->broadcastMessage(Color::YELLOW . $name . " is now AFK");
                }
            }else{
                $sender->sendMessage(Color::YELLOW . "Console cannot use the AFK command!");
            }
        }
    }
}
?>
