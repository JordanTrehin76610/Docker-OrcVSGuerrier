<?php 

class Character
{
    private int $health;
    private int $mana;

    // CONSTRUCT
    public function __construct(int $newHealth, int $newMana) { //Au tout début, sert à définir un nouvelle objet
        $this->setHealth($newHealth);
        $this->setMana($newMana);
    }


    // SETTER
    public function setHealth(int $newHealth) { //Setter pour modifier la propriété privée
        $this->health = $newHealth;
    }

     public function setMana(int $newMana) { //Setter pour modifier la propriété privée
        $this->mana = $newMana;
    }


    // GETTER
    public function getHealth() { //Getter pour accéder à une propriété privée
        return $this->health;
    }

    public function getMana() { //Getter pour accéder à une propriété privée
        return $this->mana;
    }
}

?>