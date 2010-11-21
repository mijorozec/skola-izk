<?php

namespace School\Entities;

/**
 * @Entity
 * @Table(name = "teachers")
 */
class Teacher extends BaseEntity {
    private $firstName;

	private $surname;

	private $class;
}
