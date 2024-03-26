<?php

namespace OnixSystemsPHP\HyperfSupport\Entity\Trello\Member;

class Members
{
    public function __construct(public array $members) {}

    /**
     * Get string id members
     *
     * @param array $members
     * @return string
     */
    public function getIdMembers(array $members): string
    {
        if (!empty($ids = $this->getIds($members))) {
            return implode(',', $ids);
        }

        return '';
    }

    /**
     * Retrieve an array containing only the IDs of the specified members.
     *
     * @param array $members
     * @return array
     */
    private function getIds(array $members): array
    {
        return array_map(fn($member) => $member['id'],
            array_filter($this->members, fn($member) => in_array($member['username'], $members))
        );
    }
}
