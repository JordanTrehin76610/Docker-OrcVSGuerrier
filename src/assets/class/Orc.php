<?php
require_once'Character.php'; 

class Orc extends Character
{
    private int $damageMin;
    private int $damageMax;

    // CONSTRUCT
    public function __construct(int $newHealth, int $newMana, int $damageMin, int $damageMax) { //Au tout début, sert à définir un nouvelle objet
        parent::__construct($newHealth, $newMana);
        $this->setDamageMin($damageMin);
        $this->setDamageMax($damageMax);
    }


    // SETTER
    public function setDamageMin(int $damageMin) { //Setter pour modifier la propriété privée
        $this->damageMin = $damageMin;
    }

    public function setDamageMax(int $damageMax) { //Setter pour modifier la propriété privée
        $this->damageMax = $damageMax;
    }


    // GETTER
    public function getDamageMin() { //Getter pour accéder à une propriété privée
        return $this->damageMin;
    }

    public function getDamageMax() { //Getter pour accéder à une propriété privée
        return $this->damageMax;
    }


    // METHODE DE COMBAT
    public function attack() {
        $attaque = rand($this->getDamageMin(), $this->getDamageMax());
        return $attaque;
    }

    public function useMagic() {
        $attaque = rand($this->getDamageMin(), $this->getDamageMax());
        $attaque = intval($attaque * 2.25);
        $this->setMana($this->getMana() - 200);
        if ($this->getMana() < 0) {
            $this->setMana(0);
        }
        return $attaque; 
    }
}

?>