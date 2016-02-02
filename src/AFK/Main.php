<?php
namespace AFK;
// AFK v1.2.8.5
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as Color;
use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerDropItemEvent;

class Main extends PluginBase implements Listener {
    public $time;
    public $pos = [];
    private $afk = [];

    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->MoveTimeout();
    }

    public function onHurt(EntityDamageEvent $event) {
        $entity = $event->getEntity();
        if (!($entity instanceof Player)) return;
        if ($this->isAFK($entity->getName())) {
            $event->setCancelled();
        }
    }

    public function MoveTimeout() {
        $time = 5;
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new AFKTimeout($this), $time);

    }

    public function getPos(Player $p) {
        return [round($p->x),round($p->y),round($p->z),$p->getLevel()];
    }

    public function onMove(PlayerMoveEvent $event) {
        $p = $event->getPlayer()->getName();
        if ($this->isAFK($p) && !isset($this->time[$p])) ) {
            $event->setCancelled();
        }
    }

    public function onDrop(PlayerDropItemEvent $event) {
        $p = $event->getPlayer()->getName();
        if ($this->isAFK($p)) {
            $event->setCancelled();
        }
    }

    private function isAFK($p) {
        return in_array($p, $this->afk);
    }

    private function enableAFK($p) {
        $this->afk[] = $p;
    }

    private function disableAFK($p) {
        if (($key = array_search($p, $this->afk)) !== null) {
            unset($this->afk[$key]);
        }
    }

    public function hasMoved(Player $p) {
        if($this->pos[$p->getName()] != $this->getPos($p)) {
            $jmw = false;
        }else{
            $jmw = true;
        }
        return $jmw;
    }

    public function isPlayerSet(Player $p) {
        return isset($this->afk[$p->getName()]);
    }

    public function setPlayer(Player $p) {
        $this->afk[$p->getName()] = $p;
    }

    public function RemovePlayer(Player $p) {
        unset($this->afk[$p->getName()]);
        unset($this->time[$p->getName()]);
        unset($this->pos[$p->getName()]);
    }

    public function onCommand(CommandSender $sender, Command $cmd, $label, array $args) {
        if (strtolower($cmd->getName()) === "afk") {
            if (count($args) === 0) {
                if ($sender instanceof Player) {
                    $name = $sender->getName();
                    $p = $sender->getName();
                    if ($this->isAFK($name)) {
                        $this->disableAFK($name);
                        $this->getServer()->broadcastMessage(Color::YELLOW . $name . " is no longer AFK");
                    } else {
                        $this->enableAFK($name);
                        $this->getServer()->broadcastMessage(Color::YELLOW . $name . " is now AFK");
                    }
                } else {
                    $sender->sendMessage(Color::YELLOW . "Console cannot use the AFK command!");
                }
            } else {
                $name = $args[0];
                if ($this->isAFK($name)) {
                    $this->disableAFK($name);
                    $this->getServer()->broadcastMessage(Color::YELLOW . $name . " is no longer AFK");
                } else {
                    $this->enableAFK($name);
                    $this->getServer()->broadcastMessage(Color::YELLOW . $name . " is now AFK");
                }
            }
        }
    }

    public function setTime(Player $p) {
        $this->time[$p->getName()] = time();
    }

    public function setPos(Player $p) {
        $this->pos[$p->getName()] = [round($p->x),round($p->y),round($p->z),$p->getLevel()];
    }

    public function checkTime() {
        if ($this->afk != NULL) {
            foreach ($this->afk as $p) {
                if (isset($this->time[$p])) {
                    $name = $p;
                    $time = $this->time[$name];
                    if (time()-$time>=10) {
                        if ($name instanceof Player) {
                            if ($this->isAFK($name)) {
                                $this->disableAFK($name);
                                $this->getServer()->broadcastMessage(Color::YELLOW . $name . " is no longer AFK");
                            } else {
                                $this->enableAFK($name);
                                $this->getServer()->broadcastMessage(Color::YELLOW . $name . " is now AFK");
                            }
                        }
                    }
                }
            }
        }
    }
}
?>
