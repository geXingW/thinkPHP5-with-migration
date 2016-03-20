<?php

use think\phinx\seed\AbstractSeed;

class UserTableMockData extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $data = [
            [
                'username' => 'user_1',
                'password' => '123456',
                'status'   => 2
            ]
        ];

        $this->insert('user', $data);
    }
}
