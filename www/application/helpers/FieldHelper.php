<?php

namespace Application\Helpers;

class FieldHelper {
    private $field;

    private $ships;

    private $steps;

    public function __construct($warships, $steps = null) {
        $this->ships = $warships;
        $this->steps = $steps;
    }

    public function placeShip($ship) {

    }

    private function createField() {
        $this->field = array_fill(0, 9, array_fill(0, 9, null));
        foreach ($this->ships as $ship) {
            switch ($ship['orientation']) {
                case 'vertical':
                    $x = $ship['x'];
                    for ($y = $ship['y']; $y < $ship['size']; $y++)
                        $this->field[$y][$x] = 1;
                    break;
                case 'horizontal':
                    $y = $ship['y'];
                    for ($x = $ship['x']; $x < $ship['size']; $x++)
                        $this->field[$y][$x] = 1;
                    break;
            }
        }
    }

    public function isPossibleToPlace($size, $number, $orientation, $x, $y) {
        $countShipsSameTypes = array_reduce($this->ships, function ($carry, $item) use ($size) {  // check the limit for ships of this type
            ($item['size'] == $size) ? $carry++ : null;
            return $carry;
        }, 0);
        if ($size + $countShipsSameTypes > 4) return False;

        $isAlreadyExist = array_reduce($this->ships, function ($carry, $item) use ($size, $number) {  // check if we already have this ship
            ($item['size'] == $size && $item['number'] == $number) ? $carry = True : null;
            return $carry;
        }, False);
        if ($isAlreadyExist) return False;

        switch ($orientation) {  // check the borders
            case 'vertical':
                if ($y + $size - 1 > 9) return False;
                break;
            case 'horizontal':
                if ($x + $size  - 1 > 9) return False;
                break;
        }
        if ($x < 0 || $x > 9 || $y < 0 || $y > 9) return False;

        if (!$this->field) {
            $this->createField();
        }
        switch ($orientation) {  //check location relative to other ships
            case 'vertical':  // А вот это вот нихуя не работает!!!!
                for ($i = $y - 1; $i < $y + $size; $i++) {
                    if ($this->field[$i][$x - 1] || $this->field[$i][$x] || $this->field[$i][$x + 1]) return False;
                }
                break;
            case 'horizontal':
                for ($j = $x - 1; $j < $x + $size; $j++) {
                    if ($this->field[$y - 1][$j] || $this->field[$y][$j] || $this->field[$y + 1][$j]) return False;
                }
                break;
        }

        echo "132321132";

        return True;
    }
}