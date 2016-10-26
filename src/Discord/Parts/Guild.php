<?php

/*
 * This file is a part of the oauth2-discord project.
 *
 * Copyright (c) 2016 David Cole <david@team-reflex.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the LICENSE.md file.
 */

namespace Discord\OAuth\Parts;

use Discord\OAuth\Part;

class Guild extends Part
{
    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'id',
        'name',
        'icon',
        'owner',
        'permissions',
    ];
	
    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
	public function getId()
	{
		return $this->id;
	}
    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
	public function getName()
	{
		return $this->name;
	}
    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
	public function getIcon()
	{
		return $this->icon;
	}
    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
	public function isOwner()
	{
		return $this->owner;
	}
    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
	public function getPermissions()
	{
		return $this->permissions;
	}
}
