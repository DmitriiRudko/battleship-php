<?php

namespace Application\Helpers;

class FieldHelper {
    private $fieldShips;

    private $fieldShoots;

    private $ships;

    private $steps;

    public function __construct($warships, $steps = null) {
        $this->ships = $warships;
        $this->steps = $steps;
        if (isset($this->steps)) {
             foreach ($this->ships as &$ship) {
                $ship['health'] = $ship['size'];
            }
        }
    }

    public function placeShip($size, $number, $orientation, $x, $y) {
        switch ($orientation) {
            case 'vertical':
                for ($i = 0; $i <= $size; $i++)
                    $this->fieldShips[$y + $i][$x] = 1;
                break;
            case 'horizontal':
                for ($j = 0; $j <= $size; $j++)
                    $this->fieldShips[$y][$x + $j] = 1;
                break;
        }
        $this->ships = array_merge($this->ships, [
            'number' => $number,
            'size' => $size,
            'x' => $x,
            'y' => $y,
            'orientation' => $orientation,
        ]);
    }

    private function createShipsField() {
        $this->fieldShips = array_fill(0, 10, array_fill(0, 10, null));
        foreach ($this->ships as &$ship) {
            switch ($ship['orientation']) {
                case 'vertical':
                    $x = $ship['x'];
                    for ($y = $ship['y']; $y < $ship['y'] + $ship['size']; $y++)
                        $this->fieldShips[$y][$x] = &$ship;
                    break;
                case 'horizontal':
                    $y = $ship['y'];
                    for ($x = $ship['x']; $x < $ship['x'] + $ship['size']; $x++)
                        $this->fieldShips[$y][$x] = &$ship;
                    break;
            }
        }
    }

    private function createShootsField() {
        $this->fieldShoots = array_fill(0, 10, array_fill(0, 10, null));
        foreach ($this->steps as $step) {
            $this->shoot($step['x'], $step['y']);
        }
    }

    public function isPossibleToPlace($size, $number, $orientation, $x, $y) {
        if ($size < 1 || $size > 4) return false;

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
                if ($x + $size - 1 > 9) return False;
                break;
        }
        if ($x < 0 || $x > 9 || $y < 0 || $y > 9) return False;

        if (!isset($this->fieldShips)) {
            $this->createShipsField();
        }
        switch ($orientation) {  //check location relative to other ships
            case 'vertical':
                for ($i = $y - 1; $i <= $y + $size; $i++) {
                    if ($this->fieldShips[$i][$x - 1] || $this->fieldShips[$i][$x] || $this->fieldShips[$i][$x + 1]) return False;
                }
                break;
            case 'horizontal':
                for ($j = $x - 1; $j <= $x + $size; $j++) {
                    if ($this->fieldShips[$y - 1][$j] || $this->fieldShips[$y][$j] || $this->fieldShips[$y + 1][$j]) return False;
                }
                break;
        }
        return True;
    }

    public function isPossibleToShoot($x, $y) {
        if (!isset($this->fieldShips)) {
            $this->createShipsField();
        }
        if (!isset($this->fieldShoots)) {
            $this->createShootsField();
        }
        return !(bool)$this->fieldShoots[$y][$x];
    }

    public function shoot($x, $y) {
        if (!isset($this->fieldShips)) {
            $this->createShipsField();
        }

        $this->fieldShoots[$y][$x] = 1;
        if ($this->fieldShips[$y][$x]) {
            $this->fieldShips[$y][$x]['health']--;
        }
        if (!$this->fieldShips[$y][$x]['health']) {
            switch ($this->fieldShips[$y][$x]['orientation']) {  //check location relative to other ships
                case 'vertical':
                    for ($i = $y - 1; $i <= $y + $this->fieldShips[$y][$x]['size']; $i++) {
                        $this->fieldShoots[$i][$x - 1] = 1;
                        $this->fieldShoots[$i][$x] = 1;
                        $this->fieldShoots[$i][$x + 1] = 1;
                    }
                    break;
                case 'horizontal':
                    for ($j = $x - 1; $j <= $x + $this->fieldShips[$y][$x]['size']; $j++) {
                        $this->fieldShoots[$y - 1][$j] = 1;
                        $this->fieldShoots[$y][$j] = 1;
                        $this->fieldShoots[$y + 1][$j] = 1;
                    }
                    break;
            }
        }
        return (bool)$this->fieldShips[$y][$x];
    }

    public function isOver() {
        $result = array_reduce($this->ships, function ($carry, $item) {
            $carry += $item['health'];
            return $carry;
        }, 0);
        return !(bool)$result;
    }
}