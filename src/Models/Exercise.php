<?php

/*
 * Modules/Exercise.php: handle exercise data requests
 *
 * Copyright (C) 2021 Eric Marty
 */

namespace Models;

use Database\Record;

class Exercise extends Record
{
    public function table()
    {
        return 'exercises';
    }

    public function config()
    {
        return [
            'exerciseType_id' => function ($entry) {
                return is_numeric($entry);
            },
            'workout_id' => function ($entry) {
                return is_numeric($entry);
            },
            'user_id' => function ($entry) {
                return is_numeric($entry);
            },
            'sets' => function ($entry) {
                return is_numeric($entry);
            },
            'feedback' => function ($entry) {
                return true;
            },
        ];
    }

    public function transforms()
    {
        return [
            'exerciseType_id' => function ($entry) {
                return (int) $entry;
            },
            'workout_id' => function ($entry) {
                return (int) $entry;
            },
            'user_id' => function ($entry) {
                return (int) $entry;
            },
            'sets' => function ($entry) {
                return (int) $entry;
            },
            'feedback' => function ($entry) {
                return trim($entry);
            },
        ];
    }
}
