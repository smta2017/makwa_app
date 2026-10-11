<?php

namespace App\Gamify\Badges;

use QCod\Gamify\BadgeType;

class FirstContribution extends BadgeType
{
    protected $level = 2;
    /**
     * Description for badge
     *
     * @var string
     */
    protected $description = 'ern more 1000 point';

    /**
     * Check is user qualifies for badge
     *
     * @param $user
     * @return bool
     */
    public function qualifier($user)
    {
        return $user->getPoints() >= 1300;
    }
}
