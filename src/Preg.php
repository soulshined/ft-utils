<?php


final class Preg {

    public static function count(string $pattern, string $subject, int $flags = 0, int $offset = 0) : int {
        $count = @preg_match_all($pattern, $subject, $matches, $flags, $offset);

        if (preg_last_error() !== PREG_NO_ERROR)
            return 0;

        return $count ?: 0;
    }

    public static function match_all(string $pattern, string $subject, &$matches, int $flags = 0, int $offset = 0) {
        $matches = [];
        $subject = Strings::utf8($subject);

        @preg_match_all($pattern, $subject, $out, PREG_SET_ORDER | PREG_OFFSET_CAPTURE | $flags, $offset);

        if (preg_last_error() !== PREG_NO_ERROR) return;

        foreach($out as $match) {
            $groups = [];
            foreach (array_slice($match, 1, null, true) as $index => $group) {
                $g = new PregMatchGroup(
                    $group[0],
                    $group[1],
                    $group[1] + mb_strlen($group[0])
                );
                $groups[$index] = $g;
            }

            $matches[] = new PregMatch(
                $match[0][0],
                $match[0][1],
                $match[0][1] + mb_strlen($match[0][0]),
                (object)$groups
            );
        }
    }

}

final class PregMatch {
    public function __construct(
        public readonly string $value,
        public readonly int $start,
        public readonly int $end,
        public readonly object $groups
    ) { }

    public function __get($name)
    {
        if (preg_match("/^g\d+$/", $name) === 1) {
            $groupNum = substr($name, 1);
            if (!property_exists($this->groups, $groupNum)) {
                trigger_error("Undefined group #: $groupNum", E_USER_WARNING);
                return;
            }
            return $this->groups->{$groupNum};
        }

        return $this->$name;
    }
}

final class PregMatchGroup {
    public function __construct(
        public readonly string $value,
        public readonly int $start,
        public readonly int $end
    )
    { }

    public function __toString()
    {
        return $this->value;
    }
}