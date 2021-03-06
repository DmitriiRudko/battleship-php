<?php

namespace Application\Helpers;

class FieldHelper {
    private $fieldShips;

    private $fieldShoots;

    private $ships;

    private $steps;

    private function createShipsField(): void {
        $this->fieldShips = array_fill(0, 10, array_fill(0, 10, null));
        foreach ($this->ships as &$ship) {  // here we keep a link to the ship to reduce health in every cell
            switch ($ship['orientation']) {  //  by accessing one of them
                case 'vertical':
                    $x = $ship['x'];
                    for ($y = $ship['y']; $y < $ship['y'] + $ship['size']; $y++) {
                        $this->fieldShips[$y][$x] = &$ship;
                    }
                    break;
                case 'horizontal':
                    $y = $ship['y'];
                    for ($x = $ship['x']; $x < $ship['x'] + $ship['size']; $x++) {
                        $this->fieldShips[$y][$x] = &$ship;
                    }
                    break;
            }
        }
    }

    private function createShootsField(): void {
        $this->fieldShoots = array_fill(0, 10, array_fill(0, 10, null));
        foreach ($this->steps as $step) {
            $this->shoot($step['x'], $step['y']);
        }
    }

    private function countShipsSameTypes(array $ships, int $size): int {
        return array_reduce($this->ships, function ($carry, $item) use ($size) {
            return ($item['size'] === $size) ? $carry++ : $carry;
        }, 0);
    }

    private function isAlreadyExist(array $ships, int $size, int $number): bool {
        foreach ($this->ships as $ship) {
            if ($ship['size'] === $size && $ship['number'] === $number) return true;
        }
        return false;
    }

    public function __construct(array $warships = null, array $steps = null) {
        $this->ships = $warships;
        $this->steps = $steps;

        if (isset($this->steps)) {
            foreach ($this->ships as &$ship) {  // keeping links to the ships in every cell of the field
                $ship['health'] = $ship['size'];  // to easier access to health status of the ship
            }
        }
    }

    public function placeShip(int $size, int $number, string $orientation, int $x, int $y) {
        switch ($orientation) {
            case 'vertical':
                for ($i = 0; $i <= $size; $i++) {
                    $this->fieldShips[$y + $i][$x] = 1;
                }
                break;
            case 'horizontal':
                for ($j = 0; $j <= $size; $j++) {
                    $this->fieldShips[$y][$x + $j] = 1;
                }
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

    public function isPossibleToPlace(int $size, int $number, string $orientation, int $x, int $y): bool {
        if ($size < 1 || $size > 4) return false;

        if ($size + $this->countShipsSameTypes($this->ships, $size) > 4) return false;  // check the limit for ships of this type

        if ($this->isAlreadyExist($this->ships, $size, $number)) return false;  // check if we already have this ship

        switch ($orientation) {  // check the borders
            case 'vertical':
                if ($y + $size - 1 > 9) return false;
                break;
            case 'horizontal':
                if ($x + $size - 1 > 9) return false;
                break;
        }

        if ($x < 0 || $x > 9 || $y < 0 || $y > 9) return false;

        if (!isset($this->fieldShips)) {
            $this->createShipsField();
        }

        switch ($orientation) {  //check location relative to other ships
            case 'vertical':
                for ($i = $y - 1; $i <= $y + $size; $i++) {
                    if (isset($this->fieldShips[$i][$x - 1])
                        || isset($this->fieldShips[$i][$x])
                        || isset($this->fieldShips[$i][$x + 1]))
                        return false;
                }
                break;
            case 'horizontal':
                for ($j = $x - 1; $j <= $x + $size; $j++) {
                    if (isset($this->fieldShips[$y - 1][$j])
                        || isset($this->fieldShips[$y][$j])
                        || isset($this->fieldShips[$y + 1][$j]))
                        return false;
                }
                break;
        }

        return true;
    }

    public function isPossibleToShoot($x, $y): bool {
        if (!isset($this->fieldShips)) {
            $this->createShipsField();
        }

        if (!isset($this->fieldShoots)) {
            $this->createShootsField();
        }

        return !(bool)$this->fieldShoots[$y][$x];
    }

    public function shoot($x, $y): bool {
        if (!isset($this->fieldShips)) {
            $this->createShipsField();
        }

        $this->fieldShoots[$y][$x] = 1;
        if (isset($this->fieldShips[$y][$x])) {

            $this->fieldShips[$y][$x]['health']--;

            if (!$this->fieldShips[$y][$x]['health']) {
                switch ($this->fieldShips[$y][$x]['orientation']) {
                    case 'vertical':
                        $x = $this->fieldShips[$y][$x]['x'];
                        $y = $this->fieldShips[$y][$x]['y'];

                        for ($i = $y - 1; $i <= $y + $this->fieldShips[$y][$x]['size']; $i++) {
                            $i >= 0 && $x > 0 ? $this->fieldShoots[$i][$x - 1] = 1 : null;
                            $i >= 0 ? $this->fieldShoots[$i][$x] = 1 : null;
                            $x <= 9 ? $this->fieldShoots[$i][$x + 1] = 1 : null;
                        }
                        break;

                    case 'horizontal':
                        $x = $this->fieldShips[$y][$x]['x'];
                        $y = $this->fieldShips[$y][$x]['y'];

                        for ($j = $x - 1; $j <= $x + $this->fieldShips[$y][$x]['size']; $j++) {
                            $j >= 0 && $y > 0 ? $this->fieldShoots[$y - 1][$j] = 1 : null;
                            $j >= 0 ? $this->fieldShoots[$y][$j] = 1 : null;
                            $y <= 9 ? $this->fieldShoots[$y + 1][$j] = 1 : null;
                        }
                        break;
                }
            }
        }
        return (bool)$this->fieldShips[$y][$x];
    }

    public static function getFieldsInfo($myShips, $enemyShips, $mySteps, $enemySteps): array {
        $myField = array_fill(0, 10, array_fill(0, 10, [
            0 => 'empty',
            1 => 0,
        ]));

        $field = new FieldHelper($myShips, $enemySteps);
        $field->createShipsField();
        $field->createShootsField();

        for ($i = 0; $i < 10; $i++)
            for ($j = 0; $j < 10; $j++) {
                if ($field->fieldShips[$i][$j])
                    $myField[$i][$j][0] = $field->fieldShips[$i][$j]['size'] . '-' . $field->fieldShips[$i][$j]['number'];
                if ($field->fieldShoots[$i][$j])
                    $myField[$i][$j][1] = $field->fieldShoots[$i][$j];
            }

        $enemyField = array_fill(0, 10, array_fill(0, 10, [
            0 => 'empty',
            1 => 0,
        ]));

        $field = new FieldHelper($enemyShips, $mySteps);
        $field->createShipsField();
        $field->createShootsField();

        for ($i = 0; $i < 10; $i++)
            for ($j = 0; $j < 10; $j++) {
                if ($field->fieldShips[$i][$j])
                    $enemyField[$i][$j][0] = $field->fieldShoots[$i][$j] ?
                        $field->fieldShips[$i][$j]['size'] . '-' . $field->fieldShips[$i][$j]['number'] : 'hidden';
                if ($field->fieldShoots[$i][$j])
                    $enemyField[$i][$j][1] = $field->fieldShoots[$i][$j];
            }

        return [
            'fieldMy' => $myField,
            'fieldEnemy' => $enemyField,
        ];
    }

    public function isOver(): bool {
        $result = array_reduce($this->ships, function ($carry, $item) {
            $carry += $item['health'];
            return $carry;
        }, 0);

        return $result == 1;
    }
}