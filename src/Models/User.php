<?php

/*
 * Models/User.php: a workout app user
 *
 * Copyright (C) 2021 Eric Marty
 */

namespace Models;

use \Database\Record;
use \Database\Query;

class User extends Record
{
    private const USER_TABLE = 'users';

    /*
     * Load properties from database using attributes.
     * @return bool
     */
    public function load()
    {
        $this->filter($this->config());
        $this->transform($this->transforms());

        $results = Query::select(self::USER_TABLE, "*", $this->attributes);

        // If this select failes it may cause errors TODO
        if (array_key_exists(0, $results))
        {
            // Add to attributes
            foreach ($results[0] as $key => $value)
            {
                $this->attributes[$key] = $value;
            }
        }
        else
        {
            $this->messages[] = "User not found.";
            return false;
        }

        return true;
    }

    /*
     * Login user by verifying user and creating cookie. This funciton will
     * add the cookie to the Response.
     * @return bool
     */
    public function login()
    {
        if ($this->load())
        {
            $this->createNewSession();
            return true;
        }

        return false;
    }

    /*
     * Return the users cookie his function assumes the user has been loaded
     * @return string - cookie
     */
    public function createNewSession()
    {
        $cookie = new Session(['user_id' => $this->id]);
        if ($cookie->load())
        {
            $cookie->setExpiredCookie();
            $cookie->delete();
        }

        $newCookie = new Session(['user_id' => $this->id]);
        $newCookie->createNewCookie($this);
        $newCookie->addCookie();
    }

    public function config()
    {
        return [
            'email' => function ($entry) {
                if (empty($entry))
                    return "Email address should not be empty.";
                return true;
            },
            'password' => function ($entry) {
                if (empty($entry))
                    return "Password should not be empty.";
                return true;
            },
        ];
    }

    public function transforms()
    {
        return [
            'email' => function ($entry) {
                return $entry;
            },
            'password' => function ($entry) {
                return empty($entry) ? null : md5($entry);
            },
        ];
    }
}
