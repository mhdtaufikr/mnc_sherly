<?php

namespace App\Support;

class SalesApprovalRoute
{
    public static function approvers(): array
    {
        return [
            ['order' => 1, 'stage' => 'initial', 'group' => 'Marketing & Sales Operation', 'position' => 'Marketing Head', 'name' => 'Hardianti Asmi', 'username' => 'hardianti.asmi', 'email' => 'hardianti.asmi@mnc-project.local'],
            ['order' => 2, 'stage' => 'initial', 'group' => 'Marketing & Sales Operation', 'position' => 'Sales Operation Head', 'name' => 'Hengki Dwiyanto', 'username' => 'hengki.dwiyanto', 'email' => 'hengki.dwiyanto@mnc-project.local'],
            ['order' => 3, 'stage' => 'initial', 'group' => 'Marketing & Sales Operation', 'position' => 'General Manager Marketing & Sales Operation', 'name' => 'Daniel Tambunan', 'username' => 'daniel.tambunan', 'email' => 'daniel.tambunan@mnc-project.local'],
            ['order' => 4, 'stage' => 'initial', 'group' => 'Finance & Accounting', 'position' => 'Finance Head', 'name' => 'Dina Anggraini', 'username' => 'dina.anggraini', 'email' => 'dina.anggraini@mnc-project.local'],
            ['order' => 5, 'stage' => 'initial', 'group' => 'Finance & Accounting', 'position' => 'General Manager Finance', 'name' => 'Mochamad Ari', 'username' => 'mochamad.ari', 'email' => 'mochamad.ari@mnc-project.local'],
            ['order' => 6, 'stage' => 'initial', 'group' => 'Finance & Accounting', 'position' => 'General Manager Accounting', 'name' => 'Chandra Liew', 'username' => 'chandra.liew', 'email' => 'chandra.liew@mnc-project.local'],
            ['order' => 7, 'stage' => 'initial', 'group' => 'Legal', 'position' => 'General Manager Legal', 'name' => 'Gillbert T', 'username' => 'gillbert.t', 'email' => 'gillbert.t@mnc-project.local'],
            ['order' => 8, 'stage' => 'initial', 'group' => 'Board Approval', 'position' => 'Deputy CFO', 'name' => 'Christian', 'username' => 'christian', 'email' => 'christian@mnc-project.local'],
            ['order' => 9, 'stage' => 'final', 'group' => 'Board Approval', 'position' => 'CFO', 'name' => 'Andrea Frans Tambunan', 'username' => 'andrea.frans', 'email' => 'andrea.frans@mnc-project.local'],
            ['order' => 10, 'stage' => 'final', 'group' => 'Board Approval', 'position' => 'President Director', 'name' => 'Suryo Eko Hardianto', 'username' => 'suryo.eko', 'email' => 'suryo.eko@mnc-project.local'],
        ];
    }

    public static function usernames(): array
    {
        return array_column(self::approvers(), 'username');
    }
}
